<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Install\Controller;

use RS\Application\Application;

class Wizard extends \RS\Controller\AbstractModule
{    
    protected
        $api;
        
    function __construct()
    {
        parent::__construct();
        $this->api = new \Install\Model\Api();
        $this->router->gzipEnabled(false);
    }
    
    function actionIndex()
    {
        $step = $this->url->get('step', TYPE_STRING, 1);
        if ($step) {
            //Проверка на допустимость шага
            $min_allow_step = $this->api->getKey('min_allow_step', 1);
            $max_allow_step = $this->api->getKey('max_allow_step', 1);
            
            if ($step < $min_allow_step) $step = $min_allow_step;
            if ($step > $max_allow_step) $step = $max_allow_step;
            
            if (method_exists($this, "step{$step}")) {
                return $this->{"step{$step}"}();
            } else {
                return $this->e404();
            }
        }
    }
    
    function step1()
    {
        $current_lang = \RS\Language\Core::getCurrentLang();
        $locale_list = \RS\Language\Core::getSystemLanguages();
        $this->api->setMaxAllowStep(2);
        
        $this->view->assign([
            'license_text' => $this->api->getLicenseText($current_lang),
            'locale_list' => $locale_list,
            'current_lang' => $current_lang,
            'step' => 1
        ]);
        return $this->wrapHtml(  $this->view->fetch('%install%/step1.tpl') );
    }    
    
    function step2()
    {
        $this->view->assign([
            'check' => $this->api->checkServerParams(),
            'step' => 2
        ]);
        return $this->wrapHtml(  $this->view->fetch('%install%/step2.tpl') );
    }
    
    function step3()
    {       
        if ($this->url->isPost()) 
        {            
            $CHK = new \RS\Helper\FieldChecker();
            
            $config = $this->url->post([
                'start',
                'db_host',
                'db_port',
                'db_name',
                'db_prefix',
                
                'supervisor_email',
                'supervisor_pass',
                'supervisor_pass_confirm',
                'admin_section',
                'set_demo_data'
            ], TYPE_STRING);
            
            $config['db_user'] = (string)$this->url->post('db_user', TYPE_MIXED);
            $config['db_pass'] = (string)$this->url->post('db_pass', TYPE_MIXED);
            
            $nesessary = t('Не заполнено обязательное поле');
            $CHK->Chk_empty($config['db_host'], t('Не заполнено обязательное поле Хост'), 'db_host');
            $CHK->Chk_empty($config['db_name'], t('Необходимо указать имя базы данных'), 'db_name');
            $CHK->Chk_empty($config['db_user'], t('Необходимо указать имя пользователя базы данных'), 'db_user');
            
            $CHK->Chk_email($config['supervisor_email'], t('Необходимо указать корректный e-mail администратора'), 'supervisor_email');
            $CHK->Chk_minmax(mb_strlen($config['supervisor_pass']), 6, false, t('Пароль должен содержать не менее 6 знаков'), 'supervisor_pass');
            $CHK->Chk_pattern($config['admin_section'], t('В имени могут использоваться только цифры, английские буквы, символы "-_" тире и подчеркивания'), 'admin_section', '/^[a-z0-9\-_]+$/i');
            
            if ($config['supervisor_pass_confirm'] !== $config['supervisor_pass']) {
                $CHK->SetErr(t('Неверный повтор пароля'), 'supervisor_pass_confirm');
            }
            
            if (!$CHK->isErr()) {
                //Начало процесса установки
                @ignore_user_abort(true);
                @set_time_limit(0);
                
                if ($this->url->post('start', TYPE_INTEGER)) {
                    $data = $this->api->resetInstall($config);
                } else {
                    $data = $this->api->progress(); //Выполняет установку одного модуля
                }
                
            } else {
                $errors = [];
                foreach($CHK->GetErrArr() as $err_text) {
                    $errors[] = [
                        'moduleTitle' => count($errors)+1,
                        'message' => $err_text
                    ];
                }
                $data = ['errors' => $errors];
            }
            
            return json_encode($data);
        }
        
        $this->view->assign([
            'step' => 3,
            'generated_prefix' => \RS\Helper\Tools::generatePassword(4, range('a', 'z')).'_'
        ]);
        return $this->wrapHtml(  $this->view->fetch('%install%/step3.tpl') );
    }    
    
    
    function step4()
    {
        $license = $this->url->request('license', TYPE_STRING, 'trial');
        $license_key = $this->url->request('license_key', TYPE_STRING);
        $result = true;
        
        if ($this->url->isPost()) {
            if ($license == 'license') {
                $orm_license = new \Main\Model\Orm\License();
                $orm_license->setNeedType('script');
                $orm_license['license'] = $license_key;
                
                if (!$orm_license->insert()) {
                    if ($orm_license->isTypeError()) {
                        $result = t('Установите основную лицензию');
                    }
                    if ($orm_license->isNeedActivation()) {
                        $this->api->setKey('license', $license_key);
                        Application::getInstance()->redirect($this->router->getUrl('install', ['step' => '4a']));
                    }
                }
            }
            if ($result === true) {
                $this->api->setKey('min_allow_step', 5);
                $this->api->setMaxAllowStep(5);
                Application::getInstance()->redirect($this->router->getUrl('install', ['step' => '5']));
            }
        }
        
        $this->view->assign([
            'license_key' => $license_key,
            'license' => $license,
            'result' => $result,
            'step' => 4
        ]);
        return $this->wrapHtml(  $this->view->fetch('%install%/step4.tpl') );
    }        
    
