<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model;

use RS\Application\Auth;
use RS\Config\Loader;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Exception as RSException;
use RS\Helper\PhpMailer\phpmailerException;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Exception as OrmException;
use RS\Orm\Request;
use RS\Orm\Type\Checker;
use Users\Config\File;
use Users\Model\Orm\User;
use Users\Model\Orm\UserInGroup;

class Api extends EntityList
{
    const RECOVER_TYPE_EMAIL = 'email';
    const RECOVER_TYPE_PHONE = 'phone';
    const RECOVER_TYPE_NONE_EMAIL = 'none';
    const RECOVER_TYPE_NONE_PHONE = 'none_phone';

    const RECOVER_PASSWORD_EMAIL_TPL = '%users%/email/recover_pass.tpl';

    const USER_LIKE_FILTER_GROUPS = 'groups';
        
    protected $group_obj = '\Users\Model\Orm\UserInGroup';
    /**
     * @var File
     */
    protected $config;
    
    function __construct()
    {
        parent::__construct(new User);
        $this->config = Loader::byModule($this);
    }

    /**
     * Устанавливает фильтр для последующей выборки элементов
     *
     * @param string | array $key - имя поля (соответствует имени поля в БД) или массив для установки группового фильтра
     * Пример применения группового фильтра:
     * array(
     *   'title' => 'Название',                     // AND title = 'Название'
     *   '|title:%like%' => 'Текст'                 // OR title LIKE '%Текст%'
     *   '&title:like%' => 'Текст'                  // AND title LIKE 'Текст%'
     *   'years:>' => 18,                           // AND years > 18
     *   'years:<' => 21,                           // AND years < 21
     *   ' years:>' => 30,                          // AND years > 30  #пробелы по краям вырезаются
     *   ' years:<' => 40,                          // AND years < 40  #пробелы по краям вырезаются
     *   'id:in' => '12,23,45,67,34',               // AND id IN (12,23,45,67,34)
     *   '|id:notin' => '44,33,23'                  // OR id NOT IN (44,33,23)
     *   'id:is' => 'NULL'                          // AND id IS NULL
     *   'id:is' => 'NOT NULL'                      // AND id IS NOT NULL
     *
     *   array(                                     // AND (
     *       'name' => 'Артем',                     // name = 'Артем'
     *       '|name' => 'Олег'                      // OR name = 'Олег'
     *   ),                                         // )
     *
     *   '|' => array(                              // OR (
     *       'surname' => 'Петров'                  // surname = 'Петров'
     *       '|surname' => 'Иванов'                 // OR surname = 'Иванов'
     *   )                                          // )
     * )
     * Общая маска ключей массива:
     * [пробелы][&|]ИМЯ ПОЛЯ[:ТИП ФИЛЬТРА]
     *
     * @param mixed $value - значение
     * @param string $type - =,<,>, in, notin, fulltext, %like%, like%, %like тип соответствия поля значению.
     * @param string $prefix условие связки с предыдущими условиями (AND/OR/...)
     * @param array $options
     * @return EntityList
     * @throws RSException
     */
    public function setFilter($key, $value = '', $type = '=', $prefix = 'AND', array $options = [])
    {
        if ($key == 'group')
        {
            $q = $this->queryObj();
            if (!$q->issetTable(new Orm\UserInGroup)) {
                $q->leftjoin(new Orm\UserInGroup, "{$this->def_table_alias}.id = X.user", 'X');
                $q->groupby("{$this->def_table_alias}.id");
            }
                
            parent::setFilter('X.group', $value, $type, 'AND');
            return $this;
        }
        
        return parent::setFilter($key, $value, $type, $prefix, $options);
    }

    /**
     * Возвращает список пользователей, которые соответствуют условиям
     *
     * @param string $term - строка поиска
     * @param array $fields - поля, по которым осущесвлять частичный поиск
     * @param array $filters - фильтры. ключи фильтров перечислены в константах self::USER_LIKE_FILTER_*
     * @param int $limit - количество результатов
     * @return User[] $user
     */
	public function getLike($term, array $fields, $filters = [], $limit = 5)
	{
        $q = Request::make();

        //Фильтр по группе пользователей
        if (isset($filters[self::USER_LIKE_FILTER_GROUPS])) {
            $groups = $filters[self::USER_LIKE_FILTER_GROUPS];
            if ($groups && !in_array('', $groups)) {
                $q->join(new UserInGroup(), 'UG.user = U.id', 'UG');
                $q->whereIn('UG.group', $groups);
            }
        }

        $words = explode(" ", $term);
        $q->openWGroup();
        if (count($words)==1){  
           foreach ($fields as $field) {
              $q->where("$field like '%#term%'", ['term' => $term], 'OR');
           } 
        }else{ //Если несколько полей, проверяем по ФИО
           foreach ($words as $word) {
              if (!empty($word)){
                $q->where("CONCAT(`surname`, `name`, `midname`) like '%#term%'", ['term' => $word], 'AND');
              }
           } 
        }
        $q->closeWGroup();
		
        $q->from($this->obj_instance, 'U')->limit($limit)->exec();
        return $q->objects();
	}

