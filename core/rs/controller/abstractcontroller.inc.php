<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace RS\Controller;

use RS\Application\Application;
use RS\Controller\Result\Standard as ResultStandard;
use RS\Event\Exception as EventException;
use RS\Event\Manager as EventManager;
use RS\Http\Request as HttpRequest;
use RS\Router\Manager as RouterManager;
use RS\View\Engine as ViewEngine;

/**
 * Абстрактный класс контроллера.
 */
class AbstractController implements IController
{
    const DEFAULT_ERROR_PAGE_TPL = '%THEME%/exception.tpl'; //Путь к шаблону страницы ошибок

    /** @var ViewEngine $view - Smarty Шаблонизатор */
    public $view;
    /** @var HttpRequest $url */
    public $url;
    /** @var Application $app */
    public $app;
    /** @var RouterManager $router */
    public $router;

    protected $act = null;
    protected $action_var = 'Act';
    /** @var Result\Standard $result */
    protected $result;

    private $preset_act = null;

    /**
     * Базовый конструктор контроллера. желательно вызывать в конструкторах порожденных классов.
     */
    function __construct()
    {
        $this->view = new ViewEngine();
        $this->app = Application::getInstance(); //Получили экземпляр класса страница
        $this->url = HttpRequest::commonInstance();
        $this->router = RouterManager::obj();
        $this->result = new ResultStandard($this);
    }

    /**
     * Включает кэширование шаблонов и проверяет истёк ли кэш
     *
     * @param string $cache_id_str - идентификатор кэша, для последующих вызовов is_cached, fetch, display
     * @param string $template - шаблон
     * @param int|null $lifetime - время кэширования
     * @return bool
     */
    public function isViewCacheExpired(string $cache_id_str, string $template, int $lifetime = null): bool
    {
        // todo - описать собылтие "controller.viewcache.ИМЯ_КОНТРОЛЛЕРА.ИМЯ_ДЕЙСТВИЯ" в документации
        $event_result = EventManager::fire("controller.viewcache.{$this->getUrlName()}.{$this->getAction()}", [
            'template' => $template,
        ]);

        if ($lifetime !== null) {
            if ($lifetime == 0) return true; //Если время кэширования = 0, то дальше не проверяем актуальность кэша
            $this->view->cache_lifetime = $lifetime;
        }

        if (!$event_result->getEvent()->isStopped()) {
            return !$this->view->cacheOn($cache_id_str)->isCached($template);
        }
        return true;
    }

    /**
     * Оборачивает HTML секциями body, html добавляет секцию head с мета тегами, заголовком
     *
     * @param string $body - HTML, внутри тега body, который нужно обернуть
     * @param string $html_template - имя оборачивающего шаблона
     * @return string
     * @throws EventException
     * @throws \SmartyException
     */
    function wrapHtml($body, $html_template = null)
    {
        if (!$html_template) {
            $html_template = '%system%/html.tpl';
        }

        $result = EventManager::fire('controller.beforewrap', [
            'controller' => $this,
            'body' => $body,
        ]);

        $result_array = $result->getResult();
        $body = $result_array['body'];

        $html = new ViewEngine();
        $html->assign([
            'app' => $this->app,
            'body' => $body
        ]);

        return $html->fetch($html_template);
    }

    /**
     * Взвращает Action который будет вызван
     *
     * @return string
     */
    function getAction()
    {
        if ($this->preset_act !== null) {
            $act = $this->preset_act;
        } else {
            $act = $this->action_var ? $this->url->request($this->action_var, TYPE_STRING, "index") : "index";
        }
        return $act;
    }

    /**
     * Выполняет action(действие) текущего контроллера, возвращает результат действия
     *
     * @param boolean $returnAsIs - возвращать как есть. Если true, то метод будет возвращать точно то,
     * что вернет действие, иначе результат будет обработан методом processResult
     *
     * @return mixed|null
     * @throws Exception
     * @throws ExceptionPageNotFound
     * @throws EventException
     */
    function exec($returnAsIs = false)
    {
        $this->act = $this->getAction();

        if (!is_callable([$this, 'action' . $this->act])) {
            $this->e404(t('Указанного действия не существует'));
        }

        $event_result = EventManager::fire('controller.beforeexec.' . $this->getUrlName(), [
            'controller' => $this,
            'action' => $this->act,
        ])->getResult();

        if (isset($event_result['output'])) {
            $result = $event_result['output'];
        } else {
            $result = $this->{'action' . $this->act}();
        }

        $result = EventManager::fire('controller.afterexec.' . $this->getUrlName(), $result)->getResult();

        if ($returnAsIs) return $result;
        return $this->processResult($result);
    }

    /**
     * Обрабатывает результат выполнения действия, возвращает HTML
     * Отправляет подготовленные заголовки в браузер
     *
     * @param string|Result\IResult $result
     * @return string
     * @throws Exception
     */
    function processResult($result)
    {
        if (is_object($result)) {
            if ($result instanceof Result\IResult) {
                $result_html = $result->getOutput();
            } else {
                throw new Exception(t('Контроллер должен возвращать объект, имплементирующий интерфейс \RS\Controller\Result\IResult'));
            }
        } else {
            $result_html = $result;
        }
        $this->app->headers->sendHeaders();
        return $result_html;
    }


    /**
     * Позволяет переопределить вызываемый метод в контроллере
     * @param string $act - метод контроллера
     * @return void
     */
    function presetAct($act)
    {
        $this->preset_act = $act;
    }

    /**
     * Отдает в output страницу с ошибкой 404. Прекращает выполнение скрипта
     *
     * @param mixed $reason
     * @throws ExceptionPageNotFound
     */
    function e404($reason = null)
    {
        if ($reason == null) {
            $reason = t('Проверьте правильность набранного адреса');
        }
        throw new ExceptionPageNotFound($reason, get_class($this));
    }

    /**
     * Перенаправляет браузер пользователя на другую страницу. Прекращает выполнение скрипта
     *
     * @param string $url Если url не задан, то перенаправляет на корневую страницу сайта
     * @param int $status - номер статуса редиректа с которым выполнять редирект
     * @deprecated (18.12) - метод перенесён в \RS\Application\Application
     */
    function redirect($url = null, $status = 302)
    {
        Application::getInstance()->redirect($url, $status);
    }

    /**
     * Перезагружает у пользователя текущую страницу
     *
     * @return void
     */
    function refreshPage()
    {
        $url = $this->url->server('REQUEST_URI', TYPE_STRING);
        $this->app->redirect($url, 302);
    }

    /**
     * Возврщает имя текущего контроллера для использования в URL
     *
     * @return string
     */
    function getUrlName()
    {
        $class = get_class($this);
        $class = strtolower(trim(str_replace('\\', '-', $class), '-'));
        return str_replace('-controller', '', $class);
    }
}
