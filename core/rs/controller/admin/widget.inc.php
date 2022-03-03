<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller\Admin;

/**
* Базовый абстрактный класс для контроллеров виджетов
*/
abstract class Widget extends Block 
{
    protected
        $info_title = 'Не задано', //Определить у наследников название виджета.
        $info_description = 'Не задано', //Определить у наследников описание виджета
        
        $info_full_description, //Определить у наследников полное описание виджета, по умолчанию то же, что и $info_description
        $title; //Заголовок окна виджета
    
    function __construct($param = [])
    {
        parent::__construct($param);
        if (!isset($this->title)) $this->title = $this->info_title;
        if (!isset($this->info_full_description)) $this->info_full_description = $this->info_description;
    }
    
    /**
    * Возвращает информацию о виджете
    * 
    * @return array
    */
    function getWidgetInfo()
    {
        if (\RS\Module\Manager::staticModuleExists($this->mod_name)) {
            $module = new \RS\Module\Item($this->mod_name);
            $config = $module->getConfig();
        }
        
        return [
            'title' => t($this->info_title),
            'description' => t($this->info_description),
            
            'full_title' => t($this->title),
            'full_description' => t($this->info_full_description),
            'module' => (isset($config) ? $config['name'] : $this->mod_name),
            'short_class' => $this->getUrlName()
        ];
    }
    
    /**
    * Запускает контроллер и пропускает вывод через метод _wrapOutput
    * @param mixed $act
    */
    function exec($returnAsIs = false)
    {
        $returned_data = parent::exec(true);
        if (is_object($returned_data) && $returned_data instanceof \RS\Controller\Result\IResult) {
            $returned_data = $returned_data->getHtml();
        }
        return $this->result->setHtml( $this->wrapWidget($returned_data) )->getOutput();
    }
    
    /**
    * Оборачивает вывод окном виджета
     *
    * @return string
    */
    function wrapWidget($return_data)
    {
        //Если есть параметр "ajax", то не оборачиваем виджет
        if ($this->url->isAjax() && $this->getParam('force_wrap', false) === false ) return $return_data;
        
        $widget = $this->getParam('widget');
        $tpl = new \RS\View\Engine();
        $tpl->addTemplateDir(\Setup::$PATH.\Setup::$MODULE_FOLDER.'/main'.\Setup::$MODULE_TPL_FOLDER.'/');
        $tpl->assign('widget', [
            'id' => $widget['id'],
            'class' => $this->getUrlName(),
            'title' => $this->title,
            'inside_html' => $return_data,
            'item' => $widget,
            'self' => $this
        ]);
        $tpl->assign('app', $this->app);
        return $tpl->fetch('widget_wrapper.tpl');
    }

    /**
     * Возвращает массив с кнопками, которые будут отображаться в шапке виджета.
     * Все элементы массива будут добавлены как атрибуты к тегу <a>
     *
     * @return array
     * Пример:
     * array(
     *     array(
     *          'title' => t('Обновить'),
     *          'class' => 'zmdi zmdi-refresh'
     *     )
     * );
     */
    function getTools()
    {
        return [];
    }
}

