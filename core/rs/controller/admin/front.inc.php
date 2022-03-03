<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Controller\Admin;

use Partnership\Model\Api as PartnershipApi;
use RS\Controller\AbstractAdmin;
use RS\Controller\Exception as ControllerException;
use RS\Controller\ExceptionPageNotFound;
use RS\Controller\Result\Standard;
use RS\Event\Exception as EventException;
use RS\Exception as RSException;
use RS\Module\Manager as ModuleManager;
use RS\Site\Manager as SiteManager;

class Front extends AbstractAdmin
{
    /** @var Standard */
    protected $result;
    protected $action_var = 'do';
    protected $wrap_output = true;
    protected $wrap_template = '%SYSTEM%/admin/body.tpl';
    protected $block_tpl = '%SYSTEM%/admin/block.tpl'; //стандартный шаблон для оборачивания вывода в админке
    protected $block_form_tpl = '%SYSTEM%/admin/crud_form.tpl'; //стандартный шаблон для оборачивания форм в админке

    function __construct()
    {
        parent::__construct();
        $this->result = new Standard($this); //Helper, который помогает возвращать стандартизированый вывод
    }

    /**
     * Устанавливает, оборачивать ли вывод шаблоном текущей страницы.
     *
     * @param mixed $bool
     * @return Front
     */
    function wrapOutput($bool)
    {
        $this->wrap_output = $bool;
        return $this;
    }

    /**
     * Возвращает false, если нет ограничений на запуск контроллера, иначе вызывает исключение 404
     * @return bool(false)
     * @throws ExceptionPageNotFound
     */
    function checkAccessRight()
    {
        //Для доступа к контроллеру пользователь должен быть администратором
        if ($error = parent::checkAccessRight()) {
            return $this->e404($error);
        }
        return false;
    }

    /**
     * Выполняет action(действие) текущего контроллера, возвращает результат действия
     *
     * @param boolean $returnAsIs - возвращать как есть. Если true, то метод будет возвращать точно то,
     * что вернет действие, иначе результат будет обработан методом processResult
     *
     * @return mixed
     * @throws ControllerException
     * @throws ExceptionPageNotFound
     * @throws EventException
     * @throws RSException
     * @throws \SmartyException
     */
    function exec($returnAsIs = false)
    {
        $config = $this->getModuleConfig();
        if (!$config->isActive() && $config->getModuleId() != 'modcontrol') { // не блокируем только молдуль "Управление модулями"
            if ($config['deactivated']) {
                $this->e404(t('Модуль "%0" деактивирован', [$config['name']]));
            }
            $this->e404(t('Модуль "%0" отключён', [$config['name']]));
        }

        $result_html = parent::exec($returnAsIs);

        //Если имя метода начинается со слова "ajax", то отменяем оборачивание
        //Если в REQUEST есть ajax=1, то отменяем оборачивание
        if ($this->wrap_output && substr($this->act, 0, 4) != 'ajax' && !$this->url->isAjax()) {

            if (ModuleManager::staticModuleEnabled('partnership')) {
                $partner = PartnershipApi::getCurrentPartner();
            }
            if (isset($partner)) {
                $root_url = $partner->getRootUrl(true);
            } else {
                $site = SiteManager::getSite();
                $root_url = $site->getRootUrl(true);
            }
            $this->view->assign(['site_root_url' => $root_url]);

            $this->app->blocks
                ->setRouteId($this->router->getCurrentRoute()->getId())
                ->setMainContent($result_html)
                ->setView($this->view);
            return $this->wrapHtml($this->view->fetch($this->wrap_template));
        } else {
            return $result_html;
        }
    }
}
