<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Users\Model\Orm;

use Alerts\Model\Manager as AlertsManager;
use Catalog\Model\CurrencyApi;
use RS\Config\Cms as RSConfig;
use RS\Config\Loader as ConfigLoader;
use RS\Config\UserFieldsManager;
use RS\Db\Exception as DbException;
use RS\Exception as RSException;
use RS\Helper\CustomView;
use RS\Orm\Exception as OrmException;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use Users\Model\OrmType as CustomType;
use RS\Router\Manager as RouterManager;
use Shop\Model\TransactionApi;
use Site\Model\Orm\Site;
use Users\Config\File as UsersConfig;
use Users\Model\Api;
use Users\Model\GroupApi;
use Users\Model\Notice\UserRegisterAdmin as NoticeUserRegisterAdmin;
use Users\Model\Notice\UserRegisterUser as NoticeUserRegisterUser;
use Users\Model\Verification\Action\TwoStepRegister;

/**
 * Объект - пользователь системы.
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $fio Ф.И.О.
 * @property string $name Имя
 * @property string $surname Фамилия
 * @property string $midname Отчество
 * @property string $e_mail E-mail
 * @property string $login Логин
 * @property string $openpass Новый пароль
 * @property string $pass Пароль
 * @property string $phone Телефон
 * @property string $sex Пол
 * @property string $hash Ключ
 * @property integer $subscribe_on Получать рассылку
 * @property string $dateofreg Дата регистрации
 * @property float $balance Баланс
 * @property string $balance_sign Подпись баланса
 * @property string $ban_expire Заблокировать до ...
 * @property string $ban_reason Причина блокировки
 * @property string $last_visit Последний визит
 * @property string $last_ip Последний IP, который использовался
 * @property string $registration_ip IP пользователя при регистрации
 * @property integer $is_enable_two_factor Включить двухфакторную авторизацию для данного пользователя
 * @property integer $is_company Это юридическое лицо?
 * @property string $company Название организации
 * @property string $company_inn ИНН организации
 * @property array $groups Группы
 * @property array $data 
 * @property integer $changepass Сменить пароль
 * @property string $_serialized 
 * --\--
 */
class User extends OrmObject
{
    const SESSION_LAST_VISIT_VAR = 'last_visit';
    const PASSWORD_LEN = 4;
    const SUPERVISOR_GROUP = 'supervisor';

    protected static $table = "users";

    protected $default_group = 'guest'; //Группа, к которой определять пользователя по умолчанию
    protected $authorized_user_group = 'clients'; //Группа, к которой относятся все авторизованные пользователи
    protected $access_menu_table;
    protected $access_module_table;
    protected $cache_mod_access;
    protected $cache_menu_access;
    protected $cache_admin_menu_access;
    /** @var Site[] */
    protected $cache_allow_sites;
    protected $groups = [];
    protected $ignore_client_side_groups;
    protected $link_to_auth_in_error;


    function __construct($id = null, $cache = true)
    {
        $this->access_menu_table = \Setup::$DB_TABLE_PREFIX . 'access_menu';
        $this->access_module_table = \Setup::$DB_TABLE_PREFIX . 'access_module';
        parent::__construct($id, $cache);
    }

