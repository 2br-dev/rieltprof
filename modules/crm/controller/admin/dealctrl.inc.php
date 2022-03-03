<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Model\CallHistoryApi;
use Crm\Model\DealApi;
use Crm\Model\Orm\Deal;
use Crm\Model\StatusApi;
use RS\Config\Loader;
use RS\Controller\Admin\Helper\CrudCollection;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Toolbar\Element as ToolbarElement,
    \RS\Html\Filter,
    \RS\Html\Table;
use RS\Orm\AbstractObject;

/**
* Контроллер Управление списком магазинов сети
*/
class DealCtrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        //Устанавливаем, с каким API будет работать CRUD контроллер
        parent::__construct(new \Crm\Model\DealApi());
        $this->api->initRightsFilters();
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex(); //Получим helper по-умолчанию
        $helper->setTopTitle('Сделки'); //Установим заголовок раздела
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить сделку')]));
        $helper->setTopHelp(t('Создавайте сделку перед тем, как взаимодействовать с пользователем с целью дальнейшей продажи товаров или услуг и связывания сделки с оформленным заказом. Фиксируйте все взаимодействия с клиентом внутри сделки. Здесь отображаются сделки по всем объектам в системе, независимо от выбранного мультисайта.'));
        $helper->addCsvButton('crm-deal');
        $helper->getTopToolbar()->addItem(
            new ToolbarButton\Button($this->router->getAdminUrl(false, ['type' => $this->api->getElement()->getShortAlias()], 'crm-boardctrl'), t('Показать на доске'))
        );

        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));

        $field_manager = Loader::byModule($this)->getDealUserFieldsManager();
        $custom_table_columns = $this->api->getCustomTableColumns($field_manager);
        
        //Отобразим таблицу со списком объектов
        $helper->setTable(new Table\Element([
            'Columns' => array_merge([
                    new TableType\Checkbox('id'),
                    new TableType\Text('deal_num', t('Номер'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                    new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                    new TableType\Text('status_id', t('Статус'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Usertpl('client_type', t('Клиент'), '%crm%/admin/table/client_type.tpl', ['Sortable' => SORTABLE_BOTH]),
                    new TableType\User('manager_id', t('Создатель'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Datetime('date_of_create', t('Создано'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('cost', t('Сумма сделки'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Usertpl('links', t('Связи'), '%crm%/admin/table/links.tpl', ['hidden' => true]),
                    new TableType\StrYesno('is_archived', t('Архивная')),
            ],
                $custom_table_columns,
                [
                    new TableType\Actions('id', [
                            new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                    ],
                        ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                    ),
                ]
        )]));

        $this->api->addCustomFieldsData($helper['table'], $this->api->getElement()->getShortAlias());
        
        //Добавим фильтр значений в таблице по названию
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                                'Lines' =>  [
                                    new Filter\Line( ['items' => [
                                            new Filter\Type\Text('deal_num', t('Номер'), ['SearchType' => '%like%']),
                                            new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                                            new Filter\Type\Select('status_id', t('Статус'), StatusApi::staticSelectList(['' => t('Любой')], 'crm-deal')),
                                            new Filter\Type\Select('is_archived', t('Архивная'), ['' => t('Не важно'), '0' => t('Нет'), '1' => t('Да')]),
                                            new Filter\Type\User('manager_id', t('Менеджер')),
                                            new Filter\Type\User('client_id', t('Авторизованный клиент')),
                                            new Filter\Type\Text('client_name', t('Имя неавторизованного клиента'), ['SearchType' => '%like%']),
                                            new Filter\Type\DateRange('date_of_create', t('Дата создания')),
                                            new Filter\Type\Text('cost', t('Сумма сделки'), ['ShowType' => true]),
                                            new \Crm\Model\FilterType\CustomFields('custom_fields', $field_manager, $this->api->getElement()->getShortAlias()),
                                            new \Crm\Model\FilterType\Links('links', Deal::getAllowedLinkTypes(), Deal::getLinkSourceType())
                                    ]
                                    ])
                                ],
            ])
        ]));

        return $helper;
    }

    /**
     * Добавляет сделку
     *
     * @param null $primaryKeyValue
     * @param bool $returnOnSuccess
     * @param null $helper
     * @return bool|\RS\Controller\Result\Standard
     */
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $link_type = $this->url->get('link_type', TYPE_STRING);
        $link_id = $this->url->get('link_id', TYPE_INTEGER);
        $from_call = $this->url->get('from_call', TYPE_INTEGER);

        $helper = $this->getHelper();
        $element = $this->api->getElement();
        if (!$primaryKeyValue) { //Если создание сделки
            $element['deal_num'] = \RS\Helper\Tools::generatePassword(8, range('0', '9'));
            $element['manager_id'] = $this->user->id;
            $element['date_of_create'] = date('Y-m-d H:i:s');

            $element->setTemporaryId();

            if ($link_type && $link_id) {
                $element['__links']->setVisible(false);
            }
            $helper->setTopTitle(t('Добавить сделку'));
            $element->initUserRights(AbstractObject::INSERT_FLAG);
        } else {
            $helper->setTopTitle(t('Редактировать сделку').' {title}');
            $element->initUserRights(AbstractObject::UPDATE_FLAG);
        }

        $element['create_from_call'] = $from_call;
        if (!$primaryKeyValue && $from_call) {
            $call_data = CallHistoryApi::getDataForDeal($from_call);
            $element->getFromArray($call_data);
        }


        if ($link_type && $link_id) {
            $this->user_post_data['links'] = [
                $link_type => [$link_id]
            ];
        }

        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     * Формирует хелпер для редактирования элемента
     *
     * @return CrudCollection
     */
    function helperEdit()
    {
        $id = $this->url->get('id', TYPE_INTEGER, 0);

        $helper = parent::helperEdit();
        $helper['bottomToolbar']
            ->addItem(
                new ToolbarButton\delete( $this->router->getAdminUrl('deleteOne', ['id' => $id, 'dialogMode' => $this->url->request('dialogMode', TYPE_INTEGER)]), null, [
                    'noajax' => true,
                    'attr' => [
                        'class' => 'btn-alt btn-danger delete crud-get crud-close-dialog',
                        'data-confirm-text' => t('Вы действтельно хотите удалить данную сделку?')
                    ]
                ]), 'delete'
            );

        return $helper;
    }


    /**
     * Удаляет одну сделку
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionDeleteOne()
    {
        $id = $this->url->request('id', TYPE_INTEGER);
        $this->api->setFilter('id', $id);
        $reason = '';
        if ($task = $this->api->getFirst()) {
            if ($task->delete()) {
                if (!$this->url->request('dialogMode', TYPE_INTEGER)) {
                    $this->result->setAjaxWindowRedirect($this->url->getSavedUrl($this->controller_name.'index'));
                }

                return $this->result
                    ->setSuccess(true)
                    ->setNoAjaxRedirect($this->url->getSavedUrl($this->controller_name.'index'));
            } else {
                $reason = t(' Причина: %0', [$task->getErrorsStr()]);
            }
        }

        return $this->result->setSuccess(false)->addEMessage(t('Не удалось удалить сделку.').$reason);
    }

    /**
     * Групповое редактирование элементов
     *
     * @return \RS\Controller\Result\Standard
     * @throws \Exception
     * @throws \SmartyException
     */
    function actionMultiedit()
    {
        $element = $this->api->getElement();
        $element->initUserRights(AbstractObject::UPDATE_FLAG);

        return parent::actionMultiEdit();
    }
}
