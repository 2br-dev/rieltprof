<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Feedback\Model\Notice;

/**
* Уведомление о поступлении нового сообщения в форму обратной связи
*/
class NewResultAdmin extends \Alerts\Model\Types\AbstractNotice
                     implements \Alerts\Model\Types\InterfaceDesktopApp
{
    public 
        $result_item;
    
    /**
    * Возвращает краткое описание уведомления
    * 
    * @return string
    */
    public function getDescription()
    {
        return t('Заполнение формы обратной связи (администратору)');
    }
    
    /**
    * Инициализирует уведомление
    * 
    * @return void
    */
    public function init(\Feedback\Model\Orm\ResultItem $result_item)
    {
        $this->result_item = $result_item;
    }
    
    /**
    * Возвращает путь к шаблону уведомления для Desktop приложения
    * 
    * @return string
    */
    public function getTemplateDesktopApp()
    {
        return '%feedback%/notice/desktop_new_result.tpl';
    }
    
    /**
    * Возвращает данные, которые необходимо передать при инициализации уведомления
    * 
    * @return NoticeDataDesktopApp
    */
    public function getNoticeDataDesktopApp()
    {
        $notice_data = new \Alerts\Model\Types\NoticeDataDesktopApp();
        $notice_data->title = t("Получено сообщение №%0 из формы на сайте", [
            $this->result_item->id
        ]);
        $notice_data->short_message = t("Название формы: '%0'", [$this->result_item->getFormObject()->title]);
        
        $notice_data->link = \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->result_item['id']], 'feedback-resultctrl', true);
        $notice_data->link_title = t('Перейти к заявке');
        
        $notice_data->vars = $this;
        return $notice_data;
    }
}
