<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace AtolOnline\Controller\Admin;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

/**
* Содержит действия по обслуживанию
*/
class Tools extends \RS\Controller\Admin\Front
{
    function actionCheckAuth()
    {
        $cash_register = new \AtolOnline\Model\CashRegisterType\AtolOnline();
        $cash_register->makeAuth();
        if ($cash_register->hasError()){
            return $this->result->setSuccess(false)
                                ->addEMessage($cash_register->getErrors());
        }
        return $this->result->setSuccess(true)
                            ->addMessage(t("Авторизация прошла успешно"));
        
    }

    function actionLoadXml()
    {
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper->setTopTitle(t('Загрузить файл настроек для CMS'));
        $helper->setBottomToolbar(new \RS\Html\Toolbar\Element([
            'Items' => [
                new \RS\Html\Toolbar\Button\SaveForm()
            ]
        ]));
        $helper->viewAsForm();
        $helper['form'] = $this->view->fetch('cms_settings_form.tpl');

        if ($this->url->isPost()) {

            if ($error = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_UPDATE)) {
                return $this->result->addEMessage($error);
            }
            $file = $this->url->files('file');
            $step = $this->url->request('step', TYPE_INTEGER);
            if ($step == 1) {
                try {
                    $xml = new \SimpleXMLElement($file['tmp_name'], 0, true);

                    $shops = [];
                    $params = [];
                    foreach($xml->shop as $shop) {
                        $shops[] = (string)$shop['hostname'];
                        $params[] = [
                           'inn' => (string)$xml['INN'],
                           'group_code' => (string)$shop->access['group_code'],
                           'pass' => (string)$shop->access['password'],
                           'login' => (string)$shop->access['login'],
                        ];
                    }

                    $_SESSION['atol_import_params'] = $params;
                    $this->view->assign('shops', $shops);
                    $helper['form'] = $this->view->fetch('cms_settings_form2.tpl');
                    return $this->result
                                    ->setSuccess(true)
                                    ->setTemplate($helper['template']);


                } catch (Exception $e) {
                    return $this->result->addEMessage(t('Некорректный XML файл'));
                }
            }

            if ($step == 2) {
                //Импортируем
                $shop = $this->url->request('shop', TYPE_STRING);
                if (isset($_SESSION['atol_import_params'][$shop])) {
                    $params = $_SESSION['atol_import_params'][$shop];
                    $config = $this->getModuleConfig();
                    $config->getFromArray($params);
                    $config->update();
                }

                return $this->result->setSuccess(true);
            }
        }

        return $this->result->setTemplate($helper['template']);
    }
}