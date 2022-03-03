<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;

use Main\Model\WallPostApi;
use RS\Controller\Admin\Front;

/**
 * Контроллер, обеспечивает отключение уведомления
 * о возможности получить бонус за пост в социальной сети
 */
class WallPost extends Front
{
    /**
     * Отключает уведомление для одной соц. сети
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAjaxCloseSocialNotice()
    {
        $social_type = $this->url->get('social_type', TYPE_STRING);
        WallPostApi::hideWallPostNotice($social_type);

        return $this->result->setSuccess(true)->getOutput(true);
    }
}