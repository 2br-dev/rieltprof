<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Telephony\Provider\Telphin;

use Crm\Model\Telephony\CallEvent;
use Crm\Model\Orm\Telephony\CallHistory;
use Crm\Model\Telephony\Provider\AbstractProvider;
use Crm\Model\Telephony\Provider\AbstractProviderTest;
use Crm\Model\Telephony\Requester;
use RS\Application\Auth;
use RS\Config\Loader;
use RS\File\Tools;
use RS\HashStore\Api as HashStoreApi;
use RS\Http\Request;
use RS\Router\Manager;
use RS\Router\Route;

/**
 * Коннектор для провайдера телефонии Телфин
 */
class TelphinProvider extends AbstractProvider
{
    protected $api_hostname = 'https://apiproxy.telphin.ru';
    protected $api_base_url = '/api/ver1.0';
    protected $settings_info_template = '%crm%/telephony/telphin/settings_info.tpl';

    /**
     * Возвращает название провайдера телефонии
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Телфин');
    }

    /**
     * Возвращает внутренний строковый идентификатор провайдера связи
     *
     * @return string
     */
    public function getId()
    {
        return 'telphin';
    }

    /**
     * Обрабатывает входящий запрос с событием от сервиса телефонии
     *
     * @param Request $url
     * @return CallEvent Возвращает унифицированный объект события для RS
     */
    public function onEvent(Request $url)
    {
        $call_event = new CallEvent($this);
        $call_event
            ->setEventType($url->request('EventType', TYPE_STRING))
            ->setCallerNumber($url->request('CallerIDNum', TYPE_STRING))
            ->setCalledNumber($url->request('CalledNumber', TYPE_STRING))
            ->setCallFlow($url->request('CallFlow', TYPE_STRING))
            ->setCalledDID($url->request('CalledDID', TYPE_STRING))
            ->setCallID($url->request('CallID', TYPE_STRING))
            ->setSubCallID($url->request('SubCallID', TYPE_STRING))
            ->setRecID($url->request('RecID', TYPE_STRING))
            ->setDuration($url->request('Duration', TYPE_STRING))
            ->setCallAPIID($url->request('CallAPIID', TYPE_STRING))
            ->setEventTime(date('Y-m-d H:i:s', $url->request('EventTime', TYPE_STRING)/1000000 ))
            ->setCallerId($url->request('CallerExtensionID', TYPE_STRING))
            ->setCalledId($url->request('CalledExtensionID', TYPE_STRING))
            ->setData([
                'CalledExtension' => $url->request('CalledExtension', TYPE_STRING),
                'CallerExtension' => $url->request('CallerExtension', TYPE_STRING),
            ])
            ->setReturnData('OK');

        if ($call_event->getEventType() == self::EVENT_TYPE_HANGOUT) {
            $call_event->setCallSubStatus($url->request('CallStatus', TYPE_STRING));
        }

        return $call_event;
    }

    /**
     * Возвращает объект, который описывает тесты
     * для данного провайдера.
     *
     * @return AbstractProviderTest
     */
    public function getEventTestObject()
    {
        return new TelphinTest($this);
    }

    /**
     * Возвращает массив действий для различных статусов звонка
     *
     * @param CallHistory $call
     * @return array
     */
    public function getActionsByCall(CallHistory $call)
    {
        $actions = [];

        if ($this->canApiRequest()) {
            $router = Manager::obj();

            $hangup_url = $router->getAdminUrl('doAction', [
                'call_id' => $call['call_id'],
                'call_action' => 'hangup'
            ], 'crm-callactions');

            switch ($call['call_status']) {
                case CallHistory::CALL_STATUS_ANSWER:
                case CallHistory::CALL_STATUS_CALLING:

                    $actions[] = [
                        'text' => t('Отклонить'),
                        'attr' => [
                            'data-url' => $hangup_url,
                            'class' => 'btn btn-danger tel-action'
                        ]
                    ];

                    break;
            }
        }

        return $actions;
    }

