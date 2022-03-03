<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\PrintForm;

/**
* Абстрактный класс печатной формы заказа.
*/
abstract class AbstractPrintForm
{
    protected
        $order;
    
    /**
    * Конструктор печатной формы
    * 
    * @param \Shop\Model\Orm\Order $order - заказ, который должен быть использован для формирования печатной формы
    * @return AbstractPrintForm
    */
    function __construct($order = null)
    {
        $this->setOrder($order);
    }
    
    public function setOrder($order = null)
    {
        $this->order = $order;
    }
    
    /**
    * Возвращает объект печатной формы по символьному идентификатору
    * 
    * @param mixed $id
    * @param \Shop\Model\Orm\Order $order
    * @return AbstractPrintForm | false
    */
    public static function getById($id, $order = null)
    {
        $all = self::getList();
        if (isset($all[$id])) {
            $item = $all[$id];
            $item->setOrder($order);
            return $item;
        }
        return false;
    }
    
    /**
    * Возвращает список всех печатных форм, имеющихся в системе
    * 
    * @return array
    */
    public static function getList()
    {
        $result = [];
        $event_result = \RS\Event\Manager::fire('printform.getlist', []);
        $list = (array)$event_result->getResult();
        foreach($list as $print_form) {
            $result[$print_form->getId()] = $print_form;
            
        }
        return $result;
    }
    
    /**
    * Возвращает краткий символьный идентификатор печатной формы
    * 
    * @return string
    */
    abstract function getId();
    
    /**
    * Возвращает название печатной формы
    * 
    * @return string
    */
    abstract function getTitle();
    
    /**
    * Возвращает шаблон формы
    * 
    * @return string
    */
    abstract function getTemplate();
    
    /**
    * Возвращает HTML готовой печатной формы
    * 
    * @return string
    */
    function getHtml() 
    {
        $view = new \RS\View\Engine();
        $view->assign([
            'order' => $this->order
        ]);
        $view->assign(\RS\Module\Item::getResourceFolders($this));
        return $view->fetch($this->getTemplate());
    }
}