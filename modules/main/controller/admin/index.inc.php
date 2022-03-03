<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;

use Main\Model\Comet\LongPolling;
use Main\Model\Comet\LongPollingLoop;
use RS\Application\Application;
use RS\Application\Auth as AppAuth;
use RS\Cache\Cleaner as CacheCleaner;
use Main\Model\NoticeSystem\Meter;
use RS\Config\Loader;
use RS\Controller\AbstractController;
use RS\Controller\Result\Standard as ResultStandard;
use RS\Controller\Result\Standard;
use RS\Debug\Mode as DebugMode;
use RS\Helper\Tools as HelperTools;
use RS\Language\Core as LanguageCore;
use RS\Site\Manager as SiteManager;
use Users\Model\Api;
use Users\Model\Api as UsersApi;

/**
 * Основной контроллер администраторской панели
 * Предает управление фронт-кнтроллерам модулей
 */
class Index extends AbstractController
{

    public function __construct()
    {
        parent::__construct();
        $this->app->title->addSection(t('Административная панель'));
        $this->app->setJsDefaultFooterPosition(false);
        $this->app->setCssDefaultFooterPosition(false);
        $this->app->addJsVar([
            'authUrl' => $this->router->getAdminUrl(false, ['Act' => 'auth'], false),
        ]);
        $this->app->meta->add(['name' => 'robots', 'content' => 'noindex, nofollow']);
    }

