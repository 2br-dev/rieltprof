<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\Behavior;

use Catalog\Model\CostApi;
use Catalog\Model\Orm\Typecost;
use RS\Behavior\BehaviorAbstract;
use RS\Router\Manager as RouterManager;
use RS\Site\Manager as SiteManager;
use Users\Model\Orm\User;

class UsersUser extends BehaviorAbstract
{
    protected $cache_cost_id = [];

    /**
     * Возвращает объект типа цены пользователя для указанного сайта,
     * если сайт не указан, то для текущего сайта.
     *
     * @param integer $site_id - id сайта
     * @param bool $cache - использовать кэш
     * @return Typecost
     */
    public function getUserTypeCost($site_id = null, $cache = true)
    {
        $user = $this->owner;
        $cost_id = $user->getUserTypeCostId($site_id, $cache); // добавлен через behavior
        $user_type_cost = new Typecost($cost_id);
        if (!$user_type_cost['id']) {
            $user_type_cost = new Typecost(CostApi::getDefaultCostId());
        }
        
        return $user_type_cost;
    }

    /**
     * Возвращает id персонального типа цены пользователя для указанного сайта или 0 для цены по умолчанию,
     * если сайт не указан, то для текущего сайта.
     *
     * @param int $site_id - id сайта
     * @param bool $cache - использовать кэш
     * @return int
     */
    public function getUserTypeCostId($site_id = null, $cache = true)
    {
        if ($site_id === null) {
            $site_id = SiteManager::getSiteId();
        }

        if (!isset($this->cache_cost_id[$site_id]) || !$cache) {
            /** @var User $user */
            $user = $this->owner;
            $user_cost = @unserialize($user['cost_id']);

            $cost_id = (isset($user_cost[$site_id])) ? $user_cost[$site_id] : 0;
            if (!$cost_id) {
                $groups = $user->getUserGroups(false);
                if (RouterManager::obj()->isAdminZone()) {
                    $groups += $user->getClientSideGroups();
                }

                $sorted_groups = [];
                foreach ($groups as $group) {
                    $sorted_groups[$group['sortn']] = $group;
                }
                ksort($sorted_groups);

                foreach ($sorted_groups as $group) {
                    $user_cost = @unserialize($group['cost_id']);
                    if (isset($user_cost[$site_id])) {
                        $cost_id = $user_cost[$site_id];
                        break;
                    }
                }
            }

            $this->cache_cost_id[$site_id] = $cost_id;
        }
        return $this->cache_cost_id[$site_id];
    }
}
