<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Menu\Controller\Block;

use Menu\Model\Api as MenuApi;
use RS\Controller\StandartBlock;
use RS\Debug\Action as DebugAction;
use RS\Debug\Tool as DebugTool;
use RS\Event\Manager as EventManager;
use RS\Orm\Type;

/**
 * Блок - горизонтальное меню
 */
class Menu extends StandartBlock
{
    protected static $controller_title = 'Меню';
    protected static $controller_description = 'Отображает публичные пункты меню';

    protected $default_params = [
        'indexTemplate' => 'blocks/menu/hor_menu.tpl',
        'root' => 0,
    ];

    /** @var MenuApi */
    public $api;

    public function init()
    {
        $this->api = new MenuApi();

        /**
         * @deprecated (10.18) вместо данного события следует использовать событие "controller.afterinit.КОРОТКОЕ-ИМЯ-КОНТРОЛЛЕРА"
         */
        EventManager::fire('init.api.' . $this->getUrlName(), $this);
    }

    public function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'root' => new Type\Varchar([
                'description' => t('Какой элемент принимать за корневой?'),
                'tree' => [['Menu\Model\Api', 'staticTreeList'], 0, [0 => t('- Верхний уровень -')]]
            ])
        ]);
    }

    public function actionIndex()
    {
        if ($debug_group = $this->getDebugGroup()) {
            $create_href = $this->router->getAdminUrl('add', [], 'menu-ctrl');
            $debug_group->addDebugAction(new DebugAction\Create($create_href));
            $debug_group->addTool('create', new DebugTool\Create($create_href));
        }

        $root = $this->getParam('root');
        //Кэшируем меню только для неавторизованных пользователей, 
        //т.к. авторизованные могут иметь различные права доступа к пунктам меню
        $menu_vars = $this->api->getMenuItems($root);

        $this->view->assign($menu_vars);

        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
