<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\MailRu;

class MailRu extends \Export\Model\ExportType\Yandex\Yandex 
{
    /**
    * Возвращает название типа экспорта
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Товары@Mail.Ru');
    }
    
    /**
    * Возвращает описание типа экспорта для администратора. Возможен HTML
    * 
    * @return stringe
    */
    function getDescription()
    {
        return t('Экспорт товаров на площадку Товары@Mail.Ru');
    }
    
    /**
    * Возвращает идентификатор данного типа экспорта. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return 'mailru';
    }
    
    /**
    * Возвращает корневой тэг документа
    * 
    * @return string
    */
    protected function getRootTag()
    {
        return "torg_price";
    }
    
}
