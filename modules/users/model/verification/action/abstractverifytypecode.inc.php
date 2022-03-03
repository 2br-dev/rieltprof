<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Action;

use RS\View\Engine;

/**
 * Абстрактный класс для действия верификации,
 * которое предполагает
 */
abstract class AbstractVerifyTypeCode extends AbstractVerifyAction
{
    /**
     * Возвращает тип верификации, который характерен для данного действия
     *
     * @return string
     */
    public function getTypeVerification()
    {
        return self::TYPE_VERIFICATION_CODE;
    }

    /**
     * Возвращает готовый HTML формы верификации
     *
     * @return string
     * @throws \SmartyException
     * @throws \Users\Model\Verification\VerificationException
     */
    public function getFormView()
    {
        $view = new Engine();
        $view->assign([
            'verify_session' => $this->getVerificationSession()
        ]);

        return $view->fetch('%users%/verification/type_code.tpl');
    }
}