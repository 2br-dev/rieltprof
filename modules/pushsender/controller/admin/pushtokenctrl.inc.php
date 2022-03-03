<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Controller\Admin;

use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Toolbar\Element as ToolbarElement,
    \RS\Html\Filter,
    \RS\Html\Table;
    
/**
* Контроллер списка правил для 301 редиректов
*/
class PushTokenCtrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        //Устанавливаем, с каким API будет работать CRUD контроллер
        parent::__construct(new \PushSender\Model\PushTokenApi());
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex(); //Получим helper по-умолчанию
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('Добавить токен')]));
        $helper->setTopTitle(t('Список push токенов')); //Установим заголовок раздела
        $helper->setTopHelp(t('Push токен выдается устройству. Далее с помощью данного токена можно отправлять на данное устройство push-уведомления.'));
        
        //Отобразим таблицу со списком объектов
        $helper->setTable(new Table\Element([
            'Columns' => [
                    new TableType\Checkbox('id', ['showSelectAll' => true]),
                    new TableType\Userfunc('user_id', t('Пользователь'), function($value) {
                        if ($value){
                            $user = new \Users\Model\Orm\User($value);    
                            $user_name = $user->getFio()."(".$value.")";
                        }else{
                            $user_name = t("Неизвестный пользователь");
                        }
                        return $user_name;
                    }, ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                    new TableType\Text('model', t('Модель'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('manufacturer', t('Производитель'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('platform', t('Платформа'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('uuid', t('Уникальный идентификатор'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\Text('version', t('Версия платформы'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    new TableType\Text('cordova', t('Версия cordova'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true]),
                    
                    new TableType\Datetime('dateofcreate', t('Дата создания'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                    new TableType\Text('app', t('Приложение'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Text('push_token', t('Токен'), ['Sortable' => SORTABLE_BOTH, 'hidden' => true, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                    new TableType\Actions('id', [
                            new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                            new TableType\Action\DropDown([
                                [
                                    'title' => t('Отправить сообщение'),
                                    'attr' => [
                                        'class' => 'crud-edit',
                                        '@href' => $this->router->getAdminPattern('addsendmessage', [':chk[]' => '@id']),
                                    ]
                                ],
                            ])
                    ],
                        ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                    )
            ]]));
        
        //Добавим фильтр значений в таблице по названию
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                                'Lines' =>  [
                                    new Filter\Line( ['items' => [
                                            new Filter\Type\User('user_id', t('Пользователь')),
                                            new Filter\Type\Text('model', t('Модель'), ['SearchType' => '%like%']),
                                            new Filter\Type\Text('platform', t('Платформа'), ['SearchType' => '%like%']),
                                            new Filter\Type\Text('app', t('Приложение'), ['SearchType' => '%like%']),
                                            new Filter\Type\DateRange('dateofcreate', t('Дата создания')),

                                    ]
                                    ])
                                ],
                                'SecContainers' => [
                                    new Filter\Seccontainer([
                                        'Lines' => [
                                            new Filter\Line( [
                                                'Items' => [
                                                    new Filter\Type\Text('push_token', t('Push токен'), ['SearchType' => '%like%']),
                                                    new Filter\Type\Text('manufacturer', t('Производитель'), ['SearchType' => '%like%']),
                                                    new Filter\Type\Text('uuid', t('Уникальный идентификатор'), ['SearchType' => '%like%']),
                                                    new Filter\Type\Text('version', t('Версия платформы'), ['SearchType' => '%like%']),
                                                    new Filter\Type\Text('cordova', t('Версия cordova'), ['SearchType' => '%like%']),
                                                ]
                                            ]),
                                        ]
                                        ]
                                )]
            ])]));
        
        if (\RS\Module\Manager::staticModuleExists('mobilesiteapp') && \RS\Module\Manager::staticModuleEnabled('mobilesiteapp')){
            $helper->setBottomToolbar(new \RS\Html\Toolbar\Element([
                'Items' => [
                    new ToolbarButton\Button($this->router->getAdminUrl('addsendmessage'), t('Отправить сообщение'), [
                        'attr' => [
                            'class' => 'edit crud-multiedit'
                        ],
                        'noajax' => false
                    ]),
                    $this->buttons('delete')
                ]
            ]));
        }

        return $helper;
    }
    
    /**
    * Отправляет сообшение для уведомления
    * 
    */
    function actionAddSendMessage()
    {
        $ids  = $this->modifySelectAll( $this->url->request('chk', TYPE_ARRAY, []) );
        $elem = new \PushSender\Model\Orm\PushTokenMessage();
        
        if ($this->url->isPost()) { //Если это POST запрос и сообщение заполнено
            $offset = $this->url->request('offset', TYPE_INTEGER, 0);

            $elem->getFromArray($this->url->getSource(POST));
            if ($elem['send_type'] == $elem::TYPE_PAGE  && empty($elem['message'])){
                return $this->result->setSuccess(true)
                                    ->addEMessage(t('Сообщение не отправлено. Необходимо заполнить текст сообщения для отправки.'));
            }
            if ($elem['send_type'] == $elem::TYPE_PRODUCT  && empty($elem['product_id'])){
                return $this->result->setSuccess(true)
                                    ->addEMessage(t('Сообщение не отправлено. Необходимо указать товар.'));
            }
            if (($elem['send_type'] == $elem::TYPE_CATEGORY  && empty($elem['category_id'])) || ($elem['send_type'] == $elem::TYPE_CATEGORY && !$elem['category_id'])){
                return $this->result->setSuccess(true)
                                    ->addEMessage(t('Сообщение не отправлено. Необходимо указать категории.'));
            }
            
            
            $push_token_api = new \PushSender\Model\PushTokenApi();
            $result = $push_token_api->sendPushMessageToUsers($elem, $ids, $offset);

            if ($result === true) {
                $this->result->setSuccess(true)
                    ->addMessage(t('Сообщение успешно отправлено'));
            } else {
                $this->result->addSection([
                    'repeat' => true,
                    'queryParams' => [
                        'url' => $this->url->selfUri(),
                        'data' => [
                            'offset' => $result,
                        ] + $elem->getValues()
                    ]
                ]);
            }

            return $this->result;
        }
        
        $helper = parent::helperAdd();
        $helper->setHeaderHtml(t('Выбрано элементов: <b>%0</b>', [count($ids)]));
        $helper->setFormObject($elem);   
        
        return $this->result->setTemplate( $helper['template'] );
    }

}
