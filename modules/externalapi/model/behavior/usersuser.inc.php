<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\Behavior;
use ExternalApi\Model\Orm\UserApiMethodAccess;
use RS\Orm\Request;
use RS\Site\Manager;

/**
 * Класс дополняет объект пользователя новыми методами
 */
class UsersUser extends \RS\Behavior\BehaviorAbstract
{
    /**
     * Возвращает объект персонального типа цены пользователя для указанного сайта,
     * если сайт не указан, то для текущего сайта.
     *
     * @param integer $site_id - id сайта, если не указан, то результат содержит разрешения для всех сайтов
     * @return array
     */
    function getExternalApiAllowMethods($site_id = null)
    {
        $user = $this->owner;

        $result = Request::make()
            ->from(new UserApiMethodAccess())
            ->where([
                'user_id' => $user['id']
            ])->exec()->fetchSelected('site_id', 'api_method', true);

        if ($site_id) {
            return isset($result[$site_id]) ? $result[$site_id] : [];
        } else {
            return $result;
        }
    }
}
