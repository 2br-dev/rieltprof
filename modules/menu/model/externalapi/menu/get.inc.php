<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Model\ExternalApi\Menu;
use ExternalApi\Model\Utils;
use \RS\Orm\Type;

/**
* Возвращает меню по ID
*/
class Get extends \ExternalApi\Model\AbstractMethods\AbstractGet
{
    const
        RIGHT_LOAD = 1;

    protected
        $view, //Объект движка шаблонизатора
        $site; //Текущий объект сайта
    protected $token_require = false;
    /**
     * @var \Menu\Model\Orm\Menu $object
     */
    protected $object;

    function __construct()
    {
        parent::__construct();
        $this->view = new \RS\View\Engine();
        $this->site = \RS\Site\Manager::getSite();
    }

    /**
     * Возвращает комментарии к кодам прав доступа
     *
     * @return [
     *     КОД => КОММЕНТАРИЙ,
     *     КОД => КОММЕНТАРИЙ,
     *     ...
     * ]
     */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Загрузка объекта')
        ];
    }

    /**
     * Возвращает название секции ответа, в которой должен вернуться список объектов
     *
     * @return string
     */
    public function getObjectSectionName()
    {
        return 'menu';
    }

    /**
     * Возвращает объект с которым работает
     *
     */
    public function getOrmObject()
    {
        return new \Menu\Model\Orm\Menu();
    }

    /**
     * Добавляет секции с HTML к меню
     */
    private function appendArticleHtmlContent()
    {
        $this->getOrmObject()->getPropertyIterator()->append([
            'html' => new Type\Richtext([
                'description' => t('HTML представляющий страницу'),
                'appVisible' => true,
            ]),
            'link' => new Type\Text([
                'description' => t('Ссылка на страницу'),
                'appVisible' => true,
            ]),
        ]);

        if ($this->object['typelink'] != 'link'){
            $template = $this->object->getTypeObject()->getTemplate();

            if ($this->object['typelink'] == 'article'){ //Если статья то допишем путь к файлы модуля меню
                $template = '%menu%/'.$template;
            }
            $this->view->assign(
                ['menu_item' => $this->object] +
                $this->object->getTypeObject()->getTemplateVar()
            );
            //Проверим есть ли относительные ссылки и картинки, чтобы их заменить на абсолютные
            $html = Utils::prepareHTML($this->view->fetch($template));
            $this->object['html'] = $html;
        }else{
            $this->object['link'] = $this->site->getAbsoluteUrl($this->object->getTypeObject()->getHref());
        }
    }


    /**
     * Возвращает категорию по ID
     *
     * @param string $token Авторизационный токен
     * @param integer $menu_id ID пункта меню
     *
     * @example GET api/methods/menu.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&menu_id=1
     * Ответ
     * <pre>
     * {
     *     "response": {
     *         "menu": {
     *           "id": "2",
     *           "title": "Оплата",
     *           "hide_from_url": "0",
     *           "alias": "payment",
     *           "parent": "0",
     *           "public": "0",
     *           "typelink": "article",
     *           "html": "<p>Текст статьи или шаблона</p>",
     *           "link": "http://site.ru/link/",
     *           "mobile_public": "0",
     *           "mobile_image": null,
     *           "affiliate_id": "0"
     *         }
     *     }
     * }
     * </pre>
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    function process($token = null, $menu_id)
    {
        $response = parent::process($token, $menu_id);
        $this->appendArticleHtmlContent();
        $response['response']['menu'] = \ExternalApi\Model\Utils::extractOrm($this->object);

        return $response;
    }
}