    /**
     * Возвращает пользователя по публичному хэшу
     *
     * @param string $hash
     * @return User $user
     * @throws RSException
     */
	public function getByHash($hash)
	{
		$this->clearFilter();
    	$this->setFilter('hash', $hash);
    	return $this->getFirst();
	}
    
    /**
    * Возвращает уникальный ключ пользователя, основанный на его логине, пароле и id
    * 
    * @param \Users\Model\Orm\User $user
    * @return string
    */
    public static function getUserUniq(Orm\User $user, $xor_key = '')
    {
        $str = $user['login'].$user['id'].\Setup::$SECRET_SALT.$user['pass'];
        return sha1(self::applyXOR($str, $xor_key));
    }
    
    
    /**
    * Искажает строку используя ключ
    * 
    * @param string $string - строка
    * @param string $key - ключ
    * 
    * @return string
    */
    protected static function applyXOR($string, $key)
    {
        for($i = 0; $i < strlen($string); $i++)
            for($j = 0; $j < strlen($key); $j++)
                $string[$i] = $string[$i] ^ $key[$j];
        return $string;
    }

    /**
     * Отправляет письмо с инструкцией по восстановлению данных
     * Предварительно загружает пользователя по полю авторизации
     *
     * @param string $login - Логин пользователя
     * @param bool $admin
     * @return boolean
     * @throws DbException
     * @throws OrmException
     * @throws RSException
     * @throws phpmailerException
     */
    public function sendRecoverEmail($login, $admin = false)
    {
        $user = Auth::getUserByLogin($login);

        if ($user) {
          return $this->sendRecoverEmailByUser($user);
        }
        $this->addError(t('Пользователь не найден'));
        return false;
    }


    /**
     * Отправляет письмо с инструкцией по восстановлению пароля клиенту
     *
     * @param User $user Пользователь, которому необходимо отправить письмо с инструкцией по восстановлению пароля
     * @param bool $admin Если true, значит восстановление происходит в административной панели
     * @return bool
     * @throws phpmailerException
     */
    public function sendRecoverEmailByUser(User $user, $admin = false)
    {
        $uniq = $user['hash'];
        if ($admin) {
            $recover_href = \RS\Router\Manager::obj()->getUrl('main.admin', ['Act' => 'changePassword', 'uniq' => $uniq], true);
        } else {
            $recover_href = \RS\Router\Manager::obj()->getUrl('users-front-auth', ['Act' => 'changePassword', 'uniq' => $uniq], true);
        }
        $host = \RS\http\Request::commonInstance()->getDomainStr();
        $tpl = new \RS\View\Engine();
        $data = [
            'host' => $host,
            'user' => $user,
            'recover_href' => $recover_href
        ];

        $mailer = new \RS\Helper\Mailer();
        $mailer->Subject = t('Восстановление пароля на сайте %0', [$host]);
        $mailer->addEmails($user['e_mail']);
        $mailer->renderBody(self::RECOVER_PASSWORD_EMAIL_TPL, $data);
        $mailer->send();

        return true;
    }

    /**
     * Изменяет пароль пользователя
     *
     * @param mixed $hash
     * @param mixed $new_pass
     * @param mixed $new_pass_confirm
     * @return bool
     * @throws EventException
     */
    public function changeUserPassword(Orm\User $user, $new_pass, $new_pass_confirm)
    {
        if ($new_pass !== $new_pass_confirm) {
            $this->addError(t('Повтор пароля не соответствует паролю'));
            return false;
        }
        $check_result = Orm\User::checkPassword($new_pass);
        if ( $check_result !== true) {
            $this->addError($check_result);
            return false;
        }
        
        $user['openpass'] = $new_pass;
        $user['no_validate_userfields'] = true;
        if ($user->update()) {
            
            // Уведомление пользователю
            $notice = new \Users\Model\Notice\UserRecoverPassUser;
            $notice->init($user, $new_pass);
            \Alerts\Model\Manager::send($notice); 
            
            // Уведомление администратору
            $notice = new \Users\Model\Notice\UserRecoverPassAdmin;
            $notice->init($user, $new_pass);
            \Alerts\Model\Manager::send($notice); 

            return true;
        } else {
            $this->addError( implode(',', $user->getErrors()) );
            return false;
        }
    }

