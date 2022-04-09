<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Front;

use RS\Controller\Front;

/**
 * Контроллер, выводящий полный список категорий.
 * В новых темах используется для получения по AJAX сайдбар-меню
 */
class Category extends Front
{
    function actionIndex()
    {
        $this->wrapOutput(false);
        $this->app->blocks
            ->setRouteId($this->router->getCurrentRoute()->getId())
            ->setView($this->view);

        return $this->result->setHtml($this->app->blocks->renderLayout());
    }
}