<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExtCsv\Config;

class Handlers extends \RS\Event\HandlerAbstract{
  
    function init()
    {
        $this->bind('controller.exec.catalog-admin-ctrl.index');
    }
    
    /**
    * Функция подвешивается на событие контролера - каталог товаров
    * 
    */
    public static function controllerExecCatalogAdminCtrlIndex(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        
        $tool_bar   = $helper['topToolbar']->getItems();
        $request    = new \RS\Http\Request();
        $dir        = $request->get('dir',TYPE_INTEGER,0);  //Текущая категория товаров
        
        /**
        * @var \RS\Html\Toolbar\Button\Dropdown
        */
        $import_buttons = $tool_bar['import'];
        $import_buttons->addItem( //Добавляем кнопки импорта и экспорта
           [
               'title' => t('Расширенный импорт товаров из CSV'),
               'attr' => [
                 'href' => \RS\Router\Manager::obj()->getAdminUrl(
                                                        'importCsv', 
                                                        [
                                                                'schema'      => 'extcsv-product', 
                                                                'referer'     => \RS\Router\Manager::obj()->getAdminUrl(),
                                                                'params[dir]' => $dir
                                                        ],
                                                            'main-csv'
                                                        ),
                 'class' => 'crud-add'
               ]
           ]);
           
        $import_buttons->addItem(
           [
              'title' => t('Расширенный экспорт товаров в CSV'),
              'attr' => [
                 'href' => \RS\Router\Manager::obj()->getAdminUrl(
                                                        'exportCsv', 
                                                        [
                                                                'schema'      => 'extcsv-product', 
                                                                'referer'     => \RS\Router\Manager::obj()->getAdminUrl(),
                                                                'params[dir]' => $dir
                                                        ],
                                                            'main-csv'
                                                        ),
                 'class' => 'crud-add'
              ]
           ]
        ); //Добавим кнопку
        
    }
}
