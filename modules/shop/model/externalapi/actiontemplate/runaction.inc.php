<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\ActionTemplate;
use \ExternalApi\Model\Exception as ApiException;
use Shop\Model\Orm\ActionTemplate;
use Shop\Model\Orm\Order;

/**
 * Выполняет одно действие по шаблону для курьеров
 */
class RunAction extends \ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod
{
    const
        RIGHT_RUN = 1;
    /**
     * Возвращает комментарии к кодам прав доступа
     *
     * @return [
     *     КОД => КОММЕНТАРИЙ,
     *     КОД => КОММЕНТАРИЙ,
     *     ...
     * ]
     */
    public function getRightTitles()
    {
        return [
            self::RIGHT_RUN => t('Выполнение действия по шаблону для курьеров')
        ];
    }

    /**
     * Выполняет одно действие с заказом по шаблону (Например, отправляет SMS клиенту, если тот не ответил курьеру)
     *
     * @param string $token Авторизационный token
     * @param integer $order_id ID заказа, для которого будет выполнено действие
     * @param integer $action_id ID шаблона действия
     *
     * @example GET /api/methods/actionTemplate.runAction?token=894b9df5ebf40531d560235d7379a8cff50f930f
     * Ответ:
     * <pre>
     * {
     *     "response": {
     *         "success": true
     *     }
     * }
     * </pre>
     *
     * @return array Возвращает сообщение об успешно проведенной операции или ошибку
     */
    public function process($token, $order_id, $action_id)
    {
        $order = new Order($order_id);
        if (!$order['id']) {
            throw new ApiException(t('Заказ с указанным order_id не найден'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        $action = new ActionTemplate($action_id);
        if (!$action['id']) {
            throw new ApiException(t('Шаблон действия с указанным action_id не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
        }

        if ($action->run($order)) {
            return [
                'response' => [
                    'success' => true
                ]
            ];
        } else {
            throw new ApiException(t('Не удалось выполнить действие'), ApiException::ERROR_INSIDE);
        }
    }

}