    /**
     * Возвращает добавочный номер для администратора user_id, если таковой задан. Иначе - false
     *
     * @param integer $user_id
     * @return integer|bool(false)
     */
    public function getExtensionIdByUserId($user_id)
    {
        $config = Loader::byModule($this);
        $user_map = (array)$config['telphin_user_map'];

        foreach($user_map as $item) {
            if (isset($item['user_id']) && $item['user_id'] == $user_id) {
                return $item['extension_id'];
            }
        }

        return $this->setError(t('Не найден добавочный номер для пользователя'));
    }

    /**
     * Возвращает ID пользователя по добавочному номеру, если таковой задан. Иначе - false
     *
     * @param $extension_id
     * @return integer | bool(false)
     */
    public function getUserIdByExtensionId($extension_id)
    {
        $config = Loader::byModule($this);
        $user_map = (array)$config['telphin_user_map'];

        foreach($user_map as $item) {
            if (isset($item['extension_id']) && $item['extension_id'] == $extension_id) {
                return $item['user_id'];
            }
        }

        return false;
    }

    /**
     * Возвращает true, если заполнены все данные для проведения исходящих запросов к API
     *
     * @return bool
     */
    public function canApiRequest()
    {
        $config = Loader::byModule($this);
        return $config->telphin_app_id && $config->telphin_app_secret;
    }

    /**
     * Получает авторизационный токен и устанавливает его в Requester
     *
     * @param Requester $requester
     * @param bool $force
     * @return void
     */
    public function authorizeRequester(Requester $requester, $force = false)
    {
        $config = Loader::byModule($this);

        $params = [
            'client_id' => $config->telphin_app_id,
            'client_secret' => $config->telphin_app_secret
        ];

        $token = $this->getAccessToken($params, $force);

        if ($token) {
            $requester->addHeader('Authorization', 'Bearer ' . $token);
        }
    }

    /**
     * Возвращает последний полученный AccessToken, если он еще не протух.
     * Если force=true, то происходит принудительное переполучение токена
     *
     * @param array $params Параметры для авторизации
     * @param bool $force Если true, то исключает получение токена из кэша
     * @return string | bool(false)
     */
    public function getAccessToken($params = [], $force = false)
    {
        if (!isset($params['client_id'])) {
            return $this->setError(t('Не установлен App ID для Телфин в настройках модуля CRM'));
        }

        if (!isset($params['client_secret'])) {
            return $this->setError(t('Не установлен App Secret для Телфин в настройках модуля CRM'));
        }

        $store_key = $params['client_id'].'-'.$params['client_secret'];
        if (!$force) {
            $saved_before = HashStoreApi::get($store_key);

            if ($saved_before && $saved_before->expires > time()) {
                return $saved_before->access_token;
            }
        }

        $url = $this->api_hostname.'/oauth/token';

        $result = $this->getRequester()
            ->setMethod('POST')
            ->setData([
                'grant_type' => 'client_credentials'
                ] + $params)
            ->request($url, false, false);

        if ($result->getStatusCode() == 200) {
            $json = $result->getJsonData();
            if ($json && !empty($json->access_token)) {

                $json->expires = time() + $json->expires_in;
                HashStoreApi::set($store_key, $json);

                return $json->access_token;

            } else {
                $this->setError(t('Не удалось распарсить резульатат запроса %raw', [
                    'raw' => $result->getRawData()
                ]));
            }
        } else {
            $json = $result->getJsonData();
            $this->setError(t('Неудачная авторизация. Статус ответа %status. Ответ: %response.', [
                'status' => $result->getStatusCode(),
                'response' => !empty($json->error) ? $json->error : t('нет')
            ]));
        }

        HashStoreApi::set($store_key, null);
        return false;
    }

    /**
     * Возвращает true, если телфония поддерживает исходящие звонки
     *
     * @return bool
     */
    public function canCalling()
    {
        return true;
    }

