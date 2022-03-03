<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Config;

use Crm\Model\Telephony\Manager;
use Crm\Model\Telephony\Provider\Telphin\TelphinProvider;
use RS\Config\UserFieldsManager;
use RS\Orm\ConfigObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;

/**
* Класс конфигурации модуля
*/
class File extends ConfigObject
{
    public function _init()
    {
        $telphin_provider = new TelphinProvider();

        parent::_init()->append([
            t('Основные'),
                'widget_task_pagesize' => new Type\Integer([
                    'description' => t('Количество выводимых задач в виджете')
                ]),
            t('Задачи'),
                'expiration_task_notice_statuses' => new Type\ArrayList([
                    'description' => t('Подходящие статусы для отправки уведомлений об истечении срока действия задачи (Удерживая CTRL можно выбрать несколько)'),
                    'hint' => t('ReadyScript будет уведомлять об истечении срока действия задачи, только если она находится в одном из выбранных статусов'),
                    'list' => [['\Crm\Model\Orm\Status', 'getStatusesTitles'], 'crm-task', [0 => 'Любой статус']],
                    'Attr' => [['size' => 5, 'multiple' => 'multiple', 'class' => 'multiselect']],
                    'runtime'=>false,
                ]),
                'expiration_task_default_notice_time' => new Type\Integer([
                    'description' => t('Время для уведомлений по умолчанию'),
                    'hint' => t('На сайте должен бытьнастроен планировщик для работы уведомлений. См.документацию для пользователей'),
                    'list' => [[__CLASS__, 'getNoticeExpirationTimeList']]
                ]),
                'implementer_user_groups' => new Type\ArrayList([
                    'description' => t('Группа, пользователи которой могут быть исполнителями задач'),
                    'hint' => t('Используется для фильтрации пользователей в выпадающем списке'),
                    'list' => [['Users\Model\GroupApi', 'staticSelectList'], ["" => t('Все')]],
                    'Attr' => [['size' => 5, 'multiple' => 'multiple', 'class' => 'multiselect']],
                    'runtime' => false,
                ]),
            t('Дополнительные поля сделок'),
                '__deal_userfields__' => new Type\UserTemplate('%crm%/form/config/deal_userfield.tpl'),
                'deal_userfields' => new Type\ArrayList([
                    'description' => t('Дополнительные поля сделок'),
                    'runtime' => false,
                    'visible' => false
                ]),
            t('Дополнительные поля взаимодействий'),
                '__interaction_userfields__' => new Type\UserTemplate('%crm%/form/config/interaction_userfield.tpl'),
                'interaction_userfields' => new Type\ArrayList([
                    'description' => t('Дополнительные поля взаимодействий'),
                    'runtime' => false,
                    'visible' => false
                ]),
            t('Дополнительные поля задач'),
                '__task_userfields__' => new Type\UserTemplate('%crm%/form/config/task_userfield.tpl'),
                'task_userfields' => new Type\ArrayList([
                    'description' => t('Дополнительные поля задач'),
                    'runtime' => false,
                    'visible' => false
                ]),
            t('Телефония'),
                'tel_secret_key' => new Type\Varchar([
                    'description' => t('Произвольный секретный ключ (придумайте его)'),
                    'hint' => t('Предназначен, чтобы скрыть URL для уведомлений о входящих звонках'),
                    'template' => '%crm%/telephony/tel_secret_key.tpl',
                ]),
                'tel_active_provider' => new Type\Varchar([
                    'description' => t('Провайдер телефонии для исходящих звонков'),
                    'list' => [['Crm\Model\Telephony\Manager', 'getProvidersTitles'], Manager::FILTER_ONLY_WITH_CALLING, [0 => t('Не выбрано')]]
                ]),
                'tel_enable_call_notification' => new Type\Integer([
                    'description' => t('Включить уведомления о звонках в административной панели'),
                    'checkboxView' => [1,0]
                ]),
                'tel_bottom_offset_px' => new Type\Integer([
                    'description' => t('Смещение окна в пикселях снизу'),
                    'hint' => t('Используйте данную настройку, чтобы оптимальнее настроить работу совместно с софтофоном')
                ]),
            t('Телефония Телфин'),
                'telphin_app_id' => new Type\Varchar([
                    'description' => t('App ID'),
                    'hint' => t('Выдается Телфин после создания приложения с типом trusted. Если не указать App ID часть функций телефонии будут недоступны')

                ]),
                'telphin_app_secret' => new Type\Varchar([
                    'description' => t('App Secret'),
                    'hint' => t('Выдается Телфин после создания приложения с типом trusted'),
                    'template' => '%crm%/telephony/telphin/check_auth.tpl',
                    'provider' => $telphin_provider
                ]),
                'telphin_user_map' => new Type\VariableList([
                    'runtime' => false,
                    'description' => t('Связь администраторов с добавочными номерами'),
                    'hint' => t('При поступлении входящих звонков на указанный добавочный номер, они будут отображаться в административной панели соответствующего администратора'),
                    'tableFields' => [[
                        new Type\VariableList\UserVariableListField('user_id', t('Пользователь')),
                        new Type\VariableList\TextVariableListField('extension_id', t('SIP ID'))
                    ]]
                ]),
                'telphin_info' => new Type\MixedType([
                    'description' => t('Настройка на стороне Телфин'),
                    'provider' => $telphin_provider,
                    'template' => '%crm%/telephony/telphin/settings.tpl',
                    'visible' => true
                ]),
                'telphin_download_record' => new Type\Integer([
                    'description' => t('Скачивать записи телефонных разговоров и удалять затем их на сервере'),
                    'hint' => t('Скачивание будет происходить сразу после завершения вызова'),
                    'checkboxView' => [1,0]
                ])
        ]);
    }

