<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller;

use Partnership\Model\Api as PartnershipApi;
use RS\Config\Loader;
use RS\Config\Loader as ConfigLoader;
use RS\Module\Manager as ModuleManager;
use RS\Performance\Timing;
use RS\Site\Manager as SiteManager;
use RS\View\Engine as ViewEngine;
use RS\Event\Manager as EventManager;
use RS\Application\Application as Application;
use RS\Cache\Manager as CacheManager;
use PageSeo\Model\Orm\PageSeo as PageSeo;

/**
* Базовый класс всех фронтальных контроллеров
*/
abstract class Front extends AbstractClient
{
    const CONTROLLER_ID_PARAM = '_controller_id';

    protected $wrap_template = DEFAULT_LAYOUT;
    protected $wrap_output   = true;

    /**
     * Устанавливает, оборачивать ли вывод шаблоном текущей страницы.
     *
     * @param boolean $bool - true или false
     * @return $this
     */
    function wrapOutput($bool)
    {
        $this->wrap_output = $bool;
        return $this;
    }

    /**
     * Возвращает отрендеренный шаблон фронт контроллера
     *
     * @param bool $returnAsIs - возвращать как есть? Если true, то метод будет возвращать точно то, что вернет действие, иначе результат будет обработан методом processResult
     *
     * @return mixed|string|void|null
     * @throws Exception
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    function exec($returnAsIs = false)
    {
        $timing = Timing::getInstance();
        $timing->startMeasure( Timing::TYPE_CONTROLLER_FRONT, '-', get_class($this));

        $config = $this->getModuleConfig();
        if (!$config->isActive()) {
            if ($config['deactivated']) {
                $this->e404(t('Модуль "%0" деактивирован', [$config['name']]));
            }
            $this->e404(t('Модуль "%0" отключён', [$config['name']]));
        }

        //Устанавливаем заголовки для текущей страницы
        if (\Setup::$INSTALLED) {
            $pageseo = CacheManager::obj()
                            ->watchTables(new PageSeo())
                            ->request(['\PageSeo\Model\PageSeoApi', 'getPageSeo'], $this->router->getCurrentRoute()->getId(), SiteManager::getSiteId());
            if ($pageseo) {
                Application::getInstance()->title->addSection($pageseo['meta_title']);
                Application::getInstance()->meta->addKeywords($pageseo['meta_keywords']);
                Application::getInstance()->meta->addDescriptions($pageseo['meta_description']);
            }
            $this->addFavicon();
            $this->addFaviconSvg();
        }

        if (!$this->wrap_output) {
            //Запрещаем выводить информацию в режиме отладки, если контроллер запретил обертки
            $this->debug_group = null;
        }

        $result_html = parent::exec($returnAsIs);
        $this->view->caching = false;

        //Если имя метода начинается со слова "ajax", то отменяем оборачивание
        //Если в REQUEST есть ajax=1, то отменяем оборачивание
        if ($this->wrap_output && substr($this->act, 0, 4) != 'ajax' && $this->url->request('ajax', TYPE_INTEGER) != 1) {
            $this->app->blocks
                ->setRouteId($this->router->getCurrentRoute()->getId())
                ->setMainContent($result_html)
                ->setView($this->view);
            $result = $this->view->fetch($this->wrap_template);
            if ($this->user->isAdmin() && $this->user->checkSiteRights(SiteManager::getSiteId())) {
                //Изменяем текущий сайт в админ панели.
                SiteManager::setAdminCurrentSite(SiteManager::getSiteId());

                $this->app->addJsVar('customer_zone', true);

                if (ConfigLoader::getSystemConfig()->show_debug_header) {
                    $debug_top_wrapper = new ViewEngine();
                    $debug_top_wrapper->assign([
                        'timing' => $timing,
                        'result_html' => $result
                        ] + $this->view->getTemplateVars());
                    $result = $debug_top_wrapper->fetch('%system%/debug/top.tpl');
                    $this->app->addJsVar(['adminSection' => '/'.\Setup::$ADMIN_SECTION, 'scriptType' => \Setup::$SCRIPT_TYPE]);
                }
            }
            $result = EventManager::fire('controller.front.beforewrap', $result)->getResult();
            $result_html = $this->wrapHtml($result);
        }

        $timing->endMeasure();

        return $result_html;
    }

    /**
     * Добавляет ссылку на favicon, загруженный в админ. панели в секцию HEAD
     *
     * @return void
     * @throws \RS\Exception
     */
    private function addFavicon()
    {
        $site_config = ConfigLoader::getSiteConfig();
        if ($site_config['favicon']) {
            $favicon = $site_config['__favicon']->getLink();
            if (ModuleManager::staticModuleEnabled('partnership') && $partner = PartnershipApi::getCurrentPartner()) {
                if ($partner['favicon']) {
                    $favicon = $partner['__favicon']->getLink();
                }
            }

            $params = [
                'type' => false,
                'rel' => 'shortcut icon'
            ];

            if (substr(strtolower($favicon), -4) == '.png') {
                $params = [
                    'type' => 'image/png',
                    'rel' => 'icon'
                ];
            }
            if (substr(strtolower($favicon), -4) == '.ico') {
                $params = [
                    'type' => 'image/x-icon',
                    'rel' => 'shortcut icon'
                ];
            }
            $params['header'] = true;

            $this->app->addCss($favicon, null, 'root', true, $params);
        }
    }

