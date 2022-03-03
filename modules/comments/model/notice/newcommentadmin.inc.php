<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Comments\Model\Notice;

/**
* Уведомление администратору о новом комментарии
*/
class NewCommentAdmin extends \Alerts\Model\Types\AbstractNotice
                      implements \Alerts\Model\Types\InterfaceEmail, 
                                 \Alerts\Model\Types\InterfaceDesktopApp
{
    public
        $comment;

    public function getDescription()
    {
        return t('Оставлен новый комментарий (администратору)');
    } 
    
    /**
    * Инициализация уведомления
    *         
    * @param \Comments\Model\Orm\Comment $comment - Объект нового комментария
    * @return void
    */
    public function init(\Comments\Model\Orm\Comment $comment)
    {
        $this->comment = $comment;
    }
    
    public function getNoticeDataEmail()
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        
        $notice_data = new \Alerts\Model\Types\NoticeDataEmail();
        
        $notice_data->email     = $site_config['admin_email'];
        $notice_data->subject   = t('Поступил новый комментарий на сайте %0 №-%1', [\RS\Http\Request::commonInstance()->getDomainStr()]);
        $notice_data->vars      = $this;
        
        return $notice_data;
    }
    
    public function getTemplateEmail()
    {
        return '%comments%/notice/toadmin_newcomment.tpl';
    }
    
    /**
    * Возвращает путь к шаблону уведомления для Desktop приложения
    * 
    * @return string
    */
    public function getTemplateDesktopApp()
    {
        return '%comments%/notice/desktop_newcomment.tpl';
    }
    
    /**
    * Возвращает данные, которые необходимо передать при инициализации уведомления
    * 
    * @return NoticeDataDesktopApp
    */
    public function getNoticeDataDesktopApp()
    {
        $notice_data = new \Alerts\Model\Types\NoticeDataDesktopApp();
        $notice_data->title = t('Оставлен комментарий');
        $notice_data->short_message = t('%type %nl%comment', [
            'nl' => "\n",
            'type' => $this->comment->getTypeObject()->getTitle().": ".$this->comment->getTypeObject()->getLinkedObjectTitle(),
            'comment' => \RS\Helper\Tools::teaser($this->comment->message, 100, true),
        ]);
        $notice_data->link = \RS\Router\Manager::obj()->getAdminUrl('edit', ['id' => $this->comment['id']], 'comments-ctrl', true);
        $notice_data->link_title = t('Перейти к отзыву');
        
        $notice_data->vars = $this;
        return $notice_data;
    }
}
