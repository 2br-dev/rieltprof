<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model;

/**
* Вспомогательные возможности для внешних API общего назначения
*/
class Utils
{
    /**
    * Возвращает значения свойств ORM объекта, которые разрешены для отдачи через API
    * 
    * @param mixed $orm_object
    * @return array
    */
    public static function extractOrm(\RS\Orm\AbstractObject $orm_object)
    {
        $result = [];
        
        foreach($orm_object->getProperties() as $key => $property) {
            if ($property->isVisible('app') 
                && !($property instanceof \RS\Orm\Type\UserTemplate)) 
            {
                if ($property instanceof \RS\Orm\Type\Image && $orm_object[$key]){
                   $result[$key] = \Catalog\Model\ApiUtils::prepareImagesSection($property) ; 
                }else{
                   $result[$key] = $orm_object[$key]; 
                }
                
            }
        }
        
        return $result;
    }
    
    /**
    * Возвращает значения свойств ORM объектов в списке, которые разрешены для отдачи через API
    * 
    * @param array $list_of_orm_objects - массив объектов
    * @param string $index_key - указывается если необходим информация по определённому ключу
    * @return array
    */
    public static function extractOrmList($list_of_orm_objects, $index_key = null)
    {
        $result = [];
        foreach($list_of_orm_objects as $orm_object) {
            if ($index_key === null) {
                $result[] = self::extractOrm($orm_object);
            } else {
                $result[$orm_object[$index_key]] = self::extractOrm($orm_object);
            }
        }
        return $result;
    }
    
    
    /**
    * Возвращает значения свойств ORM объектов в списке, которые разрешены для отдачи через API
    * 
    * @param array $list_of_orm_objects - массив объектов
    * @return array
    */
    public static function extractOrmTreeList($list_of_orm_objects)
    {
        $result = [];
        foreach($list_of_orm_objects as $index_key=>$orm_object) {
            $result[$index_key] = self::extractOrm($orm_object->getObject());
            $result[$index_key]['child'] = [];
            if ($orm_object->getChildsCount()){
                $result[$index_key]['child'] = self::extractOrmTreeList($orm_object['child']);
            }
        }
        return $result;
    }
    
    /**
    * Возвращает PHPDoc комментарии к константам, т.к. в Reflection 
    * такого, к сожалению, на сегодняшний день нет
    * 
    * @param \ReflectionClass $reflection
    * @return array
    */
    public static function getConstantComments(\ReflectionClass $reflection)
    {
        $tokens = token_get_all(file_get_contents($reflection->getFileName()));
        
        $doc_comments = [];
        $doc = null;
        $isConst = false;        
        foreach($tokens as $n => $token) {
            if (!is_array($token)) continue;
            list($tokenType, $tokenValue) = $token;

            switch ($tokenType)
            {
                case T_WHITESPACE:
                case T_COMMENT:
                case T_LNUMBER:
                case T_CONSTANT_ENCAPSED_STRING:
                    break;

                case T_DOC_COMMENT:
                    $doc = $tokenValue;
                    break;

                case T_CONST:
                    $isConst = true;
                    break;

                case T_STRING:
                    if ($isConst && $doc) {
                        $doc_comments[$tokenValue] = $doc;
                        $doc = null;
                    }
                    break;
                default:
                    $doc = null;
                    $isConst = false;
                    break;
            } 
        }
        
        return $doc_comments;
    }
    
    /**
     * Изменяет ссылки в HTML на абсолютные
     *
     * @param string $body - HTML для редактирования
     * @return string
     */
    public static function prepareHTML($body)
    {
        $replace_function = function($matches) {
            $src = trim($matches[2],"'\"");
            if (mb_stripos($src, '://') === false) {
                if ((mb_stripos($src, 'mailto:') === false) && (mb_stripos($src, 'tel:') === false)) { //Если это ссылка не на E-mail и не на телефон
                    //Если путь относительный, значит фото локальное
                    $return = $matches[1] . \RS\Site\Manager::getSite()->getAbsoluteUrl($src) . $matches[3];
                }else{
                    $return = $matches[1].$src.$matches[3];
                }
            }else{
                $return = $matches[0];
            }

            return $return;
        };

        $body = preg_replace_callback('/(<img[^>]*src=["\'])(.*?)(["\'][^>]*>)/i', $replace_function, $body);
        $body = preg_replace_callback('/(style=["\'][^>]*url\()(.*?)(\))/i', $replace_function, $body);
        $body = preg_replace_callback('/(background=["\'])(.*?)(["\'])/i', $replace_function, $body);
        $body = preg_replace_callback('/(<a[^>]*href=["\'])(.*?)(["\'][^>]*>)/i', $replace_function, $body);

        $body = preg_replace('/(<img[^>]*)(width=["\'].*?["\'])([^>]*>)/i', "$1$3", $body);
        $body = preg_replace('/(<img[^>]*)(height=["\'].*?["\'])([^>]*>)/i', "$1$3", $body);

        return $body;
    }


    /**
     * Проверяет зарегистрировано ли в системе приложение по его секретному ключу и идентификатору, если нет то кидает исключение
     *
     * @param string $client_id - id клиентского приложения
     * @param string $client_secret - секретный ключ приложения
     *
     * @return void
     */
    public static function checkAppIsRegistered($client_id, $client_secret)
    {
        $app = \RS\RemoteApp\Manager::getAppByType($client_id);

        if (!$app || !($app instanceof \ExternalApi\Model\App\InterfaceHasApi)) {
            throw new ApiException(t('Приложения с таким client_id не существует или оно не поддерживает работу с API'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }

        //Производим валидацию client_id и client_secret
        if (!$app || !$app->checkSecret($client_secret)) {
            throw new ApiException(t('Приложения с таким client_id не существует или неверный client_secret'), ApiException::ERROR_BAD_CLIENT_SECRET_OR_ID);
        }
    }
}
