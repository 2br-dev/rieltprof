<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model;

/**
 * API для работы с уведомлениями для Desktop приложения
 */
class NoticeItemsApi extends \RS\Module\AbstractModel\EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\NoticeItem(), [
            'multisite' => true
        ]);
    }
    
    /**
    * Добавляет новое уведомление. При необходимости производит очистку старых уведомлений
    * Desktop приложение затем сделает запрос к API для получения новых уведомлений
    * 
    * @param \Alerts\Model\Types\NoticeDataDesktopApp $notice_data
    * @return \Alerts\Model\Orm\NoticeItem
    */
    public static function addNoticeItem(Types\AbstractNotice $notice, $template)
    {
        $notice_data = $notice->getNoticeDataDesktopApp();

        if ($notice_data) {
            $notice_item = new Orm\NoticeItem();
            $notice_item['title'] = $notice_data->title;
            $notice_item['short_message'] = $notice_data->short_message;
            $notice_item['notice_type'] = $notice->getSelfType();
            $notice_item['link'] = $notice_data->link;
            $notice_item['link_title'] = $notice_data->link_title;

            if ($notice_data->destination_user_id) {
                $notice_item['destination_user_id'] = $notice_data->destination_user_id;
            }

            if ($template) {
                $view = new \RS\View\Engine();
                $view->assign('data', $notice_data->vars);
                $notice_item['full_message'] = $view->fetch($template);
            }

            $notice_item->insert();
            return $notice_item;
        }
    }
    
    /**
    * Удаляет старые уведомления
    * 
    * @return integer возвращает количество удаленных уведомлений
    */
    public static function cleanOldNoticeItems()
    {
        $config = \RS\Config\Loader::byModule(__CLASS__);
        
        $datetime = date('c', strtotime('-'.$config->notice_items_delete_hours.' DAYS'));
        \RS\Orm\Request::make()
            ->delete()
            ->from(new Orm\NoticeItem())
            ->where("dateofcreate < '#datetime'", [
                'datetime' => $datetime
            ])->exec();
    }
}