    /**
     * Возвращает true, если включена автоматическая загрузка записей разговоров после отбоя
     *
     * @return bool
     * @throws \RS\Exception
     */
    public function isEnableAutoDownloadRecord()
    {
        $config = Loader::byModule($this);
        return $config->telphin_download_record;
    }

    /**
     * Возвращает путь записи на локальном диске обязательно внутри папки /storage/records/PROVIDER_ID/. Или false - в случае, если провайдер
     * не поддерживает работу с записями
     *
     * @param CallHistory $call
     * @return string|bool(false)
     */
    public function getRecordDataLocalPath(CallHistory $call)
    {
        if (preg_match('/^(\d+)-(..)/', $call['record_id'], $match)) {
            $prefix = $match[1].'/'.$match[2].'/';
        } else {
            $prefix = '';
        }

        $path = self::getRecordLocalBaseDir().'/'.$call->getProvider()->getId().'/'.$prefix.$call['record_id'];
        return $path;
    }

    /**
     * Возвращает true, если запись разговора присутствует локально
     *
     * @param CallHistory $call
     * @return bool
     */
    public function issetRecordLocal(CallHistory $call)
    {
        $path = $this->getRecordDataLocalPath($call);
        return $path && file_exists($path);
    }

    /**
     * Производит попытку загрузки записи на локальный диск
     *
     * @param CallHistory $call
     * @return bool
     */
    public function downloadRecord(CallHistory $call)
    {
        $data = $this->getRecordData($call, false);
        if ($data !== false) {
            $path_to_save = $this->getRecordDataLocalPath($call);

            Tools::makePath($path_to_save, true);
            Tools::makePrivateDir(dirname($path_to_save));

            if (file_put_contents($path_to_save, $data)) {
                return $this->deleteRecord($call);
            } else {
                return $this->setError(t('Не удалось сохранить файл локально'));
            }
        }
        return $this->setError(t('Не удалось загрузить файл записи'));
    }

    /**
     * Возвращает содержимое файла записи телефонного разговора
     *
     * @param CallHistory $call
     * @param bool $find_local
     * @return bool
     */
    public function getRecordData(CallHistory $call, $find_local = true)
    {
        if ($call['record_id']) {
            //Сперва ищем в локальном хранилище
            if ($find_local) {
                if ($this->issetRecordLocal($call)) {
                    $path = $this->getRecordDataLocalPath($call);
                    return file_get_contents($path);
                }
            }

            //Затем на удаленном
            $extension_id = $call->getAdminExtensionId();

            $requester = $this->getRequester();
            $url = $this->api_hostname . $this->api_base_url . "/extension/{$extension_id}/record/{$call['record_id']}";

            $response = $requester->request($url, true, true, true);

            if ($response->getStatusCode() != 200) {
                return $this->setError(t('Не удалось выполнить запрос. Ошибка: %code', [
                    'code' => $response->getStatusCode()
                ]));
            }

            return $response->getRawData();
        } else {
            return $this->setError(t('Звонок без записи'));
        }
    }

    /**
     * Возвращает Mime тип аудиозаписи
     *
     * @return string
     */
    public function getRecordContentType()
    {
        return 'audio/mpeg; charset=utf-8';
    }

    /**
     * Возвращает Client_id для текущего пользователя
     *
     * @param bool $cache
     * @return integer|bool(false)
     * @throws \RS\Exception
     */
    public function getClientId($cache = true)
    {
        $config = Loader::byModule($this);

        if ($cache) {
            return \RS\Cache\Manager::obj()
                ->expire(0)
                ->request([$this, 'getClientId'], false, md5($config['telphin_app_id'], $config['telphin_app_secret']));
        } else {
            $requester = $this->getRequester();
            $url = $this->api_hostname . $this->api_base_url . "/user/";

            $response = $requester->request($url);

            if ($response->getStatusCode() == 200) {
                $json = $response->getJsonData();
                if ($json && isset($json->client_id)) {
                    return $json->client_id;
                }
            }

            return $this->setError(t('Не удалось получить client_id. Статус: %status', [
                'status' => $response->getStatusCode()
            ]));
        }
    }

