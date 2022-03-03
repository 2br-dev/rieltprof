<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Admin;
use RS\Config\Install;
use RS\Html\Toolbar;
use RS\Html\Toolbar\Button as ToolbarButton;

/**
* Контроллер системных настроек
*/
class Options extends \RS\Controller\Admin\ConfigEdit
{
    protected
        $orm;
        
    function __construct()
    {
        $this->cms_config = \RS\Config\Loader::getSystemConfig();
        parent::__construct($this->cms_config);
        $this->cms_config->setFormTemplate('system_options');        
    }
    
    /**
    * Системные настройки
    */
    function actionIndex()
    {
        $this->view->assign('partName', t('Настройки'));
        $this->app->addJs( $this->mod_js.'cacheclean.js',null, BP_ROOT);
        $this->app->addJsVar('cleanCacheUrl', $this->router->getAdminUrl('cleanCache'));

        $old_values = $this->orm->getValues();
        $result     = parent::actionIndex();
        $new_values = $this->orm->getValues();
        $old_sec    = $old_values['ADMIN_SECTION'];
        $new_sec    = $new_values['ADMIN_SECTION'];

        // Если изменился адрес админ-панели
        if ($old_sec !== $new_sec)
        {
            // Очистка кэша
            \RS\Cache\Cleaner::obj()->clean(\RS\Cache\Cleaner::CACHE_TYPE_FULL);

            // Переадресация на новый URL
            $new_url = str_replace("/{$old_sec}/", "/{$new_sec}/", $this->url->getSelfUrl());
            $result->setAjaxWindowRedirect($new_url);
        }
        return $result;
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex();
        $helper->setTopTitle(t('Настройка системы'));
        $helper->setTemplate('%main%/crud-options.tpl');
        return $helper;
    }

    
    /**
    * AJAX
    */
    function actionCleanCache()
    {
        $type = $this->url->request('type', TYPE_STRING);
        $this->result->setSuccess( \RS\Cache\Cleaner::obj()->clean($type) );
        return $this->result->getOutput();
    }
    
    /**
    * Исправляет структуру БД
    */
    function actionSyncDb()
    {
        $module_manager = new \RS\Module\Manager();
        $count = $module_manager->syncDb();

        return $this->result
            ->addSection('noUpdate', true)
            ->addMessage(t('Обновлено %0 таблиц', [$count]));
    }
    
    /**
    * Отправляет тестовое письмо администратору сайта
    */
    function actionTestMail()
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        if (!$site_config['admin_email']) {
            $this->result->addEMessage(t('Не задан Email администратора в разделе Веб-сайт->Настройка сайта'));
        } else {
            $mailer = new \RS\Helper\Mailer(false);
            $mailer->Subject = t('Проверка отправки писем с сайта');
            $mailer->addEmails($site_config['admin_email']);
            $mailer->isHTML(false);
            $mailer->Body = t('Если вы видите данный текст, значит письмо с сайта %0 успешно доставлено.', [\Setup::$DOMAIN]);
            
            if ($mailer->send()) {
                $this->result->addMessage(t('Письмо успешно отправлено на Email администратора'));
            } else {
                $this->result->addEMessage(t('Ошибка отправки письма: %0', [$mailer->ErrorInfo]));
            }
        }
    
        return $this->result->addSection('noUpdate', true);
    }


    /**
     * Удаляет файл блокировки планировщика заданий
     */
    function actionUnlockCron()
    {
        $file = \Setup::$PATH . \Setup::$STORAGE_DIR . '/locks/cron';
        $this->result->addSection('noUpdate', true);

        if(file_exists($file))
        {
            @unlink($file);
            if(file_exists($file))
            {
                return $this->result->addEMessage(t('Ошибка удаления'));
            }
            else
            {
                return $this->result->addMessage(t('Файл блокировки удален'));
            }
        }
        else
        {
            return $this->result
                ->addMessage(t('Блокировка не обнаружена'));
        }
    }

    /**
     * Отображает все настройки PHP
     */
    function actionPhpInfo()
    {
        //Только супервизорам и не в облаке доступна данная функция
        if ($this->user->isSupervisor() && !defined('CLOUD_UNIQ')) {
            $this->wrapOutput(false);
            phpinfo();
        } else {
            $this->e404(t('Недостаточно прав. Требуются права супервизора.'));
        }
    }
    
    function actionAjaxShowChangelog()
    {
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper->setTopTitle(t('История изменений ядра'));
        $helper->setBottomToolbar(new Toolbar\Element([
            'items' => [
                new ToolbarButton\Cancel('')
            ]
        ]));
        $helper->viewAsForm();        
        
        $this->view->assign([
            'changelog' => $this->cms_config->getChangelog()
        ]);
        
        $helper['form'] = $this->view->fetch('show_changelog.tpl');
        $this->result->setTemplate( $helper['template'] );
        
        return $this->result;
    }

    /**
     * Отображает подписчиков на все события
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionShowEventListeners()
    {
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper->setTopTitle(t('Подписчики на системные события'));
        $helper->setTopHelp(t('С помощью данного раздела разработчики могут понять, какие модули обрабатывают те или иные события'));
        $helper->viewAsAny();
        $listeners = \RS\Event\Manager::getListeners();

        $this->view->assign([
           'listeners' => $listeners
        ]);

        $helper->setForm($this->view->fetch('show_event_listeners.tpl'));

        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Переустанавливает Ядро
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionReinstallCore()
    {
        $installer = new Install();
        $this->result->setSuccess($installer->update());

        if ($this->result->isSuccess()) {
            $this->result->addMessage(t('Ядро успешно переустановлено'));
        } else {
            $this->result->addEMessage(t('Не удалось переустановить ядро'));
        }
        return $this->result;
    }
}

