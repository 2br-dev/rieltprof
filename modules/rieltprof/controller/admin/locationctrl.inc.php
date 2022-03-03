<?php
namespace Rieltprof\Controller\Admin;

use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Toolbar,
    \RS\Html\Filter,
    \RS\Html\Table;
    
/**
* Контроллер Управление скидочными купонами
*/
class LocationCtrl extends \RS\Controller\Admin\Crud
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
        parent::__construct(new \Rieltprof\Model\LocationApi());
    }
    
    function helperIndex()
    {
        $this->parent = $this->url->request('pid', TYPE_INTEGER, 0);
        $this->level = $this->getLevel();
        
        $helper = parent::helperIndex();
        $helper->setTopHelp(t('В этом разделе можно завести 3х уровневый (страна, область/край, город) справочник, который будет использован в формах при оформлении заказа. Укажите здесь те регионы, в которые вы желаете продавать свои товары. Клик по элементу переместит вас к следующему уровню вложенности.'));
        $helper->setTopToolbar(new Toolbar\Element( array(
            'Items' => array(
                new ToolbarButton\Add($this->router->getAdminUrl('add', array('pid' => $this->parent)), t('Добавить'))
            )
        )));
        
        $helper->addCsvButton('shop-region', array('whereCondition' => '', 'pid' => $this->parent));
        $helper->setTable(new Table\Element(array(
            'Columns' => array_merge(
                    array(
                        new TableType\Checkbox('id'),
                        new TableType\Text('title', t('Название'), array('Sortable' => SORTABLE_BOTH, 'CurrentSort' => SORTABLE_ASC, 'href' => $this->router->getAdminPattern(false, array(':pid' => '@id')), 'LinkAttr' => array('class' => 'call-update') )),
                    ),
                    $this->level == self::LEVEL_CITY ? array(
                        new TableType\Text('area', t('Муниципальный район'), array('Sortable' => SORTABLE_BOTH)),
                        new TableType\Text('kladr_id', t('КЛАДР ID'), array('Sortable' => SORTABLE_BOTH)),
                        new TableType\Text('zipcode', t('Индекс'), array('Sortable' => SORTABLE_BOTH)),
                    ) : array(),
                    array(
                        new TableType\Text('id', '№', array('ThAttr' => array('width' => '50'), 'Sortable' => SORTABLE_BOTH)),
                        new TableType\Text('sortn', 'Порядок', array('ThAttr' => array('width' => '50'), 'Sortable' => SORTABLE_BOTH)),
                        new TableType\Actions('id', array(
                                new TableType\Action\Edit($this->router->getAdminPattern('edit', array(':id' => '~field~')), null, array(
                                    'attr' => array(
                                        '@data-id' => '@id'
                                    ))),
                                new TableType\Action\DropDown(array(
                                        array(
                                            'title' => t('Клонировать регион'),
                                            'attr' => array(
                                                'class' => 'crud-add',
                                                '@href' => $this->router->getAdminPattern('clone', array(':id' => '~field~')),
                                            )
                                        ),
                                )),
                            )
                        ),
                    )
            )
        )));
        
        $helper->setFilter(new Filter\Control( array(
            'Container' => new Filter\Container( array( 
                                'Lines' =>  array(
                                    new Filter\Line( array('Items' => array(
                                                            new Filter\Type\Text('id','№', array('attr' => array('class' => 'w100'))),
                                                            new Filter\Type\Text('title', t('Название'), array('SearchType' => '%like%')),
                                                        )
                                    ))
                                )
                            )),
            'Caption' => t('Поиск по регионам'),
            'AddParam' => array('hiddenfields' => array('pid' => $this->parent))
        )));
        
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
            $helper['table']->getTable()->insertAnyRow(array(
                new TableType\Text(null, null, array('href' => $this->router->getAdminUrl(false, array('pid' => 0)), 'Value' => t('.. (к списку городов)'), 'LinkAttr' => array('class' => 'call-update'), 'TdAttr' => array('colspan' => 4)))
            ), 0);
        }

        elseif ($this->level == self::LEVEL_CITY) {

            $columns = $helper['table']->getTable()->getColumns();
            $helper->setTopTitle($this->parent_item['title'].'. '.t('Города'));
            $columns[1]->setHref($this->router->getAdminPattern('edit', array(':id' => '@id')));
            $columns[1]->setLinkAttr(array('class' => 'crud-edit'));

            $helper['table']->getTable()->insertAnyRow(array(
                new TableType\Text(null, null, array('href' => $this->router->getAdminUrl(false, array('pid' => $this->parent_item->getParent()->id)), 'Value' => t('.. (к списку регионов)'), 'LinkAttr' => array('class' => 'call-update'), 'TdAttr' => array('colspan' => 4)))
            ), 0);
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
