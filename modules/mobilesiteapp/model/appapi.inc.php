<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Model;

/**
 * Класс обеспечивает взаимодействие с API на сервере ReadyScript для получения информации
 * о подписке на мобильное приложение.
 */
class AppApi extends \RS\Module\AbstractModel\BaseModel
{
    const
        ERROR_NOT_APP_SUBSCRIBE = 'object_not_found',
        CACHE_LIFETIME = 300; //Кэшируем для комфортной работы ответы сервера на 5 минут.

    /**
     * Возвращает ссылку на раздел, где описывается, как можно бесплатно
     * посмотреть на мобильное приложение.
     *
     * @return string
     */
    public function getDemoUrl()
    {
        return \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$RS_SERVER_DOMAIN.'/mobile-site-app/#try';
    }

    /**
     * Возвращает ссылку на раздел для управления подпиской
     *
     * @param string $domain доменное имя без http://
     * @return string
     */
    public function getControlUrl($domain)
    {
        return \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$RS_SERVER_DOMAIN.'/my/apps/';
    }

    /**
     * Выполняет запрос на сервер ReadyScript для получения сведений о подписке
     * на мобильное приложение. В случае успеха возвращает массив со сведениями,
     * полученными от сервера ReadyScript
     *
     * Успешные запросы кэшируются на время CACHE_LIFETIME
     *
     * @return array | bool(false)
     */
    public function getAppSubscribeInfo($domain, $main_license_hash, $main_license_data_hash, $cache_enable = true)
    {
        $cache = \RS\Cache\Manager::obj()
                    ->expire(self::CACHE_LIFETIME);

        $cache_key = $cache->generateKey($domain.$main_license_hash);

        if ($cache_enable && $cache->validate($cache_key)) {

            return $cache->read($cache_key);

        } else {
            if (!$main_license_hash && !$main_license_data_hash) {
                return false; //Без лицензии на продукт невозможно взаимодействовать с API ReadyScript Mobile
            }

            $data = [
                'domain' => $domain,
                'main_license_hash' => $main_license_hash,
                'main_license_data_hash' => (string)$main_license_data_hash
            ];

            $result = $this->requester('mobileSiteApp.getSettings', $data);
            if (!$result) {
                return false;
            }

            if (isset($result['error'])) {
                $cache->invalidate($cache_key);
                \RS\HashStore\Api::set("APP_SUBSCRIBE_{$domain}", null);

                //Ошибку "Подписка не найдена" не считаем ошибкой
                if ($result['error']['code'] != self::ERROR_NOT_APP_SUBSCRIBE) {
                    $this->addError($result['error']['title']);
                }
                return false;

            } else {
                //Сохраняем кэш только в случае успеха
                $data = $result['response'];

                if ($data['app']) {
                    //Обновляем информацию в долгосрочном кэше
                    \RS\HashStore\Api::set("APP_SUBSCRIBE_{$domain}", $data['app']);
                }

                $cache->write($cache_key, $data);
                return $data;
            }
        }
    }

    /**
     * Выполняет запрос на удаленный сервер
     *
     * @param $api_method - метод АПИ
     * @param $data - Дополнительные данные
     * @return string
     */
    private function requester($api_method, $data)
    {
        $context = stream_context_create([
            'http'=> [
                'method' => "POST",
                'timeout' => 10,
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => http_build_query($data)
            ]
        ]);

        $url = \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$RS_API_SERVER_DOMAIN.'/api/methods/'.$api_method;

        $result = @file_get_contents($url, null, $context);

        if (!$result) {
            return $this->addError(t('Не удалось соединиться с сервером ReadyScript'));
        }

        $data = json_decode($result, true);
        if (!$data) {
            return $this->addError(t('Ошибка данных. Повторите попытку позже'));
        }

        return $data;
    }

    /**
     * Возвращает количество оформленных с помощью данного приложения заказов
     *
     * @return integer
     */
    public function getAppOrderCount($site_id = null)
    {
        if ($site_id === null) {
            $site_id = \RS\Site\Manager::getSiteId();
        }

        return \RS\Orm\Request::make()
            ->from(new \Shop\Model\Orm\Order())
            ->where([
                'site_id' => $site_id,
                'is_mobile_checkout' => 1
            ])->count();
    }

    /**
     * Возвращает массив с текстами с количеством оставшихся дней подписки
     * на приложение или false, в случае, если еще не пришло время для отображения текста.
     *
     * Данный текст отображается в области уведомлений
     */
    public function getExpireText($domain)
    {
        $app_data = \RS\HashStore\Api::get("APP_SUBSCRIBE_{$domain}", null);

        if ($app_data !== null) {
            if (isset($app_data['date_of_expire'])) {
                $remaining_days = ceil((strtotime($app_data['date_of_expire']) - time()) / 86400);

                if ($remaining_days > 0 && $remaining_days < 15) {
                    return t('До завершения подписки на мобильное приложение для домена %domain осталось %days [plural:%days:день|дня|дней]. Нажмите, чтобы продлить подписку.', [
                        'domain' => $domain,
                        'days' => $remaining_days
                    ]);
                }

                //Показываем уведомление еще 14 дней после истечения подписки
                if ($remaining_days > -15 && $remaining_days < 0) {
                    return t('Подписка на мобильное приложение для домена %domain истекла. Нажмите, чтобы продлить подписку.', [
                        'domain' => $domain
                    ]);
                }
            }

        }
        return false;
    }

    /**
     * Возвращает данные подписки на приложения
     * @return array | null
     */
    public function getSubscribeInfo($domain)
    {
        return \RS\HashStore\Api::get("APP_SUBSCRIBE_{$domain}", null);
    }
}