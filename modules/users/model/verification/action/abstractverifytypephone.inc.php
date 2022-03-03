<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Model\Verification\Action;

use RS\Module\Item;
use RS\View\Engine;

/**
 * Абстрактный класс для действия верификации,
 * которое предполагает
 */
abstract class AbstractVerifyTypePhone extends AbstractVerifyAction
{
    /**
     * Возвращает тип верификации, который характерен для данного действия
     *
     * @return string
     */
    public function getTypeVerification()
    {
        return self::TYPE_VERIFICATION_PHONE_INLINE;
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
            'verify_session' => $this->getVerificationSession(),
            'users_resource' => Item::getResourceFolders('users')
        ]);

        return $view->fetch('%users%/verification/type_phone_inline.tpl');
    }

    public function setPhoneInputName($name)
    {
        $this->addData('phoneInputName', $name);
        return $this;
    }

    public function setPhoneInputAttrLine($attr)
    {
        $this->addData('phoneInputAttrLine', $attr);
        return $this;
    }

    public function setTokenInputName($name)
    {
        $this->addData('tokenInputName', $name);
        return $this;
    }

    public function getPhoneInputName()
    {
        return $this->getData('phoneInputName');
    }

    public function getPhoneInputAttrLine()
    {
        return $this->getData('phoneInputAttrLine');
    }

    public function getTokenInputName()
    {
        return $this->getData('tokenInputName');
    }
}