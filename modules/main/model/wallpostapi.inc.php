<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;

use \RS\HashStore\Api as HashStoreApi;

/**
 * Класс содержит функции для работы с бонусной системой ReadyScript за пост в соц. сетях
 */
class WallPostApi
{
    const
        SOCIAL_VK = 'vk',
        SOCIAL_FB = 'fb';

    /**
     * Возвращает true, если необходимо отобразить информацию о том,
     * что можно получить бонус за пост ВКонтакте
     *
     * @return bool
     */
    public function canShowNotice($social_type)
    {
        //Если ранее не было поста
        if ($was_posted = HashStoreApi::get(self::getHashStoreKey($social_type))) {
            return false;
        }

        //Если активна подписка на обновления у основной лицензии
        if (defined('CLOUD_UNIQ')) {
            return true;
        } else {
            $main_license = false;
            if (function_exists('__GET_LICENSE_LIST')) {
                __GET_LICENSE_LIST($main_license);
            }
            return $main_license && $main_license['update_expire'] > time();
        }
    }

    /**
     * Возвращает абсолютный URL на страницу получения бонуса за пост ВК
     *
     * @param string $social_type vk или fb
     * @return string
     */
    public function getPostUrl($social_type)
    {
        $site = \RS\Site\Manager::getAdminCurrentSite();
        $auth_params = \RS\Helper\RSApi::getAuthParams();
        $link = \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$RS_SERVER_DOMAIN.'/wallpost/?'.http_build_query($auth_params + [
            'social_type' => $social_type,
            'shop_url' => $site->getRootUrl(true)
                ]);

        return $link;
    }

    /**
     * Скрывает уведомление о том, что можно воспользоваться бонусом за пост в соц. сетях
     *
     * @return bool
     * @throws \RS\Exception
     */
    public static function hideWallPostNotice($social_type)
    {
        HashStoreApi::set(self::getHashStoreKey($social_type), true);
        return true;
    }


    /**
     * Возвращает идентификатор в для хранилища для необходимой соц.сети
     *
     * @param $social_type
     * @return string
     * @throws \RS\Exception
     */
    public static function getHashStoreKey($social_type)
    {
        if (!in_array($social_type, [
            self::SOCIAL_VK,
            self::SOCIAL_FB]))
        {
            throw new \RS\Exception(t('Неизвестный идентификатор социальной сети'));
        }

        return 'wall-post-'.$social_type;
    }

    /**
     * Возвращает ссылку на отключение уведомления
     *
     * @param $social_type
     * @return string
     */
    public function getCloseAlertUrl($social_type)
    {
        $router = \RS\Router\Manager::obj();
        return $router->getAdminUrl('ajaxCloseSocialNotice', ['social_type' => $social_type], 'main-wallpost');
    }
}