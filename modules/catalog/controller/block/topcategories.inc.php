<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Block;

use Catalog\Model\DirApi;
use RS\Controller\StandartBlock;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\Type;

/**
 * Контроллер - выводит отобранные категории
 */
class TopCategories extends StandartBlock
{
    protected static $controller_title = 'Выборка категорий';
    protected static $controller_description = 'Отображает изображения и названия некоторых выбранных категорий';

    protected $default_params = [
        'indexTemplate' => 'blocks/topcategories/top_categories.tpl', //Должен быть задан у наследника
        'category_ids' => [],
        'sort' => '',
        'cache_html_lifetime' => 300
    ];

    /** @var Dirapi */
    public $dir_api;

    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'category_ids' => new Type\ArrayList([
                'description' => t('Отображать категории'),
                'tree' => [['\Catalog\Model\DirApi', 'staticTreeList']],
                'attr' => [[
                    AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                ]],
            ]),
            'sort' => new Type\Varchar([
                'description' => t('Сортировка'),
                'listFromArray' => [[
                    '' => t('Без сортировки'),
                    'id' => t('Идентификатор'),
                    'name' => t('Наименование'),
                    'sortn' => t('Порядок в административной панели')
                ]]
            ]),
            'cache_html_lifetime' => new Type\Integer([
                'description' => t('Время кэширования HTML блока, секунд?'),
                'hint' => t('0 - кэширование выключено. Значение больше нуля ускоряет работу сайта, но допускает неактуальность данных на срок кэширования. Работает только если в настройках системы включено кэширование данных.'),
            ]),
        ]);
    }

    function init()
    {
        $this->dir_api = new Dirapi();
    }

    function actionIndex()
    {
        $cache_id = json_encode($this->getParam());
        $template = $this->getParam('indexTemplate');

        if ($this->isViewCacheExpired($cache_id, $template, $this->getParam('cache_html_lifetime'))) {
            $sort_by_title = $this->getParam('sort');
            if ($dir_ids = $this->getParam('category_ids')) {
                $this->dir_api->setFilter('id', $dir_ids, 'in');
                if (!empty($sort_by_title)) {
                    $this->dir_api->setOrder($this->getParam('sort'));
                }
            }

            $this->view->assign([
                'categories' => $this->dir_api->getList()
            ]);
        }

        return $this->result->setTemplate($template);
    }
}
