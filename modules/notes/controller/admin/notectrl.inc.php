<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Notes\Controller\Admin;

use Notes\Model\Orm\Note;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Filter,
    \RS\Html\Table;
    
/**
* Контроллер Управление списком магазинов сети
*/
class NoteCtrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        //Устанавливаем, с каким API будет работать CRUD контроллер
        $note_api = new \Notes\Model\NoteApi();
        $note_api->initPrivateFilter();

        parent::__construct($note_api);
    }
    
    function helperIndex()
    {
        $helper = parent::helperIndex(); //Получим helper по-умолчанию
        $helper->setTopTitle(t('Заметки')); //Установим заголовок раздела
        $helper->setTopHelp(t('Используйте заметки, чтобы структурировать свои задачи по работе с клиентами или вашими менеджерами. Добавьте виджет на рабочий стол и управляйте заметками прямо со старвого экрана'));
        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));
        
        //Отобразим таблицу со списком объектов
        $helper->setTable(new Table\Element([
            'Columns' => [
                    new TableType\Checkbox('id'),
                    new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                    new TableType\Text('status', t('Статус'), ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Userfunc('creator_user_id', t('Пользователь'), function($value, $_this) {
                        return $_this->getRow()->getCreatorUser()->getFio()."($value)";
                    }, ['Sortable' => SORTABLE_BOTH]),
                    new TableType\Actions('id', [
                            new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~'])),
                    ],
                        ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                    ),
            ]]));
        
        //Добавим фильтр значений в таблице по названию
        $helper->setFilter(new Filter\Control( [
            'Container' => new Filter\Container( [
                                'Lines' =>  [
                                    new Filter\Line( ['items' => [
                                            new Filter\Type\Text('title', t('Заголовок'), ['SearchType' => '%like%']),
                                            new Filter\Type\Select('status', t('Статус'), ['' => t('Любой')] + Note::getStatusList()),
                                            new Filter\Type\User('creator_user_id', t('Создатель')),
                                            new Filter\Type\Text('message', t('Сообщение'), ['SearchType' => '%like%'])
                                    ]
                                    ])
                                ],
            ])
        ]));

        return $helper;
    }

    function helperAdd()
    {
        $helper = parent::helperAdd();
        if ($context = $this->url->request('context', TYPE_STRING)) {
            $helper->setFormSwitch($context);
        }
        return $helper;
    }

    function helperEdit($primaryKey = null)
    {
        $helper = parent::helperEdit();

        $id = $this->url->get('id', TYPE_STRING, 0);
        $helper['bottomToolbar']->addItem(
            new ToolbarButton\delete( $this->router->getAdminUrl('delNote', ['id' => $id, 'dialogMode' => $this->url->request('dialogMode', TYPE_INTEGER)]), null, [
                'noajax' => true,
                'attr' => [
                    'class' => 'delete crud-get crud-close-dialog',
                    'data-confirm-text' => t('Вы действтельно хотите удалить данную заметку?')
                ]
            ]), 'delete'
        );

        return $helper;
    }

    function actionDelNote()
    {
        $id = $this->url->request('id', TYPE_INTEGER);
        if (!empty($id)) {
            $comment = $this->api->getOneItem($id);
            $comment->delete();
        }

        if (!$this->url->request('dialogMode', TYPE_INTEGER)) {
            $this->result->setAjaxWindowRedirect($this->url->getSavedUrl($this->controller_name.'index'));
        }

        return $this->result
            ->setSuccess(true)
            ->setNoAjaxRedirect($this->url->getSavedUrl($this->controller_name.'index'));
    }
}
