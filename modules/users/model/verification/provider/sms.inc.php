<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Provider;

use Alerts\Model\SMS\Manager as SmsManager;
use RS\Exception;
use RS\Http\Request;
use Users\Model\Orm\VerificationSession;

class Sms extends AbstractProvider
{
    protected $template = '%users%/verification/verification_notice_sms.tpl';

    /**
     * Доставляет код к пользователю
     *
     * @param VerificationSession $session Сессия верификации
     * @param string $code Код верификации
     * @return bool
     *
     * @throws Exception
     * @throws \Users\Model\Verification\VerificationException
     */
    function send(VerificationSession $session, $code)
    {
        if ($session['phone']) {
            $domain = Request::commonInstance()->getDomainStr();

            SmsManager::send(
                $session['phone'],
                $this->template,
                [
                    'operation' => $session->getAction()->getRpTitle(),
                    'domain' => $domain,
                    'code' => $code
                ],
                false
            );

            return true;
        }

        throw new Exception(t('Не задан телефон'));
    }

    /**
     * Возвращает название
     *
     * @return mixed
     */
    public static function getTitle()
    {
        return t('СМС');
    }

    /**
     * Возвращает строковый идентификатор провайдера
     * @return mixed
     */
    public static function getId()
    {
        return 'sms';
    }
}