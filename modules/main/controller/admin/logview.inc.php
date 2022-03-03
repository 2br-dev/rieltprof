<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Admin;

use Main\Model\LogApi;
use RS\Controller\Admin\Front;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Log\AbstractLog;
use RS\Log\LogManager;
use RS\Html\Filter;
use RS\Html\Paginator;
use RS\Html\Table\Element as TableElement;
use RS\Html\Table\Type as TableType;
use RS\Html\Toolbar\Element as ToolbarElement;
use RS\Html\Toolbar\Button;

/**
 * Контроллер отвечает за просмотр логов
 */
class LogView extends Front
{
    protected $api;

    public function __construct()
    {
        parent::__construct();
        $this->api = new LogApi();
    }

    function helperIndex()
    {
        $helper = new CrudCollection($this, $this->api, $this->url, [
            'paginator'
        ]);
        $helper->viewAsTable();
        $helper->setAppendModuleOptionsButton(false);
        $helper->setTopTitle(t('Логи'));
        $helper->setTopHelp(t('В случае, если у вас включено лоирование каких-либо объектов в <a href="%log_settings">настройках системы</a>, логи можно будет увидеть здесь. Используйте логи, чтобы разобраться что происходит внутри системы во время различных операций.', [
            'log_settings' => $this->router->getAdminUrl(false, [], 'main-options')
        ]));

        $helper->setTopToolbar(new ToolbarElement([
            'items' => [
                new Button\Dropdown([
                    [
                        'title' => t('другие логи'),
                    ],
                    [
                        'title' => t('Внешние API'),
                        'attr' => [
                            'href' => $this->router->getAdminUrl(false, [], 'externalapi-logctrl'),
                        ]
                    ],
                ]),
                new Button\ModuleConfig($this->router->getAdminUrl(false, [], 'main-options'))
            ]
        ]));

        $logs = ['' => t('Любой')];
        foreach(LogManager::getInstance()->getLogList() as $key => $log) {
            $logs[$key] = $log->getTitle();
        }

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line([
                        'Items' => [
                            new Filter\Type\Select('group_title', t('Класс логирования'), $logs),
                        ]
                    ])
                ]]),
            'Caption' => t('Поиск по лог-файлам')
        ]));

        $helper->setTable(new TableElement([
            'Columns' => [
                new TableType\Usertpl('site', t('Лог'), '%main%/form/log/site_column.tpl', [
                        'href' => $this->router->getAdminPattern('view', [':log_class' => '@log_class', ':site_id' => '@site_id']),
                        'linkAttr' => ['target' => '_blank'],
                    ]
                ),
                new TableType\Datetime('last_change', t('Последнее изменение')),
            ]
        ]));

        $helper->setBottomToolbar(new ToolbarElement(['items' => []]));

        $event_name = 'controller.exec.' . $this->getUrlName() . '.index' ; //Формируем имя события
        $helper = EventManager::fire($event_name, $helper)->getResult();
        return $helper;
    }

    /**
     * Отображает список файлов логов, которые были обнаружены
     *
     * @return \RS\Controller\Result\Standard
     */
    function actionIndex()
    {
        $helper = $this->helperIndex();
        $helper->active();
        $table = $helper->getTableControl()->getTable();

        foreach($this->api->getTableGroupRows() as $any_row_data) {
            $table
                ->insertAnyRow([
                    new TableType\Text(null, null, ['Value' => $any_row_data['title'], 'TdAttr' => ['colspan' => 2]])
                ], $any_row_data['index'])
                ->setAnyRowAttr($any_row_data['index'], [
                    'class' => 'table-group-row no-hover'
                ]);
        }

        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Просмотр одного лога
     *
     * @return \RS\Controller\Result\Standard|string
     */
    public function actionView()
    {
        $this->wrapOutput(false);
        $log_class = $this->url->request('log_class', TYPE_STRING);
        $site_id = $this->url->request('site_id', TYPE_INTEGER);

        $log_list = LogManager::getInstance()->getLogList();
        if (!isset($log_list[$log_class])) {
            return t('Указанного класса логирования не существует');
        }
        /** @var AbstractLog $log */
        $log = $log_list[$log_class];
        try {
            $reader = $log->getReaderForSite($site_id);
        } catch (RSException $e) {
            return $e->getMessage();
        }

        $date_from = $this->url->request('date_from', TYPE_STRING, '');
        $time_from = $this->url->request('time_from', TYPE_STRING, '');
        $date_to = $this->url->request('date_to', TYPE_STRING, '');
        $time_to = $this->url->request('time_to', TYPE_STRING, '');
        $levels = $this->url->request('levels', TYPE_ARRAY, []);
        $text = $this->url->request('text', TYPE_STRING, '');
        $page = $this->url->request('page', TYPE_STRING, 1);
        $page_size = $this->url->request('page_size', TYPE_STRING, 1000);

        $reader->setDateFrom($date_from);
        $reader->setTimeFrom($time_from);
        $reader->setDateTo($date_to);
        $reader->setTimeTo($time_to);
        $reader->setLevels($levels);
        $reader->setText($text);
        $reader->setPagination($page, $page_size);

        $this->view->assign([
            'log' => $log,
            'reader' => $reader,
            'log_class' => $log_class,
            'site_id' => $site_id,
            'date_from' => $date_from,
            'time_from' => $time_from,
            'date_to' => $date_to,
            'time_to' => $time_to,
            'levels' => $levels,
            'text' => $text,
            'page' => $page,
            'page_size' => $page_size,
            'controller' => $this,
        ]);
        return $this->result->setTemplate('%main%/form/log/log_view_item.tpl');
    }

    /**
     * Возвращает HTML-код пагинатора
     *
     * @param $page
     * @param $page_size
     * @param $total
     * @return string
     * @throws RSException
     * @throws \SmartyException
     */
    public function getItemViewPaginatorHtml($page, $page_size, $total)
    {
        $paginator_element = new Paginator\Element();
        $paginator_element->setPage($page);
        $paginator_element->setPageSize($page_size);
        $paginator_element->setTotal($total);
        $paginator_element->setPageKey('page');
        $paginator_element->setPageSizeKey('page_size');

        $paginator = new Paginator\Control(['paginator' => $paginator_element, 'autoFill' => false]);
        $paginator->page_var = 'page';
        $paginator->pagesize_var = 'page_size';
        $paginator->fill();

        return $paginator->getView([
            'no_ajax' => true
        ]);
    }

    public function actionClearLog()
    {
        $log_class = $this->url->request('log_class', TYPE_STRING);
        $site_id = $this->url->request('site_id', TYPE_STRING);

        /** @var AbstractLog[] $log_list */
        $log_list = LogManager::getInstance()->getLogList();

        $file = $log_list[$log_class]->getFileLinks()[$site_id];

        file_put_contents($file, '');

        return $this->result->setSuccess(true);
    }
}
