<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;

use RS\Helper\RSApi;

/**
 * Класс позволяет получать с сервера ReadyScript лучшие предложения услуг
 */
class BestSellersApi
{
    const
        BESTSELLERS_DIALOG_HASHSTORE_KEY = 'bestsellers-dialog-shown',
        BESTSELLERS_CACHE = 'widget-bestsellers',
        BESTSELLERS_CACHE_EXPIRE = 86400; // 24 часа

    /**
     * Возвращает объект кэша для рекомендуемых модулей
     *
     * @return \RS\Cache\Manager
     */
    private function getCache()
    {
        return \RS\Cache\Manager::obj()->expire(self::BESTSELLERS_CACHE);
    }

    /**
     * Возвращает лучшие предложения или false
     *
     * @return array|bool(false)
     */
    public function getCachedBestSellers()
    {
        $cache = $this->getCache();
        $cache_key = $cache->generateKey(self::BESTSELLERS_CACHE);

        if ($cache->validate($cache_key)) {
            return $cache->read($cache_key);
        }
        return false;
    }

    /**
     * Возвращает URL API для получения лучших предложений
     *
     * @return string
     */
    public function getBestSellersApiUrl()
    {
        return 'http://' . \Setup::$RS_API_SERVER_DOMAIN . '/api/methods/bestsellers.getList';
    }

    /**
     * Загружает с сервера ReadyScript информацию о лучших предложениях,
     * в случае успеха сохраняет эту информацию в кэш и возвращает её,
     * в случае ошибки возвращается текст ошибки.
     *
     * @param int $limit
     * @return string | array
     */
    public function updateCacheBestSellers()
    {
        $api_url = $this->getBestSellersApiUrl();

        $module_api = new \RS\Module\Manager();

        $params = array_merge([
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
            if (isset($data['response']['list'])) {

                $cache = $this->getCache();
                $cache_key = $cache->generateKey(self::BESTSELLERS_CACHE);
                $cache->write($cache_key, $data['response']['list']);

                return $data['response']['list'];
            } else {
                return (string)$data['error']['title'];
            }
        } else {
            return t('Ошибка возвращаемых данных');
        }
    }

    /**
     * Возвращает true, если нужно отобразить диалог лучших предложений
     *
     * @return bool
     */
    public function needShowBestSellerDialog()
    {
        //Идентификатор текущего обучающего тура
        $request = \RS\Http\Request::commonInstance();
        $controller = $request->parameters('mod_controller', TYPE_STRING);
        $action = $request->get('do', TYPE_STRING);

        $is_adding_widget = ($controller == 'main-widgets' && $action == 'ajaxAddWidget');
        $tour_id = $request->cookie('tourId', TYPE_STRING);
        $was_shown = \RS\HashStore\Api::get(self::BESTSELLERS_DIALOG_HASHSTORE_KEY, false);

        return !$was_shown && !$tour_id && !$is_adding_widget; //Показываем диалог, если он еще не был показан или идет обучающий тур
    }

    /**
     * Устанавливает отметку о том, что диалог лучших предложений уже был показан
     *
     * @return void
     */
    public function disableShowBestSellerDialog()
    {
        \RS\HashStore\Api::set(self::BESTSELLERS_DIALOG_HASHSTORE_KEY, true);
    }

    /**
     * Подготоваливает ссылку. Заменяет в ней переменные
     *
     * @param string $link Ссылка
     * @return string
     */
    public function prepareLink($link)
    {
        $link = str_replace('%ADMIN%', '/'.\Setup::$ADMIN_SECTION, $link);
        return $link;
    }
}