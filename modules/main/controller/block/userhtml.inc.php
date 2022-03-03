<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Block;

use RS\Controller\Block;
use RS\Orm\ControllerParamObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;

/**
 * Блок - Произвольное содержимое
 */
class UserHtml extends Block
{
    protected static
        $controller_title = 'Произвольный HTML',
        $controller_description = 'Отображает заданное пользователем содержимое';

    /**
     * Возвращает ORM объект, содержащий настриваемые параметры или false в случае,
     * если контроллер не поддерживает настраиваемые параметры
     * @return ControllerParamObject | false
     */
    function getParamObject()
    {
        $controller_param_object = new ControllerParamObject(
            new PropertyIterator([
                'html' => new Type\Richtext([
                    'description' => t('Произвольное содержимое')
                ])
            ])
        );
        $controller_param_object->setParentObject($this);
        $controller_param_object->setParentParamMethod('Param');
        return $controller_param_object;
    }

    function actionIndex()
    {
        return $this->result->setHtml($this->getParam('html'));
    }
}
