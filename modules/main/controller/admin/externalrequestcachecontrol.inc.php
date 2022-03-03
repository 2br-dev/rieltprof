<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Admin;

use Main\Model\ExternalRequestCacheApi;
use RS\Controller\Admin\Crud;
use RS\Controller\Result\Standard;
use RS\Html\Filter;
use RS\Html\Table\Type as TableType;
use RS\Html\Table;
use RS\Html\Toolbar;
use RS\Router\Manager as RouterManager;

/**
 * Контроллер управляет кэшем внешних запросов
 */
class ExternalRequestCacheControl extends Crud
{
    /** @var ExternalRequestCacheApi */
    protected $api;

    function __construct()
    {
        parent::__construct(new ExternalRequestCacheApi());
    }

    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Кэш внешних запросов'));
        $helper->setTopToolbar((new Toolbar\Element())->setItems([
            new Toolbar\Button\Button(RouterManager::obj()->getAdminUrl('clearCache'), t('Очистить весь кэш'), [
                'attr' => ['class' => 'crud-get'],
            ]),
        ]));
        $helper->setTopHelp(t('В этом разделе вы можете просматривать сохранённые в кэше результаты внешних запросов.'));

        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]),
                new TableType\Datetime('date', t('Время запроса'), [
                    'Sortable' => SORTABLE_BOTH,
                    'CurrentSort' => SORTABLE_DESC,
                    'href' => $this->router->getAdminPattern('edit', [':id' => '@id']),
                    'LinkAttr' => ['class' => 'crud-edit'],
                ]),
                new TableType\Text('source_id', t('Инициатор запроса'), [
                    'href' => $this->router->getAdminPattern('edit', [':id' => '@id']),
                    'LinkAttr' => ['class' => 'crud-edit'],
                ]),
                new TableType\Text('request_url', t('UR запросаL'), [
                    'href' => $this->router->getAdminPattern('edit', [':id' => '@id']),
                    'LinkAttr' => ['class' => 'crud-edit'],
                ]),
                new TableType\Text('response_status', t('Статус ответа'), [
                    'href' => $this->router->getAdminPattern('edit', [':id' => '@id']),
                    'LinkAttr' => ['class' => 'crud-edit'],
                ]),
                new TableType\Actions('id', [
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                ], ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]),
            ],
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line([
                        'items' => [
                            new Filter\Type\DateRange('date', t('Время запроса')),
                            new Filter\Type\Text('source_id', t('Инициатор запроса'), ['SearchType' => '%like%']),
                            new Filter\Type\Text('request_url', t('URL запроса'), ['SearchType' => '%like%']),
                            new Filter\Type\Text('response_status', t('Статус ответа'), ['SearchType' => '%like%']),
                        ]
                    ])
                ],
            ]),
            'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()],
        ]));

        return $helper;
    }

    /**
     * Очищает кэш запросов
     *
     * @return Standard
     */
    public function actionClearCache()
    {
        $this->api->clearCache();
        return $this->result->setSuccess(true)->addMessage('Кэш внешних запросов полностью очищен');
    }
}
