<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;

use Main\Model\ModuleLicenseApi;
use RS\Controller\Admin\Front;
use RS\Controller\ExceptionPageNotFound;
use RS\Controller\Result\Standard;
use RS\Db\Exception as DbException;
use RS\Event\Exception as EventException;
use RS\Exception as RSException;

/**
 * Контроллер, обеспечивает работу виджета модульных лицензий
 */
class ModuleLicensesControl extends Front
{
    /**
     * Отключает уведомление для одной соц. сети
     *
     * @return Standard
     * @throws DbException
     * @throws EventException
     * @throws RSException
     */
    public function actionAjaxReloadLicenseData()
    {
        $result = ModuleLicenseApi::reloadAllLicenseData();

        if (is_array($result)) {
            return $this->result->setSuccess(true)->addMessage(t('Данные по лицензиям успешно обновлены'));
        } else {
            return $this->result->setSuccess(false)->addEMessage($result);
        }
    }

    /**
     * Перенаправляет на страницу обновления лицензии для модуля
     *
     * @return Standard
     * @throws DbException
     * @throws EventException
     * @throws ExceptionPageNotFound
     * @throws RSException
     */
    public function actionModuleLicenseUpdate()
    {
        $module = $this->url->request('module', TYPE_STRING);

        $licenses = __MODULE_LICENSE_GET_ALL();
        if (isset($licenses[$module])) {
            $license = $licenses[$module];
            $this->view->assign([
                'url' => \Setup::$RS_SERVER_PROTOCOL."://".\Setup::$RS_SERVER_DOMAIN."/update-modulelicense/",
                'post_params' => [
                    'license' => $license['uniq'],
                    'shop_uniq_hash' => md5($license['shop_uniq'])
                ]
            ]);

            $this->wrapOutput(false);
            return $this->result->setTemplate('post_form.tpl');

        } else {
            $this->e404();
        }
    }
}