    /**
     * Точка входа в администраторскую панель
     *
     * @return bool|mixed|Standard|string|void|null
     * @throws \RS\Controller\Exception
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     * @throws \SmartyException
     */
    public function actionIndex()
    {
        $config = Loader::byModule($this);

        if ($auth = $this->needAuthorize(null, true)) {
            return $auth; //Требуется авторизация
        }

        if (SiteManager::getAdminCurrentSite() === false) {
            return $this->authPage(t('Вы не имеете прав на администрирование ни одного сайта'));
        }

        $this->app->setBaseJs(\Setup::$JS_PATH);
        $this->app->setBaseCss(\Setup::$CSS_PATH . '/admin');

        $meter = Meter::getInstance();

        if (!$this->url->isAjax()) {
            $this->app->addJsVar([
                'adminSection' => '/' . \Setup::$ADMIN_SECTION,
                'scriptType' => \Setup::$SCRIPT_TYPE,
                'resImgUrl' => \Setup::$IMG_PATH,
                'meterNextRecalculation' => $meter->getNextRecalculateInterval(),
                'meterRecalculationUrl' => $meter->getRecalculationUrl()
            ]);

            $this->app
                ->addJs('jquery.min.js', 'jquery')
                ->addJs('jquery.ui/jquery-ui.min.js', null, BP_COMMON)
                ->addJs('jquery.ui/jquery.ui.touch-punch.min.js', null, BP_COMMON)
                ->addJs('jquery.datetimeaddon/jquery.datetimeaddon.min.js', null, BP_COMMON)
                ->addJs('lab/lab.min.js', null, BP_COMMON)
                ->addJs('jquery.form/jquery.form.js', null, BP_COMMON)
                ->addJs('jquery.cookie/jquery.cookie.js', null, BP_COMMON)
                ->addJs('jquery.rs.admindebug.js')
                ->addJs('jquery.rs.admin.js')
                ->addJs('jquery.rs.ormobject.js', null, BP_COMMON)
                ->addJs('rs.barcodescanner.js')
                ->meta
                    ->add(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=Edge', 'unshift' => true]);

            if (LongPolling::getInstance()->isEnable() && $config->long_polling_can_enable) {
                $this->app->addJsVar([
                    'enableLongPolling' => true,
                    'longPollingLastId' => LongPollingLoop::getLastId(),
                    'longPollingUrl' => $this->router->getAdminUrl(false, [], 'main-longpollinggate')
                ])
                    ->addJs('%main%/jquery.longpolling.js');
            }
        }

        $controller_name = $this->url->request('mod_controller', TYPE_STRING);

        if (preg_match('/^(.*?)-(.*)/', $controller_name, $match)) {
            //Строим полное имя фронт контроллера 
            $full_controller_name = '\\' . str_replace('-', '\\', $match[1] . '-controller-admin-' . $match[2]);

            if (class_exists($full_controller_name) && is_subclass_of($full_controller_name, '\RS\Controller\AbstractModule')) {
                /** @var \RS\Controller\AbstractModule $front_controller */
                $front_controller = new $full_controller_name();
                return $front_controller->exec();
            }
        }

        return $this->e404();
    }

    /**
     * Авторизация пользователя
     *
     * @return Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Orm\Exception
     * @throws \SmartyException
     * @throws \Users\Model\Exception\UsersLog
     */
    function actionAuth()
    {
        $error = "";
        $referer = HelperTools::cleanOpenRedirect($this->url->request('referer', TYPE_STRING, $this->router->getUrl('main.admin')));
        $data = [
            'login' => $this->url->request('login', TYPE_STRING, ''),
            'pass' => $this->url->request('pass', TYPE_STRING, ''),
            'remember' => $this->url->request('remember', TYPE_INTEGER),
            'do' => $this->url->request('do', TYPE_STRING)
        ];

        if ($this->url->isPost()) {
            if ($data['do'] == 'recover') {
                //Восстановление пароля
                $user_api = new UsersApi();
                $this->result->setSuccess($user_api->sendRecoverEmail($data['login'], true));
                if ($this->result->isSuccess()) {
                    $data['successText'] = t('Письмо успешно отправлено. Следуйте инструкциям в письме');
                    $this->result->addSection('successText', $data['successText']);
                } else {
                    $error = $user_api->getErrorsStr();
                    $this->result->addSection('error', $error);
                }

            } else {
                //Авторизация
                AppAuth::logout();
                $auth_user = AppAuth::login($data['login'], $data['pass'], false, false, true);

                if ($auth_user) {
                    //Дополнительно проверим, имеет ли пользователь статус администратора.
                    //Запрещаем через форму авторизации администраторов, авторизацию обычных пользователей
                    if ($auth_user->isAdmin()) {
                        AppAuth::setCurrentUser($auth_user);
                        if (AppAuth::onSuccessLogin($auth_user, $data['remember'])) {
                            return $this->result
                                ->setSuccess(true)
                                ->setNoAjaxRedirect($referer);
                        }
                    } else {
                        $error = t('Пользователь не является администратором');
                    }
                }

                $this->result
                    ->setSuccess(false)
                    ->addSection('error', $error ? $error : AppAuth::getError());
            }

            if ($this->url->isAjax()) {
                return $this->result;
            }
        }

        return $this->authPage($error, $referer, false, $data);
    }

    /**
     * Возвращает диалог со сменой пароля пользователя
     *
     * @return string|void
     * @throws \RS\Controller\ExceptionPageNotFound
     * @throws \RS\Event\Exception
     * @throws \SmartyException
     */
    function actionChangePassword()
    {
        $hash = $this->url->get('uniq', TYPE_STRING);
        $user_api = new UsersApi();
        $error = '';
        $user = $user_api->getByHash($hash);
        if (!$user) {
            return $this->e404();
        }

        if ($this->url->isPost() && $this->url->checkCsrf('change_password')) {
            $new_pass = $this->url->post('new_pass', TYPE_STRING, '', false);
            $new_pass_confirm = $this->url->post('new_pass_confirm', TYPE_STRING, '', false);

            if ($user_api->changeUserPassword($user, $new_pass, $new_pass_confirm)) {
                //Авторизовываем пользователя
                AppAuth::setCurrentUser($user);
                Application::getInstance()->redirect($this->router->getAdminUrl(false, null, false));
            } else {
                $error = $user_api->getErrorsStr();
            }
        }

        $this->view->assign([
            'current_lang' => LanguageCore::getCurrentLang(),
            'locale_list' => LanguageCore::getSystemLanguages(),
            'uniq' => $hash,
            'user' => $user,
            'err' => $error
        ]);
        return $this->wrapHtml($this->view->fetch('%system%/admin/change_pass.tpl'));
    }


    /**
     * Отображает страницу авторизации
     *
     * @param string $error - сообщение об ошибке для отображения
     * @param null|string $referer - куда перенаправлять после авторизации
     * @param bool $js - подключать JS библиотеку для авторизации?
     * @param array $data - данные авторизации
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \SmartyException
     */
    function authPage($error = "", $referer = null, $js = true, $data = [])
    {
        $user_api = new Api();
        $result_helper = new ResultStandard($this);
        $result_helper->setNeedAuthorize(true);

        if (!$this->url->isAjax()) {
            if ($referer === null) {
                $referer = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            }

            if (file_exists(\Setup::$ROOT . \Setup::$BRAND_SPLASH_IMAGE)) {
                $this->view->assign([
                    'alternative_background_url' => \Setup::$BRAND_SPLASH_IMAGE
                ]);
            }

            $this->view->assign([
                'data' => $data,
                'current_lang' => LanguageCore::getCurrentLang(),
                'locale_list' => LanguageCore::getSystemLanguages(),
                'js' => $js,
                'err' => $error,
                'referer' => $referer,
                'auth_url' => $this->router->getAdminUrl(false, ['Act' => 'auth'], false),
                'login_placeholder' => $user_api->getAuthLoginPlaceholder(),
                'recover_login_placeholder' => $user_api->getRecoverLoginPlaceholder()
            ]);

            $body = $this->view->fetch('%system%/admin/auth.tpl');
            $result_helper->setHtml($this->wrapHtml($body));
        }

        return $result_helper;
    }

    /**
     * Изменяет язык администраторчкой панели
     */
    function actionChangeLang()
    {
        $referer = $this->url->request('referer', TYPE_STRING, '/');
        $lang = $this->url->request('lang', TYPE_STRING);

        LanguageCore::setSystemLang($lang);
        Application::getInstance()->redirect($referer);
    }

    /**
     * Измняет текущий сайт в администраторской панели
     */
    function actionChangeSite()
    {
        $site = $this->url->get('site', TYPE_INTEGER, false);
        $referer = urldecode($this->url->get('referer', TYPE_BOOLEAN));

        SiteManager::setAdminCurrentSite($site);
        if ($referer) {
            Application::getInstance()->redirect($referer);
        } else {
            Application::getInstance()->redirect($this->router->getAdminUrl(false, null, false));
        }
    }

    /**
     * Сбрасывает авторизацию
     *
     * @throws \RS\Event\Exception
     */
    function actionLogout()
    {
        AppAuth::logout();
        Application::getInstance()->redirect($this->router->getUrl('main.admin'));
    }

    /**
     * Включение режима отладки
     */
    function actionInDebug()
    {
        DebugMode::enable();
        Application::getInstance()->redirect(SiteManager::getSite()->getRootUrl(true));
    }

    /**
     * Перелючает режим отладки вкл./выкл
     *
     * @return \RS\Controller\Result\Standard
     */

    function actionOutDebug()
    {
        DebugMode::disable();
        Application::getInstance()->redirect(SiteManager::getSite()->getRootUrl(true));
    }

    /**
     * Включение режима отладки
     *
     * @return Standard
     */
    function actionAjaxToggleDebug()
    {
        DebugMode::enable(!DebugMode::isEnabled());
        return $this->result->setSuccess(true);
    }


    /**
     * Перелючает в режиме отладки вид отладки (Блоки, Строки, Секции, Контейнеры)
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionAjaxToggleDebugMode()
    {
        if (!\RS\Debug\Mode::isEnabled()){
            return $this->result->setSuccess(false)->addEMessage(t('Переключитесь в режим отладки'));
        }

        $mode = $this->url->request('mode', TYPE_STRING, 'blocks');
        $this->app->setDebugMode($mode);
        return $this->result->setSuccess(true);
    }




    /**
     * Отображает страницу авторизации и прерывает выполнение скрипта, если у пользователя не хватает прав
     *
     * @param null|string $need_group - alias требуемой у пользователя группы
     * @param bool $need_admin - требуется наличие группы с пометкой "Администратор" ?
     *
     * @return bool|\RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \SmartyException
     */
    function needAuthorize($need_group = null, $need_admin = false)
    {
        $result = AppAuth::checkUserRight($need_group, $need_admin);
        if ($result !== true) {
            return $this->authPage($result);
        }
        return false;
    }

    /**
     * Очищает кэш системы
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionCleanCache()
    {
        CacheCleaner::obj()->clean();
        return $this->result->setSuccess(true);
    }

    /**
     * Производит пересчет счетчиков.
     * Возвращает новые пересчитанные числа в браузер
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionRecalculateMeters()
    {
        $meter = Meter::getInstance();
        $meter->recalculateNumbers();

        return $this->result->setSuccess(true)->addSection([
            'numbers' => $meter->getNumbers(),
            'nextRecalculation' => $meter->getNextRecalculateInterval()
        ]);
    }
}
