<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Article\Controller\Block;

use Article\Model\CatApi;
use RS\Controller\StandartBlock;
use RS\Orm\Type;

/**
 * Блок-контроллер Список категорий статей
 */
class Category extends StandartBlock
{
    protected static $controller_title = 'Список категорий статей';
    protected static $controller_description = 'Отображает список категорий статей';

    protected $path;
    protected $pathids = [];
    protected $default_params = [
        'indexTemplate' => 'blocks/category/category.tpl',
        'root' => 0,
    ];

    /** @var CatApi */
    public $api;

    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'root' => new Type\Varchar([
                'description' => t('Корневая директория'),
                'tree' => [['\Article\Model\CatApi', 'staticTreeList'], 0, ['' => t('- Верхний уровень -')]]
            ])
        ]);
    }

    function init()
    {
        $this->api = new CatApi();
        $this->api->setFilter('public', 1);
    }

    function actionIndex()
    {
        $root_id = (int)$this->getParam('root', 0);

        //Определяем текущую категорию
        $route = $this->router->getCurrentRoute();
        $category = null;
        if ($route && ($route->getId() == 'article-front-previewlist' || $route->getId() == 'article-front-view')) {
            //Получаем информацю о текущей категории из URL
            $category = $this->url->request('category', TYPE_STRING);
        }

        //Кэшируем список категорий.
        $cache_id = $root_id . $category . $this->api->queryObj()->toSql();
        $template = $this->getParam('indexTemplate');
        if ($this->isViewCacheExpired($cache_id, $template)) {
            if (!is_numeric($root_id) && $dir = $this->api->getById($root_id)) {
                $root_id = $dir['id'];
            }

            $item = false;
            if ($category) {
                //Определяем активные элементы категорий вплоть до корневого элемента
                $full_api = Catapi::getInstance();
                $item = $full_api->getById($category);
                if ($item) {
                    $this->path = $full_api->getPathToFirst($item['id']);
                    foreach ($this->path as $one) {
                        $this->pathids[] = $one['id'];
                    }
                }
            }

            if ($debug_group = $this->getDebugGroup()) {
                $create_href = $this->router->getAdminUrl('add_dir', [], 'article-ctrl');
                $debug_group->addDebugAction(new \RS\Debug\Action\Create($create_href));
                $debug_group->addTool('create', new \RS\Debug\Tool\Create($create_href));
            }

            $this->view->assign([
                'dirlist' => $this->api->getTreeList($root_id),
                'current_item' => $item,
                'pathids' => $this->pathids,
            ]);
        }
        return $this->result->setTemplate($template);
    }
}
