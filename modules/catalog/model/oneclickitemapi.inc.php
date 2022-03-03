<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model;
use Main\Model\NoticeSystem\HasMeterInterface;
use Users\Model\Orm\User;


/**
 * Класс содержит API функции для работы с объектом купить в 1 клик
 */
class OneClickItemApi extends \RS\Module\AbstractModel\EntityList
                        implements HasMeterInterface
{
    const
        METER_ONECLICK = 'rs-admin-menu-oneclick';

    function __construct()
    {
        parent::__construct(new \Catalog\Model\Orm\OneClickItem(), [
            'multisite' => true
        ]);
    }


    /**
     * Возвращает API по работе со счетчиками
     *
     * @return \Main\Model\NoticeSystem\MeterApiInterface
     */
    function getMeterApi($user_id = null)
    {
        return new \Main\Model\NoticeSystem\MeterApi($this->obj_instance,
            self::METER_ONECLICK,
            $this->getSiteContext(),
            $user_id);
    }
    
    /**
    * Подготавливает сериализованный массив из товаров
    * 
    * @param array $products - массив товаров и выбранными комплектациями
    * @return string
    */
    function prepareSerializeTextFromProducts($products)
    {
        $arr = [];
        foreach ($products as $product){
            $arr[] = [
                'id' => $product['id'],
                'title' => $product['title'],
                'barcode' => $product['barcode'],
                'offer_fields' => $product['offer_fields']
            ];
        }
        return serialize($arr);
    }

    /**
     * Ищет покупку в 1 клик по различным полям
     *
     * @param string $term поисковая строка
     * @param array $fields массив с полями, в которых необходимо произвести поиск
     * @param integer $limit максимальное количество результирующих строк
     * @return array
     */
    function search($term, $fields, $limit)
    {
        $this->resetQueryObject();
        $q = $this->queryObj();
        $q->select = 'A.*';

        $q->openWGroup();
        if (in_array('user', $fields)) {
            $q->leftjoin(new User(), 'U.id = A.user_id', 'U');
            $q->where("CONCAT(`U`.`surname`, ' ', `U`.`name`,' ', `U`.`midname`) like '%#term%'", [
                'term' => $term
            ]);
        }

        foreach($fields as $field) {
            if ($field == 'user') continue;
            $this->setFilter($field, $term, '%like%', 'OR');
        }

        $q->closeWGroup();

        return $this->getList(1, $limit);
    }
      
}