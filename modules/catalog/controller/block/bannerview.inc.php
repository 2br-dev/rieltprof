<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Controller\Block;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\Dirapi;
use Catalog\Model\Orm\Dir;
use RS\Cache\Manager as CacheManager;
use RS\Controller\Result\Standard as ResultStandard;
use RS\Controller\StandartBlock;
use RS\Module\AbstractModel\TreeList\AbstractTreeListIterator;
use RS\Orm\Type;

/**
 * Блок-контроллер Товары в виде баннера
 */
class BannerView extends StandartBlock
{
    protected static $controller_title = 'Товары в виде баннера';
    protected static $controller_description = 'Товары из заданных категорий отображаются в виде баннера';

    protected $action_var = 'bndo';
    protected $default_params = [
        'indexTemplate' => 'blocks/bannerview/bannerview.tpl',
        'slideTemplate' => 'blocks/bannerview/slide.tpl',
        'cache_html_lifetime' => 300
    ];

    /**
     * Возвращает ORM объект, содержащий настриваемые параметры или false в случае,
     * если контроллер не поддерживает настраиваемые параметры
     * @return \RS\Orm\ControllerParamObject | false
     */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'categories' => new Type\ArrayList([
                'description' => t('Товары каких спецкатегорий показывать?'),
                'tree' => [['\Catalog\Model\DirApi', 'staticSpecTreeList']],
                'attr' => [[
                    AbstractTreeListIterator::ATTRIBUTE_MULTIPLE => true,
                ]]
            ]),
            'cache_html_lifetime' => new Type\Integer([
                'description' => t('Время кэширования HTML блока, секунд?'),
                'hint' => t('0 - кэширование выключено. Значение больше нуля ускоряет работу сайта, но допускает неактуальность данных на срок кэширования. Работает только если в настройках системы включено кэширование данных.'),
            ]),
        ]);
    }

    function actionIndex()
    {
        $cache_id = json_encode($this->getParam());
        $template = $this->getParam('indexTemplate');

        if ($this->isViewCacheExpired($cache_id, $template, $this->getParam('cache_html_lifetime'))) {
            $dir_api = new Dirapi();
            $ids_or_aliases = $this->getParam('categories');
            $ids = [];
            foreach ($ids_or_aliases as $some) {
                if (is_numeric($some)) {
                    $ids[] = $some;
                } else {
                    $dir = Dir::loadByWhere(['alias' => $some]);
                    $ids[] = (int)$dir['id'];
                }
            }

            if (!empty($ids)) {
                $dir_api->setFilter('id', (array)$ids, 'in');
                $dirs = $dir_api->getList();
                $current_dir = reset($dirs)->id;
                $element_html = $this->actionGetSlide($current_dir)->getHtml();
                if ($element_html) {
                    $this->view->assign([
                        'dirs' => $dirs,
                        'current_dir' => $current_dir,
                        'element_html' => $element_html
                    ]);
                }
            }
        }

        return $this->result->setTemplate($template);
    }

    /**
     * Возвращает HTML одного товара
     *
     * @param int $default_dir - категория по умолчанию
     * @return ResultStandard
     */
    function actionGetSlide($default_dir = null)
    {
        $item = $this->url->post('item', TYPE_INTEGER);
        $dir = $this->url->post('dir', TYPE_INTEGER, $default_dir);
        $total = 0;
        if ($dir > 0) {
            $product_api = new ProductApi();
            $product_api
                ->setFilter('dir', $dir)
                ->setFilter('public', 1);
            $product = $product_api->getList($item + 1, 1);

            $total = CacheManager::obj()->request([$product_api, 'getListCount'], $dir);
        }

        if (!empty($product)) {
            $product = reset($product);
            $this->view->assign([
                'product' => $product,
                'item' => $item,
                'dir' => $dir,
                'total' => $total
            ]);
            $this->result->setTemplate($this->getParam('slideTemplate'));
        }
        return $this->result;
    }
}
