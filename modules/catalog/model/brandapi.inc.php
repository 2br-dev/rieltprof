<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;
 
use RS\Helper\Tools;

/**
* API функции для работы с магазинами сети
*/
class BrandApi extends \RS\Module\AbstractModel\EntityList
{
    static protected
        $_cache_brands = [];
    
    protected
        //Английкие буквы
        $eng_letters = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M",
                             "N", "O", "P", "Q", "R", "S", "T", "U", "X", "V", "W", "Y", "Z"],
        //Русские буквы
        $rus_letters = ["А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", "З", "И", "Й", "К", "Л",
                             "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш",
                             "Щ", "Э", "Ю", "Я"];
 
    function __construct()
    {
        parent::__construct(new Orm\Brand(), [
            'name_field' => 'title',
            'id_field' => 'id',
            'alias_field' => 'alias',
            'sort_field' => 'sortn',
            'multisite' => true,
            'defaultOrder' => 'sortn'
        ]);
    }
    
    /**
    * Возвращает бренды, для блока брендов
    * 
    * @param integer $pageSize - количество брендов для отображения. Если 0, то все.
    * @param boolean $cache    - флаг использования кэша
    * @return array
    */
    static function getBrandsForBlock($pageSize = 0, $cache = true){
        if ($cache) {
            $site_id = \RS\Site\Manager::getSiteId();
            return \RS\Cache\Manager::obj()
                    ->expire(0)
                    ->request(['\Catalog\Model\BrandApi', 'getBrandsForBlock'],$pageSize, false, $site_id);
        }else{
            $api = new \Catalog\Model\BrandApi();
            $api->setOrder("sortn ASC");
            $api->setFilter('public',1);
               
            if ($pageSize>0){ //Если задано ограничение на вывод количества
               $brands = $api->getList(1,$pageSize); 
            }else{
               $brands = $api->getList(); 
            } 
            
            return $brands;
        }
    }  
    
    /**
    * Возвращает массив разделённый по языкам и буквам алфавита
    * 
    * @param array $list - массив объектов брэндов
    * @return array
    */
    function divideByLanguage($list){
       $all_brands = [];
       //Английский алфавит и Русский пройдёмся, чтобы получить значения
       foreach ($list as $item){
          
          $letter = $item['title'];
          $letter = mb_substr($item['title'],0,1);
          if (in_array($letter,$this->eng_letters)){
              $all_brands['ENG'][$letter][] = $item; 
          }
          if (in_array($letter,$this->rus_letters)){
              $all_brands['RU'][$letter][]  = $item; 
          }
       }    
       return $all_brands;
    }
    
    /**
    * Получение списка брендов статикой
    * при вызове без параметров - добавляет элемент '-Не выбрано-'
    *
    * @param array $first = значения, которые нужно добавить в начало списка
    * @return array
    */
    static function staticSelectList($first = true)
    {
        static $cache_list = [];

        if ($first === true) { // для совместимости
            $first = [0 => t('-Не выбрано-')];
        }

        if (empty($cache_list)) {
            $api  = new \Catalog\Model\BrandApi();
            $api->setOrder('title ASC');
            $cache_list = $api->getSelectList($first);
        }

        return $cache_list;
    }
    
    /**
    * Получает бренд по его имени
    * 
    * @param string $brand_name - название бренда
    * @return \Catalog\Model\Orm\Brand|false
    */
    function getByName($brand_name){
       $this->setFilter('title',$brand_name);
        /**
         * @var \Catalog\Model\Orm\Brand $brand
         */
       $brand = $this->getFirst('sortn ASC');  
       return $brand;
    }
    
    
    /**
    * Получает все директории в виде массива, в которых содержится товар с данным брендом
    * 
    * @param \Catalog\Model\Orm\Brand $brand - бренд для которого ищются директории
    * @param boolean $cache - флаг использования кэша
    * @return array|false
    */
    static function getBrandDirs($brand, $cache = true){
       if ($cache) {
            return \RS\Cache\Manager::obj()
                    ->request(['\Catalog\Model\BrandApi', 'getBrandDirs'],$brand, false);
       }else{ 
           $dirs = [];
           if (!empty($brand)){
               //Получим id директорий 
               $q = \RS\Orm\Request::make()
                  ->select('X.dir_id as dir, COUNT(P.id) as cnt')  
                  ->from(new \Catalog\Model\Orm\Product(),'P')
                  ->join(new \Catalog\Model\Orm\Xdir(),'X.product_id = P.id','X')
                  ->groupby('dir_id')
                  ->where([
                    'P.brand_id' => $brand['id'],
                    'P.site_id' => \RS\Site\Manager::getSiteId(),
                    'P.public' => 1
                  ]);
               if (\RS\Config\Loader::byModule('catalog')->hide_unobtainable_goods == 'Y') {
                   $q->where('P.num > 0');
               }
               $dir_ids = $q->exec()->fetchSelected('dir','cnt');
               
               if (!empty($dir_ids)){
                  //Получим сами директории
                  $dirs = \RS\Orm\Request::make()
                            ->from(new \Catalog\Model\Orm\Dir())
                            ->where([
                                'public' => 1
                            ])
                            ->whereIn('id',array_keys($dir_ids))
                            ->orderby('name ASC')
                            ->objects();
                  
                  //Приплюсуем наличие товаров данного бренда          
                  if (!empty($dirs)){
                      foreach ($dirs as $dir){
                         $dir['brands_cnt'] = $dir_ids[$dir['id']]; 
                      }     
                  }
              }
           } 
           
           return $dirs;
       }
    }
    
    /**
    * Получает товары, которые находятся в спец категориях, но соответсвуют бренду 
    * 
    * @param \Catalog\Model\Orm\Brand $brand - объект бренда
    * @param integer $limit - лимит товаров для вывода
    * @param boolean $cache - флаг кэша
    * @return array
    */
    static function getProductsInSpecDirs($brand, $limit, $cache = true)
    {
        if ($cache) {
            return \RS\Cache\Manager::obj()->request(['\Catalog\Model\BrandApi', 'getProductsInSpecDirs'], $brand, $limit, false);
        } else { 
            $config = \RS\Config\Loader::byModule('catalog');
            $products = [];
            if ($config['brand_products_specdir']) {
                $product_api = new Api();
                $product_api->setFilter('dir', $config['brand_products_specdir'])
                            ->setFilter('brand_id', $brand['id'])
                            ->setFilter('public', 1);
                if ($config['brand_products_hide_unobtainable']) {
                    $product_api->setFilter('num', 0, '>');
                }
                $products = $product_api->getList(1, $limit);
            }
            return $products;
        }
    }
    
    /**
    * Получить id бренда по where запросу. Результат кешируется
    * Если бренд отсутствует - он будет создан
    * 
    * @param array $where - массив для where части запроса
    * @param array $fields - данные для создания бренда, если он не найден
    * @return int
    */
    static function getBrandIdByWhere(array $where, array $fields)
    {
        $cache_key = serialize($where);
        if (!array_key_exists($cache_key, self::$_cache_brands)) {
            $brand = \Catalog\Model\Orm\Brand::loadByWhere($where); 
            if(!$brand->id) {
                $brand['site_id'] = \RS\Site\Manager::getSiteId();
                $brand['public'] = 1;
                $brand->getFromArray($fields);
                if (empty($brand['alias'])) {
                    $brand['alias'] = \RS\Helper\Transliteration::str2url($brand['title']);
                }
                
                $same_aliases = \RS\Orm\Request::make()
                                ->select('alias')
                                ->from(new \Catalog\Model\Orm\Brand())
                                ->where('alias like "#brand_alias%"', ['brand_alias' => $brand['alias']])
                                ->exec()->fetchSelected(null, 'alias'); 
                if (in_array($brand['alias'], $same_aliases)) {
                    $counter = 2;
                    while(in_array($brand['alias'].$counter, $same_aliases)) {
                        $counter++;
                    }
                    $brand['alias'] .= $counter;
                }
                
                $brand->insert();
            }
            self::$_cache_brands[$cache_key] = $brand['id'];
        }
        return self::$_cache_brands[$cache_key];
    }


    /**
     * Добавляет символьные идентификаторы брендам, у которых они не установлены
     *
     * @return integer
     */
    function addTranslitAliases()
    {
        $count = 0;
        $this->queryObj()->where("(alias IS NULL OR alias='')");
        $res = $this->getListAsResource();
        while($row = $res->fetchRow()) {
            $count++;
            $brand = new Orm\Brand();
            $brand->getFromArray($row);
            $i = 0;
            $ok = false;
            while(!$ok && $i<15) {
                $brand[$this->alias_field] = \RS\Helper\Transliteration::str2url(Tools::unEntityString($brand['title'])).(($i>0) ? "-$i" : '');
                $ok = $brand->update();
                $i++;
            }
        }
        return $count;
    }
}