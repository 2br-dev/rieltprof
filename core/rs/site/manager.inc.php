<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Site;

use RS\Application\Auth;
use RS\Cache\Manager as CacheManager;
use RS\Db\Exception as DbException;
use RS\HashStore\Api as HashStoreApi;
use RS\Helper\IdnaConvert;
use RS\Http\Request as HttpRequest;
use RS\Orm\Exception as OrmException;
use RS\Orm\Request as OrmRequest;
use Site\Model\Orm\Site;

/**
 * Класс содержит инструменты для работы с сайтами внутри данной CMS
 * Зависит от модуля Site
 */
class Manager
{
    protected static $adminSite;
    protected static $defaultSite;
    protected static $site;
    protected static $siteId;

    /**
     * Возвращает id текущего сайта
     *
     * @return integer | false
     */
    public static function getSiteId()
    {
        if (!self::$siteId) {
            $site = self::getSite();
            self::$siteId = $site ? $site['id'] : 0;
        }
        return self::$siteId;
    }

    /**
     * Возвращает объект текущего сайта, если вы находитесь в клиентской зоне
     * или возвращает редактируемый в настоящее время сайт, если вы находитесь в администратовной панели
     *
     * @return Site|false
     */
    public static function getSite()
    {
        if (!self::$site) {
            if (!\Setup::$INSTALLED) {
                self::$site = new Site();
                self::$site['id'] = 1;
            } else {
                if (\RS\Router\Manager::obj()->isAdminZone()) {
                    self::$site = self::getAdminCurrentSite();
                } else {
                    $host = HttpRequest::commonInstance()->server('HTTP_HOST', TYPE_STRING);
                    $uri = HttpRequest::commonInstance()->server('REQUEST_URI', TYPE_STRING);
                    self::$site = self::getSiteByUrl($host, $uri);
                }
            }
        }
        return self::$site;
    }

    /**
     * Возвращает объект сайта по доменному имени и URI
     * Если ни один сайт не определен для данного домена, возвращается сайт по-умолчанию
     *
     * @param string $host
     * @param string $uri
     * @return Site
     * Исключение \RS\Db\Exception не может быть брошено после установки
     */
    public static function getSiteByUrl($host, $uri)
    {
        $idnaconvert = new IdnaConvert();
        $sites = self::getSiteList();
        $default = false;
        $haystack = strtolower(ltrim($uri, '/'));
        foreach ($sites as $site) {
            $domains = explode(',', strtolower($site['domains']));
            $domains = array_map('trim', $domains);

            if (in_array(strtolower($host), $domains)
                || in_array(strtolower($idnaconvert->decode($host)), $domains)
            ) { //Проверяем соответствие домену
                //Проверяем соответствие папки
                $needle = strtolower(trim($site['folder'], '/')) . '/';

                if ($needle != '/') {
                    if (substr($haystack, 0, strlen($needle)) === $needle) {
                        //Вырезаем эту секцию из URL
                        $new_uri = '/' . substr(ltrim($uri, '/'), strlen($needle));
                        HttpRequest::commonInstance()->set('REQUEST_URI', $new_uri, SERVER);
                        return $site;
                    }
                } else {
                    return $site;
                }
            }
            if ($site['default']) {
                $default = $site;
            }
        }
        return $default;
    }

    /**
     * Возвращает список сайтов
     *
     * @param bool $cache - возвращать кэшированное значение
     * @return Site[]
     * @throws DbException
     */
    public static function getSiteList($cache = true)
    {
        if ($cache) {
            return CacheManager::obj()->tags(CACHE_TAG_SITE)->request([__CLASS__, 'getSiteList'], false);
        } else {
            $result = [];

            try {
                $result = OrmRequest::make()
                    ->from(new Site())
                    ->orderby('folder DESC')
                    ->objects(null, 'id');
            } catch (DbException $e) {
                if (\Setup::$INSTALLED) throw $e;
            }

            return $result;
        }
    }

    /**
     * Возвращает объект активного сайта в админке
     *
     * @return Site|false
     * @throws DbException
     * @throws OrmException
     */
    public static function getAdminCurrentSite()
    {
        if (!self::$adminSite) {
            $current_user = Auth::getCurrentUser();
            if ($current_user['id'] > 0) {
                $user_site_id = HashStoreApi::get('USER_SITE_' . $current_user['id']);
                $site = new Site($user_site_id);

                if (!$site['id'] || !$current_user->checkSiteRights($site['id'])) {
                    $allow_site_list = $current_user->getAllowSites();
                    if (count($allow_site_list)) {
                        self::$adminSite = reset($allow_site_list);
                    } else {
                        self::$adminSite = false;
                    }
                } else {
                    self::$adminSite = $site;
                }
            } else {
                $host = HttpRequest::commonInstance()->server('HTTP_HOST', TYPE_STRING);
                $uri = HttpRequest::commonInstance()->server('REQUEST_URI', TYPE_STRING);
                self::$adminSite = self::getSiteByUrl($host, $uri);
            }
        }
        return self::$adminSite;
    }

    /**
     * Устанавливает текущий сайт для администрирования в администраторской панели
     *
     * @param integer $site_id - ID сайта
     * @return boolean
     */
    public static function setAdminCurrentSite($site_id)
    {
        $current_user = Auth::getCurrentUser();
        $sites = $current_user->getAllowSites();

        if (count($sites) > 1 && isset($sites[$site_id])) {
            HashStoreApi::set('USER_SITE_' . $current_user['id'], $site_id);
            return true;
        }
        return false;
    }

    /**
     * Устанавливает текущий сайт, обходя механизмы определения сайта
     *
     * @param Site $site
     */
    public static function setCurrentSite(Site $site)
    {
        self::$site = $site;
        self::$siteId = $site["id"];
    }
}
