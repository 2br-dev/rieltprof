<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\Behavior;

use RS\Behavior\BehaviorAbstract;
use RS\Config\Loader as ConfigLoader;
use RS\Router\Manager as RouterManager;
use Users\Model\Orm\User;

class UsersUser extends BehaviorAbstract
{
    /** @var float */
    protected $cache_basket_min_limit;

    /**
     * Возвращает менедрера пользователя
     *
     * @return User|null
     */
    public function getManager(): ?User
    {
        if ($this->owner['manager_user_id']) {
            return new User($this->owner['manager_user_id']);
        }
        return null;
    }

    /**
     * Возвращает минимальную сумму заказа
     *
     * @param bool $cache - использовать кэш
     * @return float
     */
    public function getBasketMinLimit(bool $cache = true): float
    {
        if ($this->cache_basket_min_limit === null || !$cache) {
            /** @var User $user */
            $user = $this->owner;
            $limit = (float)$user['basket_min_limit'];

            if (!$limit) {
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
                    if ($group['basket_min_limit']) {
                        $limit = (float)$group['basket_min_limit'];
                        break;
                    }
                }
            }

            if (!$limit) {
                $shop_config = ConfigLoader::byModule('shop');
                $limit = (float)$shop_config['basketminlimit'];
            }

            $this->cache_basket_min_limit = $limit;
        }

        return $this->cache_basket_min_limit;
    }
}
