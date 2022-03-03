<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Controller\Block;

use RS\Controller\StandartBlock;
use Shop\Model\SelectedAddress;

/**
 * Блок-контроллер Выбранный регион
 */
class SelectedAddressBlock extends StandartBlock
{
    protected static $controller_title = 'Выбранный регион';
    protected static $controller_description = 'Отображает текущий выбранный регион';

    protected $default_params = [
        'indexTemplate' => 'blocks/selectedaddress/selected_address.tpl',
    ];

    function actionIndex()
    {
        $this->view->assign([
            'selected_address' => SelectedAddress::getInstance(),
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