    /**
     * Обработчик, который вызывается во время фильтрации данных на странице со списком
     * в админ. панели
     *
     * @param array of \RS\Html\Filter\Type\AbstractType $items
     * @param \RS\Html\Filter\Control $filter_control
     * @return array
     * @throws RSException
     */
    function beforeSqlWhereCallback($items, $filter_control)
    {
        //Фильтруем по группе
        $group = $items['group']->getValue();
        if ($group !== '') {
            if ($group == 'NULL') $group = null;
            $this->setFilter('group', $group);
        }
        
        //Фильтруем по начальной дате регистрации
        $dateofreg_from = $items['dateofreg_from']->getValue();
        if ($dateofreg_from) {
            $this->setFilter([
                'dateofreg:>=' => $dateofreg_from
            ]);
        }

        //Фильтруем по конечной дате регистрации
        $dateofreg_to = $items['dateofreg_to']->getValue();
        if ($dateofreg_to) {
            $this->setFilter([
                'dateofreg:<=' => $dateofreg_to.' 23:59:59'
            ]);
        }
        
        //Фильтруем по типу цен
        if (isset($items['typecost'])) {
            $typecost = $items['typecost']->getValue();
            if ($typecost !== '') {
                $typecost_str = '"' . $typecost . '"';
                if ($typecost == '0') {
                    $this->setFilter([
                        'cost_id:is' => 'NULL',
                        '|cost_id:%like%' => $typecost_str,
                    ]);
                } else {
                    $this->setFilter('cost_id', $typecost_str, '%like%');
                }
            }
        }
        
        return ['group', 'dateofreg_from', 'dateofreg_to', 'typecost'];
    }

    /**
     * Генерирует новые пароли для пользователей и отправляет соответствующее
     * уведомление на почту пользователей
     *
     * @param array $ids
     * @return bool
     * @throws RSException
     */
    function generatePasswords($ids)
    {
        if ($this->noWriteRights()) return false;
        
        $config = \RS\Config\Loader::byModule($this);
        $user = new $this->obj_instance();
        foreach($ids as $id) {
            if ($user->load($id)) {
                $user['changepass'] = 1;
                $user['openpass'] = \RS\Helper\Tools::generatePassword($config['generate_password_length'], $config['generate_password_symbols']);
                $user->update();
                
                //Отправляем уведомление
                $notice = new \Users\Model\Notice\UserGeneratePassword();
                $notice->init($user, $user['openpass']);
                \Alerts\Model\Manager::send($notice);
            }
        }
        
        return true;
    }

    /**
     * Функция быстрого группового редактирования пользователей
     *
     * @param array $data - массив данных для обновления
     * @param array $ids - идентификаторы товаров на обновление
     * @return void
     * @throws DbException
     * @throws RSException
     */
    function multiUpdate(array $data, $ids = [])
    {
        $useringroup_obj = new \Users\Model\Orm\UserInGroup();
        $useringroup_table = $useringroup_obj->_getTable();
        // обновляем типы цен
        if (isset($data['user_cost'])) {
            $data['cost_id'] = serialize($data['user_cost']);
            unset($data['user_cost']);
        }
        // обновляем группы у пользователей
        if (isset($data['groups'])) {
            \RS\Orm\Request::make()
                ->delete()
                ->from($useringroup_table)
                ->whereIn('user', $ids)
                ->exec();
            
            if (!empty($data['groups'])) {
                $insert_values = [];
                foreach ($ids as $user_id) {
                    foreach ($data['groups'] as $group) {
                        $insert_values[] = "($user_id, '$group')";
                    }
                }
                $sql = "INSERT INTO ".$useringroup_table." (`user`, `group`) VALUES " . implode(', ', $insert_values);
                \RS\Db\Adapter::sqlExec($sql);
            }
            
            unset($data['groups']);
        }
        
        parent::multiUpdate($data, $ids);
    }

