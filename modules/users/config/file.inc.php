<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Config;

use RS\Config\Loader as ConfigLoader;
use RS\Config\UserFieldsManager;
use RS\Orm\ConfigObject;
use RS\Orm\Request;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;
use RS\Router\Manager;
use Users\Model\ApiVerification;
use Users\Model\Orm\User;

class File extends ConfigObject
{
    public static $access_system_version = 4;
    const TYPE_AUTH_STANDARD = 0;
    const TYPE_AUTH_2AUTH = 1;
    const TYPE_AUTH_PHONE = 2;

    const TWO_FACTOR_AUTH_NO = 0;
    const TWO_FACTOR_AUTH_SOME_USER = 1;
    const TWO_FACTOR_AUTH_ALWAYS = 2;

    const MIN_SMS_CODE_LENGTH = 4;
    const MAX_SMS_CODE_LENGTH = 8;

    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'generate_password_length' => new Type\Integer([
                    'description' => t('Длина пароля для генерации')
                ]),
                'replace_country_phone_code' => new Type\Integer([
                    'description' => t('Локальный код страны'),
                    'hint' => t('Код, который подлежит замене на международный. Например, в России первая 8 заменяется на +7. Замена будет происходить только если в номере будет 11 цифр. Оставьте пустое поле, если не нужно заменять')
                ]),
                'country_phone_length' => new Type\Integer([
                    'description' => t('Количество цифр в номере, которое считается полностью корректным'),
                    'hint' => t('Для России - 11. (Например, 7 123 456 78 90). Может отличаться у других стран с более длинным кодом страны.')
                ]),
                'default_country_phone_code' => new Type\Integer([
                    'description' => t('Международный код страны (без +)'),
                    'hint' => t('Например, в России - это 7. Добавление будет происходить только если в номере будет 10 цифр.')
                ]),
                'generate_password_symbols' => new Type\Varchar([
                    'description' => t('Символы для генерации паролей')
                ]),

            t('Дополнительные поля'),
                '__userfields__' => new Type\UserTemplate('%users%/form/config/userfield.tpl'),
                'userfields' => new Type\ArrayList([
                    'description' => t('Дополнительные поля'),
                    'runtime' => false,
                    'visible' => false
                ]),

            t('Обмен данными в CSV'),
                'csv_id_fields' => new Type\ArrayList([
                    'description' => t('Поля для идентификации пользователя при импорте (удерживая CTRL можно выбрать несколько полей)'),
                    'hint' => t('Во время импорта данных из CSV файла, система сперва будет обновлять пользователей, у которых будет совпадение значений по указанным здесь колонкам. В противном случае будет создаваться новый пользователь'),
                    'list' => [['\Users\Model\CsvSchema\Users', 'getPossibleIdFields']],
                    'size' => 7,
                    'attr' => [['multiple' => true]]
                ]),

            t('Логирование'),
                'clear_for_last_time' => new Type\Integer([
                    'description' => t('За сколько последних часов очищать логи пользователей?'),
                    'size' => 7
                ]),
                'clear_random' => new Type\Integer([
                    'description' => t('Вероятность очищения лога пользователей в (1-100%)'),
                    'size' => 5
                ]),