    protected function _init()
    {
        parent::_init();

        $for_company = ['is_company' => 1];
        $chk_depend = [get_class($this), 'chkDepend'];

        $this->getPropertyIterator()->append([
            t('Основные'),
                'fio' => new Type\Varchar([
                    'description' => t('Ф.И.О.'),
                    'visible' => false,
                    'runtime' => true,
                ]),
                'name' => new Type\Varchar([
                    'maxLength' => '100',
                    'description' => t('Имя'),
                    'meVisible' => false,
                    'checker' => [[__CLASS__, 'checkUserNameField'], t('Укажите, пожалуйста, Имя'), 'name'],
                ]),
                'surname' => new Type\Varchar([
                    'maxLength' => '100',
                    'description' => t('Фамилия'),
                    'meVisible' => false,
                    'checker' => [[__CLASS__, 'checkUserNameField'], t('Укажите, пожалуйста, Фамилию'), 'surname'],
                ]),
                'midname' => new Type\Varchar([
                    'maxLength' => '100',
                    'description' => t('Отчество'),
                    'meVisible' => false,
                    'checker' => [[__CLASS__, 'checkUserNameField'], t('Укажите, пожалуйста, Отчество'), 'midname'],
                ]),
                'e_mail' => new Type\Varchar([
                    'maxLength' => '150',
                    'description' => 'E-mail',
                    'checker' => [[__CLASS__, 'checkAuthField'], null, 'e_mail'],
                    'meVisible' => false,
                    'trimString' => true,
                ]),
                'login' => new Type\Varchar([
                    'maxLength' => '64',
                    'unique' => true,
                    'description' => t('Логин'),
                    'checker' => [[__CLASS__, 'checkAuthField'], null, 'login'],
                    'meVisible' => false,
                ]),
                'openpass' => new Type\Varchar([
                    'maxLength' => '100',
                    'description' => t('Новый пароль'),
                    'runtime' => true,
                    'Attr' => [['size' => '20', 'type' => 'password', 'autocomplete' => 'off']],
                    'Checker' => [[__CLASS__, 'checkOpenPassword'], ''],
                    'appVisible' => false,
                    'meVisible' => false,
                ]),
                'pass' => new Type\Varchar([
                    'maxLength' => '32',
                    'description' => t('Пароль'),
                    'Attr' => [['size' => '20', 'type' => 'password', 'autocomplete' => 'off']],
                    'listenPost' => false,
                    'visible' => false,
                    'meVisible' => false,
                ]),
                'phone' => new CustomType\VerifiedPhone([
                    'maxLength' => '50',
                    'description' => t('Телефон'),
                    'checker' => [[__CLASS__, 'checkAuthField'], null, 'phone'],
                    'meVisible' => false,
                    'verificationAction' => new TwoStepRegister()
                ]),
                'sex' => new Type\Varchar([
                    'maxLength' => '1',
                    'description' => t('Пол'),
                    'ListFromArray' => [['' => t('Не выбрано'), 'M' => t('Мужской'), 'F' => t('Женский')]],
                    'Attr' => [[]],
                    'meVisible' => false,
                ]),
                'hash' => new Type\Varchar([
                    'maxLength' => '64',
                    'index' => true,
                    'description' => t('Ключ'),
                    'visible' => false,
                    'meVisible' => false,
                ]),
                'subscribe_on' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Получать рассылку'),
                    'CheckBoxView' => [1, 0],
                    'meVisible' => false,
                ]),
                'dateofreg' => new Type\Datetime([
                    'description' => t('Дата регистрации'),
                    'meVisible' => false,
                ]),
                'balance' => new Type\Decimal([
                    'allowEmpty' => false,
                    'readOnly' => true,
                    'listenPost' => false,
                    'maxLength' => '15',
                    'decimal' => 2,
                    'description' => t('Баланс'),
                    'appVisible' => false,
                    'meVisible' => false,
                ]),
                'balance_sign' => new Type\Varchar([
                    'visible' => false,
                    'listenPost' => false,
                    'description' => t('Подпись баланса'),
                    'meVisible' => false,
                ]),
                'ban_expire' => new Type\Datetime([
                    'description' => t('Заблокировать до ...'),
                    'template' => '%users%/form/user/ban_expire.tpl',
                    'meVisible' => false,
                ]),
                'ban_reason' => new Type\Varchar([
                    'description' => t('Причина блокировки'),
                    'visible' => false,
                    'meVisible' => false,
                ]),
                'last_visit' => new Type\Datetime([
                    'description' => t('Последний визит'),
                    'meVisible' => false,
                ]),
                'last_ip' => new  Type\Varchar([
                    'description' => t('Последний IP, который использовался'),
                    'maxLength' => 100,
                    'meVisible' => false,
                ]),
                'registration_ip' => new Type\Varchar([
                    'description' => t('IP пользователя при регистрации'),
                    'maxLength' => 100,
                    'meVisible' => false,
                ]),
                'is_enable_two_factor' => new Type\Integer([
                    'description' => t('Включить двухфакторную авторизацию для данного пользователя'),
                    'hint' => t('Актуально только, если в настройках модуля Пользователи и группы установлен тип авторизации "Двухфакторная авторизация" и опция "Использовать двухфакторный вход" имеет значение "Да, у некоторых пользователей"'),
                    'checkboxView' => [1, 0],
                    'allowEmpty' => false
                ]),
            t('Организация'),
                'is_company' => new Type\Integer([
                    'maxLength' => '1',
                    'description' => t('Это юридическое лицо?'),
                    'ListFromArray' => [['0' => t('Нет'), '1' => t('Да')]],
                    'meVisible' => false,
                ]),
                'company' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Название организации'),
                    'condition' => $for_company,
                    'Checker' => [$chk_depend, t('Не указано название организации'), 'chkEmpty', $for_company],
                    'meVisible' => false,
                ]),
                'company_inn' => new Type\Varchar([
                    'maxLength' => '12',
                    'description' => t('ИНН организации'),
                    'condition' => ['is_company' => 1],
                    'Checker' => [$chk_depend, t('ИНН должен состоять из 10 или 12 цифр'), 'chkPattern', $for_company, ['/^(\d{10}|\d{12})$/']],
                    'attr' => [[
                        'size' => 20
                    ]],
                    'meVisible' => false,
                ]),
            t('Группы'),
                '__groups__' => new Type\UserTemplate('%users%/form/user/groups.tpl', '%users%/form/user/megroups.tpl', [
                    'meVisible' => true,
                ]),
                'groups' => new Type\ArrayList([
                    'description' => t('Группы'),
                    'appVisible' => false
                ]),
            t('Дополнительные сведения'),
                'data' => new Type\ArrayList([
                    'description' => '',
                    'template' => '%users%/form/user/userfields.tpl',
                    'meVisible' => false,
                    'checker' => [[__CLASS__, 'checkCustomUserFields']]
                ]),
                'changepass' => new Type\Integer([
                    'description' => t('Сменить пароль'),
                    'runtime' => true,
                    'CheckBoxView' => [1, 0],
                    'visible' => false,
                    'Attr' => [['id' => 'chpass']],
                ]),
                '_serialized' => new Type\Text([
                    'visible' => false,
                ]),
                'captcha' => new Type\Captcha([
                    'enable' => false
                ])
        ]);
    }

    /**
     * Проверяет на корректность заполнения
     *
     * @param User $_this
     * @param string $value
     * @return bool|string
     */
    public static function checkFioField($_this, $value)
    {
        /** @var UsersConfig $config */
        $config = ConfigLoader::byModule(__CLASS__);

        if ($config['user_one_fio_field']) {
            $fields = [
                'surname' => t('Фамилию'),
                'name' => t('Имя'),
                'midname' => t('Отчество')
            ];
            $need_titles = [];

            foreach ($fields as $field => $title) {
                if ($config->fieldIsRequire($field)) {
                    $need_titles[] = $title;
                }
            }
            $fio_parts = self::explodeFio($value);

            if (count($fio_parts) < count($need_titles)) {
                return t('Укажите ') . implode(', ', $need_titles);
            }
        }

        return true;
    }

    /**
     * Проверяет Имя или Фамилию на заполненность, если соответствующие опции включены
     *
     * @param User $_this
     * @param mixed $value
     * @param string $error_text
     * @param string $field
     *
     * @return bool|string
     */
    public static function checkUserNameField($_this, $value, $error_text, $field)
    {
        /** @var UsersConfig $config */
        $config = ConfigLoader::byModule($_this);
        if ($config->fieldIsRequire($field) && (empty($_this['fio']) && $value == '')) {
            return $error_text;
        }
        return true;
    }

    /**
     * Проверяет пароль на соответствие требованиям безопасности
     *
     * @param User $_this - проверяемый ORM - объект
     * @param mixed $value - проверяемое значение
     * @return bool(true) | string возвращает true в случае успеха, иначе текст ошибки
     */
    public static function checkOpenPassword($_this, $value)
    {
        if ($_this['changepass']) {
            if (mb_strlen($value) < self::PASSWORD_LEN) {
                return t('Пароль должен содержать не менее %len знаков', ['len' => self::PASSWORD_LEN]);
            }
        }
        return true;
    }

    /**
     * Проверяет на корректность произвольные поля пользователя, заданные в настройках модуля Users
     * Возвращает true, в случае отсутствия ошибок
     *
     * @param self $_this
     * @param array $value
     * @return bool(true) | null
     */
    public static function checkCustomUserFields($_this, $value)
    {
        //Сохраняем дополнительные сведения о пользователе
        $user_fields = $_this->getUserFieldsManager();
        $ok = $user_fields->check($value);

        if (!$ok) {
            foreach ($user_fields->getErrors() as $form => $error_text) {
                $_this->addError($error_text, $form);
            }
            return null; //Не устанавливать ошибку полю data
        }

        return true;
    }

    /**
     * Проверяет корректность заполнения поля, которое может участвовать
     * в авторизации (логин, email, телефон)
     *
     * @param User $_this объект пользователя
     * @param string $value значение поля
     * @param string $error_text Текст ошибки по умолчанию
     * @param string $field Проверяемое поле
     * @return bool(true) | string
     */
    public static function checkAuthField(User $_this, $value, $error_text, $field)
    {
        /** @var UsersConfig $config */
        $config = ConfigLoader::byModule($_this);
        $value = trim($value);

        $field_title = mb_strtolower($_this->getProp($field)->getDescription());

        if ($value != '') {
            //Проверяем корректность значения в авторизационном поле
            $value_error = $_this->getLoginFieldValueError($field);
            if ($value_error !== true) {
                return $value_error;
            }

            if ($field == 'phone') {
                $value = Api::normalizePhoneNumber($value);
            }

            //Проверяем на уникальность.
            //Логин уникален всегда,
            //Email и Телефон - уникален только, если по данным полям включена авторизация
            if ($_this->isAuthFieldUniq($field)) {
                if (!$_this->isFieldAvailable($field, $value)) {

                    $error_text = t('Такой %0 уже занят', [$field_title]);
                    if ($_this->link_to_auth_in_error) {
                        $error_text .= t(', <a href="%0" class="inDialog">авторизуйтесь</a>', [$config->getAuthorizationUrl()]);
                    }
                    return $error_text;
                }
            }

            //Проверяем на уникальность среди всех полей аторизации у других пользователей.
            //Например, нет ли у кого в поле e_mail такого же значения как в login текущего пользователя
            if (!$_this->checkCrossUniqField($field, $value)) {
                return t('Такой %0 уже занят другим пользователем', [$field_title]);
            }

        } elseif ($config->fieldIsRequire($field)) {
            return t('%0 - обязательное поле', [$field_title]);
        }

        return true;
    }

    /**
     * Возвращает true, если не существует других пользователей со значением $value в поле $field
     *
     * @param $field
     * @param $value
     * @return bool
     */
    private function isFieldAvailable($field, $value)
    {
        return OrmRequest::make()
                ->from($this)
                ->where([$field => $value])
                ->where("id != '#this_id'", ['this_id' => $this['id'] ?? 0])
                ->count() == 0;
    }

    /**
     * Возвращает false, если в поле $field нет ошибок
     * Возвращает текст ошибки, если в поле $field она допущена
     *
     * @param string $field
     * @return bool(false) | string
     */
    private function getLoginFieldValueError($field)
    {
        switch($field) {
            case 'e_mail':
                return Type\Checker::chkEmail($this, $this[$field], t('Неверно указан Email'));
            case 'phone':
                return Type\Checker::chkPhone($this, $this[$field]);
        }
        return true;
    }

    /**
     * Возвращает true, если при текущих настройках авторизации поле $field может иметь такое значение
     * Проверяет, нет ли дублей в других полях авторизации у других пользователей.
     *
     * @param $field
     * @param $value
     * @return bool
     */
    private function checkCrossUniqField($field, $value)
    {
        /** @var UsersConfig $config */
        $config = ConfigLoader::byModule($this);
        $cross_fields = [];

        if ($field == 'login') {
            if ($config->fieldIsLogin('e_mail')) {
                $cross_fields[] = 'e_mail';
            }
            if ($config->fieldIsLogin('phone')) {
                $cross_fields[] = 'phone';
            }
        }
        elseif ($field == 'e_mail' || $field == 'phone') {
            if ($config->fieldIsLogin('login')) {
                $cross_fields[] = 'login';
            }
        }

        if ($cross_fields) {
            $q = OrmRequest::make()
                ->from($this)
                ->where("id != #0", [$this['id'] ?? 0])
                ->openWGroup();

            foreach($cross_fields as $cross_field) {
                $q->where([
                    $cross_field => $value
                ], null, 'OR');
            }
            $q->closeWGroup();

            return $q->count() == 0;
        }

        return true;
    }


    /**
     * Возвращает true, если поле должно быть уникально
     *
     * @param string $field
     * @return bool
     */
    public function isAuthFieldUniq($field)
    {
        if ($field == 'login') return true;

        /** @var UsersConfig $config */
        $config = ConfigLoader::byModule($this);
        return $config->fieldIsLogin($field);
    }

    /**
     * Действия перед записью объекта
     *
     * @param string $flag - insert или update
     * @return bool
     */
    public function beforeWrite($flag)
    {
        if ($this['id'] < 0) {
            $this['_tmpid'] = $this['id'];
            unset($this['id']);
        }

        $ret = true;
        //Создаем новый произвольный ключ при каждом сохранении
        $this->updateHash(false);
        $this['_serialized'] = serialize($this['data']);

        if ($flag == self::INSERT_FLAG) {
            $this['dateofreg'] = date('Y-m-d H:i:s');
            $this['registration_ip'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        }

        if (!empty($this['fio'])) {
            $fio_parts = self::explodeFio($this['fio']);
            $this['surname'] = (isset($fio_parts[0])) ? $fio_parts[0] : '';
            $this['name'] = (isset($fio_parts[1])) ? $fio_parts[1] : '';
            $this['midname'] = (isset($fio_parts[2])) ? $fio_parts[2] : '';
        }

        if (!empty($this['openpass'])) {
            $this['pass'] = self::cryptPass($this['openpass']);
        }

        foreach(['ban_expire', 'login', 'e_mail', 'phone'] as $field) {
            if ($this[$field] == '') {
                $this[$field] = null;
            }
        }

        if ($ret) {
            //Нормализуем номер телефона
            $this['phone'] = Api::normalizePhoneNumber($this['phone']);
        }

        return $ret;
    }

    /**
     * Разделяет ФИО на фамилию, имя и отчество
     *
     * @param string $value - строка с ФИО
     * @return string[]
     */
    public static function explodeFio($value)
    {
        //Парсит строку так: первое слово - фамилия, второе - имя, все остальное - отчество
        //Необходимо для тюркских отчеств, например, для Мамедов Ильгар Натиг Оглы, где Натиг Оглы - отчество
        preg_match('/^([^\s]+)\s*([^\s]+)?\s*(.+)?$/u', trim($value), $match);
        array_shift($match);
        return $match;
    }

    /**
     * Действия после записи объекта
     *
     * @param string $flag - insert или update
     */
    public function afterWrite($flag)
    {
        if ($flag == self::INSERT_FLAG && \Setup::$INSTALLED && !$this['no_send_notice']) {

            // Уведомление пользователю
            $notice = new NoticeUserRegisterUser;
            $notice->init($this, $this['openpass']);
            AlertsManager::send($notice);

            $site_config = ConfigLoader::getSiteConfig();
            if ($site_config['admin_email']) {
                // Уведомление администратору
                $notice = new NoticeUserRegisterAdmin;
                $notice->init($this, $this['openpass']);
                AlertsManager::send($notice);
            }
        }

        if ($this->isModified('groups')) {
            $this->linkGroup($this['groups']);
        }

    }

    /**
     * Действия после того как подгружен объект
     *
     * @return void
     */
    public function afterObjectLoad()
    {
        $this['data'] = (array)@unserialize($this['_serialized']);
    }

    /**
     * Возвращает группы, в которых состоит пользователь
     * В ReadyScript предусмотрены системные группы пользователей, к которым
     * пользователь автоматически причисляется при следующих условиях:
     * Гость  - присваивается всем пользователям, находящимся в клиентской части
     * Клиент - присваивается всем авторизованным пользователям, находящимся в клиентской части
     *
     * @param boolean $returnAliases - Если true, то возвращает массив с alias'ами групп, иначе array
     * @return string[]|UserGroup[]
     */
    public function getUserGroups($returnAliases = true)
    {
        $add_client_side_groups = !RouterManager::obj()->isAdminZone() && !$this->ignore_client_side_groups;
        $cache_key = ($add_client_side_groups) ? 'client' : 'default';

        if (!isset($this->groups[$cache_key])) {
            $this->groups[$cache_key] = [];

            if ($add_client_side_groups) {
                $this->groups[$cache_key] += $this->getClientSideGroups();
            }

            if ($this['id'] > 0) {
                $this->groups[$cache_key] += OrmRequest::make()->select('G.*')
                    ->from(new UserGroup())->asAlias('G')
                    ->from(new UserInGroup())->asAlias('I')
                    ->where("I.group = G.alias AND I.user='#user'", ['user' => $this['id']])
                    ->objects('\Users\Model\Orm\UserGroup', 'alias');
            }
        }

        return $returnAliases ? array_keys($this->groups[$cache_key]) : $this->groups[$cache_key];
    }

    /**
     * Устанавливает флаг "игнорировать группы клиентской стороны"
     *
     * @param bool $value - значение
     * @return void
     */
    public function setIgnoreClientSideGroups($value = true)
    {
        $this->ignore_client_side_groups = $value;
    }

    /**
     * Возвращает системные группы пользователей, к которым пользователь автоматически причисляется при нахождении в клиенской части:
     * Гость  - присваивается всем пользователям,
     * Клиент - присваивается всем авторизованным пользователям,
     *
     * return UserGroup[]
     */
    public function getClientSideGroups()
    {
        $groups = [
            $this->default_group => new UserGroup($this->default_group),
        ];
        if ($this['id'] > 0) {
            $groups[$this->authorized_user_group] = new UserGroup($this->authorized_user_group);
        }
        return $groups;
    }

    /**
     * Проверяет состоит ли пользователь в группе
     *
     * @param string $group_alias - идентификатор группы пользователей
     * @return bool
     */
    public function inGroup($group_alias)
    {
        $groups = $this->getUserGroups(false);
        return isset($groups[$group_alias]);
    }

    /**
     * Возвращает True, если пользователь состоит в группе с отметкой "Администратор"
     * Пользователи такой группы могут входить в административную панель.
     *
     * @return bool
     */
    public function isAdmin()
    {
        $groups = $this->getUserGroups(false);
        $is_admin = false;
        foreach ($groups as $group) {
            $is_admin = $is_admin || $group['is_admin'];
        }
        return $is_admin;
    }

    /**
     * Помещает пользователя в группу
     *
     * @param array $groups - массив групп в которые нужно добавить
     * @return void
     */
    public function linkGroup(array $groups)
    {
        $uig = new UserInGroup();
        $uig->linkUserToGroup($this['id'], $groups);
    }

    /**
     * Добавляет группу к уже существующим и пользователя группам
     *
     * @param string $groupid - алиас группы
     * @return void
     */
    function addGroup($groupid)
    {
        $user_groups = OrmRequest::make()
            ->from(new UserInGroup())
            ->where([
                'user' => $this['id']
            ])
            ->objects();
        if ($user_groups) { //Если состоит в группах
            foreach ($user_groups as $group) {
                if ($groupid == $group['group']) { //Если такая группа уже существует
                    return;
                }
            }
        }

        $user_group = new UserInGroup();
        $user_group['user'] = $this['id'];
        $user_group['group'] = $groupid;
        $user_group->insert();
    }

    /**
     * Удаляет пользователя из группы $group_id или из всех групп
     *
     * @param $group_id - ID группы
     * @return void
     */
    function unlinkGroup($group_id = null)
    {
        OrmRequest::make()
            ->delete()
            ->from(new UserInGroup())
            ->where(['user' => $this['id']]
                + ($group_id ? ['group' => $group_id] : [])
            )->exec();
    }

    /**
     * Возвращает права доступа ко всем модулям с учетом группы,
     * к которой принадлежит пользователь.
     *
     * @return array
     */
    function getModuleAccess()
    {
        if (isset($this->cache_mod_access)) return $this->cache_mod_access;

        $groups = $this->getUserGroups();
        if (in_array(self::SUPERVISOR_GROUP, $groups)) {
            $mod_right[AccessModule::FULL_MODULE_ACCESS] = AccessModule::MAX_ACCESS_RIGHTS;
        } else {
            $q = OrmRequest::make()
                ->select('module, BIT_OR(access) as access')
                ->from($this->access_module_table)
                ->where(empty($this['id']) ? null : "user_id={$this['id']}")
                ->groupby('module');

            if ($groups) {
                $q->whereIn('group_alias', $groups, "OR");
            }

            $mod_right = $q->exec()->fetchSelected('module', 'access');
        }

        $this->cache_mod_access = $mod_right;
        return $this->cache_mod_access;
    }

    /**
     * Возвращет права оступа к конкретному модулю, с учетом группы к кторой принадлежит пользователь
     *
     * @param string $module - название модуля
     * @return int 0 - нет прав, 255 - полный права
     */
    function getRight($module)
    {
        $module = strtolower($module);
        $mod_access = $this->getModuleAccess();
        if (isset($mod_access['all'])) return $mod_access['all'];
        return isset($mod_access[$module]) ? $mod_access[$module] : 0;
    }

    /**
     * Проверяет наличие у пользователя переданного права к модулю
     *
     * @param string $module - имя модуля
     * @param string $right - идентификатор права
     * @return bool
     * @throws DbException
     * @throws OrmException
     * @throws RSException
     */
    function checkModuleRight($module, $right)
    {
        $groups = $this->getUserGroups();

        if (in_array(self::SUPERVISOR_GROUP, $groups)) {
            return RSConfig::ACCESS_ALLOW;
        }

        $rights = GroupApi::getRights($groups);
        if (isset($rights[$module][$right]) && count($rights[$module][$right]) == 1) {
            $result = reset($rights[$module][$right]);
        } else {
            $config_cms = ConfigLoader::getSystemConfig();
            $result = $config_cms['access_priority'];
        }

        return $result == RSConfig::ACCESS_ALLOW;
    }

    /**
     * Возвращает права доступа к пунктам меню пользовательской части с учетом группы,
     * к которой принадлежит пользователь
     *
     * @return array
     */
    function getMenuAccess()
    {
        if (isset($this->cache_menu_access)) return $this->cache_menu_access;
        $groups = $this->getUserGroups();

        if (in_array(self::SUPERVISOR_GROUP, $groups)) {
            $menu_access = [
                AccessMenu::FULL_USER_ACCESS
            ];
        } else {
            $q = OrmRequest::make()
                ->select('menu_id')
                ->from($this->access_menu_table)
                ->where(['menu_type' => 'user'])
                ->openWGroup()
                ->where(empty($this['id']) ? null : "user_id={$this['id']}");

            if ($groups) {
                $q->whereIn('group_alias', $groups, "OR");
            }

            $q->closeWGroup()
                ->groupby('menu_id');

            $menu_access = $q->exec()->fetchSelected(null, 'menu_id');
        }
        $this->cache_menu_access = $menu_access;

        return $this->cache_menu_access;
    }

    /**
     * Возвращает права доступа к пунктам меню административной панели с учетом группы,
     * к которой принадлежит пользователь
     *
     * @return array
     */
    function getAdminMenuAccess()
    {
        if (isset($this->cache_admin_menu_access)) return $this->cache_admin_menu_access;
        $groups = $this->getUserGroups();

        if (in_array(self::SUPERVISOR_GROUP, $groups)) {
            $menu_access = [
                AccessMenu::FULL_ADMIN_ACCESS
            ];
        } else {
            $q = OrmRequest::make()
                ->select('menu_id')
                ->from($this->access_menu_table)
                ->where(['menu_type' => 'admin'])
                ->openWGroup()
                ->where(empty($this['id']) ? null : "user_id={$this['id']}");
            if ($groups) {
                $q->whereIn('group_alias', $groups, "OR");
            }
            $q->closeWGroup()
                ->groupby('menu_id');

            $menu_access = $q->exec()->fetchSelected(null, 'menu_id');
        }

        $this->cache_admin_menu_access = $menu_access;

        return $this->cache_admin_menu_access;
    }

    /**
     * Удаляет пользователя
     *
     * @return bool
     */
    function delete()
    {
        if ($ret = parent::delete()) {
            $this->unlinkGroup();
        }
        return $ret;
    }

    /**
     * Возвращает хэш от пароля.
     *
     * @param string $password - пароль в открытом виде
     * @return string
     */
    public static function cryptPass($password)
    {
        // для совместимости с магазинами, начинавшими с RS 1.0 разрешаем задать другой способ расчета хеша в классе конфигурации
        if (is_callable(['\Setup', 'cryptPassword'])) {
            return \Setup::cryptPassword($password);
        }
        return md5($password . sha1(\Setup::$SECRET_SALT));
    }

    /**
     * Вернет true, если пользователь будет создан,
     * В случае ошибки - false. Вызовите $this->getLastError(), чтобы увидеть текст ошибки
     * Объект должен быть заполнен даными перед вызовом данного метода
     *
     * @return bool
     */
    public function create()
    {
        if (!$this->checkCreateData()) return false;
        return $this->insert();
    }

    /**
     * Возвращает клонированный объект пользователя
     *
     * @return self
     */
    public function cloneSelf()
    {
        $this['usergroup'] = $this->getUserGroups(); //заполним группы 
        /** @var User $clone */
        $clone = parent::cloneSelf();
        $clone->setTemporaryId();

        unset($clone['e_mail']);
        unset($clone['login']);
        unset($clone['openpass']);
        unset($clone['pass']);

        return $clone;
    }

    /**
     * Проверяет, можно ли создать пользователя с текущими данными.
     * (проверяется уникальность логина, уникальность e-mail'а)
     *
     * @return bool
     */
    public function checkCreateData()
    {
        $res = OrmRequest::make()
            ->select('*')->from($this)
            ->where([
                'login' => $this['login'],
                'e_mail' => $this['e_mail']
            ], null, 'OR')
            ->exec();

        if ($res->rowCount()) {
            $user_row = $res->fetchRow();

            if (!empty($this['login']) && stristr($user_row['login'], $this['login']) !== false)
                $this->addError(t('Пользователь с таким логином уже существует'), 'login');

            if (!empty($this['e_mail']) && stristr($user_row['e_mail'], $this['e_mail']) !== false)
                $this->addError(t('Пользователь с таким E-mail`ом уже существует'), 'e_mail');

            return false;
        }
        return true;
    }

    /**
     * Возвращает массив с данными пользовательских полей
     *
     * @return array
     */
    public function getUserFields()
    {
        $struct = $this->getUserFieldsManager()->getStructure();

        foreach ($struct as &$field) {
            $field['current_val'] = isset($this['data'][$field['alias']]) ? $this['data'][$field['alias']] : $field['val'];
        }

        return $struct;
    }

    /**
     * Возвращает объект - менеджер произвольных полей
     * @return UserFieldsManager
     */
    public function getUserFieldsManager()
    {
        /** @var UsersConfig $config */
        $config = ConfigLoader::byModule($this);
        return $config->getUserFieldsManager()
            ->setErrorPrefix('userfield_')
            ->setArrayWrapper('data')
            ->setValues((array)$this['data']);
    }

    /**
     * Возвращает Фамилию, имя, отчество в одну строку
     *
     * @return string
     */
    public function getFio()
    {
        return trim($this['surname'] . ' ' . $this['name'] . ' ' . $this['midname']);
    }

    /**
     * Обновляет секретный хэш пользователя. Хэш используется для восстановления пароля
     *
     * @param bool $commit - если true, то изменения будут сразу сохранены в базе.
     * @return void
     */
    public function updateHash($commit = true)
    {
        $newhash = md5(uniqid(mt_rand(), true)) . md5(uniqid(mt_rand(), true));
        $this['hash'] = $newhash;
        if ($commit) {
            OrmRequest::make()
                ->update($this)
                ->set([
                    'hash' => $this['hash']
                ])
                ->where("id='{$this['id']}'")
                ->exec();
        }
    }

    /**
     * Возвращает true, если пароль соответствует требованиям, иначе текст ошибки
     *
     * @param string $password - пароль
     * @return bool(true) | string
     */
    public static function checkPassword($password)
    {
        if (mb_strlen($password) < self::PASSWORD_LEN) {
            return t('Пароль должен содержать не менее %len знаков', ['len' => self::PASSWORD_LEN]);
        }
        return true;
    }

    /**
     * Возвращает true, если пользователь является супервизором
     *
     * @return bool
     */
    public function isSupervisor()
    {
        return $this->inGroup(self::SUPERVISOR_GROUP);
    }

    /**
     * Возвращает true, если у пользователя есть права на данный сайт
     * @param integer $site_id - ID сайта
     *
     * @return bool
     */
    public function checkSiteRights($site_id)
    {
        $allow_sites = $this->getAllowSites();
        return in_array($site_id, array_keys($allow_sites));
    }

    /**
     * Возвращает список сайтов, доступных пользователю
     *
     * @return Site[]
     */
    public function getAllowSites()
    {
        if (!isset($this->cache_allow_sites)) {
            $groups = $this->getUserGroups();

            if ($this->isSupervisor()) {
                $q = OrmRequest::make()
                    ->from(new Site());
            } else {
                $q = OrmRequest::make()
                    ->select('S.*')
                    ->from(new AccessSite())->asAlias('A')
                    ->join(new Site(), 'A.site_id = S.id', 'S')
                    ->where(empty($this['id']) ? null : "user_id={$this['id']}");
                if ($groups) {
                    $q->whereIn('group_alias', $groups, "OR");
                }
            }

            $this->cache_allow_sites = $q->objects('\Site\Model\Orm\Site', 'id');
        }
        return $this->cache_allow_sites;
    }

    /**
     * Возвращает текущий остаток на лицевом счете пользователя
     *
     * @param bool $use_currency - если true, то значение будет возвращено в текущей валюте, иначе в базовой
     * @param bool $format - если true, то форматировать возвращаемое значение, приписывать символ валюты
     * @return string
     */
    public function getBalance($use_currency = false, $format = false)
    {
        if ($this->checkBalanceSign()) {
            $balance = ($use_currency) ? CurrencyApi::applyCurrency($this['balance']) : $this['balance'];
        } else {
            $balance = 0;
        }
        if ($use_currency) {
            return $format ? CustomView::cost($balance, CurrencyApi::getCurrecyLiter()) : $balance;
        } else {
            $base_currency = CurrencyApi::getBaseCurrency();
            return $format ? CustomView::cost($balance, $base_currency['stitle']) : $balance;
        }
    }

    /**
     * Возвращает true, если подпись к балансу является корректной
     *
     * @return bool
     */
    public function checkBalanceSign()
    {
        if ($this['balance'] == 0) return true;
        $transApi = new TransactionApi();
        $balance_sign = $transApi->getBalanceSign($this['balance'], $this['id']);
        return $balance_sign == $this['balance_sign'];
    }

    /**
     * Возвращает идентификатор группы пользователей, которая присваивается обязательно всем
     * пользователям в клиентской части сайта.
     *
     * @return string
     */
    public function getDefaultGroup()
    {
        return $this->default_group;
    }

    /**
     * Возвращает идентификатор группы пользователей, которая присваивается всем
     * авторизованным пользователям в клиентской части сайта.
     *
     * @return string
     */
    public function getAuthorizedGroup()
    {
        return $this->authorized_user_group;
    }

    /**
     * Обновляет дату последнего посещения в БД не чаще, чем 1 раз в 4 часа
     *
     * @return void
     */
    public function saveVisitDate()
    {
        $delay = 60 * 60 * 4;
        $datetime = time();

        if ($this['id'] > 0
            && (!isset($_SESSION[self::SESSION_LAST_VISIT_VAR])
                || $datetime - $_SESSION[self::SESSION_LAST_VISIT_VAR] > $delay)
        ) {
            $_SESSION[self::SESSION_LAST_VISIT_VAR] = $datetime;
            $this['last_visit'] = date('Y-m-d H:i:s', $datetime);
            $this['last_ip'] = $_SERVER['REMOTE_ADDR'];

            OrmRequest::make()
                ->update($this)
                ->set([
                    'last_visit' => $this['last_visit'],
                    'last_ip' => $this['last_ip']
                ])
                ->where([
                    'id' => $this['id']
                ])
                ->exec();
        }
    }

    /**
     * Возвращает форматированный мобильный номер телефона пользователя.
     * Если знаков в номере 10, то добавляет в начало номера стандартный код страны, который задаётся в настройках модуля
     *
     * @return string
     */
    public function getFormattedPhoneNumber()
    {
       return Api::normalizePhoneNumber($this['phone']);
    }

    /**
     * Добавляет поле openpass_confirm, которое должно совпадать с openpass
     * Используется при регистрации нового пользователя или в профиле, при смене пароля
     */
    public function enableOpenPassConfirm()
    {
        $this->getPropertyIterator()->append([
            'openpass_confirm' => new Type\Varchar([
                'name' => 'openpass_confirm',
                'maxLength' => '100',
                'description' => t('Повтор пароля'),
                'runtime' => true,
                'Attr' => [['size' => '20', 'type' => 'password', 'autocomplete' => 'off']],
                'checker' => [function($user, $value) {
                    if ($user['changepass'] && strcmp($user['openpass'], $user['openpass_confirm']) != 0) {
                        return t('Пароли не совпадают');
                    }
                    return true;
                }]
            ]),
        ]);
    }

    /**
     * Включает необходимые валидаторы для режима регистрации
     * нового пользователя с учетом текущих настроек системы
     */
    public function enableRegistrationCheckers()
    {
        $config = ConfigLoader::byModule(__CLASS__);

        if ($config['user_one_fio_field']) {
            $this['__fio']->setChecker([__CLASS__, 'checkFioField']);
            $this['__name']->removeAllCheckers();
            $this['__surname']->removeAllCheckers();
            $this['__midname']->removeAllCheckers();
        }
    }

    /**
     * Устанавливает, включать ли в текст ошибки о том, что email, телефон или логин занят,
     * ссылку на авторизацию
     *
     * @param bool $bool
     * @return void
     */
    public function addLinkToAuthInError($bool)
    {
        $this->link_to_auth_in_error = $bool;
    }
}
