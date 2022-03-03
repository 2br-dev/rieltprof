<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin;
 
use \RS\Html\Table\Type as TableType;
use \RS\Html\Toolbar\Button as ToolbarButton;
use \RS\Html\Filter;
use \RS\Html\Table;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
     
/**
* Контроллер Списка Производителей
*/
class BrandCtrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        //Устанавливаем, с каким API будет работать CRUD контроллер
        parent::__construct(new \Catalog\Model\BrandApi());
    }
     
    function helperIndex()
    {
        $helper = parent::helperIndex(); //Получим helper по-умолчанию
        $helper->setTopHelp(t('В этом разделе задаются список всех брендов, которыми вы торгуете в своем магазине. Созданные здесь бренды могут указываться в карточке товара.'));
        $helper->setTopTitle(t('Список брендов')); //Установим заголовок раздела
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить бренд')]));
        $helper->addCsvButton('catalog-brand');
        //Установим, какие кнопки отобразить в нижней панели инструментов
        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));
        //Добавим кнопку импорт/экспорт в CSV, будет использоваться схема экспорта \ShopList\Model\CsvSchema\ShopItem, сокращенно shoplist-shopitem
        
         
        //Опишем колонки табличного представления данных
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]), //Отображаем флажок "выделить элементы на всех страницах"
                new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'id', 'Sortable' => SORTABLE_ASC,'CurrentSort' => SORTABLE_ASC,'ThAttr' => ['width' => '20']]),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('alias', t('Англ. имя'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Yesno('public', t('Публичный'), ['Sortable' => SORTABLE_BOTH, 'toggleUrl' => $this->router->getAdminPattern('ajaxTogglePublic', [':id' => '@id'])]),
                new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray']]),
                new TableType\Actions('id', [
                        //Опишем инструменты, которые нужно отобразить в строке таблицы пользователю
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                            'attr' => [
                                '@data-id' => '@id'
                            ]]),
                        new TableType\Action\DropDown([
                            [
                                'title' => t('Клонировать бренд'),
                                'attr' => [
                                    'class' => 'crud-add',
                                    '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                                ]
                            ],
                        ])
                ],
                    //Включим отображение кнопки настройки колонок в таблице
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move')
            ]

        ]));
         
        //Опишем фильтр, который следует добавить
        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container( [ //Контейнер визуального фильтра
                'Lines' =>  [
                    new Filter\Line( ['Items' => [ //Одна линия фильтров
                            new Filter\Type\Text('id', '№'), //Фильтр по ID
                            new Filter\Type\Text('title', t('Название'), ['searchType' => '%like%']), //Фильтр по названию производителя
                    ]
                    ]),
                ]
            ]),
            'Caption' => t('Поиск по брендам')
        ]));
         
        return $helper;
    }
    function actionAjaxTogglePublic()
    {
        if ($access_error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }
        $id = $this->url->get('id', TYPE_STRING);

        $brand = $this->api->getOneItem($id);
        if ($brand) {
            $brand['public'] = !$brand['public'];
            $brand->update();
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Открытие окна добавления и редактирования товара
     *
     * @param integer $primaryKeyValue - первичный ключ товара(если товар уже создан)
     * @param bool $returnOnSuccess - вовратиться на страницу в случае успеха
     * @param null $helper - объект helper
     * @return bool|\RS\Controller\Result\Standard
     */
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $obj = $this->api->getElement();    
        
        if ($primaryKeyValue == null){
            $obj['public'] = 1; 
            $this->getHelper()->setTopTitle(t('Добавить бренд'));
        } else {
            $this->getHelper()->setTopTitle(t('Редактировать бренд').' {title}');
        }
        
        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
     * AJAX перемещение элементов
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $direction = $this->url->request('flag', TYPE_STRING);
        return $this->result->setSuccess( $this->api->moveElement($from, $to, $direction) )->getOutput();
    }

    /**
     * Метод для клонирования
     * @return bool|\RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    function actionClone()
    {
        $this->setHelper( $this->helperAdd() );
        $id = $this->url->get('id', TYPE_INTEGER);
        
        $elem = $this->api->getElement();
        
        if ($elem->load($id)) {
            $clone_id = null;
            if (!$this->url->isPost()) {
                $clone = $elem->cloneSelf();
                $this->api->setElement($clone);
                $clone_id = $clone['id']; 
            }
            unset($elem['id']);
            return $this->actionAdd($clone_id);
        } else {
            $this->e404();
        }
    }
}