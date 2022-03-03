<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Telephony\Provider\Telphin;

use Crm\Model\Telephony\Provider\AbstractProviderTest;

/**
 * Класс, содержащий набор методов для тестирования телефонии Телфин, эмитации запросов
 */
class TelphinTest extends AbstractProviderTest
{
    /**
     * Обрабатывает запрос на тестирование
     *
     * @param array $data
     * @return bool
     */
    public function onTest(array $data)
    {
        $this->last_event_error = null;
        $this->last_event_result = null;

        $event_data = $this->getEventData($data);
        $url = $this->getProvider()->getEventGateUrl( $event_data['EventType'] );

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => http_build_query($event_data),
            ],
        ]);

        $result = @file_get_contents($url, false, $context);


        if (!empty($data['show_request'])) {
            $request = t('. Запрос: %request', [
                'request' => $url.'?'.http_build_query($event_data)
            ]);
        }

        if ($result !== false) {
            $this->last_event_result = t('Уведомление о событии отправлено');

            if (!empty($data['show_request'])) {
                $this->last_event_result .= $request;
            }

            return true;
        } else {
            $this->last_event_error = t('Не удалось выполнить POST запрос на URL `%url`. Статус ответа: %status', [
                'url' => $url,
                'status' => isset($http_response_header[0]) ? $http_response_header[0] : ''
            ]);


            if (!empty($data['show_request'])) {
                $this->last_event_error .= $request;
            }
            return false;
        }
    }

    /**
     * Возвращает тестовые данные для генерации события от телефонии
     *
     * @param array $data Данные формы тестирования телефонии
     * @return array
     */
    private function getEventData(array $data)
    {
        $call_id = md5($data['called_id'].$data['caller_number']);
        $params = [
            'CallID' => $call_id,
            'SubCallID' => '193958-'.$call_id,
            'CallAPIID' => '3584709139-3849618a-cd61-11e9-b297-bd083c9cfa95',
            'EventTime' => time() * 1000000
        ];

        if ($data['call_flow'] == self::CALL_FLOW_IN) {
            $params += [
                'CalledExtension' => $data['called_id'].'@sipproxy.telphin.ru',
                'CalledExtensionID' => '193958',
                'CallerExtension' => '0000*000@sipproxy.telphin.ru',
                'CallerExtensionID' => '193978',
                'CalledNumber' => $data['called_id'],
                'CalledDID' => '70000000000',
                'CallerIDNum' => $data['caller_number'],
                'CallerIDName' => 'Testing call',
                'CallFlow' => 'in'
            ];
        } else {
            $params += [
                'CallerExtension' => $data['called_id'].'@sipproxy.telphin.ru',
                'CallerExtensionID' => '193958',
                'CalledNumber' => $data['caller_number'],
                'CallerIDNum' => $data['called_id'],
                'CallerIDName' => 'Testing call',
                'CallFlow' => 'out',
            ];
        }

        switch($data['call_event_type']) {
            case self::CALL_EVENT_TYPE_DIAL:
                $params += [
                    'EventType' => ($data['call_flow'] == self::CALL_FLOW_IN) ? 'dial-in' : 'dial-out',
                    'CallStatus' => 'CALLING',
                ];
                break;

            case self::CALL_EVENT_TYPE_ANSWER:
                $params += [
                    'EventType' => 'answer',
                    'CallStatus' => 'ANSWER',

                ];
                break;

            case self::CALL_EVENT_TYPE_HANGUP:
                $params += [
                    'EventType' => 'hangup',
                    'CallStatus' => 'ANSWER',
                    'RecID' => '193958-3a849fa6cb6111e9b6b1bd083c9cfa95',
                    'Duration' => 7529999
                ];
                break;
        }

        return $params;
    }
}