    /**
     * Добавляет ссылку на favicon, загруженный в админ. панели в секцию HEAD
     *
     * @return void
     * @throws \RS\Exception
     */
    private function addFaviconSvg()
    {
        $site_config = ConfigLoader::getSiteConfig();
        if ($site_config['favicon_svg']) {
            $favicon = $site_config['__favicon_svg']->getLink();
            if (ModuleManager::staticModuleEnabled('partnership') && $partner = PartnershipApi::getCurrentPartner()) {
                if ($partner['favicon_svg']) {
                    $favicon = $partner['__favicon_svg']->getLink();
                }
            }

            $params = [
                'type' => 'image/svg+xml',
                'rel' => 'icon'
            ];

            $params['header'] = true;

            $this->app->addCss($favicon, null, 'root', true, $params);
        }
    }

    /**
    * Возвращает input[type="hidden"] с id текущего контроллера, чтобы отметить, что данный пост идет по его инициативе.
    *
    * @return string
    */
    public function myBlockIdInput()
    {
        return '<input type="hidden" name="'.self::CONTROLLER_ID_PARAM.'" value="'.$this->getMyId().'">';
    }

    /**
    * Возвращает true, если инициатором POST запроса выступил данный контроллер
    *
    * @return bool
    */
    public function isMyPost()
    {
        return $this->url->isPost() && $this->url->post(self::CONTROLLER_ID_PARAM, TYPE_STRING) == $this->getMyId();
    }

    /**
    * Возвращает id текущего контроллера
    *
    * @return integer
    */
    public function getMyId()
    {
        return sprintf('%u', crc32($this->getControllerName()));
    }


    /**
     * Выполняет redirect На страницу авторизации
     *
     * @param string $error - ошибка
     * @param null|string $referer - страница с которой пришли
     * @return Result\Standard
     */
    function authPage($error = "", $referer = null)
    {
        if ($referer === null) {
            $referer = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }
        $_SESSION['auth_access_error'] = $error;

        $authorization_url = Loader::byModule('users')->getAuthorizationUrl(['referer' => $referer]);
        return $this->result
                    ->setNeedAuthorize(true)
                    ->setNoAjaxRedirect($authorization_url);
    }

    /**
     * Проверяет нужен ли редирект на адрес, где адрес формируется не id объекта, а указанного значения в alias
     *
     * @param integer|string $id - идентификатор
     * @param \RS\Orm\OrmObject $item - объект для просмотра
     * @param string $redirect_url - адрес для редиректа
     * @param string $alias_field - наименование поля с alias
     * @param string $id_field - идентификатор поля
     */
    function checkRedirectToAliasUrl($id, $item, $redirect_url, $alias_field = 'alias', $id_field = 'id')
    {
        if ((int)$id == $item[$id_field] && $item[$id_field] != $item[$alias_field] && !empty($item[$alias_field])) {
            Application::getInstance()->redirect($redirect_url, 301);
        }
    }
}
