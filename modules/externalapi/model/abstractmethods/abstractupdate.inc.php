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
* Абстрактный класс для API обновления объектов
*/
abstract class AbstractUpdate extends AbstractAuthorizedMethod
{
    const
        RIGHT_UPDATE = 1;
    
    public
        $data_field = 'fields',
        $orm_object;
        
    private 
        $validator;
    
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
            self::RIGHT_UPDATE => t('Изменение заказа'),
        ];
    }
    
    /**
    * Возвращает параметры валидации данных для обновления
    * 
    * Допустимые технические переменные:
    * 
    * @title - описание данных
    * @type - тип данных
    * @arrayitemtype - тип значений массива, если @type = array
    * @validate_callback - callback для валидации значения. В качестве аргументов получает $value (значение данного ключа), $data (все значения)
    * @allowable_values - допустимые значения
    * 
    * @example
    * array(
    *     'fields' => array(
    *         '@title' => '...',
    *         '@validate' => '....',
    * 
    *         'status' => array(
    *             '@title' => t('ID статуса'),
    *             '@type' => 'integer',
    *             '@validate_callback' => function($value) {
    *             }
    *         ),
    *         'payment' => array(
    *             '@title' => t('ID способа оплаты'),
    *             '@type' => 'integer',
    *             '@validate_callback' => function($value) {
    *             }
    *         ),
    *         'is_payed' => array(
    *             '@title' => t('Флаг оплаты'),
    *             '@type' => 'integer',
    *             '@allowable_values' => array(1,0)
    *         ),
    *         'courier_id' => array(
    *             '@title' => t('ID курьера'),
    *             '@type' => 'integer',
    *             '@validate_callback' => function($value) {
    *             }
    *         )
    *     ),
    *     'remove_items' => array(
    *         '@title' => t('Уникальные коды удаляемых из заказа товаров'),
    *         '@type' => 'array',
    *         '@arrayitemtype' => 'string',
    *     )
    * );
    * 
    * @return array
    */
    abstract public function getUpdateDataScheme();
    
    public function getUpdateDataValidator()
    {
        if ($this->validator === null) {
            $this->validator = new \ExternalApi\Model\Validator\ValidateArray($this->getUpdateDataScheme());
        }
        
        return $this->validator;
    }
    
    /**
    * Форматирует комментарий, полученный из PHPDoc
    * 
    * @param string $text - комментарий
    * @return string
    */
    protected function prepareDocComment($text, $lang)
    {
        $text = parent::prepareDocComment($text, $lang);
        
        $validator = $this->getUpdateDataValidator();
        $text = preg_replace_callback('/\#data-info/', function() use($validator) {
            return $validator->getParamInfoHtml();
        }, $text);
        
        return $text;
    }

    
    /**
    * Возвращает объект, который необходимо обновить
    * 
    * @return \Shop\Model\Orm\Order
    */
    abstract public function getOrmObject();
    
    /**
    * Валидирует значения для обновления
    * 
    * @param array $data 
    * @return Возвращает true, 
    */
    public function validateData($data)
    {
        return $this->getUpdateDataValidator()->validate('data', $data, $this->method_params);
    }
    
    /**
    * Обновляет данные в БД
    * 
    * @param \RS\Orm\AbstractObject $orm_object - объект обновления
    * @param array $data - данные для обновления
    * @return void
    */
    public function updateData($orm_object, $data)
    {
        if (isset($data[$this->data_field])) {
            $orm_object->getFromArray($data[$this->data_field]);
            if (!$orm_object->update()) {
                throw new ApiException($orm_object->getErrorsStr(), ApiException::ERROR_INSIDE);
            }
        }
    }
    
    /**
    * Загружает объект из БД по ID
    * 
    * @param \RS\Orm\AbstractObject $orm_object
    * @param integer $object_id
    * 
    * @throws \ExternalApi\Model\Exception
    * @return void
    */
    protected function loadObject($orm_object, $object_id)
    {
        //Загружаем объект
        if (!$orm_object->load($object_id)) {
            throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $token
    * @param mixed $object_id
    * @param mixed $data
    */
    protected function process($token, $object_id, $data)
    {
        $data = $this->validateData($data);
        $this->orm_object = $this->getOrmObject();
        
        $this->loadObject($this->orm_object, $object_id);
        
        //Обновляем объект
        $this->updateData($this->orm_object, $data);
        
        return [
            'response' => [
                'success' => true
            ]
        ];
    }
}
