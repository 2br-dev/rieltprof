<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Feedback\Controller\Block;

use RS\Controller\StandartBlock;
use \RS\Orm\Type;

class Button extends StandartBlock
{
    protected static $controller_title = 'Кнопка обратная связь';
    protected static $controller_description = 'Отображает ссылку на страницу с обратной связью';

    protected $default_params = [
        'indexTemplate' => 'blocks/button/button.tpl', //Должен быть задан у наследника
    ];

    /**
     * Возвращает ORM объект, содержащий настриваемые параметры или false в случае,
     * если контроллер не поддерживает настраиваемые параметры
     *
     * @return \RS\Orm\ControllerParamObject | false
     */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'form_id' => new Type\Varchar([
                'description' => t('Выберите форму из списка'),
                'list' => [['\Feedback\Model\FormApi', 'staticSelectList']]
            ])
        ]);
    }

    function actionIndex()
    {
        $this->view->assign([
            'form_id' => $this->getParam('form_id')
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
