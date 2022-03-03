<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller\Admin;
use \RS\Application\Auth;

/**
* Данный класс должен быть родителем контроллера модуля для админ панели.
*/
class Block extends \RS\Controller\AbstractAdmin
{

    /**
     * @var \Users\Model\Orm\User $user
     */
    protected $user; //Текущий пользователь.
    protected $action_var = null;
    /**
    * @var \RS\Controller\Result\Standard $result
    */
    protected $result;
        //Путь к модулям в админке
    protected $mod_url;
    protected $mod_ajax_url;
        
    function __construct($param = [])
    {
        parent::__construct($param);
        $this->result = new \RS\Controller\Result\Standard($this); //Helper, который помогает возвращать стандартизированый вывод
        if (isset($this->param['_rendering_mode'])) {
            //Отключаем возврат данных в json, если блок вставлен в шаблоне
            $this->result->checkAjaxOutput(false); 
        }        
    }        
        
    /**
    * Устанавливает основные пути для компонента, исходя из его имени.
    * 
    * @return void
    */
    function setResource()
    {
        parent::setResource();
        $this->user = Auth::getCurrentUser();
        $this->view->assign('admin_section', '/'.\Setup::$ADMIN_SECTION.'/');
    }
    
    /**
    * Возврщает имя текущего контроллера для использования в URL
    *
    * @return string
    */
    function getUrlName()
    {
        $class = get_class($this);
        $class = strtolower(trim(str_replace('\\', '-', $class),'-'));
        return str_replace('-controller-admin', '', $class);
    }
    
    /**
    * Возвращает значение параметра из get только если запрос идет конкретно к текущему контроллеру.
    * 
    * @return mixed
    */
    public function myRequest($key, $type, $default = null)
    {
        if ($this->url->request('mod_controller', TYPE_STRING) == $this->getUrlName()) {
            return $this->url->request($key, $type, $default);
        }
        return $default;
    }    
    
}
