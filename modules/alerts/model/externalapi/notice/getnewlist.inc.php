<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model\ExternalApi\Notice;
use \ExternalApi\Model\Exception as ApiException;

/**
* Возвращает список новых уведомлений для администратора
*/
class getNewList extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{    
    const
        RIGHT_LOAD = 1;
        
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
            self::RIGHT_LOAD => t('Загрузка списка уведомлений')
        ];
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
        $text = preg_replace_callback('/\#desktop-notice-types/', function($match) {
            $api = new \Alerts\Model\Api();
            $types = $api->getDesktopNoticeTypes();
                        
            $result = '<ul>';
            foreach($types as $type) {
                $result .= '<li><b>'.$type['type'].'</b> - '.$type['title'].'</li>';
            }
            $result .= '</ul>';
            return $result;
        }, $text);
        
        return $text;
    }
    
    /**
    * Возвращает все новые уведомления, которые предназначены для авторизованного администратора.
    * 
    * @param string $token Авторизационный токен
    * @param array $allow_notice Массив возможных типов уведомлений. Отсутствие параметра означает - возврат уведомлений всех типов
    * Возможные типы: #desktop-notice-types
    * @param integer $last_id ID от которого нужно выбирать уведомления. Если не передан, то будут возвращены только мета данные для последующих запросов.
    * 
    * @example GET /api/methods/notice.getNewList?token=d120a2a96a00fd8f197e7d46bc48add136909e87&allow_notice[0]=shop-checkoutadmin&allow_notice[1]=catalog-oneclickadmin&last_id=33
    * Ответ:
    * <pre>{
    *     "response": {
    *         "techdata": {
    *             "last_id": "35"
    *         },
    *         "notice": [
    *             {
    *                 "id": "34",
    *                 "dateofcreate": "2016-10-09 22:51:39",
    *                 "title": "Покупка в один клик",
    *                 "short_message": "(2234)",
    *                 "full_message": "<h3>Контакты заказчика</h3>\r\n<p>Имя заказчика: ...",
    *                 "link": "http://full.readyscript.local/admin/catalog-oneclickctrl/?id=90&do=edit",
    *                 "notice_type": "catalog-oneclickadmin",
    *                 "destination_user_id": "0"
    *             },
    *             {
    *                 "id": "35",
    *                 "dateofcreate": "2016-10-10 14:12:32",
    *                 "title": "Покупка в один клик",
    *                 "short_message": "(123123)",
    *                 "full_message": "<h3>Контакты заказчика</h3>\r\n<p>Имя заказчика: ...",
    *                 "link": "http://full.readyscript.local/admin/catalog-oneclickctrl/?id=91&do=edit",
    *                 "notice_type": "catalog-oneclickadmin",
    *                 "destination_user_id": "0"
    *             }
    *         ]
    *     }
    * }
    * </pre>
    * 
    * @return Возвращает ID последнего уведомления, а также список уведомлений
    */
    protected function process($token, $allow_notice = [], $last_id = null)
    {
        $alerts_api = new \Alerts\Model\Api();
        $all_types = array_keys($alerts_api->getDesktopNoticeTypes());

        if (in_array('all', $allow_notice)) {
            $allow_notice = $all_types;
        } else {
            //Фильтруем невалидные имена типов уведомлений (например, с другим регистром)
            $allow_notice = array_intersect($all_types, $allow_notice);
        }

        //Исключаем заблокированные типы уведомлений
        $locked = $alerts_api->getUserLockedNotices($this->token['user_id']);
        $allow_notice = array_diff($allow_notice, $locked);

        $api = new \Alerts\Model\NoticeItemsApi();
        
        //Получим максимальный ID уведомления на текущий момент
        $q = clone $api->queryObj();
        $q->select('MAX(id) maxid');
        $max_id = $q->exec()->getOneField('maxid', 0);
        
        $result = ['response' => [
            'techdata' => [
                'last_id' => $max_id
            ]
        ]];
        
        $api->setFilter([
            [
            'destination_user_id' => 0,
            '|destination_user_id' => $this->token->user_id
            ]]);
        
        if ($last_id !== null) {
            $api->setFilter(['id:>' => $last_id]);
        }
        
        if ($allow_notice) {
            $api->setFilter('notice_type', $allow_notice, 'in');
        }        
        
        if ($last_id !== null) {
            $result['response']['notice'] = \ExternalApi\Model\Utils::extractOrmList($api->getList());
        }
        
        //Возвращаем уведомления
        return $result;
    }
}
