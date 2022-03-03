<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin\Block;

class HeaderPanel extends \RS\Controller\Admin\Block
{
    /**
     * Основная обраюотка блока
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionIndex()
    {
        $this->view->assign([
            'items' => $this->getParam('public', false) ? \RS\Controller\Admin\Helper\HeaderPanel::getPublicInstance()->getItems() : \RS\Controller\Admin\Helper\HeaderPanel::getInstance()->getItems()
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
