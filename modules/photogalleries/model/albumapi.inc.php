<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photogalleries\Model;
 
/**
* API функции для работы с страницей альбомов
*/
class AlbumApi extends \RS\Module\AbstractModel\EntityList
{
 
    function __construct()
    {
        parent::__construct(new Orm\Album(), [
            'multisite' => true,
            'alias_field' => 'alias',
            'name_field' => 'title',
            'sort_field' => 'sortn',
            'defaultOrder' => 'sortn'
        ]);
    }
    
    
    /**
    * Аналог getSelectList, только для статичского вызова
    * 
    */
    static function myStaticSelectList()
    {
        $arr  = [
            0 => '-Не выбрано-'
        ];
        $list = parent::staticSelectList();
        
        return array_merge($arr, $list);
    }
}