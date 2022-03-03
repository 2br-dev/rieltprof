<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Helper;
use \RS\Config\Loader as ConfigLoader;
use RS\Event\Manager;
use RS\View\Engine;

/**
* Рекомендованный класс для формирования и отправки Email сообщений в ReadyScript.
* Генерирует событие перед отправкой каждого письма, тем самым позволяет 
* сторонним модулям вмешиваться в подготовку данных для отправки.
*/
class Mailer extends PhpMailer\PHPMailer
{        
    public
        $RenderBodyTemplates,
        $RenderBodyVars,
        $SingleTo = true;
    
    protected
        $EventId,
        $EventParams = [];
    
    /**
    * Расширенный конструктор класса. 
    * Устанавливает все параметры, согласно настройкам в административной панели
    * 
    * @param bool | null $exceptions - Бросать исключения в случае ошибок
    * если null - значение будет взято из настроек системы \Setup::$DETAILED_EXCEPTIONS
    * если true - да
    * если false - нет, ошибки сохраняются во внутренней переменной
    * 
    * @return Mailer
    */
    function __construct($exceptions = null)
    {
        if ($exceptions === null) {
            $exceptions = (bool)\Setup::$DETAILED_EXCEPTION;
        }
        parent::__construct($exceptions);
        
        $system_config = ConfigLoader::getSystemConfig();
        $site_config = ConfigLoader::getSiteConfig();
        
        $this->CharSet = 'utf-8';
        $this->SMTPAutoTLS = false;
        $this->Hostname = $system_config['hostname'];
        $this->FromName = $system_config->getNoticeFrom(false);
        $this->From     = $system_config->getNoticeFrom(true);
        $this->addReplyTo($system_config->getNoticeReply(true), $system_config->getNoticeReply(false));
        $this->isHTML();

        if ($site_config['dkim_is_use'] && $site_config['dkim_domain'] && $site_config['dkim_private']) {
            $this->DKIM_domain = $site_config['dkim_domain'];
            $this->DKIM_private = $site_config['__dkim_private']->getFullPath();
            $this->DKIM_selector = $site_config['dkim_selector'];
            $this->DKIM_passphrase = $site_config['dkim_passphrase']; //key is not encrypted
        }
        
        if ($site_config['smtp_is_use']) {
            $this->isSMTP();
            $this->Host = $site_config['smtp_host'];
            $this->Port = $site_config['smtp_port'];
            $this->SMTPSecure = $site_config['smtp_secure'];
            $this->SMTPAuth = $site_config['smtp_auth'] == true;
            $this->Username = Tools::unEntityString($site_config['smtp_username']);
            $this->Password = Tools::unEntityString($site_config['smtp_password']);
        } elseif ($system_config['smtp_is_use']) {
            $this->isSMTP();
            $this->Host = $system_config['smtp_host'];
            $this->Port = $system_config['smtp_port'];
            $this->SMTPSecure = $system_config['smtp_secure'];
            $this->SMTPAuth = $system_config['smtp_auth'] == true;
            $this->Username = Tools::unEntityString($system_config['smtp_username']);
            $this->Password = Tools::unEntityString($system_config['smtp_password']);
        }
    }
    
    /**
    * Устанавливает тело письма, путем рендеринга шаблона
    * 
    * @param string $template - путь к шаблону
    * @param mixed $vars - переменные для шаблона. Будут доступы в нем в переменной $data
    * @return void
    */
    function renderBody($template, $vars)
    {
        $this->RenderBodyTemplates = $template;
        $this->RenderBodyVars = $vars;
        
        $view = new Engine();
        $view->assign('data', $this->RenderBodyVars);
        $this->Body = $view->fetch($this->RenderBodyTemplates);
        $this->AltBody = $this->html2text($this->Body);
    }
    
    /**
    * Добавляет адресатов для отправки письма
    * 
    * @param string $comma_separated_emails - email адреса, разделенные запятой
    * @return void
    */
    function addEmails($comma_separated_emails)
    {
        $emails = explode(',', $comma_separated_emails);
        foreach($emails as $email) {
            $this->addAddress(trim($email));
        }
    }
    
    /**
    * Устанавливает параметры вызываемого перед отправкой события. 
    * 
    * @param string $event_id - Идентификатор, который будет включен в имя генерируемого события,
    * Например, если идентификатор bar, то событие будет носить имя mailer.bar.beforesend
    * @param array $params параметры, которые будут переданы в событие
    */
    function setEventParams($event_id, array $params = [])
    {
        $this->EventId = $event_id.'.';
        $this->EventParams = $params;
    }
    
    /**
    * Подготавливает сообщение для отправки
    * 
    * @throws phpmailerException
    * @return bool
    */
    function preSend()
    {
        //Не бросаем исключение, если нет получателя
        if ((count($this->to) + count($this->cc) + count($this->bcc)) < 1) {
            $this->setError($this->lang('provide_address'));
            return false;
        }
        
        //Генерируем событие до отправки письма
        $event_result = Manager::fire('mailer.'.$this->EventId.'beforesend', ['mailer' => $this] + $this->EventParams);
        if ($event_result->getEvent()->isStopped()) {
            $this->setError(implode(',', $event_result->getEvent()->getErrors()));
            return false;
        }
        
        return parent::preSend();
    }
    
    /**
     * Add an address to one of the recipient arrays.
     * Addresses that have been added already return false, but do not throw exceptions
     * @param string $kind One of 'to', 'cc', 'bcc', 'ReplyTo'
     * @param string $address The email address to send to
     * @param string $name
     * @throws phpmailerException
     * @return boolean true on success, false if address already used or invalid in some way
     * @access protected
     */    
    protected function addAnAddress($kind, $address, $name = '')
    {
        //Не бросаем исключение, если произошла попытка добавить пустого получателя. 
        try {
            return parent::addAnAddress($kind, $address, $name);
        } catch (PhpMailer\phpmailerException $e) {
            return false;
        }
    }

    /**
     * Add an address to one of the recipient arrays or to the ReplyTo array. Because PHPMailer
     * can't validate addresses with an IDN without knowing the PHPMailer::$CharSet (that can still
     * be modified after calling this function), addition of such addresses is delayed until send().
     * Addresses that have been added already return false, but do not throw exceptions.
     * @param string $kind One of 'to', 'cc', 'bcc', or 'ReplyTo'
     * @param string $address The email address to send, resp. to reply to
     * @param string $name
     * @throws phpmailerException
     * @return boolean true on success, false if address already used or invalid in some way
     * @access protected
     */
    protected function addOrEnqueueAnAddress($kind, $address, $name)
    {
        //Не бросаем исключение, если произошла попытка добавить пустого получателя.
        try {
            return parent::addOrEnqueueAnAddress($kind, $address, $name);
        } catch (PhpMailer\phpmailerException $e) {
            return false;
        }
    }
}
