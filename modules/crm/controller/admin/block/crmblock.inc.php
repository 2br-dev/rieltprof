<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin\Block;

use RS\Controller\Admin\Block;

/**
 * Блок CRM, отображает связанные сделки, взаимодействия, взаимодействия с пользователем, задачи
 */
class CrmBlock extends Block
{
    const
        TAB_DEAL = 'deal',
        TAB_INTERACTION = 'interaction',
        TAB_USER_INTERACTION = 'userInteraction',
        TAB_TASK = 'task';

    protected
        $default_params = [
            'tabs' => []
    ];

    function actionIndex()
    {

        return $this->result->setTemplate('admin/blocks/crm/crmblock.tpl');
    }
}