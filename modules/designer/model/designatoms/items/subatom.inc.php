<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Items;

use \Designer\Model\DesignAtoms;

/**
 * Class Form - класс подэлемпента
 */
class SubAtom extends DesignAtoms\AbstractAtom {
    protected $title = "Подэлемент"; //Название компонента
    protected $tag   = "div";//Тег с помощью которого будет формироваться содержимое
    protected $type = "subatom";
    protected $name  = "Подэлемент #{n}"; //Имя данного атома на странице

    /**
     * Конструктор класса
     */
    function __construct(){}
}