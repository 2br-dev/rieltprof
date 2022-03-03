<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Config;
use \Users\Model\Orm;

class Install extends \RS\Module\AbstractInstall
{
    function install()
    {
        $result = parent::install();
        if ($result) {
            
            $site_id = \RS\Site\Manager::getSiteId();
            //Добавляем стандартные группы
            $group = new Orm\UserGroup();
            $group->getFromArray([
                'alias' => Orm\UserGroup::GROUP_SUPERVISOR,
                'name' => t('Супервизоры'),
                'description' => t('Пользователь имеющий доступ абсолютно всегда ко всем  модулям и сайтам'),
                'is_admin' => 1
            ])->insert();

            $group->getFromArray([
                'alias' => Orm\UserGroup::GROUP_ADMIN,
                'name' => t('Администраторы'),
                'description' => t('Пользователи, имеющие права на удаление, добавление, изменение контента'),
                'is_admin' => 1
            ])->insert();
            $group->setMenuAccess([Orm\AccessMenu::FULL_USER_ACCESS], Orm\AccessMenu::USER_MENU_TYPE);
            $group->setMenuAccess([Orm\AccessMenu::FULL_ADMIN_ACCESS], Orm\AccessMenu::ADMIN_MENU_TYPE);
            $group->setSiteAccess($site_id);

            $group->getFromArray([
                'alias' => Orm\UserGroup::GROUP_CLIENT,
                'name' => t('Клиенты'),
                'description' => t('Авторизованные пользователи'),
                'is_admin' => 0
            ])->insert();
            $group->setMenuAccess([Orm\AccessMenu::FULL_USER_ACCESS]);
            
            $group->getFromArray([
                'alias' => Orm\UserGroup::GROUP_GUEST,
                'name' => t('Гости'),
                'description' => t('Неавторизованные пользователи'),
                'is_admin' => 0
            ])->insert();
            $group->setMenuAccess([Orm\AccessMenu::FULL_USER_ACCESS]);
            
            //Создаем пользователя СУПЕРВИЗОР
            $install_api = new \Install\Model\Api();
            $install_data = $install_api->getKey('progress');
            
            $user = new Orm\User();
            $user['id'] = 1;
            $user['name'] = t('Супервизор');
            $user['surname'] = ' ';
            $user['midname'] = ' ';
            $user['e_mail'] = $user['login'] = $install_data['config']['supervisor_email'];
            $user['openpass'] = $install_data['config']['supervisor_pass'];
            if (!$user->insert()) {
                $this->addError(t('Не удалось создать пользователя'));
                return false;
            }
            
            $user->linkGroup([Orm\UserGroup::GROUP_SUPERVISOR]);
        }
        return $result;
    }
          
}
