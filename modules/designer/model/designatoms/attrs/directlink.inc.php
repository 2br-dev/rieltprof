<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Attrs;

/**
 * Class DirectLink - аттрибут типа прямая ссылка
 */
class DirectLink extends AbstractAttr {

    /**
     * AbstractCssProperty constructor.
     *
     * @param string $attribute - название аттрибута
     * @param string $title - имя аттрибута
     * @param mixed $value - значение аттрибута
     * @param mixed $params - параметры для запроса
     */
    function __construct($attribute, $title, $value = "", $params = [])
    {
        parent::__construct($attribute, $title, $value);

        if (!empty($params)){
            $this->setAdditionalDataByKey('params', $params);
        }
    }
}