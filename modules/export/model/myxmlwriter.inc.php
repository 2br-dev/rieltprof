<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model;

class MyXMLWriter extends \XMLWriter
{
    
    /**
     * Записать тэг (экранируется)
     * @param string $name
     * @param string $content
     * @return bool
     */
    function writeElement($name, $content = null) 
    {
        $content = $this->myescape($content);
        $this->startElement($name);
        parent::writeRaw($content);
        $this->endElement();
        return true;
    }
    
    /**
     * Запись текста (экранируется)
     * @param string $content
     * @return bool
     */
    function text($content)
    {
        $content = $this->myescape($content);
        return parent::writeRaw($content);
    }
    
    /**
    * Записать аттрибут (экранируется)
    * 
    * @param mixed $name
    * @param mixed $value
    * @return bool
    */
    function writeAttribute($name, $value)
    {
        $value = $this->myescape($value);
        parent::startAttribute($name);
        parent::writeRaw($value);
        parent::endAttribute();
        return true;
    }
    
    /**
    * Запись без экранирования (запрещено)
    * 
    * @param mixed $content
    * @return bool
    */
    function writeRaw($content)
    {
        throw new \Exception(t('Запись без экранирования запрещена!'));
    } 
    
    
    /**
    * Наше специальное экранирование (согласно требованиям Yandex)
    * 
    * @param string $field
    * @return string
    */
    private function myescape($field){
        if(is_array($field)) throw new \Exception(t('Ожидается строка. Дано массив'));
        $from = ['&', '"', '>', '<',  '\''];
        $to = ['&amp;', '&quot;', '&gt;', '&lt;', '&apos;'];
        $field = str_replace($from, $to, $field);
        $field = preg_replace('/[\x00-\x08\x0B-\x0C\x0E-\x1F]+/is', ' ', $field);
        return trim($field);
    }
}
