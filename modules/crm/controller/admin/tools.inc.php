<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Controller\Admin;

use Crm\Config\ModuleRights;
use Crm\Model\Telephony\Manager;
use RS\AccessControl\Rights;
use RS\Controller\Admin\Front;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Html\Toolbar;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Html\Toolbar\Button\ApplyForm;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;

/**
 * Контроллер содержит вспомогательные действия для модуля CRM
 */
class Tools extends Front
{
    /**
     * Отображает диалог эмуляции событий телефонии
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    public function actionTestTelephony()
    {
        if ($error = Rights::CheckRightError('crm', ModuleRights::CALL_HISTORY_OTHER_READ)) {
            return $this->result->setSuccess(false)->addEMessage($error)->addSection('close_dialog', true);
        }

        $providers = \Crm\Model\Telephony\Manager::getProviders();
        if (!$providers) {
            return $this->result->addEMessage(t('Не зарегистрировано ни одного провайдера телефонии'));
        }

        $provider_id = $this->url->request('provider', TYPE_STRING, key($providers));
        $provider_object = Manager::getProviderById($provider_id);

        $form_object =  new FormObject(new PropertyIterator([
            'provider' => new Type\Varchar([
                'description' => t('Провайдер телефонии'),
                'list' => [['Crm\Model\Telephony\Manager', 'getProvidersTitles'], Manager::FILTER_ONLY_WITH_TEST]
            ]),
            'provider_fields' => new Type\ArrayList([
                'description' => '',
                'provider_object' => $provider_object,
                'template' => '%crm%/telephony/provider_test_fields.tpl'
            ])
        ]));

        if ($this->url->isPost() && $form_object->checkData()) {

            $test_object = $provider_object->getEventTestObject();
            if ($test_object->onTest($form_object['provider_fields'])) {
                return $this->result
                    ->addMessage($test_object->getEventLastResult());
            } else {
                return $this->result
                    ->addEMessage($test_object->getEventLastError());
            }
        }

        $helper = new CrudCollection($this);
        $helper->viewAsForm();
        $helper->setTopTitle(t('Эмулятор входящих событий телефонии'));
        $helper->setFormObject($form_object);
        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ApplyForm(null, t('Выполнить'))
            ]
        ]));


        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Возвращает форму тестирования нужного провайдера
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    public function actionGetTestProviderForm()
    {
        if ($error = Rights::CheckRightError('crm', ModuleRights::CALL_HISTORY_OTHER_READ)) {
            return $this->result->setSuccess(false)->addEMessage($error)->addSection('close_dialog', true);
        }

        $provider_id = $this->url->request('provider', TYPE_STRING);
        $provider_object = Manager::getProviderById($provider_id);
        $test_object = $provider_object->getEventTestObject();

        if ($test_object) {
            return $this->result->setHtml($test_object->getFormHtml());
        }
    }

    /**
     * Возвращает обновленные адреса для синхронизации
     */
    public function actionRefreshEventUrlsTelephony()
    {
        $provider_id = $this->url->get('provider', TYPE_STRING);
        $secret_key = $this->url->post('secret_key', TYPE_STRING);

        $provider_object = Manager::getProviderById($provider_id);
        $provider_object->setUrlSecret($secret_key);

        return $this->result
                        ->setSuccess(true)
                        ->setHtml($provider_object->getConnectSettingsInfo());
    }

    /**
     * Открывает диалог управления записями разговоров
     */
    public function actionDeleteRecordsTelephony()
    {
        $form_object = new FormObject(new PropertyIterator([
            'delete_all' => new Type\Integer([
                'description' => t('Удалить все локальные файлы записей'),
                'checkboxView' => [1,0]
            ]),
            'delete_before_date' => new Type\Datetime([
                'description' => t('Удалить локальные файлы записей до указанной даты'),
                'hint' => t('Важно: С целью увеличения производительности, проверяется дата изменения файла записи разговора'),
                'checker' => [function($_this, $value) {
                    if (!$_this['delete_all'] && !$_this['delete_before_date']) {
                        return t('Укажите дату и время, ранее которых необходимо удалить записи');
                    }
                    return true;
                }]
            ])
        ]));

        $helper = new CrudCollection($this);
        $helper->viewAsForm();
        $helper->setTopTitle(t('Управление записями разговоров'));
        $helper->setHeaderHtml($this->view->fetch('%crm%/telephony/show_record_dialog.tpl'));
        $helper->setFormObject($form_object);


        if ($this->url->isPost()) {

            if ($error = Rights::CheckRightError('crm', ModuleRights::CALL_HISTORY_OTHER_DELETE)) {
                return $this->result->setSuccess(false)->addEMessage($error);
            }

            if ($form_object->checkData()) {

                $result = Manager::deleteCallRecords($form_object['delete_all'], $form_object['delete_before_date']);

                if ($result === true) {
                    return $this->result
                                    ->addMessage(t('Операция успешно выполнена'))
                                    ->setSuccess(true);
                } else {
                    return $this->result->addSection([
                        'repeat' => true,
                        'counter' => $result,
                        'queryParams' => [
                            'url' => $this->router->getAdminUrl('DeleteRecordsTelephony'),
                            'data' => $form_object->getValues(),
                        ]
                    ]);
                }
            } else {
                return $this->result->setSuccess(false)
                    ->setErrors($form_object->getDisplayErrors());
            }
        }


        $helper->setBottomToolbar(new Toolbar\Element([
            'Items' => [
                new ToolbarButton\SaveForm(null, t('Удалить'), [
                    'attr' => [
                        'class' => 'btn-danger'
                    ],
                ]),
                new ToolbarButton\Cancel('')
            ]
        ]));


        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Возвращает объем занимаемого места на диске записями разговоров
     */
    public function actionGetRecordsTelephonySize()
    {
        $size_string = Manager::getRecordsSize();

        return $this->result
            ->setSuccess(true)
            ->addSection('record_size', $size_string);
    }

}