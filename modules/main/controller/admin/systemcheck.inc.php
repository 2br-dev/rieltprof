<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;
use Main\Model\SystemCheck\CheckApi;

/**
 * Контроллер для самотестирования системных параметров
 */
class SystemCheck extends \RS\Controller\Admin\Front
{
    function actionIndex()
    {
        $system_check_api = new CheckApi(\Setup::$PATH, \Setup::$MODULE_FOLDER);
        $tests = $system_check_api->findTests();

        $this->view->assign([
            'tests' => $tests
        ]);

        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper->setTopTitle(t('Самотестирование серверного окружения'));
        $helper->viewAsForm();
        $helper['form'] = $this->view->fetch('systemcheck.tpl');

        return $this->result->setTemplate( $helper['template'] );
    }
}