<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;

/**
* Базовый класс исключений для внешних API.
* Рекомендуется использовать приоритетно коды ошибок, расположенные в классе Exception. Только в 
* случае весомой необходимости создавать наследников данного класса и собственные коды ошибок.
*/
abstract class AbstractException extends \RS\Exception
{    
    const
        INTERNAL_ERROR_ID = 'inside';
    
    //У наследников здесь должны быть определены константы с кодами ошибок.
    //Имена констант должны начинаться на ERROR_....
    //Каждая константа должна иметь строковое значение
    //У каждой константы должен быть комментарий в формате phpDoc, который будет автоматически отображаться в документации
    
    private
        $extra_api_data = [],
        $code_string;
        
    /**
    * Конструктор исключения, возникшего при выполнении запроса к API
    * 
    * @param string $message - Пояснение к ошибке
    * @param string $code_string - Строковый идентификатор ошибки (вместо числового, для исключения конфликтов. Любой сторонний модуль может привносить свои ошибки)
    * @param Exception $previous - Предыдущее исключение в цепочке
    * @param string $extra_info - Дополнительная информация для отображения
    */
    function __construct($message = '', $code_string = '', Exception $previous = null, $extra_info = '')
    {
        $this->code_string = $code_string;
        parent::__construct($message, 0, $previous, 'Code:'.$code_string.'.'.$extra_info);
    }
    
    /**
    * Возвращает строковый идентификатор ошибки
    * 
    * @return string
    */
    public function getCodeString()
    {
        return $this->code_string;
    }
    
    /**
    * Добавляет произвольную пару ключ -> значение в сведения об ошибке
    * 
    * @param string $key - ключ
    * @param mixed $value - значение
    * @return void
    */
    public function addExtraApiData($key, $value)
    {
        $this->extra_api_data = array_merge_recursive($this->extra_api_data, [$key => $value]);
    }
    
    /**
    * Возвращает полную информацию об ошибке для её передачи 
    * в ответ на вызов метода API
    * 
    * @return array
    */
    public function getApiError()
    {
        //Отображать ли детали внутреннего исключения?
        $is_show_detail_internal_error = \RS\Config\Loader::byModule($this)->show_internal_error_details;

        return array_merge_recursive([
            'error' => [
                'code' => $this->getCodeString(),
                'title' => ($is_show_detail_internal_error || $this->getCodeString() != self::INTERNAL_ERROR_ID) ? $this->getMessage() : t('Внутренняя ошибка')
            ]
        ], $this->extra_api_data);
    }
    
    /**
    * Возвращает информацию о кодах ошибок, которые обрабатывает текущий класс исключений
    * 
    * @return array
    */
    public function getInfo($lang)
    {
        $reflection = new \ReflectionClass($this);
        
        $result = [
            'class_info' => $this->getComment( $reflection->getDocComment(), $lang),
            'error_codes' => []
        ];
        
        foreach(Utils::getConstantComments($reflection) as $name => $comment) {
            $result['error_codes'][$name] = [
                'code' => $reflection->getConstant($name),
                'message' => $this->getComment($comment, $lang)
            ];
        }
        
        return $result;
    }
    
    /**
    * Возвращает описание метода API, исходя из PHPDoc описания
    * 
    * @param string $comment полный PHPDoc комментарий к функции process...
    * @return string | null
    */
    protected function getComment($comment, $lang)
    {
        if (preg_match('/\/\*\*(.*?)(\* \@|\*\/)/msu', $comment, $match)) {
            return $this->prepareDocComment($match[1], $lang);
        }
    }    
    
    /**
    * Форматирует комментарий, полученный из PHPDoc
    * 
    * @param string $text - комментарий
    * @return string
    */
    protected function prepareDocComment($text, $lang)
    {
        $text = preg_replace('/\r/', '', $text);
        $text = preg_replace('/\n\s*\*/', "\n", $text);
        
        //Парсим языковые версии
        if ($lang !== null) {
            if (preg_match('/\#lang-'.$lang.'\:(.*?)(\#lang-.*)?$/s', $text, $match)) {
                $text = trim($match[1]);
            } else {
                if (preg_match('/^(.*?)(\#lang-.*)?$/s', $text, $match)) {
                    $text = trim($match[1]);
                }
            }
        }        
        
        $text = preg_replace('/\n/', '<br>', trim($text));
        return $text;
    }    
}