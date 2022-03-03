<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Model\Orm\Telephony\CallHistory;
use Crm\Model\Telephony\Manager;
use RS\Controller\Admin\Front;

/**
 * Контроллер действий во всплывающем окне вызова телефонии
 */
class CallActions extends Front
{
    /**
     * Возвращает звонок по ID
     *
     * @param $call_id
     * @return CallHistory
     * @throws \RS\Controller\ExceptionPageNotFound
     */
    private function loadCallById($call_id)
    {
        $call_history = CallHistory::loadByWhere([
            'call_id' => $call_id
        ]);
        if (!$call_history['id']) {
            $this->e404();
        }

        return $call_history;
    }
    /**
     * Обновляет содержимое окна телефонии
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Orm\Exception
     */
    public function actionRefreshCallWindow()
    {
        $call_id = $this->url->get('call_id', TYPE_STRING);
        $call_history = $this->loadCallById($call_id);
        $this->result->addSection($call_history->buildMessage());

        return $this->result;
    }

    /**
     * Отмечает, что окно с данным звонком показывать более не следует
     */
    public function actionCloseCallWindow()
    {
        $call_id = $this->url->get('call_id', TYPE_STRING);
        $call_history = $this->loadCallById($call_id);
        $call_history['is_closed'] = 1;
        if ($call_history->update()) {
            return $this->result->setSuccess(true);
        } else {
            return $this->result->setSuccess(false);
        }
    }

    /**
     * Выполняет действие со звонком
     */
    public function actionDoAction()
    {
        $call_id = $this->url->get('call_id', TYPE_STRING);
        $call_action = $this->url->request('call_action', TYPE_STRING);

        $call = $this->loadCallById($call_id);
        $provider = $call->getProvider();
        $method = 'do'.$call_action;

        if (is_callable([$provider, $method])) {
            $result = $provider->$method($call, $this->url);
            if ($result !== false) {
                return $this->result
                                ->setSuccess(true)
                                ->addSection($result);
            } else {
                $this->result
                        ->setSuccess(false)
                        ->addSection('error', $provider->getLastError());
            }
        } else {
            $this->result
                    ->setSuccess(false)
                    ->addSection('error', t('Провайдер не поддерживает данное действие'));
        }

        return $this->result->setSuccess(false);
    }

    /**
     * Выполняет исходящий вызов
     */
    public function actionCalling()
    {
        $this->wrapOutput(false);
        $phone_number = $this->url->request('number', TYPE_STRING);
        $provider_id = $this->getModuleConfig()->tel_active_provider;

        $this->result->addSection('noUpdate', true);

        if ($provider_id) {
            $provider = Manager::getProviderById($provider_id);
            if ($provider->canCalling()) {
                if ($provider->callPhoneNumber($phone_number)) {
                    return $this->result->setSuccess(true);

                } else {
                    $error = $provider->getLastError();
                }

            } else {
                $error = t('Провайдер не поддерживает исходящие звонки');
            }
        } else {
            $error = t('Провайдер для исходящих звонков не назначен');
        }

        return $this->result
                ->setSuccess(false)
                ->addEMessage($error);
    }

    /**
     * Возвращает содержимое файла записи
     */
    public function actionGetRecord()
    {
        $this->wrapOutput(false);

        $call_id = $this->url->get('call_id', TYPE_STRING);
        $call_history = $this->loadCallById($call_id);
        $provider = $call_history->getProvider();
        $data = $provider->getRecordData($call_history);

        if ($data === false) {
            $this->e404($provider->getLastError());
        }

        $this->app->headers
            ->addHeader('Content-type', $provider->getRecordContentType())
            ->addHeader('Content-length', strlen($data));

        return $data;
    }
}