    function actionCheckLicense()
    {
        $license_key = $this->url->request('license_key', TYPE_STRING);
        $res = __CHECK_LICENSE($license_key);
        if ($res === true) {
            $result = ['success' => true, 'result' => t('Ключ корректный')];
        } else {
            $result = ['success' => false, 'result' => $res];
        }
        return json_encode($result);
    }
    
    function step4a()
    {
        $license = new \Main\Model\Orm\License();
        $license['license'] = $this->api->getKey('license');
        $license['check_domain'] = 1;
        $license['is_activation'] = 1;
        $license->setNeedType('script');
        
        if ($this->url->isPost()) {
            if ($license->save()) {
                $this->api->setKey('min_allow_step', 5);
                $this->api->setMaxAllowStep(5);
                Application::getInstance()->redirect($this->router->getUrl('install', ['step' => '5']));
            }
        }
        
        $this->view->assign([
            'license' => $license,
            'step' => '4a'
        ]);
        return $this->wrapHtml(  $this->view->fetch('%install%/step4_activate_license.tpl') );
    }

    function step5()
    {
        $progress = $this->api->getKey('progress');
        $this->api->installComplete();
        
        //Устанавливаем cookie, запускающую интерактивный обучающий тур по сайту
        $this->app->headers->addCookie('tourId', 'welcome', time() + 60*60*365*2, '/');
        
        $this->view->assign([
            'step' => 5,
            'email' => $progress['config']['supervisor_email'],
            'password' => $progress['config']['supervisor_pass'],
            'admin_section' => $progress['config']['admin_section'],
        ]);
        return $this->wrapHtml(  $this->view->fetch('%install%/step5.tpl') );
    }            
    
    function actionPhpInfo()
    {
        phpinfo();
    }
    
    function actionChangeLang()
    {
        $lang = $this->url->get('lang', TYPE_STRING);
        \RS\Language\Core::setSystemLang($lang);

        Application::getInstance()->redirect($this->router->getUrl('install'));
    }
    
    function checkAccessRight()
    {
        if (\Setup::$INSTALLED) {
            return t('ReadyScript уже установлен'); //Запрещаем использовать контроллер после установки
        }
        return false; //Отключаем проверку прав доступа к действиям контроллера
    }
}