    /**
     * Возвращает список возможных временных интервалов для отправки уведомлений до истечения срока действия
     * @return int[] В ключе - кол-во секунд
     */
    public static function getNoticeExpirationTimeList()
    {
        return [
            0      => t('Не выбрано'),
            300    => t('5 минут'),
            900    => t('15 минут'),
            1800   => t('30 минут'),
            3600   => t('1 час'),
            10800  => t('3 часа'),
            21600  => t('6 часов'),
            43200  => t('12 часов'),
            86400  => t('24 часа'),
            172800 => t('48 часов')
        ];
    }

    /**
     * Сообщаем, что данный модуль не мультисайтовый и его настройки общие для всех сайтов
     *
     * @return bool
     */
    public function isMultisiteConfig()
    {
        return false;
    }

    /**
     * Возвращает список действий для панели конфига
     *
     * @return array
     */
    public static function getDefaultValues()
    {
        $router = RouterManager::obj();

        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => $router->getAdminUrl(false, [], 'crm-statusctrl'),
                    'title' => t('Управление статусами'),
                    'description' => t('В этом разделе можно настроить статусы сделок, задач.'),
                    'class' => ' '
                ],
                [
                    'url' => $router->getAdminUrl('deleteUnlinked', [], 'crm-interactionctrl'),
                    'title' => t('Удалить несвязанные с другими объектами взаимодействия'),
                    'description' => t('Не связанные взаимодействия бесполезны, поэтому вы можете использовать данный инструмент для их удаления'),
                    'confirm' => t('Вы действительно желаете удалить все несвязанные взаимодействия?')
                ],
                [
                    'url' => 'https://telphin.ru?utm_source=partner&utm_medium=crm-intergration&utm_campaign=readyscript.ru',
                    'title' => t('Создать аккаунт у провайдера Телфин'),
                    'description' => t('Воспользовавшись данной ссылкой, вы можете создать аккаунт на сервисе Телфин со специальным бонусом в виде 15% скидки на услугу виртуальной АТС.'),
                    'class' => 'partner-link'
                ],
                [
                    'url' => $router->getAdminUrl('testTelephony', [], 'crm-tools'),
                    'title' => t('Проверка работы телефонии'),
                    'description' => t('Данный инструмент позволяет эмулировать входящие запросы с различными событиями телефонии'),
                    'class' => 'crud-edit crud-sm-dialog'
                ],
                [
                    'url' => $router->getAdminUrl('DeleteRecordsTelephony', [], 'crm-tools'),
                    'title' => t('Управлять записями разговоров'),
                    'description' => t('Показывает, сколько занимают записи разговоров на диске, позволяет удалить все записи разговоров'),
                    'class' => 'crud-edit crud-sm-dialog'
                ]
            ]
            ];
    }


    /**
     * Возвращает объект, отвечающий за работу с пользовательскими полями объекта сделки.
     *
     * @return \RS\Config\UserFieldsManager
     */
    public function getDealUserFieldsManager()
    {
        return new UserFieldsManager($this['deal_userfields'], null, 'deal_userfields');
    }


    /**
     * Возвращает объект, отвечающий за работу с пользовательскими полями объекта взаимодействий.
     *
     * @return \RS\Config\UserFieldsManager
     */
    public function getInteractionUserFieldsManager()
    {
        return new UserFieldsManager($this['interaction_userfields'], null, 'interaction_userfields');
    }

    /**
     * Возвращает объект, отвечающий за работу с пользовательскими полями объекта задачи.
     *
     * @return \RS\Config\UserFieldsManager
     */
    public function getTaskUserFieldsManager()
    {
        return new UserFieldsManager($this['task_userfields'], null, 'task_userfields');
    }
}
