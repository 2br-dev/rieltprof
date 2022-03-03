<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Admin;

use RS\Controller\Admin\Front;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\Router\Manager as RouterManager;

class ExternalRequestLogViewer extends Front
{
    /**
     * @return Standard
     * @throws \SmartyException
     */
    function actionIndex()
    {
        $log_directory = \Setup::$PATH . \Setup::$LOGS_DIR . "/externalrequests";
        $links = [];
        foreach (scandir($log_directory) as $log_file) {
            if (!in_array($log_file, ['.', '..'])) {
                $links[$log_file] = RouterManager::obj()->getAdminUrl('openFile', ['file_name' => $log_file]);
            }
        }

        $this->view->assign([
            'links' => $links,
        ]);

        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Список лог-файлов'));
        $helper->viewAsAny();
        $helper['form'] = $this->view->fetch('form/externalrequestlogviewer/log_list.tpl');

        return $this->result->setTemplate($helper['template']);
    }

    /**
     * Отображает указанный лог-файл
     */
    public function actionOpenFile()
    {
        $this->wrap_output = false;

        $file = $this->url->get('file_name', TYPE_STRING);
        $log_directory = \Setup::$PATH . \Setup::$LOGS_DIR . "/externalrequests";
        $full_name = $log_directory . '/' . $file;

        if (file_exists($full_name)) {
            echo '<pre>' . file_get_contents($full_name) . '</pre>';
        } else {
            echo t('Файл не найден');
        }
    }
}
