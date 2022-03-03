<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Notes\Controller\Admin\Widget;
use Notes\Model\NoteApi;
use Notes\Model\Orm\Note;

class Notes extends \RS\Controller\Admin\Widget
{
    const
        NO_FILTER = 'all',

        FILTER_CREATOR_MY = 'my',

        FILTER_STATUS_CLOSED = 'closed',
        FILTER_STATUS_UNCLOSED = 'unclosed',

        SORT_UPDATE = 'update',
        SORT_CREATE = 'create';


    protected
        $info_title = 'Заметки',
        $info_description = 'Отображает список заметок. Позволяет создать и отредактировать любую заметку.';

    function actionIndex()
    {
        $filter_creator = $this->getRequestParam('notes_filter_creator', [
            self::NO_FILTER,
            self::FILTER_CREATOR_MY]);

        $filter_status = $this->getRequestParam('notes_filter_status', [
            self::NO_FILTER,
            self::FILTER_STATUS_CLOSED,
            self::FILTER_STATUS_UNCLOSED]);

        $sort = $this->getRequestParam('notes_sort', [
            self::SORT_CREATE,
            self::SORT_UPDATE
        ]);

        $page = $this->myRequest('p', TYPE_INTEGER, 1);

        $api = new NoteApi();
        $api->initPrivateFilter();
        //Устанавливаем фильтр по создателю
        if ($filter_creator == self::FILTER_CREATOR_MY) {
            $api->setFilter('creator_user_id', $this->user->id);
        }

        //Устанвливаем фильтр по статусу
        if ($filter_status == self::FILTER_STATUS_CLOSED) {
            $api->setFilter('status', Note::STATUS_CLOSE);
        }
        elseif($filter_status == self::FILTER_STATUS_UNCLOSED) {
            $api->setFilter('status', [Note::STATUS_OPEN, Note::STATUS_INWORK], 'in');
        }

        //Устанавливаем сортировку
        if ($sort == self::SORT_UPDATE) {
            $api->setOrder('date_of_update DESC');
        } else {
            $api->setOrder('date_of_create DESC');
        }
        $page_size = $this->getModuleConfig()->widget_notes_page_size;
        $notes = $api->getList($page, $page_size);
        $paginator = new \RS\Helper\Paginator($page,
                                              $api->getListCount(),
                                              $page_size,
                                              'main.admin',
                                              ['mod_controller' => $this->getUrlName()]);

        $this->view->assign([
            'notes' => $notes,
            'notes_filter_creator' => $filter_creator,
            'notes_filter_status' => $filter_status,
            'notes_sort' => $sort,
            'paginator' => $paginator
        ]);

        return $this->result->setTemplate( 'widget/notes.tpl' );
    }

    private function getRequestParam($param_name, $allowed_values)
    {
        $site_id = \RS\Site\Manager::getSiteId();
        $cookie_var_name = $param_name.$site_id;
        $default_value = $this->url->cookie($cookie_var_name, TYPE_STRING);
        $value = $this->url->convert( $this->myRequest($param_name, TYPE_STRING, $default_value), $allowed_values);

        $cookie_expire = time()+60*60*24*730;
        $cookie_path = $this->router->getUrl('main.admin');
        $this->app->headers
            ->addCookie($cookie_var_name, $value, $cookie_expire, $cookie_path);

        return $value;
    }


    function getTools()
    {
        $router = \RS\Router\Manager::obj();
        return [
            [
                'title' => t('Добавить заметку'),
                'class' => 'zmdi zmdi-plus crud-add',
                'href' => $router->getAdminUrl('add', ['context' => 'widget'], 'notes-notectrl'),
                '~data-crud-options' => "{ \"updateBlockId\": \"notes-widget-notes\" }",
                'id' => 'notes-add'
            ],
            [
                'title' => t('Все заметки'),
                'class' => 'zmdi zmdi-open-in-new',
                'href' => $router->getAdminUrl(false, [], 'notes-notectrl')
            ]
        ];
    }
}