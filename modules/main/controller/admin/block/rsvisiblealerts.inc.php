<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin\Block;

use Main\Model\NoticeSystem\VisibleAlerts;
use RS\Controller\Admin\Block;

/**
 * Блок отвечает за отображение уведомлений, которые невозможно закрыть
 */
class RsVisibleAlerts extends Block
{
    function actionIndex()
    {
        $visible_alerts = VisibleAlerts::getInstance();
        $this->view->assign([
            'visible_alerts' => $visible_alerts,
            'timestamp' => time(),
            'messages_hash' => $visible_alerts->getMessagesHash(),
            'cookie_param_name' => VisibleAlerts::COOKIE_SHOW_KEY
        ]);
        return $this->result->setTemplate('%main%/adminblocks/rsvisiblealerts/visible_alerts.tpl');
    }
}