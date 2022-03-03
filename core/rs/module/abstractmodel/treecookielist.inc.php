<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Module\AbstractModel;

use RS\Orm\AbstractObject;

/**
 * Древовидный список, способный дополнять данные узлов информацией из cookie. (развернут он или свернут)
 */
abstract class TreeCookieList extends TreeList
{
    protected $opened_elements = null;

    public $uniq;

    public function __construct(AbstractObject $orm_element, array $options = [])
    {
        $this->uniq = md5(get_class($this));
        parent::__construct($orm_element, $options);
    }

    /**
     * Возвращает - какие элементы были закрыты. берет информацию из cookie
     *
     * @return string[]
     */
    public function getOpenedElements()
    {
        if ($this->opened_elements === null) {
            $this->opened_elements = isset($_COOKIE[$this->uniq]) ? explode(',', $_COOKIE[$this->uniq]) : [];
        }
        return $this->opened_elements;
    }

    /**
     * @deprecated (02.19) - вместо данного метода следует использовать getOpenedElements()
     */
    public function getClosedElement()
    {
        return [];
    }
}