            t('Настройки входа/регистрации'),
                'type_auth' => new Type\Integer([
                    'description' => t('Тип авторизации/регистрации'),
                    'listFromArray' => $this->getTypeAuth(),
                    'radioListView' => true,
                    'template' => '%users%/form/config/type_auth_radio.tpl',
                ]),
                'type_code_provider' => new Type\Varchar([
                    'description' => t('Тип провайдера для отправки кода'),
                    'list' => [['Users\Model\Verification\VerificationProviderManager', 'getProviderTitles']],
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth',
                    ]],
                ]),
                'two_factor_auth' => new Type\Integer([
                    'description' => t('Использовать двухфакторный вход'),
                    'listFromArray' => [[
                        self::TWO_FACTOR_AUTH_NO => t('Нет'),
                        self::TWO_FACTOR_AUTH_SOME_USER => t('Да, у некоторых пользователей'),
                        self::TWO_FACTOR_AUTH_ALWAYS => t('Да, принудительно')
                    ]],
                    'attr' => [['2auth' => '2auth']],
                ]),
                'two_factor_register' => new Type\Integer([
                    'description' => t('Использовать двухфакторную регистрацию'),
                    'listFromArray' => [[t('Нет'), t('Использовать вместо капчи')]],
                    'hint' => t('Номер телефона при регистрации нужно будет подтверждать кодом верификации'),
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth'
                    ]],
                ]),
                'two_factor_recover' => new Type\Integer([
                    'description' => t('Разрешить восстановление пароля по номеру телефона'),
                    'listFromArray' => [[t('Нет'), t('Да')]],
                    'hint' => t('Если разрешить, то введя номер телефона, а затем верифицировав его, пользователь будет перемещен на страницу ввода нового пароля. Если запретить, то инструкция по восстановлению пароля будет отправлена на E-mail'),
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth',
                    ]],
                ]),
                'lifetime_resolved_session_hours' => new Type\Integer([
                    'description' => t('Запомнить факт прохождения второго этапа, часов'),
                    'hint' => t('0 - на все время сессии клиента. Если пользователь в течении указанного времени уже верифицировал свой номер телефона, то при повторной авторизации код верификации уже запрошен не будет.'),
                    'attr' => [['2auth' => '2auth']],
                ]),
                'register_by_phone' => new Type\Integer([
                    'attr' => [['phoneauth' => 'phoneauth']],
                    'description' => t('Автоматически регистрировать пользователя по номеру телефона'),
                    'hint' => t('В случае разрешения, новый пользователь, который пытается авторизоваться с неизвестным номером, после подтверждения кода верификации будет автоматически создан внутри системы. В случае запрета, пользователь сможет зареистрироваться только указав о себе больше сведений. '),
                    'listFromArray' => [[t('Нет'), t('Да')]],
                ]),
                'send_count_limit' => new Type\Integer([
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth',
                    ]],
                    'description' => t('Количество отправок кода'),
                    'hint' => t('По истечению данного количества отправок кода, на время будут заблокированы очередные попытки получить код.'),
                ]),
                'resend_delay_seconds' => new Type\Integer([
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth',
                    ]],
                    'description' => t('Интервал между запросами нового кода (в секундах)'),
                    'hint' => t('Опция позволяет предотвратить многократную отправку смс одним пользователем'),
                ]),
                'block_delay_minutes' => new Type\Integer([
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth',
                    ]],
                    'description' => t('Время блокировки до следующей серии попыток получения кода, при исчерпании лимита (в минутах)'),
                    'hint' => t('При исчерпании лимита отправок кода, следующая подобная операция будет возмона только по истечении этого времени'),
                ]),
                'lifetime_session_hours' => new Type\Integer([
                    'description' => t('Время жизни сессии верификации, в часах'),
                    'hint' => t('Актуально при авторизации. Если в течении этого времени не будет успешно введен код верификации, то необходимо будет повторно пройти первый фактор авторизации'),
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth',
                    ]]
                ]),
                'try_count_limit' => new Type\Integer([
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth',
                    ]],
                    'description' => t('Количество попыток ввода одного кода'),
                    'hint' => t('По истечению данного количества попыток, необходимо будет получить новый код'),
                ]),
                'lifetime_code_minutes' => new Type\Integer([
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth',
                    ]],
                    'description' => t('Время жизни проверочного кода (в минутах)'),
                    'hint' => t('После истечения данного времени, код уже будет некорректен. Необходимо будет повторно получить новый код'),
                ]),
                'ip_limit_session_count' => new Type\Integer([
                    'description' => t('Блокировать возможность получения кода, если количество сессий верификации с одного ip за последний час достигло'),
                    'hint' => t('Данная опция позволяет защититься от роботизированных попыток отправки кода веификации. Пользователь не сможет ввести код верификации с данного ip, пока количество сессий верификации с данного ip не станет ниже заданного количества.'),
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth',
                    ]],
                ]),
                'code_length' => new Type\Integer([
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth',
                    ]],
                    'description' => 'Длина отправляемого кода',
                    'hint' => 'Не больше 8 и не менее 4 символов',
                ]),
                'two_factor_demo_mode' => new Type\Integer([
                    'attr' => [[
                        'phoneauth' => 'phoneauth',
                        '2auth' => '2auth',
                    ]],
                    'checkboxView' => [1, 0],
                    'description' => 'Включить Демо-режим. Код из СМС будет виден прямо в поле его ввода',
                    'hint' => t('Используйте данный режим, чтобы проверить работу двухэтапной модели авторизации, без реальной отправки СМС'),
                ]),

            t('Настройка полей входа/регистрации'),
                '__field_options__' => new Type\UserTemplate("%users%/form/config/users_auth_field.tpl"),

                'visible_fields' => new Type\ArrayList([
                    'runtime' => false,
                    'description' => t('Показывать при регистрации')
                ]),
                'require_fields' => new Type\ArrayList([
                    'runtime' => false,
                    'description' => t('Обязательно для заполнения')
                ]),
                'login_fields' => new Type\ArrayList([
                    'runtime' => false,
                    'description' => t('Разрешить авторизацию')
                ]),
                'user_one_fio_field' => new Type\Integer([
                    'description' => t('Использовать одно поле для ввода фамилии, имени, отчества в формах регистрации и оформления заказа'),
                    'checkboxView' => [1, 0]
                ])
        ]);
    }

    /**
     * Поля, которые следует отображать при регистрации пользователя и в профиле
     *
     * @return array
     */
    public function getAuthVisibleFields()
    {
        return ['login', 'e_mail', 'phone', 'name', 'surname', 'midname'];
    }

    /**
     * Поля, заполнение которых обязательно при регистрации пользователя и в профиле
     */
    public function getAuthRequireFields()
    {
        return $this->getAuthVisibleFields();
    }

    /**
     * Возвращает список полей, по которым можно авторизоваться
     *
     * @return string[]
     */
    public function getAuthLoginFields()
    {
        return ['login', 'e_mail', 'phone'];
    }

    /**
     * Возвращает имя поля у пользователя
     *
     * @param string $field
     * @return string
     */
    public function getUserFieldName($field)
    {
        static $user;
        if ($user === null) {
            $user = new User();
        }

        $property = $user->getProp($field);
        return $property ? $property->getTitle() : '';
    }

    /**
     * Обработчик события перед записью объекта
     *
     * @param $flag
     * @return bool|false|void|null
     */
    public function beforeWrite($flag)
    {
        parent::beforeWrite($flag);
        if (empty($this['login_fields'])) {
            $this->addError('Не выбраны поля для авторизации');
            $success = false;
        }

        if (!array_intersect($this['require_fields'], $this['login_fields'])) {
            $this->addError('Хотя бы одно поле, используемое для авторизации, должно быть обязательным');
            $success = false;
        }

        $doubles = $this->getDoublesError();
        foreach ($doubles as $double) {
            $this->addError("У пользователей имеются не уникальные значения поля '$double'. Использовать авторизацию по этому полю невозможно.");
            $success = false;
        }
        if ($this->hasUnableLoginError()) {
            $this->addError('Имеются пользователи, которые не смогу авторизоваться, потому что у них не заполнено ни одно из полей авторизации.');
            $success = false;
        }

        $non_uniq_auth_fields_error = $this->getNonUniqueAuthFieldsError();
        if (!empty($non_uniq_auth_fields_error)) {
            $this->addError('Невоможно установить авторизацию по выбранным полям. Имеются пользователи, у которых пересекаются авторизационные поля: ' . $non_uniq_auth_fields_error);
            $success = false;
        }

        //  < 8 и > 4 символов
        if ($this['code_length'] > self::MAX_SMS_CODE_LENGTH) {
            $this['code_length'] = self::MAX_SMS_CODE_LENGTH;
        } elseif ($this['code_length'] < self::MIN_SMS_CODE_LENGTH) {
            $this['code_length'] = self::MIN_SMS_CODE_LENGTH;
        }

        $this['auth_options'] = null;
        return $success ?? true;
    }

    /**
     * Возвращает false, если ошибок нет, иначе возвращает строку
     * с информацией о дублирующихся значениях в разных полях
     *
     * @return bool (false) | string
     */
    private function getNonUniqueAuthFieldsError()
    {
        $errors = [];
        if (in_array('e_mail', $this['login_fields'])
            && in_array('login', $this['login_fields'])) {
            $non_uniq_email_users = $this->getNonUniqWithLoginAuthField('e_mail');

            if ($non_uniq_email_users) {
                $errors[] = t('E-mail дублирутся в Логине: %0...', [implode(',', $non_uniq_email_users)]);
            }
        }

        if (in_array('phone', $this['login_fields'])
            && in_array('login', $this['login_fields'])) {
            $non_uniq_phone_users = $this->getNonUniqWithLoginAuthField('phone');

            if ($non_uniq_phone_users) {
                $errors[] = t('Номер телефона дублируется в Логине: %0...', [implode(',', $non_uniq_phone_users)]);
            }
        }

        return $errors ? implode(', ', $errors) : false;
    }

    /**
     * Возвращает первые 5 логинов, которые дублируются с $field
     *
     * @param string $field
     * @return array
     */
    private function getNonUniqWithLoginAuthField($field)
    {
        $double_values = Request::make()
            ->select($field)
            ->from(User::_getTable())
            ->where("`{$field}` IN (" .
                Request::make()
                    ->select('login')
                    ->from(User::_getTable())
                    ->toSql()
                . ") AND `{$field}` != login AND login IS NOT NULL") //Исключаем пользователей, у которых логин дублировал друие поля
            ->limit(5)
            ->exec()
            ->fetchSelected(null, $field);

        return $double_values;
    }


    /**
     * Возвращает список действий для панели конфига
     *
     * @return array
     * @throws \RS\Module\Exception
     * @throws \RS\Module\Exception
     */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
                'tools' => [
                    [
                        'url' => RouterManager::obj()->getAdminUrl('ajaxNormalizeUserPhones', [], 'users-tools'),
                        'title' => t('Нормализовать телефонные номера пользователей'),
                        'description' => t('Используйте этот инструмент, для лучшего сопоставления .Приведет уже имеющиеся в базе телефонные номера к единому виду: удалит все знаки, кроме цифр и знаков *#+. Локальный код будет заменен международным (для россии 8XXX на +7XXX), к номерам без кода(10 знаков), будет добавлен локальный код '),
                        'confirm' => t('Вы действительно желаете нормализовать телефонные номера с учетом ваших настроек?')
                    ]
                ]
            ];
    }

    /**
     * Возвращает массив вида [alias_поля] => title_поля, в котором содержится
     * информация о полях, которые у пользователей дублируются
     * @param bool $with_empty
     * @return array
     */
    public function getDoublesError($with_empty = false)
    {
        $errors = [];
        foreach ($this->getAuthLoginFields() as $field) {
            if ($this->fieldIsLogin($field)) {
                if (!empty($this->checkDuplicateUserByField($field, false))) {
                    $errors[$field] = $this->getUserFieldName($field);
                }
            }
        }
        return $errors;
    }


    /**
     * Проверка на дубли в полях авторизации
     * @param $field
     * @param bool $with_empty
     * @return array
     */
    public static function checkDuplicateUserByField($field, $with_empty = true)
    {
        $users = Request::make()
            ->select($field)
            ->from(new User())
            ->groupby($field)
            ->having("count(*) > 1")
        ;
        if (!$with_empty) {
            $users->where("$field IS NOT NULL");
            $users->where("$field != '#empty'", ['empty' => '']);
        }
        return $users->exec()->fetchAll();
    }


    /**
     * Возвращает массив вида [alias_поля] => title_поля, если при текущих настройках авторизации кто-то не сможет авторизоваться
     *
     * @return bool
     */
    public function hasUnableLoginError()
    {
        $q = Request::make()
            ->select('id')
            ->from(new User());

        foreach ($this['login_fields'] as $field) {
            $q->openWGroup();
            $q->where("$field IS NULL");
            $q->where("$field = ''", null, 'OR');
            $q->closeWGroup();
        }

        return $q->count() > 0;
    }
    
    /**
     * Возвращает объект, отвечающий за работу с пользовательскими полями.
     *
     * @return \RS\Config\UserFieldsManager
     */
    function getUserFieldsManager()
    {
        return new UserFieldsManager($this['userfields'], null, 'userfields');
    }

    /**
     * Возвращает типы авторизации и их описание
     *
     * @return array
     */
    private function getTypeAuth()
    {
        return [[
            self::TYPE_AUTH_STANDARD => [
                'title' => t('Стандартный'),
                'description' => t('Пользователю необходимо ввести логин и пароль от своего аккаунта. Поля, выступающие в качестве логина настраиваются.')
            ],
            self::TYPE_AUTH_2AUTH    => [
                'title' => t('Двухфакторный'),
                'description' => t('После ввода логина и пароля пользователю будет необходимо пройти второй этап авторизации: ввод кода из смс. При регистрации и оформлении заказа, возможно включить подтвеждение номера телефона с помощью кода из смс')
            ],
            self::TYPE_AUTH_PHONE    => [
                'title' => t('По номеру телефона'),
                'description' => t('Для авторизации и регистрации(опционально) будет требоваться только номер телефона и подтверждение через SMS. При этом вход через логин и пароль также может быть доступен.')
            ],
        ]];
    }

    /**
     * Возвращает true, если пользователю надо пройти второй этап авторизации
     * @param $user
     * @return bool
     */
    public function isEnabledTwoFactorAuthorization($user)
    {
        if ($this['type_auth'] == self::TYPE_AUTH_2AUTH && $this['two_factor_auth'] != self::TWO_FACTOR_AUTH_NO) {
            return ($this['two_factor_auth'] == self::TWO_FACTOR_AUTH_ALWAYS || $user['is_enable_two_factor']);
        }
        return false;
    }

    /**
     * Возаращает true, если можно авторизоваться по номеру телефона
     * @return bool
     */
    public function isPhoneAuth()
    {
        return $this['type_auth'] == self::TYPE_AUTH_PHONE;
    }

    /**
     * Возвращает true, если можно авторизоваться по полю $field
     *
     * @param $field
     * @return bool
     */
    public function fieldIsLogin(string $field) :bool
    {
        if ($field == 'phone' && $this->isPhoneAuth()) {
            return true;
        }

        return in_array($field, $this['login_fields']);
    }

    /**
     * Возвращает true, если поле $field является обязательным
     * @param string $field
     * @return bool
     */
    public function fieldIsRequire(string $field) :bool
    {
        //Если включена двухфакторная регистрация, то поле телефон обязательно для заполнения
        if ($field == 'phone' && $this->isEnabledTwoFactorRegister()) {
            return true;
        }

        return in_array($field, $this['require_fields']);
    }

    /**
     * Возвращает true, если можно показать поле $field
     * @param $field
     * @return bool
     */
    public function canShowField(string $field) :bool
    {
        $require = $this->fieldIsRequire($field);

        if (!$require) {
            return in_array($field, $this['visible_fields']);
        }
        return $require;
    }

    /**
     * Возвращает true, если требуется подтверждение телефона при регистрации
     *
     * @return bool
     */
    public function isEnabledTwoFactorRegister()
    {
        return $this['two_factor_register'] && ($this['type_auth'] != self::TYPE_AUTH_STANDARD);
    }

    /**
     * Возвращает URL для авторизации с учетом параметров системы
     *
     * @param array $additional_params Дополнительные параметры, которые нужн одобавить к URL
     * @param bool $absolute
     * @return string
     */
    public function getAuthorizationUrl($additional_params = [], $absolute = false)
    {
        $router = RouterManager::obj();
        if ($this['type_auth'] == self::TYPE_AUTH_PHONE) {
            $url = $router->getUrl('users-front-auth', ['Act' => 'ByPhone'] + $additional_params, $absolute);
        } else {
            $url = $router->getUrl('users-front-auth', $additional_params, $absolute);
        }

        return $url;
    }
}