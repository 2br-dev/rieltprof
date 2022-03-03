<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Marketplace\Controller\Admin\Widget;
use Marketplace\Model\RemoteApi;
use RS\Controller\Admin\Widget;

/**
 * Виджет Полезные модули
 */
class NewModules extends Widget
{
    protected
        $api,
        $action_var = 'mpdo',

        $info_title = 'Полезные модули из Маркетплейса',
        $info_description = 'Отображает случайные модули из Маркетплейса, которые у вас еще не установлены.';

    function init()
    {
        $this->api = new RemoteApi();
    }

    function actionIndex()
    {
        $items = $this->api->getCachedRecommendedModules();

        $this->view->assign([
            'items' => $items
        ]);

        return $this->result->setTemplate('widget/newmodules/newmodules.tpl');
    }

    function actionGetItems()
    {
        $items = $this->api->updateCacheRecommendedModules();

        $this->view->assign([
            'error' => is_string($items) ? $items : null,
            'items' => $items
        ]);

        return $this->result->setSuccess(true)
                            ->setTemplate('widget/newmodules/newmodules_item.tpl');
    }

    function getTools()
    {
        return [
            [
                'title' => t('Обновить'),
                'class' => 'mp-modules-refresh zmdi zmdi-refresh'
            ]
        ];
    }
}