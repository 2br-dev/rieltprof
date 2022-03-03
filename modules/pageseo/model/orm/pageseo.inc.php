<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PageSeo\Model\Orm;
use \RS\Orm\Type;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $route_id Маршрут
 * @property string $meta_title Заголовок
 * @property string $meta_keywords Ключевые слова
 * @property string $meta_description Описание
 * --\--
 */
class PageSeo extends \RS\Orm\OrmObject
{
    protected static
        $table = 'page_seo';
        
    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'route_id' => new Type\Varchar([
                'description' => t('Маршрут'),
                'attr' => [['size' => 1]],
                'list' => [[__CLASS__, 'getRouteList']],
                'meVisible' => false
            ]),
            'meta_title' => new Type\Varchar([
                'maxLength' => 1000,
                'description' => t('Заголовок')
            ]),
            'meta_keywords' => new Type\Varchar([
                'maxLength' => 1000,
                'description' => t('Ключевые слова')
            ]),
            'meta_description' => new Type\Varchar([
                'maxLength' => 1000,
                'viewAsTextarea' => true,
                'description' => t('Описание')
            ])
        ]);
        
        $this->addIndex(['site_id', 'route_id'], self::INDEX_UNIQUE);
    }
    
    /**
    * Возвращает список маршрутов, для которых можно задать meta теги
    * @return array
    */
    public static function getRouteList()
    {
        $list = [];
        foreach(\RS\Router\Manager::getRoutes() as $key => $route) {
            if (!$route->isHidden()) {
                $list[$key] = $route->getDescription();
            }
        }
        return $list;
    }
    
    /**
    * Возращает объект маршрута
    */
    function getRoute()
    {
        return \RS\Router\Manager::obj()->getRoute($this['route_id']);
    }
}