    /**
     * Нормализует номер телефона.
     * Заменяет 8 на +7 (настраивается в настройках модуля)
     * Добавляет код страны +7 (настраивается), если он не указан
     *
     * @param string $number
     * @return string
     */
    public static function normalizePhoneNumber($number)
    {
        if (strlen($number)>0) {
            $config = Loader::byModule(__CLASS__);
            $number = ($number[0] == '+' ? '+' : '') . preg_replace('/[^0-9\*\#]/', '', $number);

            if ($config['default_country_phone_code'] && mb_substr($number, 0,1) != '+') {

                //Разница в количестве знаков локального и международного кода. Для России = 0 (8 и 7), для Украины = 1 (8 и 38)
                if ($config['replace_country_phone_code'] !== '') {
                    $delta = abs(strlen($config['default_country_phone_code']) - strlen($config['replace_country_phone_code']));
                } else {
                    $delta = 0;
                }

                if (strlen($number) == $config['country_phone_length'] - $delta
                    && $config['replace_country_phone_code'] !== ''
                    && substr($number,0, strlen($config['replace_country_phone_code'])) == $config['replace_country_phone_code']) {

                    //Получаем номер без локального кода
                    $number = substr($number, strlen($config['replace_country_phone_code']));
                }

                if (strlen($number) == ($config['country_phone_length'] - strlen($config['default_country_phone_code']))) {
                    //Добавляем межднародный код
                    $number = '+' . $config['default_country_phone_code'] . $number;
                }

                if (strlen($number) == $config['country_phone_length']
                    && substr($number, 0, strlen($config['default_country_phone_code'])) == $config['default_country_phone_code']) {
                    $number = '+' .$number;
                }
            }
        }

        return $number;
    }

    /**
     * Выполняет нормализацию телефонных номеров старых пользователей.
     * Возвращает количество нормализованных номеров
     *
     * @return integer
     * @throws DbException
     * @throws RSException
     */
    public static function normalizePhonesOldUsers()
    {
        $users = Request::make()
            ->select('id, phone')
            ->from(new User())
            ->where("phone != ''")
            ->exec()->fetchAll();

        $query = Request::make()
            ->update(new User());

        $counter = 0;
        foreach($users as $user) {
            $normalized_phone = self::normalizePhoneNumber($user['phone']);
            if ($normalized_phone !== $user['phone']) {
                $counter++;
                $query->set = '';
                $query->where = '';

                $query
                    ->set([
                        'phone' => $normalized_phone
                    ])
                    ->where([
                        'id' => $user['id']
                    ])
                    ->exec();
            }
        }

        return $counter;
    }

    /**
     * Возвращает тип восстановления пароля, который подходит для $login и $user
     * с учетом текущих настроек системы
     *
     * @param string $login
     * @param User $user
     *
     * @return string
     */
    public function getRecoverTypeByLogin($login, User $user)
    {
        $config = Loader::byModule($this);
        if ($config['type_auth'] == File::TYPE_AUTH_STANDARD || !$config['two_factor_recover']) {
            $recover_type = self::RECOVER_TYPE_EMAIL;
        } else {
            if (Checker::chkPattern(null, $login, false, '/^[0-9()\-\s+,]+$/')) {
                //Если введен телефон, то восстанавливаем только на телефон
                $recover_type = self::RECOVER_TYPE_PHONE;
            } else {
                //Если Email или логин, то отправляем письмо на Email, а если его нет, то на телефон
                $recover_type = $user['e_mail'] != '' ? self::RECOVER_TYPE_EMAIL : self::RECOVER_TYPE_PHONE;
            }
        }

        //Финальная проверка на возможность
        if ($recover_type == self::RECOVER_TYPE_EMAIL && $user['e_mail'] == '') {
            $recover_type = self::RECOVER_TYPE_NONE_EMAIL;
        }
        elseif ($recover_type == self::RECOVER_TYPE_PHONE && $user['phone'] == '') {
            $recover_type = self::RECOVER_TYPE_NONE_PHONE;
        }

        return $recover_type;
    }

    /**
     * Возвращает текст для placeholder'а поля логин
     *
     * @return string
     */
    public function getAuthLoginPlaceholder()
    {
        return $this->getPlaceholderForFields($this->config->getAuthLoginFields());
    }

    /**
     * Возвращает placeholder для поля ввода логина для восстановления пароля
     *
     * @return string
     */
    public function getRecoverLoginPlaceholder()
    {
        $fields = array_flip($this->config->getAuthLoginFields());

        if ($this->config['type_auth'] == File::TYPE_AUTH_STANDARD) {
            //При стандартной авторизации восстановить пароль по телефону нельзя, только по email'у
            unset($fields['phone']);
        }

        return $this->getPlaceholderForFields(array_keys($fields));
    }

    /**
     * Заменяет последнее вхождение $search в строку $subject на $replace
     *
     * @param $search
     * @param $replace
     * @param $subject
     * @return mixed
     */
    private function getPlaceholderForFields($fields)
    {
        $list = [];
        foreach($fields as $field) {
            if ($this->config->fieldIsLogin($field)) {
                $list[] = $this->config->getUserFieldName($field);
            }
        }

        $search = ', ';
        $subject = implode(', ', $list);
        $pos = mb_strrpos($subject, $search);
        if($pos !== false){
            $subject = mb_substr($subject, 0, $pos).t(' или ').mb_substr($subject, $pos + mb_strlen($search));
        }

        return $subject;
    }

}