<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Article\Controller\Block;

use Article\Model\Api;
use Article\Model\CatApi;
use RS\Controller\StandartBlock;
use RS\Orm\Type;

/**
 * Блок-контроллер Свежие новости
 */
class LastNews extends StandartBlock
{
    protected static $controller_title = 'Список свежих новостей';
    protected static $controller_description = 'Отображает N последних новостей и ссылку на все новости';

    protected $default_params = [
        'indexTemplate' => 'blocks/lastnews/lastnews.tpl',
        'pageSize' => 5,
        'order' => 'id desc',
        'cache_html_lifetime' => 300,
    ];

    /** @var Api */
    public $article_api;
    /** @var CatApi */
    public $category_api;

    function init()
    {
        $this->article_api = new Api();
        $this->category_api = new CatApi();
    }

    /**
     * Возвращает ORM объект, содержащий настриваемые параметры или false в случае,
     * если контроллер не поддерживает настраиваемые параметры
     * @return \RS\Orm\ControllerParamObject | false
     */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'category' => new Type\Varchar([
                'description' => t('Новости из какой рубрики отображать?'),
                'tree' => [['\Article\Model\CatApi', 'staticTreeList']],
            ]),
            'pageSize' => new Type\Integer([
                'description' => t('Количество новостей на страницу')
            ]),
            'order' => new Type\Varchar([
                'description' => t('Сортировка'),
                'listFromArray' => [[
                    'id DESC' => t('По ID в обратном порядке'),
                    'dateof DESC' => t('По дате в обратном порядке'),
                    'rand()' => t('В произвольном порядке')
                ]]
            ]),
            'show_subdirs_news' => new Type\Integer([
                'description' => t('Показывать новости из подкатегорий'),
                'checkboxView' => [1, 0],
            ]),
            'exclude_self_news' => new Type\Integer([
                'description' => t('Исключать новость, на странице которой находимся'),
                'checkboxView' => [1, 0],
            ]),
            'cache_html_lifetime' => new Type\Integer([
                'description' => t('Время кэширования HTML блока, секунд?'),
                'hint' => t('0 - кэширование выключено. Значение больше нуля ускоряет работу сайта, но допускает неактуальность данных на срок кэширования. Работает только если в настройках системы включено кэширование данных.'),
            ])
        ]);
    }

    function actionIndex()
    {
        $page = $this->myGet('p', TYPE_INTEGER, 1);

        $template = $this->getParam('indexTemplate');
        $cache_id = json_encode($this->getParam()) . $page;

        if ($this->isViewCacheExpired($cache_id, $template, $this->getParam('cache_html_lifetime'))) {

            $category = $this->getParam('category');
            $this->article_api->setOrder($this->getParam('order'));
            $this->category_api = new Catapi();
            $dir = $this->category_api->getById($category);
            if ($dir) {

                if ($this->getParam('exclude_self_news')
                    && $this->router->getCurrentRoute()->getId() == 'article-front-view') {
                    $self_id = $this->router->getCurrentRoute()->getExtra('article_id');
                    $this->article_api->setFilter('id', $self_id, '!=');
                }


                $this->article_api->setFilter([
                    'public' => 1,
                    [
                        'dont_show_before_date' => 0,
                        '|dateof:<' => date('Y-m-d H:i:s'),
                    ],
                ]);
                if ($this->getParam('show_subdirs_news')) {
                    $cat_ids = $this->category_api->getChildsId($dir['id']);
                    $this->article_api->setFilter('parent', $cat_ids, 'in');
                } else {
                    $this->article_api->setFilter('parent', $dir['id']);
                }
                $news = $this->article_api->getList($page, $this->getParam('pageSize'));

                if ($debug_group = $this->getDebugGroup()) {
                    $create_href = $this->router->getAdminUrl('add', ['dir' => $dir['id']], 'article-ctrl');
                    $debug_group->addDebugAction(new \RS\Debug\Action\Create($create_href));
                    $debug_group->addTool('create', new \RS\Debug\Tool\Create($create_href));
                }

                $this->view->assign([
                    'news' => $news,
                    'category_id' => $dir['id'],
                    'category' => $dir
                ]);
            }
        }

        return $this->result->setTemplate($template);
    }
}
