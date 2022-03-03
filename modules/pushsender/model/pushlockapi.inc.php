<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model;

class PushLockApi
{
    /**
    * Возвращает список приложений и их push уведомлений
    * 
    * @return array
    */
    function getPushNotices($include_multicast = true)
    {
        $result = [];
        $applications = \RS\RemoteApp\Manager::getAppTypes();
        
        foreach($applications as $app) {
            if ($app instanceof \PushSender\Model\App\InterfaceHasPush) {
                $app_result = [
                    'title' => $app->getTitle(),
                    'app' => $app->getId(),
                    'notices' => []
                ];
                foreach($app->getPushNotices() as $push) {
                    if ($include_multicast || !$push->isMulticast()) {
                        $app_result['notices'][$push->getId()] = $push->getTitle();
                    }
                }
                
                if ($app_result['notices']) {
                    $result[] = $app_result;
                }
            }
        }
        
        return $result;
    }
    
    /**
    * Возвращет список уведомлений, которые запрещены для получения пользователем
    * 
    * @param mixed $user_id
    */
    function getUserLocks($user_id)
    {
        $locks = \RS\Orm\Request::make()
            ->from(new \PushSender\Model\Orm\PushLock())
            ->where([
                'user_id' => $user_id
            ])->exec()->fetchAll();
            
        $result = [];
        foreach($locks as $lock) {
            $result[$lock['site_id']][$lock['app']][] = $lock['push_class'];
        }
        return $result;
    }
}
