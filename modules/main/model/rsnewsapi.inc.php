<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;
use Main\Model\NoticeSystem\Meter;
use RS\Application\Auth;
use RS\Site\Manager as SiteManager;

/**
 * Обеспечивает взаимодействие с сервером ReadyScript
 * для получения новостей, а также управляет счетчиками
 */
class RsNewsApi
{
    const
        LINK_TYPE_EXTERNAL = 'external', //Внешняя ссылка
        LINK_TYPE_CLIENT_ADMIN = 'client-admin', //Относительная ссылка для админки клиента
        LINK_TYPE_CLIENT = 'client', //Относительная ссылка для клиента

        LAST_ID_KEY = 'rs-news-last-id-',
        LAST_VIEWED_ID_KEY = 'rs-news-last-viewed-id-',
        TOTAL_KEY = 'rs-news-total',
        METER_KEY = 'rs-news';

    protected
        $readitem_api,
        $error,
        $user_id;

    function __construct($user_id = null)
    {
        $this->user_id = $user_id ?: Auth::getCurrentUser()->id;
        $this->readitem_api = new NoticeSystem\ReadedItemApi(0, $this->user_id);
    }

    /**
     * Возвращает список новостей, а также информацию о прочитанности новости
     *
     * @param $page страница
     * @param $pageSize размер страницы
     * @return array
     */
    public function getNewsList($page, $pageSize)
    {
        $params = [
            "page" => $page,
            "pageSize" => $pageSize
        ];

        $response = $this->requester('rsNews.getList', $params);

        if ($response) {
            if (isset($response['response'])) {
                $this->setLastId($response['response']['check_new']['last_id']);
                $last_id = $this->getLastViewedId();

                //Добавляем сведения о прочитанных новостях
                $readed_ids = $this->readitem_api->getReadedIds(self::METER_KEY, array_keys($response['response']['news']));
                foreach($response['response']['news'] as $id => &$item) {
                    $item['is_viewed'] = ($item['id'] <= $last_id) || in_array($id, $readed_ids);
                    $item['href'] = $this->getLink($item['link'], $item['link_type']);
                }
                return $response['response'];
            } else {
                $this->error = $response['error']['title'];
            }
        }
        return false;
    }

    /**
     * Проверяет, есть ли новые новости. Возвращает количество
     * непрочитанных новостей.
     *
     * @return integer
     */
    public function checkNews()
    {
        $response = $this->requester('rsNews.checkNew');

        //Если запрос успешно прошел
        if ($response && isset($response['response'])) {
            $this->setTotal($response['response']['new_total']);
            $this->setLastId($response['response']['last_id']);
        }

        //Получаем количество непрочитанных новостей
        return $this->readitem_api->getUnreadCount($this->getTotal(), self::METER_KEY);
    }

    /**
     * Возвращает сохраненное ранее общее количество новостей
     *
     * @return integer
     */
    public function getTotal()
    {
        return \RS\HashStore\Api::get(self::TOTAL_KEY, 0);
    }

    /**
     * Сохраняет общее количество новостей, которое присутствует
     * на сервере ReadyScript
     *
     * @param integer $total
     */
    public function setTotal($total)
    {
        return \RS\HashStore\Api::set(self::TOTAL_KEY, $total);
    }

    /**
     * Сохраняет ID последней новости, которая присутствует на сервере ReadyScript
     * @param $last_id
     */
    protected function setLastId($last_id)
    {
        return \RS\HashStore\Api::set(self::LAST_ID_KEY.$this->user_id, $last_id);
    }

    /**
     * Возвращает ID последней новости, которая присутствует на сервере ReadyScript
     */
    protected function getLastId()
    {
        return \RS\HashStore\Api::get(self::LAST_ID_KEY.$this->user_id, 0);
    }


    /**
     * Возвращает ID новости, ниже которой все новости считаются прочитанными
     *
     * @return integer
     */
    protected function getLastViewedId()
    {
        return $this->readitem_api->getLastReadedId(self::METER_KEY);
    }

    /**
     * Возвращает подготовленную ссылку новости
     *
     * @param string $link_type Тип ссылки
     * @param string $link ссылка
     */
    protected function getLink($link, $link_type)
    {
        switch($link_type) {
            case self::LINK_TYPE_CLIENT_ADMIN:
                return \RS\Router\Manager::obj()->getUrl('main.admin').$link;

            case self::LINK_TYPE_CLIENT:
                return \RS\Router\Manager::obj()->getUrl('main.index').$link;
        }
        //LINK_TYPE_EXTERNAL
        return $link;
    }

    /**
     * Помечает новость как прочитанную. Возвращает количество непрочитанных новостей.
     *
     * @param integer $id ID прочитанной новости
     * @return int
     */
    public function markAsViewed($id)
    {
        $this->readitem_api->markAsReaded($id, self::METER_KEY);

        //Получаем новый счетчик
        $unread_count = $this->readitem_api->getUnreadCount($this->getTotal(), self::METER_KEY);

        //Сохраняем в хранилище счетчиков
        Meter::getInstance()->updateNumber(self::METER_KEY, $unread_count);
        return $unread_count;
    }

    /**
     * Отмечает все новости как прочитанные
     *
     * @return integer
     */
    public function markAllAsViewed()
    {
        $this->readitem_api->markAllAsReaded($this->getLastId(), self::METER_KEY, 0);
        Meter::getInstance()->updateNumber(self::METER_KEY, 0);
        return 0;
    }

    /**
     *
     * @param $method
     * @param $params
     * @return mixed
     */
    protected function requester($method, $params = [])
    {
        if ($last_id = $this->getLastViewedId()) {
            $params['client_last_news_id'] = $last_id;
        }

        $params = array_merge($params, [
            'v' => 1,
            'lang' => \RS\Language\Core::getCurrentLang(),
        ], \RS\Helper\RSApi::getAuthParams());

        $params_str = http_build_query($params);
        $url = 'http://'.\Setup::$RS_API_SERVER_DOMAIN.'/api/methods/'.$method.'?'.$params_str;

        $context = stream_context_create([
            'http' => [
                'timeout' => 5
            ]
        ]);

        $result = @file_get_contents($url, null, $context);

        if (!$result) {
            $this->error = t('Не удалось подключиться к серверу ReadyScript');
        }
        return json_decode($result, true);
    }

    /**
     * Возвращает последнюю ошибку
     */
    public function getLastError()
    {
        return $this->error;
    }
}