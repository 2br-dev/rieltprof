<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Model\OrmType;

use RS\Orm\Type\UserTemplate;

/**
 * Тип поля "файлы", должен располагаться на отдельной вкладке ORM объекта
 */
class Files extends UserTemplate
{
    protected
        $link_type;

    /**
     * Конструктор класса
     * @param null|array $options - массив дополнительных параметров
     */
    function __construct($options = null)
    {
        $template = '%files%/ormtype/files.tpl';
        parent::__construct($template, '', $options);
    }

    /**
     * Устанавливает тип связи файлов
     *
     * @param string $link_type Строковый идентификатор типа связи
     * @return void
     */
    function setLinkType($link_type)
    {
        $this->link_type = $link_type;
    }

    /**
     * Возвращает тип связи файлов
     * @return string
     */
    function getLinkType()
    {
        return $this->link_type;
    }
}