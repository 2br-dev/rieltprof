<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;
use \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Table\Type as TableType,
    \RS\Html\Table;

class License extends \RS\Controller\Admin\Crud
{
    protected $api;
    
    function __construct()
    {
        parent::__construct(new \Main\Model\LicenseApi());
        $this->setCrudActions('index', 'del');
    }
    
    function init()
    {
        if (defined('CANT_MANAGE_LICENSE')) return '';
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex()
            ->setAppendModuleOptionsButton(false)
            ->removeSection('paginator')
            ->setListFunction('getTableList')
            ->setTopHelp(t('В данном разделе представлены все установленные в вашем магазине лицензионные ключи. Добавьте приобретенный лицензионный ключ в список, если вы желаете расширить возможности или лимиты вашей текущей лицензии.'))
            ->setTopTitle(t('Управление лицензиями'))
            ->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('license_key'),
                new TableType\Usertpl('license_key', t('Лицензионный номер'), '%main%/license_col_number.tpl'),
                new TableType\Text('license_type_str', t('Тип')),
                new TableType\Usertpl('object', t('Объект лицензирования'), '%main%/license_col_object.tpl', ['TdAttr' => ['class' => 'cell-small']]),
                new TableType\Datetime('date_of_activation', t('Дата активации'), ['format' => 'd.m.Y']),
                new TableType\Text('domain', t('Домен')),
                new TableType\Actions('license_key', [
                    new TableType\Action\Action($this->router->getAdminPattern('refreshLicense', [':key' => '~field~']), t('Обновить'), [
                        'class' => 'crud-get',
                        'iconClass' => 'refresh'
                    ])
                ])
            ]]));
            
        $helper['topToolbar']
            ->addItem(new ToolbarButton\Add($this->url->replaceKey([$this->action_var => 'addLic']), t('Добавить лицензию')), 'add')
            ->addItem(new ToolbarButton\Button($this->api->getBuyLicenseUrl(), t('Купить лицензию'), ['attr' => ['target' => '_blank']]));
        $helper['beforeTableContent'] = $this->view->fetch('license_notice.tpl');        
        return $helper;
    }
    
    function helperAddLic()
    {
        $helper = parent::helperAdd()
            ->setTopTitle(t('Введите номер лицензии'));
        
        return $helper;
    }
    
    /**
    * Добавляет лицензионный ключ
    */
    function actionAddLic($is_activate = false)
    {
        $orm_object = $this->api->getElement();
        $helper = $this->getHelper();
        
        if ($is_activate) {
            $key = $this->url->request('key', TYPE_STRING);
            $type = $this->url->request('type', TYPE_STRING);

            $helper->setTopTitle(t('Активация лицензии'));
            $helper->setFormSwitch($type != 'script' ? 'extra' : 'activation');
            $orm_object->fillDefaultActivationValue();
            $orm_object['license'] = $key;
            $orm_object['is_activation'] = 1;
            $orm_object['check_domain'] = 1;

            $orm_object->__id;
            $orm_object->setClassParameter('formbox_attr_line', "data-dialog-options='{ \"width\":600, \"height\":760 }'");
        }
    
        $orm_object->fillDefaults();

        //Если пост идет для текущего модуля
        if ($this->url->isPost()) 
        {
            $this->result->setSuccess( $this->api->save(null, $this->user_post_data) );

            //Если требуется активация ключа
            if ($this->api->getElement()->isNeedActivation()) {
                $activation_url = $this->router->getAdminUrl('activateLicense', [
                    'key' => $this->api->getElement()->license,
                    'type' => $this->api->getElement()->getLicenseType()
                ]);
                return $this->result
                                ->setSuccess(true)
                                ->addSection('callCrudAdd', $activation_url);
            }

            if ($this->url->isAjax()) { //Если это ajax запрос, то сообщаем результат в JSON
                if (!$this->result->isSuccess()) {
                    $this->result->setErrors($orm_object->getDisplayErrors());
                } else {
                    $this->result->setSuccessText(t('Изменения успешно сохранены'));
                    if (!$this->url->request('dialogMode', TYPE_INTEGER)) {
                        $this->result
                                ->setAjaxWindowRedirect( $this->url->getSavedUrl($this->controller_name.'index') );
                    }
                }
                return $this->result->getOutput();
            }
            
            if ($this->result->isSuccess()) {
                $this->successSave();
            } else {
                $helper['formErrors'] = $orm_object->getDisplayErrors();
            }
            
            return $this->result;
        } 
        
        $this->view->assign([
            'elements' => $helper->active(),
        ]);
        return $this->result->setHtml($this->view->fetch( $helper['template'] ))->getOutput();
    }
    
    /**
    * Активирует лицензионный ключ
    */
    function actionActivateLicense()
    {
        return $this->actionAddLic(true);
    }
    
    /**
    * Активирует лицензионный ключ
    */
    function helperActivateLicense()
    {
        return $this->helperAddLic();
    }

    /**
     * Переустанавливает лицензию
     */
    function actionRefreshLicense()
    {
        $key = $this->url->request('key', TYPE_STRING);
        $license = new \Main\Model\Orm\License();
        if ($license->load($key)) {
            if ($license->refresh()) {
                $this->result->setSuccess(true)->addMessage(t('Лицензия успешно обновлена'));
            } else {
                $this->result->setSuccess(false)->addEMessage($license->getErrorsStr());
            }

            return $this->result;
        } else {
            $this->e404();
        }
    }

    /**
     * Перенаправляет на обновление временного лицензионного ключа
     */
    function actionLicenseUpdate()
    {
        $key = $this->url->request('key', TYPE_STRING);

        $license = new \Main\Model\Orm\License();
        if ($license->load($key)) {
            $info = __GET_LICENSE_INFO($license);
            $site = \RS\Site\Manager::getAdminCurrentSite();
            $this->view->assign([
                'url' => \Setup::$RS_SERVER_PROTOCOL."://".\Setup::$RS_SERVER_DOMAIN."/update-temp/",
                'post_params' => [
                    'license' => $license['license'],
                    'domain' => md5($info['domain']),
                    'shop_url' => $site->getRootUrl(true),
                ]
            ]);

            $this->wrapOutput(false);
            return $this->result->setTemplate('post_form.tpl');

        } else {
            $this->e404();
        }
    }
}