    /**
     * Удаляет запись телефонного разговора
     *
     * @param CallHistory $call
     * @return bool
     */
    public function deleteRecord(CallHistory $call)
    {
        if ($call['record_id']) {

            $requester = $this->getRequester();
            $client_id = $this->getClientId();

            if ($client_id === false) {
                return false;
            }

            $url = $this->api_hostname . $this->api_base_url . "/client/{$client_id}/record/{$call['record_id']}";

            $requester->setMethod('DELETE');
            $response = $requester->request($url);

            if ($response->getStatusCode() != 200) {
                return $this->setError(t('Не удалось выполнить запрос. Ошибка: %code', [
                    'code' => $response->getStatusCode()
                ]));
            }

            return true;
        } else {
            return $this->setError(t('Звонок без записи'));
        }
    }

    /**
     * Отклоняет звонок
     *
     * @param CallHistory $call
     * @return array|bool
     */
    public function doHangup(CallHistory $call)
    {
        $extension_id = $call->getAdminExtensionId();
        $requester = $this->getRequester();
        $url = $this->api_hostname.$this->api_base_url."/extension/{$extension_id}/current_calls/{$call['call_api_id']}";

        $response = $requester
            ->setMethod('DELETE')
            ->request($url);

        if ($response->getStatusCode() != 204) {
            return $this->setError(t('Не удалось выполнить запрос. Ошибка: %code', [
                'code' => $response->getStatusCode()
            ]));
        }

        return [];
    }

    /**
     * Получает ID добавочного по имени добавочного
     *
     * @param $extension_name
     * @param bool $cache
     * @return integer | bool(false)
     * @throws \RS\Exception
     */
    public function getExtensionIdByName($extension_name, $cache = true)
    {
        $client_id = $this->getClientId();
        $uniq = $this->getId().$extension_name.$client_id;

        if ($cache) {
            return \RS\Cache\Manager::obj()
                ->expire(0)
                ->request([$this, 'getExtensionIdByName'], $extension_name, false, $uniq);
        } else {
            $requester = $this->getRequester();
            $url = $this->api_hostname . $this->api_base_url . "/client/{$client_id}/extension/?name=" . urlencode($extension_name);
            $response = $requester->request($url);

            if ($response->getStatusCode() == 200) {
                $data = $response->getJsonData();
                if (isset($data) && count($data)) {
                    return $data[0]->id;
                }
            }

            return $this->setError(t('Не удалось получить extension_id по номеру добавочного'));
        }
    }

