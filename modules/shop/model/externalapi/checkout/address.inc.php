<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Checkout;
use ExternalApi\Model\Validator\ValidateArray;
use Main\Model\StatisticEvents;
use \ExternalApi\Model\Exception as ApiException;
use RS\Application\Auth;

/**
* Реализует первый шаг оформления заказа. Этап отправления адреса и оставление контактов.
*/
class Address extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const
        RIGHT_LOAD = 1;
        
    protected
        $token_require = false,
        $register_validator,
        $address_validator;
        
    public
        /**
        * @var \Shop\Model\Orm\OrderApi
        */
        $order_api,
        /**
        * @var \Shop\Model\Orm\Order
        */
        $order,
        $shop_config;
        
    function __construct()
    {
        parent::__construct();
        $this->order     = \Shop\Model\Orm\Order::currentOrder();
        $this->order_api = new \Shop\Model\OrderApi();
        $this->order->clearErrors(); //Очистим ошибки предварительно    
        $this->shop_config = \RS\Config\Loader::byModule('shop'); //Конфиг магазина
    }
    
    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Отправка данных')
        ];
    }
    
    
    /**
    * Проверяет тип ригистрации register
    * 
    * @param array $user - массив со сведениями о пользователе
    */
    private function checkRegister($user)
    {   
        $new_user = new \Users\Model\Orm\User();
        $allow_fields = ['reg_name', 'reg_surname', 'reg_midname', 'reg_phone', 'reg_e_mail',
                                'reg_openpass', 'reg_company', 'reg_company_inn'];
        $reg_fields = array_intersect_key($this->order->getValues(), array_flip($allow_fields));
        
        $new_user->getFromArray($reg_fields, 'reg_');
        $new_user['data']       = $this->order['regfields'];
        $new_user['is_company'] = $user['is_company'];
                            
        if (!$new_user->validate()) {
            foreach($new_user->getErrorsByForm() as $form => $errors) {
                $this->order->addErrors($errors, 'reg_'.$form);
            }
        }
        
        if (!$this->order->hasError()) {
            if ($this->order['reg_autologin']) {
                $new_user['openpass'] = \RS\Helper\Tools::generatePassword(6);
            }
            
            if ($new_user->create()) { //Создание пользователя
                Auth::setCurrentUser($new_user);
                if (Auth::onSuccessLogin($new_user, true)) {
                    $this->order['user_type'] = ''; //Тип регитрации - не актуален после авторизации
                    $this->token = \ExternalApi\Model\TokenApi::createToken($new_user['id'], $user['client_id']);                     
                } else {
                    throw new ApiException(t('Не удалось авторизоваться под созданным пользователем.'), -1);                       
                }
            } else {
                $this->order->addErrors($new_user->getErrorsByForm('e_mail'), 'reg_e_mail');
            }
            
        }  
    }
    
    
    /**
    * Проверяет тип ригистрации noregister
    * 
    * @param array $user - массив со сведениями о пользователе
    */
    private function checkNoRegiter($user)
    {
        //Получим данные 
        $this->order['user_fio']   = isset($user['user_fio'])   ? $user['user_fio']   : ""; 
        $this->order['user_email'] = isset($user['user_email']) ? $user['user_email'] : "";
        $this->order['user_phone'] = isset($user['user_phone']) ? $user['user_phone'] : ""; 

        //Проверим данные
        if (empty($this->order['user_fio'])){
           $this->order->addError(t('Укажите, пожалуйста, Ф.И.О.'), 'user_fio');
        }
        if ($this->shop_config['require_email_in_noregister'] && !filter_var($this->order['user_email'], FILTER_VALIDATE_EMAIL)){
           $this->order->addError(t('Укажите, пожалуйста, E-mail'), 'user_email');
        }
        if ($this->shop_config['require_phone_in_noregister']) {
            if (empty($this->order['user_phone'])) {
                $this->order->addError(t('Укажите, пожалуйста, Телефон'), 'user_phone');
            } elseif (!preg_match('/^[0-9()\-\s+,]+$/', $this->order['user_phone'])) {
                $this->order->addError(t('Неверно указан телефон'), 'user_phone');
            }
        }
    }
    
    
    /**
    * Проверяет тип ригистрации register
    * 
    * @param array $user - массив со сведениями о пользователе
    */ 
    private function checkUserPass($user)
    {
        if (($pass_err = \Users\Model\Orm\User::checkPassword($this->order['reg_openpass'])) !== true) {
            $this->order->addError($pass_err, 'reg_openpass');
        } 
        
        if(strcmp($this->order['reg_openpass'], $this->order['reg_pass2'])){
            $this->order->addError(t('Пароли не совпадают'), 'reg_openpass');  
        }
    }
    
    /**
    * Сохраняет новый адрес принадлежащий пользователю и заказу
    * 
    */
    private function saveNewAddress()
    {
        $address = new \Shop\Model\Orm\Address();
        $address->getFromArray($this->order->getValues(), 'addr_');
        $address['user_id'] = $this->token ? $this->token->getUser()->id : \RS\Application\Auth::getCurrentUser()->id;  
                 
        if ($address->insert()) {
            $this->order->setUseAddr($address['id']);
        }
    }
    
    
    /**
    * Проверяет дополнительные поля регистрации пользователя
    * 
    * @param array $regfields_arr - массив дополнительных полей регистрации пользователя
    */
    private function checkRegisterAdditionalFields($regfields_arr)
    {
       //Запрашиваем дополнительные поля формы регистрации, если они определены
       $reg_fields_manager = \RS\Config\Loader::byModule('users')->getUserFieldsManager();
       $reg_fields_manager->setErrorPrefix('regfield_');
       $reg_fields_manager->setArrayWrapper('regfields');
       if (!empty($this->order['regfields'])){
           $reg_fields_manager->setValues($regfields_arr);
           
       } 
       $uf_err = $reg_fields_manager->check($regfields_arr);
       if (!$uf_err) {
            //Переносим ошибки в объект order
            foreach($reg_fields_manager->getErrors() as $form=>$errortext) {
                $this->order->addError($errortext, $form);
            }
       }                   
    }
    
    /**
    * Проверяет дополнительные поля регистрации адреса заказа
    * 
    * @param array $orderfields_arr - массив дополнительных полей регистрации пользователя
    */
    private function checkOrderAdditionalFields($orderfields_arr)
    {
        //Запрашиваем дополнительные поля формы заказа, если они определены в конфиге модуля
        $order_fields_manager  = $this->order->getFieldsManager();
        $order_fields_manager->setValues($orderfields_arr);
        $uf_err = $order_fields_manager->check($orderfields_arr);
        if (!$uf_err) {
            
            //Переносим ошибки в объект order
            foreach($order_fields_manager->getErrors() as $form=>$errortext) {
                $this->order->addError($errortext, $form);
            }
        }
    }
    
    
    /**
    * Проверяет переданные сведения по пользователю и возвращает ошибки в передаче параметров.
    * 
    * @param array $type - тип регистрации пользователя
    * @param array $user - массив со сведениями о пользователе
    * @param array $address - массив со сведениями о передаваемом адреск
    * @param array $regfields_arr массив дополнительных сведений для полей из настроек модуля Пользователь 
    * @param array $orderfields_arr массив дополнительных сведений для полей из настроек модуля Магазин
    */
    private function checkUserAndAddressFields($type, $user, $address, $regfields_arr, $orderfields_arr)
    {
        //Если адрес нужно принимать, то проверим её
        if ($this->order['use_addr'] == 0 && !$this->order['only_pickup_points']){
            $validator = $this->getAddressValidator();
            $validator->validate('address', $address, $this->method_params);    
        }
        
        //Назвачаем поля для проверки
        $sysdata = ['step' => 'address'];
        $my_post_vars = ['user_type' => $this->order['user_type']] + ['only_pickup_points' => $this->order['only_pickup_points']] + ['use_addr' => $this->order['use_addr']] + $user + $address;
        
        $work_fields = $this->order->useFields( $sysdata + $my_post_vars );
        
        if ($this->order['only_pickup_points']){ //Если только самовывоз то исключим поля
            $work_fields = array_diff($work_fields, ['addr_country_id', 'addr_country', 'addr_region', 'addr_region_id',
                'addr_city', 'addr_city_id', 'addr_zipcode', 'addr_address', 'use_addr', 'addr_street', 'addr_house',
                'addr_block', 'addr_apartment', 'addr_entrance', 'addr_entryphone', 'addr_floor', 'addr_subway']);
            $this->order->setUseAddr(0);
        }
        
        $this->order->setCheckFields($work_fields);
        $this->order->checkData($sysdata, $my_post_vars, null, $work_fields);
        $this->order['userfields'] = serialize($orderfields_arr);
        
        $this->order['reg_autologin'] = isset($user['reg_autologin']) ? $user['reg_autologin'] : 0; 
        //Проверяем пароль, если пользователь решил задать его вручную. (при регистрации)
          
        if ($type == 'register' && !$this->order['reg_autologin']) {
            $this->checkUserPass($user);                   
        }  
             
        $this->order['regfields'] = $regfields_arr;   
        //Регистрируем пользователя, если нет ошибок            
        if ($type == 'register') {
           $this->checkRegister($user); 
           //Сохраняем дополнительные сведения о пользователе  
           $this->checkRegisterAdditionalFields($regfields_arr);
        }
        
        //Если заказ без регистрации пользователя
        if ($this->order['user_type'] == 'noregister') {
           $this->checkNoRegiter($user); 
        }
       
        //Сохраняем дополнительные сведения
        $this->checkOrderAdditionalFields($orderfields_arr);

        //Сохраняем адрес
        if (!$this->order->hasError() && $this->order['use_addr'] == 0 && !$this->order['only_pickup_points']) {
            $this->saveNewAddress(); 
        }

        //Все успешно, присвоим этого пользователя заказу
        if (!$this->order->hasError()) {
            $this->order['user_id'] = $this->token ? $this->token->getUser()->id : \RS\Application\Auth::getCurrentUser()->id;
        }       
    }  
    
    
    /**
    * Форматирует комментарий, полученный из PHPDoc
    * 
    * @param string $text - комментарий
    * @return string
    */
    protected function prepareDocComment($text, $lang)
    {
        $text = parent::prepareDocComment($text, $lang);
        
        //Валидатор для пользователя
        $validator = $this->getUserRegisterValidator();
        $text = preg_replace_callback('/\#data-user/', function() use($validator) {
            return $validator->getParamInfoHtml();
        }, $text);
        
        //Валидатор для адреса
        $validator = $this->getAddressValidator();
        $text = preg_replace_callback('/\#data-address/', function() use($validator) {
            return $validator->getParamInfoHtml();
        }, $text);
        
        
        return $text;
    }


    /**
     * Возвращает валидатор для полей адреса
     * @return ValidateArray
     */
    private function getAddressValidator()
    {
        if ($this->address_validator === null){
            $this->address_validator = new ValidateArray([
                '@validate' => function($address, $full_data) { //Функция валидации
                    if (empty($address)){
                        return "Необходимо передать сведения по новому адресу в массив параметра user";
                    }
                    return true;
                },
                'addr_country' => [
                    '@title' => t('Название страны. Только если, параметр use_addr=0. Если не передан addr_country_id.'),
                    '@type' => 'string',
                ],
                'addr_country_id' => [
                    '@title' => t('id страны. Только если, параметр use_addr=0. Если не передан addr_country.'),
                    '@type' => 'integer',
                ],
                'addr_region' => [
                    '@title' => t('Название региона. Только если, параметр use_addr=0. Если не передан addr_region_id.'),
                    '@type' => 'string',
                ],
                'addr_region_id' => [
                    '@title' => t('id региона. Только если, параметр use_addr=0. Если не передан addr_region.'),
                    '@type' => 'integer',
                ],
                'addr_city' => [
                    '@title' => t('Название города. Только если, параметр use_addr=0. Если не передан addr_city_id.'),
                    '@type' => 'string',
                ],
                'addr_city_id' => [
                    '@title' => t('id города. Только если, параметр use_addr=0. Если не передан addr_city.'),
                    '@type' => 'integer',
                ],
                'addr_zipcode' => [
                    '@title' => t('Индекс города. Только если, параметр use_addr=0.'),
                    '@type' => 'string',
                ],
                'addr_address' => [
                    '@title' => t('Адрес. Только если, параметр use_addr=0.'),
                    '@type' => 'string',
                ],
                'addr_street' => [
                    '@title' => t('Улица. Только если, параметр use_addr=0.'),
                    '@type' => 'string',
                ],
                'addr_house' => [
                    '@title' => t('Дом. Только если, параметр use_addr=0.'),
                    '@type' => 'string',
                ],
                'addr_block' => [
                    '@title' => t('Корпус. Только если, параметр use_addr=0.'),
                    '@type' => 'string',
                ],
                'addr_apartment' => [
                    '@title' => t('Квартира. Только если, параметр use_addr=0.'),
                    '@type' => 'string',
                ],
                'addr_entrance' => [
                    '@title' => t('Подъезд. Только если, параметр use_addr=0.'),
                    '@type' => 'string',
                ],
                'addr_entryphone' => [
                    '@title' => t('Домофон. Только если, параметр use_addr=0.'),
                    '@type' => 'string',
                ],
                'addr_floor' => [
                    '@title' => t('Этаж. Только если, параметр use_addr=0.'),
                    '@type' => 'string',
                ],
                'addr_subway' => [
                    '@title' => t('Станция метро. Только если, параметр use_addr=0.'),
                    '@type' => 'string',
                ],
            ]);
        }
        return $this->address_validator;
    }
    
    /**
    * Возвращает валидатор для пользователя который либо регистрируется, ли оформляет без регистрации
    * 
    */
    private function getUserRegisterValidator()
    {
        if ($this->register_validator === null){
            $this->register_validator = new \ExternalApi\Model\Validator\ValidateArray([
                '@validate' => function($user, $full_data) { //Функция валидации
                    if (empty($user)){
                        return "Необходимо передать сведения по регистрации пользователя в массив параметра user"; 
                    }
                    return true;
                }, 
                'reg_company' => [
                    '@title' => t('Название компании. Только если, тип регистрации register и стоит ключ is_company.'), 
                    '@type' => 'string',
                ],
                'reg_company_inn' => [
                    '@title' => t('ИНН компании. Только если, тип регистрации register и стоит ключ is_company.'), 
                    '@type' => 'string',
                ],
                'reg_surname' => [
                    '@title' => t('Фамилия. Только если, тип регистрации register.'), 
                    '@type' => 'string',
                ],
                'reg_name' => [
                    '@title' => t('Имя. Только если, тип регистрации register.'), 
                    '@type' => 'string',
                ],
                'reg_midname' => [
                    '@title' => t('Отчество. Только если, тип регистрации register.'), 
                    '@type' => 'string',
                ],
                'reg_phone' => [
                    '@title' => t('Телефон. Только если, тип регистрации register.'), 
                    '@type' => 'string',
                ],
                'reg_e_mail' => [
                    '@title' => t('E-mail. Только если, тип регистрации register.'), 
                    '@type' => 'string',
                ],
                'reg_autologin' => [
                    '@title' => t('Создавать пароль пользователю автоматически или нет. 0 или 1. Только если, тип регистрации register.'), 
                    '@type' => 'string',
                ],
                'reg_openpass' => [
                    '@title' => t('Открытый пароль. Только если, тип регистрации register и reg_autologin=0.'), 
                    '@type' => 'string',
                ],
                'reg_pass2' => [
                    '@title' => t('Повтор открытого пароля. Только если, тип регистрации register и reg_autologin=0.'), 
                    '@type' => 'string',
                ],
                'is_company' => [
                    '@title' => t('Является ли клиент компанией? 0 или 1. Только если, тип регистрации register.'), 
                    '@type' => 'integer', 
                    '@validate_callback' => function($is_company, $full_data) {
                        if ($full_data['type'] == 'register' && !isset($is_company)){
                            return "Не передан ключ массива is_company в параметре user или он пустой.";        
                        } 
                        return true;
                    }
                ],
                'client_id' => [
                    '@title' => t('id клиентского приложения. Только если, тип регистрации register.'), 
                    '@type' => 'string', 
                    '@validate_callback' => function($client_id, $full_data) {
                        if ($full_data['type'] == 'register' && (!isset($client_id) || empty($client_id))){
                            return "Не передан ключ массива client_id в параметре user или он пустой.";        
                        } 
                        
                        if ($full_data['type'] == 'register'){
                            // Если токен не передан, то проверим само приложение, чтобы можно было пользователя создать 
                            $app = \RS\RemoteApp\Manager::getAppByType($client_id);
                            
                            if (!$app || !($app instanceof \ExternalApi\Model\App\InterfaceHasApi)) {
                                throw new ApiException(t('Приложения с таким client_id не существует или оно не поддерживает работу с API'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
                            }
                        }
                        return true;
                    }
                ],
                'client_secret' => [
                    '@title' => t('Секретный ключ клиентского приложения. Только если, тип регистрации register.'), 
                    '@type' => 'string', 
                    '@validate_callback' => function($client_secret, $full_data) {
                        if ($full_data['type'] == 'register' && (!isset($client_secret) || empty($client_secret))){
                            return "Не передан ключ массива client_secret в параметре user или он пустой.";        
                        } 
                        
                        if ($full_data['type'] == 'register'){
                            // Если токен не передан, то проверим само приложение, чтобы можно было пользователя создать 
                            $app = \RS\RemoteApp\Manager::getAppByType($full_data['user']['client_id']);
                            
                            //Производим валидацию client_id и client_secret
                            if (!$app || !$app->checkSecret($client_secret)) {
                                throw new ApiException(t('Приложения с таким client_id не существует или неверный client_secret'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
                            }
                        }
                        return true;
                    }
                ],
                'user_fio' => [
                    '@title' => t('ФИО. Только если, тип регистрации noregister.'), 
                    '@type' => 'string',
                ],
                'user_email' => [
                    '@title' => t('E-mail. Только если, тип регистрации noregister.'), 
                    '@type' => 'string',
                ],
                'user_phone' => [
                    '@title' => t('Телефон. Только если, тип регистрации noregister.'), 
                    '@type' => 'string',
                ],
            ]);
        }
        return $this->register_validator;
    }
    
    /**
    * Возвращает список доставок по текущему оформляемому заказу из сессии
    * 
    * @param string sortn - сортировка элементов
    * 
    * @return array
    */
    private function getDeliveryListByCurrentOrder($sortn)
    {
        return \Shop\Model\ApiUtils::getOrderDeliveryListSection($this->token, $this->order, $sortn);
    }
    
    /**
    * Возвращает список оплат по текущему оформляемому заказу из сессии
    * 
    * @param string sortn - сортировка элементов
    * 
    * @return array
    */
    private function getPaymentListByCurrentOrder($sortn)
    {
        return \Shop\Model\ApiUtils::getOrderPaymentListSection($this->token, $this->order, $sortn);
    }


    /**
    * Реализует первый шаг оформления заказа. Этап отправления адреса и оставление контактов. 
    * Если пользователь не авторизован, то нужно отправить сведения по пользователю и адрес. 
    * Если авторизован, то только сведения по выбранному адресу или новые сведения по адресу.
    * Если пользователь авторизон, то токен обязательно необходим.
    * Перед вызовом этого метода надо вызвать метод checkout.init
    * 
    * Если мы передали данные по пользователю для регистрации, то вернётся секция auth и user, со сведениями
    * об авторизационном токене и новом зарегистрированном пользователе. Смотрите метод oauth.token
    * 
    * Когда Вы используете тип регистрации пользователя <b>register</b>, то в параметрах user необходимо передавать ключи client_id и client_secret.
    * client_id - Уникальный идентификатор приложения, которое запрашивает оформление заказа (нужно при типе регистрации register)
    * client_secret - Секретный ключ приложения, которое запрашивает оформление заказа (нужно при типе регистрации register)
    * 
    * @param string $token Авторизационный токен
    * @param integer $use_addr id адреса пользователя. Если 0, то новый адрес будет сохранён к пользователю указанному в параметре user или пользователю полученному из переданного токена в параметре выше. 
    * @param string $type тип регистрации пользователя(только не авотризованные). Два варианта <b>register</b> - полная регистрация, <b>noregister</b> - оформление без регистрации, но с подачей части сведений. См. примеры
    * @param array $user массив полей по пользователю. Нужно только, если пользователь не авторизован. Зависит от параметра type. #data-user
    * @param integer $only_pickup_points 1 - только самовывоз на этапе доставки, 0 - доставка по адресу 
    * @param array $address массив сведений с адресом #data-address. Только, если use_addr=0
    * @param string $contact_person контактное лицо, которое встретит доставку
    * @param array $regfields_arr массив дополнительных сведений для полей из настроек модуля Пользователь 
    * @param array $orderfields_arr массив дополнительных сведений для полей из настроек модуля Магазин
    * @param array $order_extra массив произвольных дополнительных сведений для добавления в заказ на разных стадиях
    * 
    * @example POST /api/methods/checkout.address?use_addr=0&user_type=noregister&user[user_fio]=Иванов Иван Иванович&user[user_phone]=+79XX86XX4XX&user[user_email]=admin@admin.ru&address[reg_country_id]=1&address[reg_region_id]=1&address[reg_zipcode]=350000&address[reg_address]=ул. Фрузе 35
    * 
    * POST /api/methods/checkout.address?use_addr=0&user_type=register&user[client_id]=mobilesiteapp&user[client_secret]=78d28yd238dyd283hud93uhd989d2& user[reg_surname]=Иванов&user[reg_name]=Иван&user[reg_midname]=Иванович&user[reg_phone]=+79XX86XX4XX&user[reg_e_mail]=admin@admin.ru&address[addr_country_id]=1& address[addr_region_id]=1&address[addr_zipcode]=350000&address[addr_address]=ул. Фрузе 35&regfields_arr[myfield]=Текст
    * POST /api/methods/checkout.address?use_addr=0&user_type=noregister&user[user_fio]=Иванов Иван Иванович&user[user_phone]=+79XX86XX4XX&user[user_email]=admin@admin.ru&address[addr_country_id]=1&address[addr_region_id]=1&address[addr_zipcode]=350000&address[addr_address]=ул. Фрузе 35&regfields_arr[myfield]=Текст
    * POST /api/methods/checkout.address?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&use_addr=5&orderfields_arr[myfield]=Текст
    * 
    * Ответ:
    * <pre>
    * {
    *        "response": {
    *            "success" : false,
    *            "errors" : ['Ошибка'],    
    *            'auth': {  //Только если пользователь зарегистрирован в момент оформления заказа
    *                'token' => '38b83885448a8ad9e2fb4f789ec6b0b690d50041', //Авторизационный токен
    *                'expire' => '1504785044',
    *            },
    *            "next_step": {
    *                   "delivery": {
    *                       "errors": [],
    *                       "list": [
    *                           ...
    *                           {
    *                               "id": "4",
    *                               "title": "СДЭК",
    *                               "description": "",
    *                               "picture": null,
    *                               "xzone": null,
    *                               "min_price": "0",
    *                               "max_price": "0",
    *                               "min_cnt": "0",
    *                               "first_status": "0",
    *                               "user_type": "all",
    *                               "extrachange_discount": "0",
    *                               "extrachange_discount_type": "0",
    *                               "public": "1",
    *                               "class": "cdek",
    *                               "delivery_periods": [],
    *                               "mobilesiteapp_additional_html": "\r\n\r\n<div class=\"cdekWidjet\">\r\n                    <input type=\"hidden\" [(ngModel)]=\"current_delivery.delivery_extra.value\" [value]='{ \"tariffId\":\"139\", \"zipcode\":\"350000\"}'/>\r\n    </div>\r\n",
    *                               "extra_text": null,
    *                               "cost": false,
    *                               "additional_html": "\r\n\r\n\r\n<div id=\"cdekWidjet4\" class=\"cdekWidjet\" data-delivery-id=\"4\">\r\n                    <input id=\"dekInputMap4\" class=\"cdekDeliveryExtra\" type=\"hidden\" name=\"delivery_extra[value]\" value='{\"tariffId\":\"139\", \"zipcode\":\"350000\"}' disabled=\"disabled\"/>\r\n    </div>\r\n",
    *                               "error": "Повторите попытку позже."
    *                           }
    *                           ...
    *                       ],
    *                       "warehouses": [
    *                           ... 
    *                           {
    *                               "id": "1",
    *                               "title": "Основной склад",
    *                               "alias": "sklad",
    *                               "image": null,
    *                               "description": "<p>Наш склад находится в центре города. Предусмотрена удобная парковка для автомобилей и велосипедов.</p>",
    *                               "adress": "г. Краснодар, улица Красных Партизан, 246",
    *                               "phone": "+7(123)456-78-90",
    *                               "work_time": "с 9:00 до 18:00",
    *                               "coor_x": "45.0483",
    *                               "coor_y": "38.9745",
    *                               "default_house": "1",
    *                               "public": "0",
    *                               "checkout_public": "1",
    *                               "use_in_sitemap": "0",
    *                               "xml_id": null,
    *                               "meta_title": "",
    *                               "meta_keywords": "",
    *                               "meta_description": ""
    *                           }
    *                           ...
    *                       ]
    *                   },
    *                   "payment": {
    *                       "errors": [],
    *                       "list": [
    *                           ... 
    *                           {
    *                               "id": "4",
    *                               "title": "ЮКасса",
    *                               "description": "",
    *                               "picture": null,
    *                               "first_status": "0",
    *                               "success_status": "0",
    *                               "user_type": "all",
    *                               "target": "all",
    *                               "delivery": [
    *                                   0
    *                               ],
    *                               "public": "1",
    *                               "default_payment": "0",
    *                               "commission": "0",
    *                               "class": "yandexmoney"
    *                           }
    *                           ...
    *                       ]
    *                   }
    *               }
    *            'user': { //Только если пользователь зарегистрирован в момент оформления заказа
    *                "id": "1",
    *                "name": "Супервизор тест тест",
    *                "surname": " Моя фамилия",
    *                "midname": " ",
    *                "e_mail": "admin3@admin.ru",
    *                "login": "admin3@admin.ru",
    *                "phone": "+7(xxx)xxx-xx-xx",
    *                "sex": "",
    *                "subscribe_on": "0",
    *                "dateofreg": "2016-03-14 19:58:58",
    *                "ban_expire": null,
    *                "last_visit": "2016-11-09 15:29:14",
    *                "is_company": "0",
    *                "company": "",
    *                "company_inn": "",
    *                "data": [],
    *                "push_lock": null,
    *                "user_cost": null,
    *                "birthday": null,
    *                "fio": "Моя фамилия Супервизор тест тест",
    *                "groups": [
    *                    "guest",
    *                    "clients",
    *                    "supervisor"
    *                ],
    *                "is_courier": false
    *            }   
    *            "errors_status" : 2 //Появляется, если присутствует особый статус ошибки (истекла сессия, ошибки в корзине, корзина пуста)
    *        }
    *    }
    * </pre>
    * 
    * @return array Возращает, либо пустой массив ошибок, если успешно
    */
    protected function process($token = null,
                               $use_addr = 0,
                               $type = null, 
                               $user = [],
                               $only_pickup_points = 0,
                               $address = [],
                               $contact_person = "", 
                               $regfields_arr = [],
                               $orderfields_arr = [],
                               $order_extra = [])
    {   
        
        $errors = [];
        $response['response']['success'] = false; 
              
        //Если корзины на этот момент уже не существует.
        if ( $this->order['expired'] || !$this->order->getCart() ){ 
            $errors[] = "Корзина заказа пуста. Необходимо наполнить корзину.";
            $response['response']['errors'] = $errors;
            $response['response']['error_status'] = 2;
            return $response;
        } 
        
        $cart_data = $this->order['basket'] ? $this->order->getCart()->getCartData() : null;
        if ($cart_data === null || !count($cart_data['items']) || $cart_data['has_error'] || $this->order['expired']) {
            //Если корзина пуста или заказ уже оформлен или имеются ошибки в корзине, то выполняем redirect на главную сайта
            $errors[] = "Корзина заказа пуста, истекла сессия или в ней имеются ошибки. Оформите корзину заново.";
            $response['response']['errors']  = $errors;
            $response['response']['error_status'] = 3;
            return $response;
        }   
        
        
        
        $this->order['use_addr']           = $use_addr; //Используемый адрес доставки
        $this->order['only_pickup_points'] = $only_pickup_points; //Флаг использования только самовывоза
        $this->order['contact_person']     = $contact_person; //Контактное лицо
        $this->order_api->addOrderExtraDataByStep($this->order, 'address', $order_extra); //Заносим дополнительные данные
        $this->order['__code']->setEnable(false); //Уберём проверку каптчи
        if ($token){ // Если токен передан получим пользователя
            $this->order['user_type'] = null; 
        }else { //Если пользователь не автозован
            //Предварительные проверки параметров
            $validator = $this->getUserRegisterValidator();
            $validator->validate('user', $user, $this->method_params);
            
            if ($type == 'register'){ //Если с регистрацией
                $this->order['user_type'] = 'person';   
                $this->order['reg_autologin'] = 1; 
                if ($user['is_company']){
                    $this->order['user_type'] = 'company'; 
                } 
            }elseif ($type == 'noregister'){ //Без регистрации
                $this->order['user_type'] = 'noregister'; 
            }
        }  
        
        //Проверим пользователя и переданный адрес с дополнительными параметрами
        $this->checkUserAndAddressFields($type, $user, $address, $regfields_arr, $orderfields_arr);
        
        //Если регистрация прошла успешно, передадим сведения о пользователе
        if ($type == 'register' && !$this->order->hasError()){ 
            $response['response']['auth'] = [
                'token' => $this->token['token'],
                'expire' => $this->token['expire'],
            ];
            
            $current_user        = $this->token->getUser();
            $auth_user           = \ExternalApi\Model\Utils::extractOrm($current_user);
            $auth_user['fio']    = $current_user->getFio();
            $auth_user['groups'] = $current_user->getUserGroups();
            $response['response']['user'] = $auth_user;
        }   
        
        
        $errors = $this->order->getErrors();
        $response['response']['errors']  = $errors;
        if (!$this->order->hasError()){
           $response['response']['success'] = true;
           //Данные для следующего шага  
           if (!$this->shop_config['hide_delivery'] || !$this->shop_config['hide_payment']){ // Доставки и оплаты
              $this->order->getAddress(false); //Сбросим кэш адреса  
              $response['response']['next_step']['delivery'] = $this->getDeliveryListByCurrentOrder('sortn'); 
              $response['response']['next_step']['payment']  = $this->getPaymentListByCurrentOrder('sortn'); 
           }else{ //Подтверждение
              $api = new \Shop\Model\ApiUtils();
              $response['response']['next_step']['cartdata'] = $api->fillProductItemsData($this->order);  
           }
             
           \RS\Event\Manager::fire('statistic', ['type' => StatisticEvents::TYPE_SALES_FILL_ADDRESS]);
        }     

        return $response;
    }
}