<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Controller\Front\Policy;

use RS\Controller\Front;

class Agreement extends Front  {

    function actionIndex()
    {
        $this->app->title->addSection(t('Согласие на обработку персональных данных'));
        
        $this->view->assign([
            'document' => \RS\Config\Loader::getSiteConfig()->agreement_personal_data
        ]);

        return $this->result->setTemplate('policy/policy_wrapper.tpl');
    }
}