<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

/**
* Класс предоставляет возможность встраивать диалоги выбора товаров и групп товаров в админ. панель.
*/
class ProductDialog
{
    protected
        $fieldname,
        $hide_group_checkbox,
        $hide_product_checkbox,
        $plugin_options,
        $data,
        $template = '%catalog%/dialog/view_selected.tpl';

    /**
     * КОнструктор класса диалогового окна выбора товаров
     *
     * @param string $fieldname - имя переменной в которой будут записаны выбранные товары
     * @param bool $hide_group_checkbox - если true, то скрывать чекбокс с выбором категории товаров
     * @param array $data - массив с текущими выбранными позициями
     * @param bool $hide_product_checkbox - скрывать чекбоксы с выбором товаров
     * @param array $plugin_options - настройки для js плагина
     */
    function __construct($fieldname, $hide_group_checkbox = false, $data = null, $hide_product_checkbox = false, $plugin_options = [])
    {
        $this->fieldname = $fieldname;
        $this->hide_group_checkbox = $hide_group_checkbox;
        $this->hide_product_checkbox = $hide_product_checkbox;
        $this->plugin_options = $plugin_options;
        if ($data) {
            $this->setData($data);
        }
    }
    
    /**
    * Устанавливает текущие выбранные позиции папок и товаров
    * 
    * @param array $data
    */
    function setData($data)
    {
        $this->data = $data;
    }
    
    function setTemplate($template)
    {
        $this->template = $template;
    }
    
    /**
    * Возвращает HTML для выбора товаров
    * @return string
    */
    function getHtml()
    {
       $extdata = [];
        
        //Загружаем недостающие данные для отображения продуктов
        if (!empty($this->data['product']))
        {
            $productapi = new \Catalog\Model\Api();
            $product_dirs = $productapi->getProductsDirs($this->data['product'], true);
            $list = $productapi
                ->setFilter('id', $this->data['product'], 'in')
                ->loadAssocList('id');

            //Определим товары, которые уже удалены
            $deleted_products = array_diff($this->data['product'], array_keys($list));
            foreach ($deleted_products as $deleted_product){
                $this->data['product'] = array_diff($this->data['product'], (array)$deleted_product);
            }

            $extdata_products = [];
            foreach($list as $id => $product) {
                $extdata['product'][$id] = [
                    'obj' => $product,
                    'dirs' => isset($product_dirs[$id]) ? ','.implode(',', $product_dirs[$id]).',' : '' //Раскладываем id в строку (тех.данные - необходимо для JavaScript)
                ];
            }
        }
        
        //Загружаем недостающие данные для отображения групп
        if (!empty($this->data['group']))
        {
            $dirapi = new \Catalog\Model\Dirapi();
            foreach($this->data['group'] as $dir_id)
            {
                $parents = ','.implode(',', $dirapi->getParentsId($dir_id, true)).',';
                $obj = ($dir_id == 0) ? ['name' => t('Все')] : $dirapi->getOneItem($dir_id);
                if ($dir_id && !$obj['id']){ //Пропустим ту категорию которой уже нет
                    unset($this->data['group'][$dir_id]);
                    continue;
                }
                $extdata['group'][$dir_id] = [
                    'parents' => ($dir_id == 0) ? '' : $parents,
                    'obj' => $obj
                ];
            }
        }
        
        $view = new \RS\View\Engine();
        $view->assign( \RS\Module\Item::getResourceFolders($this)  )
             ->assign([
                'hide_group_checkbox' => $this->hide_group_checkbox,
                'hide_product_checkbox' => $this->hide_product_checkbox,
                'plugin_options' => $this->plugin_options,
                'fieldName' => $this->fieldname,
                'productArr' => $this->data,
                'extdata' => $extdata
             ]);
        
        return $view->fetch($this->template);
    }
}
