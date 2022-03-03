<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Files\Model\FilesType;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

/**
* Базовый класс, описывающий тип связи файлов и объектов
*/
abstract class AbstractType
{
    const
        ACCESS_TYPE_HIDDEN = 'hidden',
        ACCESS_TYPE_VISIBLE = 'visible';
        
    public
        /**
        * Уровень доступа файла сразу после загрузки
        */
        $default_access_type = 'hidden';
    
    /**
    * Возвращает название типа 
    * @return string
    */
    abstract function getTitle();
    
    /**
    * Возвращает массив с допустимыми разрешениями для загрузки. 
    * Если возвращен пустой массив, то это означает, что нет ограничений на 
    * загружаемые расширения
    * 
    * @return []
    */
    function getAllowedExtensions()
    {
        return [];
    }
    
    /**
    * Возвращает массив с возможными уровнями доступа
    * [id => пояснение, id => пояснение, ...]
    * или
    * [id => ['title' => 'пояснение', 'hint' => 'подробная подсказка']]
    * 
    * @return []
    */
    public static function getAccessTypes()
    {
        return [
            'hidden' => t('скрытый'),
            'visible' => t('публичный')
        ];
    }
    
    /**
    * Возвращает ID связанного объекта, если находимся на странице просмотра товара
    * 
    * @return integer | false
    */
    function getLinkObjectId()
    {
        $router = \RS\Router\Manager::obj();
        $route = $router->getCurrentRoute();
        if ($route->getId() == 'catalog-front-product') {
            return $route->product->id;
        }
        return false;
    }
    
    /**
    * Проверяет права на загрузку файла в систему
    * Возвращает текст ошибки или false - в случае отсутствия ошибки
    * 
    * @return string | false
    */
    function checkUploadRightErrors($file_arr)
    {
        //Стандартный механизм проверки прав - проверяются права на запись у модуля,
        //к которому принадлежит текущий класс связи
        return Rights::CheckRightError($this, DefaultModuleRights::RIGHT_CREATE);
    }
    
    /**
    * Проверяет права на скачивание файла
    * Возвращает текст ошибки или false - в случае отсутствия ошибки
    * 
    * @param \Files\Model\Orm\File $file
    * @return string | false
    */
    function checkDownloadRightErrors(\Files\Model\Orm\File $file)
    {
        return false;
    }
    
    /**
    * Возвращает true, если для скачивания $access требуется авторизация
    * 
    * @param \Files\Model\Orm\File $file - Файл, который скачивается
    * @return bool
    */
    function getNeedGroupForDownload(\Files\Model\Orm\File $file)
    {
        return false;
    }
    
    /**
    * Возвращает короткий идентификатор текущего класса
    * 
    * @return string
    */
    public static function getShortName()
    {
        $class = strtolower(trim(str_replace('\\', '-', get_called_class()),'-'));
        return str_replace('model-filestype-', '', $class);
    }
}
