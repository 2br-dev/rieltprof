<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Model;
use RS\Helper\RSApi;

/**
 * Класс содержит методы для работы с внешними API Marketplace ReadyScript
 */
class RemoteApi
{
    const
        RECOMMENDED_CACHE = 'widget-recommended-modules',
        RECOMMENDED_CACHE_EXPIRE = 3600; // 1 час

    /**
     * Возвращает объект кэша для рекомендуемых модулей
     *
     * @return \RS\Cache\Manager
     */
    private function getRecommendedCache()
    {
        return \RS\Cache\Manager::obj()->expire(self::RECOMMENDED_CACHE_EXPIRE);
    }

    /**
     * Возвращает рекомендуемые модули из кэша или false
     *
     * @return array|bool(false)
     */
    function getCachedRecommendedModules()
    {
        $cache = $this->getRecommendedCache();
        $cache_key = $cache->generateKey(self::RECOMMENDED_CACHE);

        if ($cache->validate($cache_key)) {
            return $cache->read($cache_key);
        }
        return false;
    }

    /**
     * Загружает из маркетплейса информацию о новых рекомендуемых модулях,
     * в случае успеха сохраняет эту информацию в кэш и возвращает её,
     * в случае ошибки возвращается текст ошибки.
     *
     * @param int $limit
     * @return string | array
     */
    function updateCacheRecommendedModules($limit = 2)
    {
        $api_url = \Setup::$RS_SERVER_PROTOCOL.'://' . \Setup::$MARKETPLACE_DOMAIN . '/modulesapi/getnew/';

        $module_api = new \RS\Module\Manager();

        $params = array_merge([
            'exclude_modules' => array_keys($module_api->getList()),
            'limit' => $limit,
            'v' => 1,
            'lang' => \RS\Language\Core::getCurrentLang(),
            'product' => \Setup::$SCRIPT_TYPE,
            'script_type' => RSApi::getScriptType()
        ], RSApi::getAuthParams());

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'timeout' => 5,
                'content' => http_build_query($params),
            ],
            'ssl' => [
                'verify_peer' => false,
            ],
        ]);

        $result = @file_get_contents($api_url, null, $context);

        if (!$result) {
            return t('Не удалось подключиться к серверу ReadyScript');
        }

        if ($data = json_decode($result, true)) {
            if (isset($data['response']['modules'])) {

                $cache = $this->getRecommendedCache();
                $cache_key = $cache->generateKey(self::RECOMMENDED_CACHE);
                $cache->write($cache_key, $data['response']['modules']);

                return $data['response']['modules'];
            } else {
                return (string)$data['error']['title'];
            }
        } else {
            return t('Ошибка возвращаемых данных');
        }
    }
}
