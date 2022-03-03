<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Application\Block;

use RS\Controller\Block;
use RS\Performance\Timing;

/**
* С помощью данного класса - можно встраивать блоки( блок-контроллеры модулей ) в шаблоны. 
* Методы данного класса используются шаблонизатором в конструкциях moduleinsert, modulegetvars
*/
class Template
{
    /**
     * Возвращает вывод блочного контроллера.
     *
     * @param string $controller_name - Имя класса блок-контроллера
     * @param array $param - Дополнительные параметры
     * @param array &$vars - Возвращает параметры, которые блок-контроллер передает в шаблон
     * @return mixed
     * @throws \SmartyException
     */
    public static function insert($controller_name, $param = [], &$vars = [])
    {
        $is_admin_zone = \RS\Router\Manager::obj()->isAdminZone();
        if ($is_admin_zone) {
            $param['skip_load_params_from_db'] = true;
        }

        $timing = Timing::getInstance();
        $timing->startMeasure(Timing::TYPE_CONTROLLER_BLOCK,
            $param[Block::BLOCK_ID_PARAM] ?? '-', $controller_name);

        try 
        {
            if (!class_exists($controller_name)) {
                throw new Exception(t("Блочный контроллер '%0' не найден", [$controller_name]));
            }
            
            if (!is_subclass_of($controller_name, '\RS\Controller\Block')
                    && !is_subclass_of($controller_name, '\RS\Controller\Admin\Block')) {
                throw new Exception(t("Блочный контроллер '%0' должен быть наследником \\RS\\Controller\\Block", [$controller_name]));
            }

            //Сообщаем блоку-контроллеру, что он вызван во время рендеринга страницы
            $com = new $controller_name($param + ['_rendering_mode' => true]);
            $result = $com->exec(true);
            
            if ($result instanceof \RS\Controller\Result\ITemplateResult) {
                //Получаем переменные, используемые в шаблоне
                $vars = $result->getTemplateVars();
            }
            
            $result_html = $com->processResult($result);
            
        } catch(\Exception $e) {
            //Если в контроллере произошло исключение, выводим информацию об этом
            $ex_tpl = new \RS\View\Engine();
            $ex_tpl->assign([
                'exception' => $e,
                'controllerName' => $controller_name,
                'type' => get_class($e),
                'uniq' => uniqid()
            ]);
            $result_html = $ex_tpl->fetch('%SYSTEM%/comexception.tpl');
        }

        $timing->endMeasure();

        return $result_html;
    }


    
    /**
    * Возвращает массив переменнх, которые должны были пойти в шаблон(на вывод) блочного контроллера.
    * 
    * @param string $controller_name - Имя класса контроллера
    * @param array $param - Дополнительные параметры
    * @return mixed
    */
    public static function getVariable($controller_name, $param = [])
    {
        $timing = Timing::getInstance();
        $timing->startMeasure(Timing::TYPE_CONTROLLER_BLOCK, $param[Block::BLOCK_ID_PARAM] ?? '-', $controller_name);

        $param = ['return_variable' => true] + $param;
        $com = new $controller_name($param + ['_rendering_mode' => true]);
        $result = $com->exec(true);
        $vars = null;

        if ($result instanceof \RS\Controller\Result\ITemplateResult) {
            $vars = $result->getTemplateVars();
        }

        $timing->endMeasure();
        return $vars;
    }
    
    /**
    * Проверяет существование блочного контроллера
    * 
    * @param string $controller_name - Имя класса контроллера
    * @return boolean
    */
    public static function isControllerExists($controller_name)
    {
        return class_exists($controller_name);
    }    
    
}

