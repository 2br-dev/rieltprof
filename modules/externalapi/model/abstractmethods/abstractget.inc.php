<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\AbstractMethods;
use \ExternalApi\Model\Exception as ApiException;

/**
* Абстрактный класс для загрузки одного объекта
*/
abstract class AbstractGet extends AbstractAuthorizedMethod
{
    const
        /**
        * Право на загрузку объекта
        */
        RIGHT_LOAD = 1;
    
    protected
        $object;
    
    /**
    * Возвращает комментарии к кодам прав доступа
    * 
    * @return [
    *     КОД => КОММЕНТАРИЙ,
    *     КОД => КОММЕНТАРИЙ,
    *     ...
    * ]
    */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Загрузка объекта')
        ];
    }
    
    /**
    * Возвращает какой объект нужно загружать
    * 
    * @return \RS\Orm\AbstractObject
    */
    abstract public function getOrmObject();
    
    /**
    * Возвращает название секции ответа, в которой должен вернуться объект
    * 
    * @return string
    */
    public function getObjectSectionName()
    {
        return strtolower(basename(str_replace('\\', '/', get_class($this->getOrmObject()))));
    }
    
    /**
    * Загружает объект по ID
    * 
    * @param string $token Авторизационный токен
    * @param integer $object_id ID объекта
    *
    * @throws ApiException
    * @return array Возвращает значения свойств объекта
    */
    protected function process($token, $object_id)
    {
        $this->object = $this->getOrmObject();
        if ($this->object->load($object_id)) {
            return [
                'response' => [
                    $this->getObjectSectionName() => \ExternalApi\Model\Utils::extractOrm($this->object)
                ]
            ];
        }
        
        throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
    }
}
