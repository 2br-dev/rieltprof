<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Controller\Admin;

use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Toolbar,
    \RS\Html\Filter,
    \RS\Html\Table;
    
/**
* Контроллер Управление скидочными купонами
*/
class RegionCtrl extends \RS\Controller\Admin\Crud
{
    const LEVEL_COUNTRY = 'country';
    const LEVEL_REGION = 'region';
    const LEVEL_CITY = 'city';

    protected
        $level,
        $parent,
        $parent_item,
        $api;
    
    function __construct()
    {
        parent::__construct(new \Shop\Model\RegionApi());
    }
    
    function helperIndex()
    {
        $this->parent = $this->url->request('pid', TYPE_INTEGER, 0);
        $this->level = $this->getLevel();
        
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('В этом разделе можно завести 3х уровневый (страна, область/край, город) справочник, который будет использован в формах при оформлении заказа. Укажите здесь те регионы, в которые вы желаете продавать свои товары. Клик по элементу переместит вас к следующему уровню вложенности.'));
        $helper->setTopToolbar(new Toolbar\Element( [
            'Items' => [
                new ToolbarButton\Add($this->router->getAdminUrl('add', ['pid' => $this->parent]), t('Добавить'))
            ]
        ]));
        
        $helper->addCsvButton('shop-region', ['whereCondition' => '', 'pid' => $this->parent]);
        $helper->setTable(new Table\Element([
            'Columns' => array_merge(
                    [
                        new TableType\Checkbox('id'),
                        new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_ASC, 'href' => $this->router->getAdminPattern(false, [':pid' => '@id']), 'LinkAttr' => ['class' => 'call-update']]),
                    ],
                    $this->level == self::LEVEL_CITY ? [
                        new TableType\Text('area', t('Муниципальный район'), ['Sortable' => SORTABLE_BOTH]),
                        new TableType\Text('kladr_id', t('КЛАДР ID'), ['Sortable' => SORTABLE_BOTH]),
                        new TableType\Text('zipcode', t('Индекс'), ['Sortable' => SORTABLE_BOTH]),
                    ] : [],
                    [
                        new TableType\Text('id', '№', ['ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH]),
                        new TableType\Text('sortn', 'Порядок', ['ThAttr' => ['width' => '50'], 'Sortable' => SORTABLE_BOTH]),
                        new TableType\Actions('id', [
                                new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                                    'attr' => [
                                        '@data-id' => '@id'
                                    ]]),
                                new TableType\Action\DropDown([
                                        [
                                            'title' => t('Клонировать регион'),
                                            'attr' => [
                                                'class' => 'crud-add',
                                                '@href' => $this->router->getAdminPattern('clone', [':id' => '~field~']),
                                            ]
                                        ],
                                ]),
                            ]
                        ),
                    ]
            )
        ]));
        
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                                'Lines' =>  [
                                    new Filter\Line( ['Items' => [
                                                            new Filter\Type\Text('id','№', ['attr' => ['class' => 'w100']]),
                                                            new Filter\Type\Text('title', t('Название'), ['SearchType' => '%like%']),
                                    ]
                                    ])
                                ]
            ]),
            'Caption' => t('Поиск по регионам'),
            'AddParam' => ['hiddenfields' => ['pid' => $this->parent]]
        ]));
        
        return $helper;
    }
    
    function actionIndex()
    {
        $helper = $this->getHelper();

        if ($this->level == self::LEVEL_COUNTRY) {
            $helper->setTopTitle(t('Страны, регионы и города доставки'));
        }

        elseif ($this->level == self::LEVEL_REGION) {
            $helper->setTopTitle($this->parent_item['title'].'. '.t('Регионы доставки'));
            $helper['table']->getTable()->insertAnyRow([
                new TableType\Text(null, null, ['href' => $this->router->getAdminUrl(false, ['pid' => 0]), 'Value' => t('.. (к списку стран)'), 'LinkAttr' => ['class' => 'call-update'], 'TdAttr' => ['colspan' => 4]])
            ], 0);
        }

        elseif ($this->level == self::LEVEL_CITY) {

            $columns = $helper['table']->getTable()->getColumns();
            $helper->setTopTitle($this->parent_item['title'].'. '.t('Города'));
            $columns[1]->setHref($this->router->getAdminPattern('edit', [':id' => '@id']));
            $columns[1]->setLinkAttr(['class' => 'crud-edit']);

            $helper['table']->getTable()->insertAnyRow([
                new TableType\Text(null, null, ['href' => $this->router->getAdminUrl(false, ['pid' => $this->parent_item->getParent()->id]), 'Value' => t('.. (к списку регионов)'), 'LinkAttr' => ['class' => 'call-update'], 'TdAttr' => ['colspan' => 4]])
            ], 0);
        }

        $this->api->setFilter('parent_id', $this->parent);
                
        return parent::actionIndex();
    }
    
    /**
    * Добавление купонов
    */
    function actionAdd($primaryKey = null, $returnOnSuccess = false, $helper = null)
    {
        if ($primaryKey === null) {
            $this->api->getElement()->parent_id = $this->url->request('pid', TYPE_INTEGER);
        }
        if (!$primaryKey) { //0 или null
            $this->getHelper()->setTopTitle(t('Добавить регион'));
        } else {
            $this->getHelper()->setTopTitle(t('Редактировать регион').' {title}');        
        }
        return parent::actionAdd($primaryKey, $returnOnSuccess, $helper);
    }
    
    
    /**
    * Хелпер перед добавлением страны, региона или города
    */ 
    function helperAdd()
    {
        $helper = parent::helperAdd();
        $pid    = $this->request('pid', TYPE_INTEGER, 0);
        
        if ($pid){ //Если есть родитель и это регион, то отобразим нужные поля
            $elem    = new \Shop\Model\Orm\Region($pid);
            $country = $elem->getParent();
            if (isset($country['id']) && $country['id']){
                $helper->setFormSwitch('city');
            } 
        }
        return $helper;
    }
    
    /**
    * Окно редактирования страны, региона или города
    */
    function helperEdit()
    {
        $helper = parent::helperEdit();
        $id     = $this->request('id', TYPE_INTEGER, 0);
        
        if ($id){ //Если есть родитель и это регион, то отобразим нужные поля
            $elem    = new \Shop\Model\Orm\Region($id);
            $country = $elem->getParent()->getParent();
            if (isset($country['id']) && $country['id']){
                $helper->setFormSwitch('city');
            } 
        }
        return $helper;
    }

    /**
     * Возвращает текущий уровень вложенности просматриваемого элемента
     *
     * @return string
     */
    protected function getLevel()
    {
        if ($this->parent > 0) {
            /**
             * @var \Shop\Model\Orm\Region
             */
            $this->parent_item = $this->api->getOneItem($this->parent);
            if (!$this->parent_item['parent_id']){
                return self::LEVEL_REGION;
            } else {
                return self::LEVEL_CITY;
            }
        } else {
            return self::LEVEL_COUNTRY;
        }
    }
}