<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Rieltprof\Controller\Admin;

use Alerts\Model\Manager as AlertsManager;
use Rieltprof\Model\Notice\UserConfirmUser as NoticeUserConfirmUser;

/**
* Содержит действия по обслуживанию
*/
class Tools extends \RS\Controller\Admin\Front
{
    
    /**
    * Обработка переключателя - Допущен к поиску на сайте в админ. части - учетные записи пользователи
    */
    function actionAjaxToggleUserAllowedToSite()
    {
        $id = $this->url->request('id', TYPE_INTEGER, 0);
        $user = new \Users\Model\Orm\User($id);  
        $user['access'] = !$user['access'];
        // Отправка пользователю сообщения о подтверждении регистрации
        if($user['access']){
            $notice = new NoticeUserConfirmUser;
            $notice->init($user);
            AlertsManager::send($notice);
        }
        $user->update();
        return $this->result->setSuccess(true)->addMessage(t('Сохранено'));
    }

    function actionAjaxTogglePartnerPublic()
    {
        $id = $this->url->request('id', TYPE_INTEGER, 0);
        $partner = new \Rieltprof\Model\Orm\Partners($id);
        $partner['public'] = !$partner['public'];
        $partner->update();
        return $this->result->setSuccess(true)->addMessage('Сохранено');
    }

    /**
     * Обработка нажатия переключателя Публиковать в меню - Черный список
     * @return \RS\Controller\Result\Standard
     */
    function actionAjaxToggleBlackListPublic()
    {
        $id = $this->url->request('id', TYPE_INTEGER, 0);
        $blackList = new \Rieltprof\Model\Orm\BlackList($id);
        $blackList['public'] = !$blackList['public'];
        $blackList->update();
        return $this->result->setSuccess(true)->addMessage('Сохранено');
    }

    /**
     * Показать телефон владельца сообщения
     */
    function actionGetOwnerPhone()
    {
        $user_id = $this->url->request('user', TYPE_INTEGER, 0);
        $user = new \Users\Model\Orm\User($user_id);
        if($user){
            echo json_encode($user['phone']);
        }else{
            echo json_encode('error');
        }
    }
}
