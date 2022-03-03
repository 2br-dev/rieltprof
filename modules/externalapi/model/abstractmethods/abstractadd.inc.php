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
* Абстрактный класс для создания одного объекта
*/
abstract class AbstractAdd extends AbstractAuthorizedMethod
{
    const
        /**
        * Право на загрузку объекта
        */
        RIGHT_ADD = 1;

    protected $token_require = false;
    
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
            self::RIGHT_ADD => t('Создание объекта')
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
    * Сохраняет объект в системе
    *
    * @param string $token Авторизационный токен
    * @param integer $data данные объекта
    * @param string $client_name имя клиентского приложения
    * @param string $client_id id клиентского приложения
    *
    * @throws ApiException
    * @return array Возвращает значения свойств объекта
    */
    protected function process($token = null, $data, $client_name, $client_id)
    {
        $this->object = $this->getOrmObject();
        if ($this->object->save($data)) {
            return [
                'response' => [
                    'success' => true,
                    $this->getObjectSectionName() => \ExternalApi\Model\Utils::extractOrm($this->object)
                ]
            ];
        }
        
        throw new ApiException(t('Ошибки при создании объекта: '.$this->object->getErrorsStr()), ApiException::ERROR_WRITE_ERROR);
    }
}
