<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Controller\Front;

use Marketplace\Model\InstallApi;
use Marketplace\Model\SignApi;
use RS\Config\Loader;

class RemInstall extends \RS\Controller\Front
{
    /**
     * Производит удаленную установку модуля из маркетплейса ReadyScript
     *
     * @return string
     */
	function actionIndex()
	{
        $signApi = new SignApi();
        $installApi = new InstallApi();
        $config = Loader::byModule($this);

        $signature_result = $signApi->checkUploadRequest($_FILES, $_POST);
        if(!$signature_result){
            $this->displayErrorResponse($signApi->getErrors());
        }

        if(!$config['allow_remote_install']){
            $this->displayErrorResponse([t('Удаленная установка запрешена настройками безопасности')]);
        }

        $installed = $installApi->installAddon($_FILES['file']['tmp_name']);

        if($installed)
            $this->displaySuccessResponse(t('Дополнение успешно установлено'));
        else
            $this->displayErrorResponse($installApi->getErrors());
	}

    /**
     * Возвращает информацию, которая необходима для принятия решения о возможности установки модуля
     *
     * @return string
     */
	function actionCheckAbility()
    {
        $signApi = new SignApi();

        if (!$signApi->checkUploadRequest(false, $_POST)) {
            $this->displayErrorResponse($signApi->getErrors());
        }

        $config = Loader::byModule($this);

        $this->displaySuccessResponse('', [
            'client_version' => $config->version,
            'core_version' => \Setup::$VERSION,
            'allow_remote_install' => $config['allow_remote_install']
        ]);
    }

    /**
     * Возвращает JSON с сообщением об ошибке
     *
     * @param array $errors
     */
    private function displayErrorResponse(array $errors)
    {
        echo json_encode([
            'success'=> false,
            'errors' => $errors
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Возвращает сообщение об успешной операции
     *
     * @param $message
     * @param array $data
     */
    private function displaySuccessResponse($message, $data = [])
    {
        echo json_encode([
            'success' => true,
            'message' => $message,
            ] + $data, JSON_UNESCAPED_UNICODE);
        exit;
    }

}

