<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\User;
use \ExternalApi\Model\Exception as ApiException;

/**
* Возвращает список пользователей-курьеров
*/
class GetCourierList extends \ExternalApi\Model\AbstractMethods\AbstractGetList
{    
    /**
    * Возвращает объект выборки объектов 
    * 
    * @return \RS\Module\AbstractModel\EntityList
    */
    public function getDaoObject()
    {
        return new \Users\Model\Api();
    }    
    
    /**
    * Устанавливает фильтр для выборки
    * 
    * @param \RS\Module\AbstractModel\EntityList $dao
    * @param array $filter
    * @return void
    */
    public function setFilter($dao, $filter)
    {
        $courier_group = \RS\Config\Loader::byModule('shop')->courier_user_group;
        if ($courier_group) {
            $dao->setFilter('group', $courier_group);            
        } else {
            //Если не настроена курьерская группа, то добавляем неразрешимое условие,
            //чтобы не вернулся ни один пользователь
            $dao->setFilter('A.id', 0); 
        }
    }
    
    
    public function getAllowableOrderValues()
    {
        return ['id', 'id desc', 'surname,name,midname'];
    }
    
    /**
    * Возвращает общее число объектов для данной выборки
    * 
    * @param \RS\Module\AbstractModel\EntityList $dao
    * @return integer
    */
    public function getResultCount($dao)
    {
        return \RS\Orm\Request::make()
            ->from('('.$dao->queryObj().')', 'subquery')
            ->count();
    }
 
    /**
    * Выполняет запрос на выборку пользователей-курьеров
    * 
    * @param string $token Авторизационный token
    * @param array $filter Зарезервировано для фильтров #filters-info
    * @param string $sort Сортировка по полю, поддерживает значения: #sort-info
    * @param integer $page Номер страницы, начинается с 1
    * @param mixed $pageSize Размер страницы
    * 
    * @example GET /api/methods/user.getcourierlist?token=b45d2bc3e7149959f3ed7e94c1bc56a2984e6a86
    * Ответ:
    * <pre>
    *{ 
    *    "response": { 
    *        "summary": { 
    *            "page": "1", 
    *            "pageSize": "20", 
    *            "total": "3" 
    *        }, 
    *        "list": [ 
    *            { 
    *                "id": "2", 
    *                "name": "Артем", 
    *                "surname": "Иванов", 
    *                "midname": "Петрович", 
    *                "e_mail": "mail@readyscript.ru", 
    *                "login": "demo@example.com", 
    *                "phone": "+700000000000", 
    *                "sex": "", 
    *                "subscribe_on": "0", 
    *                "dateofreg": "0000-00-00 00:00:00", 
    *                "ban_expire": null, 
    *                "last_visit": "2016-09-10 19:10:05", 
    *                "is_company": "1", 
    *                "company": "ООО Ромашка", 
    *                "company_inn": "1234567890", 
    *                "data": { 
    *                    "passport": "00000012233" 
    *                }, 
    *                "passport": "серия 03 06, номер 123456, выдан УВД Западного округа г. Краснодар, 04.03.2006", 
    *                "company_kpp": "0987654321", 
    *                "company_ogrn": "1234567890", 
    *                "company_v_lice": "директора Сидорова Семена Петровича", 
    *                "company_deistvuet": "устава", 
    *                "company_bank": "ОАО УРАЛБАНК", 
    *                "company_bank_bik": "1234567890", 
    *                "company_bank_ks": "10293847560192837465", 
    *                "company_rs": "19283746510293847560", 
    *                "company_address": "350089, г. Краснодар, ул. Чекистов, 12", 
    *                "company_post_address": "350089, г. Краснодар, ул. Чекистов, 15", 
    *                "company_director_post": "директор", 
    *                "company_director_fio": "Сидоров С.П.", 
    *                "user_cost": null 
    *            }
    *        ] 
    *    } 
    *} 
    *</pre>
    * 
    * @return array Возвращает список пользователей-курьеров
    */
    protected function process($token,
                               $_filter = [],
                               $sort = 'id', 
                               $page = "1", 
                               $pageSize = "20")
    {
        
        return parent::process($token, [], $sort, $page, $pageSize);
    }
}
