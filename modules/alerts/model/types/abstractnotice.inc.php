<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Alerts\Model\Types;

use RS\Exception;
use RS\Orm\AbstractObject;
use Site\Model\Orm\Site;

abstract class AbstractNotice{

    protected $site_id_field = 'site_id';

    final function __construct()
    {
        // Перегрузка конструктора невозможна
    }
    
    /**
    * Возвращает краткое описание уведомления
    * @return string
    */
    abstract public function getDescription();
    
    /**
    * Возвращает тип текущего уведомления, составленного из имени текущего класса
    * 
    * @return string
    */
    public function getSelfType()
    {
        return str_replace(['\model\notice', '\\'], ['', '-'], strtolower(get_class($this)));
    }
    
    /**
    * Возвращает экземпляр класса уведомления по типу
    * 
    * @param string $type тип уведомления
    * @return self
    */
    public static function makeByType($type)
    {
        $class_name = self::getClassnameByType($type);
        if (class_exists($class_name)) {
            return new $class_name();
        }
        throw new \RS\Exception(t("Уведомления такого типа '%0' не существует", [$type]));
    }
    
    /**
    * Возвращает имя класса уведомления по типу
    * 
    * @param string $type тип уведомления
    * @return string
    */
    public static function getClassnameByType($type)
    {
        $pre_type = str_replace('-', '-model-notice', $type, $count);
        if ($count != 1) {
            throw new \RS\Exception(t('Передан некорректный тип уведомления, должно быть ИМЯ МОДУЛЯ-ИМЯ УВЕДОМЛЕНИЯ'));
        }
        
        return str_replace('-', '\\', $pre_type);
    }

    /**
     * Возвращает символьный двухбуквенный идентификатор языка, на котором должно быть отправлено уведомление или NULL,
     * если не нужно изменять текущий язык
     *
     * @return string|null
     * @throws Exception
     */
    public function getLanguage()
    {
        $orm_object = $this->getLanguageOrmEntity();
        if ($orm_object) {
            if (!($orm_object instanceof AbstractObject)) {
                throw new Exception(t('Метод getLanguageOrmEntity должен возвращать объект класса AbstractObject'));
            }
            $site_id = $orm_object[$this->site_id_field];
            $site = new Site($site_id);

            return $site['language'];
        }
    }


    /**`
     * Возвращает ORM объект, у которого будет взят язык сайта по полю site_id
     * @return AbstractObject
     */
    public function getLanguageOrmEntity()
    {}

}