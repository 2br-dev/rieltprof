<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\AliExpress;

use Export\Model\ExportType\AbstractOfferType;
use Export\Model\ExportType\Yandex\Yandex;

class AliExpress extends Yandex
{
    /**
     * Возвращает название типа экспорта
     *
     * @return string
     */
    public function getTitle()
    {
        return t('AliExpress Бизнес');
    }

    /**
     * Возвращает описание типа экспорта для администратора. Возможен HTML
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Экспорт в формате YML для AliExpress Бизнес');
    }

    /**
     * Возвращает идентификатор данного типа экспорта. (только англ. буквы)
     *
     * @return string
     */
    public function getShortName()
    {
        return 'aliexpress';
    }

    /**
     * Возвращает список классов типов описания
     *
     * @return AbstractOfferType[]
     */
    protected function getOfferTypesClasses()
    {
        return [
            new OfferType\Yml(),
        ];
    }
}
