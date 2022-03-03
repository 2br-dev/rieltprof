<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Admin\Block;

/**
* Блок комплектаций в карточке товара
*/
class PropertyValuesBlock extends \RS\Controller\Admin\Block
{
    const
        PAGE_SIZE = 50;
        
    protected
        $action_var = 'ido';

    /**
     * @var \Catalog\Model\Orm\Property\Item $property
     */
    public $property;
    /**
     * @var \Catalog\Model\PropertyValueApi $property_api
     */
    public $property_api;
    
    
    function init()
    {
        $this->property = $this->getParam('property', new \Catalog\Model\Orm\Property\Item());
        $this->property['id'] = $this->url->request('prop_id', TYPE_INTEGER, $this->property['id']);
        $this->property['type'] = $this->url->request('prop_type', TYPE_STRING, $this->property['type']);
        $this->property_api = new \Catalog\Model\PropertyValueApi();
    }
    
    /**
    * Возвращает список комплектаций
    */
    function actionIndex()
    {
        $filter    = $this->url->request('pvl_filter', TYPE_ARRAY);
        $page      = $this->url->request('pvl_page', TYPE_INTEGER, 1);
        $page_size = $this->url->request('pvl_page_size', TYPE_INTEGER, self::PAGE_SIZE);
        
        //Фильтруем значения характеристики
        $this->app->headers->addCookie('pvl_page_size', $page_size, time()+3600*700, '/');
        
        $this->property_api->setFilter([
            'prop_id' => $this->property['id']
        ]);
        
        $this->property_api->applyFormFilter($filter, [
            'prop_id' => $this->property['id'],
            'pvl_page_size' => $page_size
        ]);
        
        //Пагинация
        $total = $this->property_api->getListCount();
        $url_pattern = $this->router->getAdminPattern(false, [
            ':pvl_page' => '%PAGE%', 
            'prop_id' => $this->property['id'],
            'pvl_page_size' => $page_size,
            'pvl_filter' => $filter
        ], 'catalog-block-propertyvaluesblock');
        
        $paginator = new \RS\Html\Paginator\Element($total, $url_pattern, [
            'pageKey' => 'pvl_page',
            'pageSizeKey' => 'pvl_page_size',
            'total' => $total,
            'pageSize' => $page_size,
            'page' => $page,
        ]);
        
        //Сохраняем последнюю выборку
        $this->property_api->saveRequest('pvl-list');
        
        $this->view->assign([
            'elem' => $this->property,
            'prop_id' => $this->property['id'],            
            'prop_type' => $this->property['type'],            
            'filter_parts' => $this->property_api->getFormFilterParts(),
            'filter' => $filter,
            
            'pvl_page' => $paginator->page,
            'pvl_page_size' => $page_size,
            'paginator' => $paginator,
            'items' => $this->property_api->getList($paginator->page, $paginator->page_size)
        ]);
        
        return $this->result->setTemplate('adminblocks/propertyvaluesblock/pvl_block.tpl');
    }
    
    /**
    * Возвращает форму редактирования комплектации
    */
    function actionEdit()
    {
        //value_id = 0, означает создание комплектации
        $value_id = $this->url->request('value_id', TYPE_INTEGER);
        $prop_type = $this->url->request('prop_type', TYPE_STRING);
        
        $property_value = $this->property_api->getElement();
        
        if ($value_id && !$property_value->load($value_id)) {
            $this->e404(t('Значение характеристики не найдено'));
        }
        
        $property_value['prop_type'] = $prop_type;
        $property_value['_ido'] = 'edit'; //тип action
        
        if ($this->url->isPost()) {
            //Сохранение
            
            $this->result->setSuccess( $this->property_api->save($value_id ?: null, ['id' => $value_id]) );
            if (!$this->result->isSuccess()) {
                //Ошибка сохранения формы
                $this->result->setErrors($this->property_api->getElement()->getDisplayErrors());
            }
            
            return $this->result;
        } else {
            //Чтение
            if (!$value_id) { //Создание комплектации
                $property_value['prop_id'] = $this->property['id'];
                $property_value['prop_type'] = $prop_type;
            }
            
            $form = $property_value->getForm(null, $prop_type, false, null, '%catalog%/adminblocks/propertyvaluesblock/pvl_form_maker.tpl');
            return $this->result->setHtml($form);
        }
    }
    
    /**
    * Добавить несколько значений
    */
    function actionAddSomeValues()
    {
        $form_object = $this->property_api->getMultiValuesForm();
        $form_object['prop_id'] = $this->property['id'];
        
        if ($this->url->isPost()) {
            
            
            $this->result->setSuccess($form_object->checkData());
            if ($this->result->isSuccess()) {
                $this->property_api->addSomeValues($form_object);
            } else {
                $this->result->setErrors($form_object->getDisplayErrors());
            }
            return $this->result;
        }
        
        $form_object['_ido'] = 'addSomeValues'; //тип action
        $template = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/catalog'.\Setup::$MODULE_TPL_FOLDER.'/form/add_some_value.auto.tpl';
        $form = $form_object->getForm(null, null, false, $template, '%catalog%/adminblocks/propertyvaluesblock/pvl_form_maker.tpl');
        return $this->result->setHtml($form);
    }
    
    
    /**
    * Удаляет комплектации
    */
    function actionRemove()
    {
        $ids = $this->getSelectedIds();
                
        if ($this->property_api->multiDelete($ids)) {
            return $this->result
                            ->setSuccess(true);
        } else {
            return $this->result
                            ->setSuccess(false)
                            ->addEMessage($this->property_api->getErrorsStr());
        }
    }


    /**
     * Перетаскивает комплектацию
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionMove()
    {
        $from = $this->url->request('from', TYPE_INTEGER);
        $to = $this->url->request('to', TYPE_INTEGER);
        $flag = $this->url->request('flag', TYPE_STRING);
        
        //Определяем контекст сортировки
        $q = \RS\Orm\Request::make()
                ->where([
                    'prop_id' => $this->property['id']
                ]);
        
        $this->result->setSuccess($this->property_api->moveElement($from, $to, $flag, $q));
        
        if (!$this->result->isSuccess()) {
            $this->result->addEMessage($this->property_api->getErrorsStr());
        }
        
        return $this->result;
    }

    /**
     * Перетаскивает комплектацию
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionNaturalSort()
    {
        $this->property->sortValues();
        return $this->result->setSuccess(true)->addMessage(t('Значения отсортированы'));
    }

    /**
     * Возвращает список id комплектаций с учетом флага "Отметить на всех страницах"
     *
     * @return array|\RS\Controller\Result\Standard
     */
    private function getSelectedIds()
    {
        $ids = $this->url->request('value_items', TYPE_ARRAY);
        $is_all = $this->url->request('selectAll', TYPE_BOOLEAN);
        
        if ($is_all) {
            $q = $this->property_api->getSavedRequest('pvl-list');
            if (!$q){
                return $this->result->setSuccess(false);
            }

            $ids = $this->property_api->getIdsByRequest($q);
        }
        return $ids;
    }    
}
