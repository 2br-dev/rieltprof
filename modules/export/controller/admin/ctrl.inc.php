<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Controller\Admin;

use Export\Model\Api;
use Export\Model\Orm\ExportProfile;
use RS\Controller\Admin\Helper\CrudCollection;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Table;
use RS\Cron\Manager as CronManager;
    
/**
* Контроллер Управление профилями экспорта данных
*/
class Ctrl extends \RS\Controller\Admin\Crud
{
    /**
     * @var $api Api
     */
    protected $api;
    
    function __construct()
    {
        parent::__construct(new \Export\Model\Api());
    }
    
    function helperIndex()
    {
        $menu_items = [];
        $menu_items[] = [
            'title' => t('Создать профиль'),
            'attr' => [
                'class' => 'btn-success',
                'onclick' => "$(this).parent().rsDropdownButton('toggle');"
            ]
        ];

        foreach($this->api->getTypes() as $one){
            $menu_items[] = [
                'title' => $one->getTitle(),
                'attr' => [
                    'class' => 'crud-add',
                    'href' => $this->router->getAdminUrl('add', ['class' => $one->getShortName()])
                ]
            ];
        }
        
        
        $helper = parent::helperIndex();
        $helper->setTopToolbar(new Toolbar\Element( [
            'Items' => [
                new ToolbarButton\Dropdown($menu_items),
            ]
        ]));

        $helper->setTopHelp(t('Профиль экспорта обеспечивает экспорт ваших товаров на различные торговые площадки, такие как Яндекс.Маркет, Товары@Mail.ru, Google Merchants, и др. Каждый профиль создаст уникальную ссылку на специальный фид, который потребуется вам для настройки автоматического обмена данными со сторонними площадками.'));
        $helper->setTopTitle(t('Профили экспорта данных'));
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id'),
                new TableType\Text(
                    'title', 
                    t('Название'), 
                    [
                        'Sortable' => SORTABLE_BOTH, 
                        'href' => $this->router->getAdminPattern('edit', [':id' => '@id']),
                        'LinkAttr' => ['class' => 'crud-edit']
                    ]
                ),
                new TableType\Text('class', t('Тип экспорта')),
                new TableType\Usertpl('id', t('URL для экспорта'), '%export%/url_cell.tpl'),
                new TableType\Text('description', t('Описание'), ['Hidden' => true]),
                new TableType\Yesno('is_enabled', t('Включен'), ['Sortable' => SORTABLE_BOTH, 'toggleUrl' => $this->router->getAdminPattern('ajaxToggleEnabled', [':id' => '@id'])]),
                new TableType\Actions('id', 
                    [
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null,
                            [
                                'attr' => ['@data-id' => '@id']
                            ]
                        ),
                    ],
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ]
        ]));
        
        return $helper;
    }

    /**
     * Включает/выключает профиль экспорта данных
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Event\Exception
     */
    function actionAjaxToggleEnabled()
    {
        $profile_id = $this->url->get('id', TYPE_INTEGER);
        $profile = new ExportProfile($profile_id);

        if (!$profile['id']) {
            $this->e404(t('Профиль не найден'));
        }

        $profile['is_enabled'] = !$profile['is_enabled'];
        $profile->update();

        return $this->result->setSuccess(true);
    }

    /**
     * Добавляет профиль экспорта
     *
     * @param null $primaryKey
     * @param bool $returnOnSuccess
     * @param null $helper
     * @return bool|\RS\Controller\Result\Standard
     */
    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        if (!$primaryKey) {
            $this->api->getElement()->class = $this->url->get('class', TYPE_STRING);
            $this->api->getElement()->initClass();
        }

        if ($this->api->getElement()->getTypeObject()->canExchangeByApi() && !CronManager::obj()->isCronWork()) {
            $this->getHelper()->setHeaderHtml($this->view->fetch('exchangable_alert.tpl'));
        }
        
        $this->getHelper()->setTopTitle($primaryKey ? t('Редактировать профиль {title}') : t('Добавить профиль экспорта данных'));
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }

    /**
     * Помечает профиль экспорта для запуска обмена по API
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Exception
     */
    function actionAjaxDoExchange()
    {
        $profile_id = $this->url->get('profile_id', TYPE_INTEGER);
        $profile = $this->loadProfile($profile_id);

        if ($this->api->planExchange($profile)) {
            return $this->result->addMessage('Обмен запланирован');
        } else {
            return $this->result->addEMessage($this->api->getErrorsStr());
        }
    }

    /**
     * Останавливает выполнение планировщика
     *
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Exception
     */
    function actionAjaxStopExchange()
    {
        $profile_id = $this->url->get('profile_id', TYPE_INTEGER);
        $profile = $this->loadProfile($profile_id);
        $type_object = $profile->getTypeObject();

        if ($type_object->isRunning()) {
            $type_object->stopExchange();

            return $this->result->addMessage(t('Отменен запланированный запуск обмена данными.'));
        }

        return $this->result->addMessage(t('Запуск обмена уже был остановлен'));
    }

    /**
     * Выводит логи профиля экспорта
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \Exception
     */
    function actionShowLog()
    {
        $refresh = $this->url->get('refresh', TYPE_INTEGER);
        $profile_id = $this->url->get('profile_id', TYPE_INTEGER);
        $export_profile = $this->loadProfile($profile_id);

        $export_type = $export_profile->getTypeObject();

        $log_content = Api::highlightLogData( $export_type->getLogContent($export_profile) );
        $this->view->assign([
            'log_content' => $log_content
        ]);

        $helper = new CrudCollection($this);
        $helper->viewAsForm();
        $helper->setTopTitle(t('Лог профиля экспорта {title}'), $export_profile);
        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\Cancel($this->url->getSavedUrl($this->controller_name . 'index'), t('закрыть')),
                new ToolbarButton\Button($this->router->getAdminUrl('showLog', ['refresh' => 1, 'profile_id' => $profile_id]), t('обновить'), [
                    'attr' => [
                        'class' => 'btn btn-warning call-update no-update-hash',
                        'data-update-container' => '#log-zone'
                    ]
                ]),
                new ToolbarButton\Delete($this->router->getAdminUrl('clearLog', ['profile_id' => $profile_id]), t('Очистить'), [
                    'attr' => [
                        'class' => 'btn btn-alt btn-danger delete crud-get crud-close-dialog',
                        'data-confirm-text' => t('Вы действительно желаете очистить лог?')
                    ]
                ])
            ]
        ]));

        if ($refresh) {
            return $this->result->setHtml($log_content);
        } else {
            $helper->setForm($this->view->fetch('show_log.tpl'));
            return $this->result->setTemplate( $helper->getTemplate() );
        }
    }

    /**
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Exception
     */
    function actionClearLog()
    {
        $profile_id = $this->url->get('profile_id', TYPE_INTEGER);
        $export_profile = $this->loadProfile($profile_id);

        $export_profile->getTypeObject()->clearLog($export_profile);

        return $this->result->addSection(['noUpdate' => true])->setSuccess(true);
    }

    /**
     * Отмечает ранее выгруженные товары для повторной выгрузки
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAjaxDoMarkToExport()
    {
        $api = new Api();
        $count = $api->markAllToExport();

        return $this->result->addMessage(t('Отмечено для выгрузки %n товаров и комплектаций', ['n' => $count]));
    }

    /**
     * Загружает профиль экспорта
     *
     * @param integer $profile_id
     * @return ExportProfile
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Exception
     */
    private function loadProfile($profile_id)
    {
        $export_profile = new ExportProfile($profile_id);
        if (!$export_profile['id']) {
            $this->e404(t('Профиль не найден'));
        }

        $export_type = $export_profile->getTypeObject();
        if (!$export_type->canExchangeByApi() || !$export_type->canSaveLog()) {
            $this->e404(t('Профиль не поддерживает обмен данными по API'));
        }

        return $export_profile;
    }
}