    /**
     * Отправляет запрос на исходящий вызов
     *
     * @param $number
     * @return bool
     */
    public function CallPhoneNumber($number)
    {
        $user_id = Auth::getCurrentUser()->id;
        $extension_name = $this->getExtensionIdByUserId($user_id);
        if ($extension_name) {
            $extension_id = $this->getExtensionIdByName($extension_name);

            if ($extension_id) {
                $requester = $this->getRequester();
                $requester->setMethod('POST');
                $requester->addHeader('Content-type', 'application/json');

                $requester->setData(json_encode([
                    'src_num' => [$extension_name],
                    'dst_num' => (string)$number,
                    'caller_id_name' => (string)$number,
                    'caller_id_number' => (string)$number
                ]));

                $url = $this->api_hostname . $this->api_base_url . "/extension/{$extension_id}/callback/";

                $response = $requester->request($url);
                if ($response->getStatusCode() == 201) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Возвращает true, если удается определить, что это внутренний вызов между сотрудниками телефонии.
     * Такие вызовы должны игнорироваться и не регистрироваться в административной панели
     *
     * @param CallHistory $call Здесь будет объект звонка, который еще не присутствует в базе (ID = null)
     * @return mixed
     */
    public function isInternalCall(CallHistory $call)
    {
        return ($call['call_flow'] == CallHistory::CALL_FLOW_IN && $this->getUserIdByExtensionId($call['caller_number']))
        || ($call['call_flow'] == CallHistory::CALL_FLOW_OUT && $this->getUserIdByExtensionId($call['called_number']));
    }

    /**
     * Устанавливает EventUrl всем пользователям добавочного
     *
     * @param string $telphin_app_id AppID приложения
     * @param string $telphin_secret_key AppSecret приложения
     * @param array $telphin_user_map Сопоставление добавочных и администраторов
     *
     * @return array | bool(false)
     */
    public function setEventUrl($telphin_app_id,
                                $telphin_secret_key,
                                $telphin_user_map)
    {
        $access_token = $this->getAccessToken([
            'client_id' => $telphin_app_id,
            'client_secret' => $telphin_secret_key
        ], true);

        if (!$access_token) return false;

        if (!$telphin_user_map) {
            return $this->setError(t('Не сопоставлен ни один администратор с SIP ID'));
        }

        $extension_ids = [];
        $sip_map = [];
        foreach($telphin_user_map as $map) {
            $extension_id = $this->getExtensionIdByName($map['extension_id']);
            if ($extension_id) {
                $extension_ids[] = $extension_id;
                $sip_map[$extension_id] = $map['extension_id'];
            }
        }

        $this->setError(null);

        if (!$extension_ids) {
            return $this->setError(t('Не удалось получить сведения ни по одному добавочному SIP ID'));
        }

        if (!$this->removeEventUrls($extension_ids)) {
            return false;
        }

        return $this->addEventUrls($extension_ids, $sip_map);
    }

    /**
     * Удаляет URL уведомлений ReadyScript у добавочных
     *
     * @param array $extension_ids
     * @return bool
     */
    private function removeEventUrls($extension_ids)
    {
        foreach($extension_ids as $id) {
            $requester = $this->getRequester();
            $url = $this->api_hostname . $this->api_base_url . "/extension/{$id}/event/";
            $result = $requester->request($url);

            if ($result->getStatusCode() == 200) {
                foreach($result->getJsonData() as $item) {
                    $full_url = $this->getEventGateUrl($item->event_type);

                    //Формируем маску для определения "нашего" URL
                    $mask = rtrim(str_replace(['http://', 'https://', $this->getUrlSecret()], '', $full_url), '/');

                    if (stripos($item->url, $mask) !== false) {

                        //Удаляем eventUrl
                        $subrequest = $this->getRequester();
                        $subrequest->setMethod('DELETE');

                        $url = $this->api_hostname . $this->api_base_url . "/extension/{$id}/event/{$item->id}";
                        $subresult = $subrequest->request($url);

                        if ($subresult->getStatusCode() != 204) {
                            return $this->setError(t('Не удалось удалить EventUrl ID: %id', [
                                'id' => $item->id
                            ]));
                        }
                    }
                }
            } else {
                return $this->setError(t('Не удалось выполнить запрос. Статус: %status', [
                    'status' => $result->getStatusCode()
                ]));
            }
        }

        return true;
    }

    /**
     * Добавляет URL уведомлений у добавочных
     *
     * @param array $extension_ids
     * @param string $url_secret_key
     * @return array | bool(false)
     */
    private function addEventUrls($extension_ids, $sip_map)
    {
        $affected_sip_ids = [];

        foreach($extension_ids as $id) {
            $requester = $this->getRequester();
            $requester->setMethod('POST');
            $requester->addHeader('Content-type', 'application/json');

            foreach($this->getAllowEventTypes() as $event_type => $method) {
                $requester->setData(json_encode([
                    'url' => $this->getEventGateUrl($event_type),
                    'event_type' => $event_type,
                    'method' => $method
                ]));

                $url = $this->api_hostname . $this->api_base_url . "/extension/{$id}/event/";
                $response = $requester->request($url);
                if ($response->getStatusCode() != 201) {
                    return $this->setError(t('Не удалось выполнить запрос на установку EventUrl. Статус: %status', [
                        'status' => $response->getStatusCode()
                    ]));
                }
            }

            $affected_sip_ids[] = $sip_map[$id];
        }

        return $affected_sip_ids;
    }
}