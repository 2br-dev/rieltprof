<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Application\Microdata;

/**
 * Абстрактный тип микроразметки
 */
abstract class AbstractMicrodataType
{
    /** @var AbstractMicrodataEntity[] */
    protected $microdata_list = [];

    /**
     * Возвращает HTML с микроданными для секции head
     *
     * @return string
     */
    public function getHeadMicrodataHtml(): string
    {
        $html = '';
        foreach ($this->microdata_list as $microdata_entity) {
            $html .= $this->getMicrodataHtml($microdata_entity);
        }
        return $html;
    }

    /**
     * Добавляет объект микроразметки
     *
     * @param AbstractMicrodataEntity $microdata_entity
     */
    public function addMicrodata(AbstractMicrodataEntity $microdata_entity)
    {
        $this->microdata_list[] = $microdata_entity;
    }

    /**
     * Возвращает HTML микроразметки
     *
     * @param AbstractMicrodataEntity $microdata_entity
     * @return string
     */
    abstract protected function getMicrodataHtml(AbstractMicrodataEntity $microdata_entity): string;
}
