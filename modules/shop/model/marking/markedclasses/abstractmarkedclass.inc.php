<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\Marking\MarkedClasses;

use Shop\Model\Marking\MarkingException;
use Shop\Model\Orm\OrderItemUIT;

abstract class AbstractMarkedClass
{
    /**
     * Возвращает имя класса маркированых товаров
     *
     * @return string
     */
    abstract public function getName():string;

    /**
     * Возвращает публичное имя класса маркированых товаров
     *
     * @return string
     */
    abstract public function getTitle():string;

    /**
     * Возвращает код товара в шестнадцатеричном представлении
     *
     * @param OrderItemUIT $uit
     * @return string
     */
    abstract public function getNomenclatureCode(OrderItemUIT $uit): string;

    /**
     * Формирует объект УИТ из текстиового кода
     *
     * @param $code - УИТ в текстовом виде
     * @return OrderItemUIT
     * @throws MarkingException
     */
    public function getUITFromCode($code)
    {
        $data = static::parseCode($code);
        return OrderItemUIT::loadFromData($data);
    }

    /**
     * Разбивает УИТ на составные части
     *
     * @param string $code - УИТ в текстовом виде
     * @return string[]
     */
    abstract protected function parseCode(string $code):array;
}
