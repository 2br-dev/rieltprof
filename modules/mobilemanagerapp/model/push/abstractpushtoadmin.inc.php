<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\Push;

use PushSender\Model\Firebase\Push\RsPushNotice;

/**
 * Абстрактный базовый класс для уведомлений для администратора
 */
abstract class AbstractPushToAdmin extends RsPushNotice
{
    /**
     * Возвращает для какого приложения (идентификатора приложения в ReadyScript) предназначается push
     *
     * @return string
     */
    public function getAppId()
    {
        return 'store-management';
    }

    /**
     * Возвращает одного или нескольких получателей - администраторов
     *
     * @return array
     */
    public function getRecipientUserIds()
    {
        $admin_groups = (array)\RS\Config\Loader::byModule($this)->allow_user_groups;
        $couriers_group = (array)\RS\Config\Loader::byModule('shop')->courier_user_group;

        $real_admin_groups = array_diff($admin_groups, $couriers_group);
        if ($real_admin_groups) {
            $user_ids = \RS\Orm\Request::make()
                ->select('user')
                ->from(new \Users\Model\Orm\UserInGroup())
                ->whereIn('group', $real_admin_groups)
                ->exec()->fetchSelected(null, 'user');

            return $user_ids;
        }
        return [];
    }
}