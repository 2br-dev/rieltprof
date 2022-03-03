<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model\Orm;
use \RS\Orm\Type,
    \Shop\Model\Orm\UserStatus,
    \Shop\Model\UserStatusApi;

/**
 * Конфигурация одного уведомления
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property integer $enable_email Отправка E-mail
 * @property integer $enable_sms Отправка SMS
 * @property integer $enable_desktop Отправка на ПК
 * @property string $class Класс уведомления
 * @property string $description Описание
 * @property string $template_email E-Mail шаблон
 * @property string $template_sms SMS шаблон
 * @property string $template_desktop ПК шаблон
 * @property string $additional_recipients Дополнительные e-mail получателей
 * --\--
 */
class NoticeConfig extends \RS\Orm\OrmObject
{
    protected static
        $table = 'notice_config';
 
 
    function _init()
    {
        parent::_init()->append([
                'site_id' => new Type\CurrentSite(),
                'enable_email' => new Type\Integer([
                    'description' => t('Отправка E-mail'),
                    'maxLength' => 1,
                    'allowEmpty' => false,
                    'default' => 1,
                    'checkBoxView' => ['on' => 1, 'off' => 0],
                ]),
                'enable_sms' => new Type\Integer([
                    'description' => t('Отправка SMS'),
                    'maxLength' => 1,
                    'allowEmpty' => false,
                    'default' => 1,
                    'checkBoxView' => ['on' => 1, 'off' => 0],
                ]),
                'enable_desktop' => new Type\Integer([
                    'description' => t('Отправка на ПК'),
                    'maxLength' => 1,
                    'allowEmpty' => false,
                    'default' => 1,
                    'checkBoxView' => ['on' => 1, 'off' => 0],
                ]),
                'class' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Класс уведомления'),
                    'visible' => false,
                ]),
                'description' => new Type\Varchar([
                    'runtime' => true,
                    'maxLength' => '255',
                    'description' => t('Описание'),
                    'visible' => false,
                ]),
                'template_email' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('E-Mail шаблон'),
                    'visible' => true,
                ]),
                'template_sms' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('SMS шаблон'),
                    'visible' => true,
                ]),
                'template_desktop' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('ПК шаблон'),
                    'visible' => true,
                ]),
                'additional_recipients' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Дополнительные e-mail получателей'),
                    'hint' => t('Список Email-ов через запятую'),
                    'visible' => true,
                ]),
        ]);
        
        $this->addIndex(['site_id', 'class'], self::INDEX_UNIQUE);
    }
    
    
    /**
     * Вызывается после загрузки объекта
     * 
     * @return void
     */
    public function afterObjectLoad()
    {
        if(class_exists($this['class'])){
            $inst = new $this['class'];
            $this['description'] = $inst->getDescription();
            if($inst instanceof \Alerts\Model\Types\InterfaceEmail && !$this['template_email']) 
                $this['template_email'] = $inst->getTemplateEmail();
            if($inst instanceof \Alerts\Model\Types\InterfaceSms && !$this['template_sms']) 
                $this['template_sms'] = $inst->getTemplateSms();
            if($inst instanceof \Alerts\Model\Types\InterfaceDesktopApp && !$this['template_desktop'])
                $this['template_desktop'] = $inst->getTemplateDesktopApp();
        }
    }
    
    /**
    * Вызывается перез записью объекта
    * 
    * @param mixed $flag
    */
    public function beforeWrite($flag)
    {
        if(class_exists($this['class'])){
            $inst = new $this['class'];
            if($inst instanceof \Alerts\Model\Types\InterfaceEmail && $this['template_email'] == $inst->getTemplateEmail())
                $this['template_email'] = '';
            if($inst instanceof \Alerts\Model\Types\InterfaceSms && $this['template_sms'] == $inst->getTemplateSms())
                $this['template_sms'] = '';
            if($inst instanceof \Alerts\Model\Types\InterfaceDesktopApp && $this['template_desktop'] == $inst->getTemplateDesktopApp())
                $this['template_desktop'] = '';
        }
    }
    
    
    /**
    * Возвращает true, если уведомление может быть отправлено по Email'у
    * 
    * @return bool
    */
    public function hasEmail()
    {
        if(!class_exists($this['class'])) return false;
        $inst = new $this['class'];
        return $inst instanceof \Alerts\Model\Types\InterfaceEmail;
    }    

    /**
    * Возвращает true, если уведомление может быть отправлено по Sms
    * 
    * @return bool
    */    
    public function hasSms()
    {
        if(!class_exists($this['class'])) return false;
        $inst = new $this['class'];
        return $inst instanceof \Alerts\Model\Types\InterfaceSms;
    }    
    
    /**
    * Возвращает true, если уведомление может быть отправлено на ПК
    * 
    * @return bool
    */
    public function hasDesktop()
    {
        if(!class_exists($this['class'])) return false;
        $inst = new $this['class'];
        return $inst instanceof \Alerts\Model\Types\InterfaceDesktopApp;        
    }
    
    /**
    * Возвращает шаблоны по-умолчанию для уведомления
    * 
    * @return array
    */
    public function getDefaultTemplates()
    {
        $templates = [];
        if(class_exists($this['class'])){
            $inst = new $this['class'];
            if($inst instanceof \Alerts\Model\Types\InterfaceEmail) {
                $templates['email'] = $inst->getTemplateEmail();
            }
            
            if($inst instanceof \Alerts\Model\Types\InterfaceSms) {
                $templates['sms'] = $inst->getTemplateSms();
            }
            
            if($inst instanceof \Alerts\Model\Types\InterfaceDesktopApp) {
                $templates['desktop'] = $inst->getTemplateDesktopApp();
            }
        }
        return $templates;
    }
}