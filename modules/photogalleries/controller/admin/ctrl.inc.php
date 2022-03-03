<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photogalleries\Controller\Admin;
 
use Photogalleries\Model\AlbumApi;
use RS\Controller\Admin\Helper\CrudCollection;
use \RS\Html\Table\Type as TableType,
    \RS\Html\Filter,
    \RS\Html\Table;
use RS\Html\Toolbar\Button as ToolbarButton;
use RS\Router\Manager as RouterManager;

/**
* Контроллер Списка фотогалереи
*/
class Ctrl extends \RS\Controller\Admin\Crud
{
    /**
     * @var \Photogalleries\Model\AlbumApi $api
     */
    public $api; //Основное АПИ
    
    function __construct()
    {
        //Устанавливаем, с каким API будет работать CRUD контроллер
        parent::__construct(new AlbumApi());
    }
     
    function helperIndex()
    {
        $helper = parent::helperIndex(); //Получим helper по-умолчанию
        $helper->setTopTitle(t('Фотогалерея')); //Установим заголовок раздела
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить альбом')]));
         
        ///ПРАВАЯ КОЛОНКА 
        //Опишем колонки табличного представления данных
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]), //Отображаем флажок "выделить элементы на всех страницах"
                new TableType\Sort('sortn', t('Порядок'), ['sortField' => 'id', 'Sortable' => SORTABLE_ASC,'CurrentSort' => SORTABLE_ASC,'ThAttr' => ['width' => '20']]),
                new TableType\Text('title', t('Название'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('alias', t('Англ. имя'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Yesno('public', t('Публичный'), ['Sortable' => SORTABLE_BOTH, 'toggleUrl' => $this->router->getAdminPattern('ajaxTogglePublic', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray']]),
                new TableType\Actions('id', [
                        //Опишем инструменты, которые нужно отобразить в строке таблицы пользователю
                        new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                            'attr' => [
                                '@data-id' => '@id'
                            ]]
                        ),
                ],
                    //Включим отображение кнопки настройки колонок в таблице
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ],
            'TableAttr' => [
                'data-sort-request' => $this->router->getAdminUrl('move')
            ]
        ]));

        $helper['topToolbar']->addItem(new ToolbarButton\Dropdown([
            [
                'title' => t('Импорт/Экспорт')
            ],
            [
                'title' => t('Экспорт альбомов в CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('exportCsv', ['schema' => 'photogalleries-album', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
            [
                'title' => t('Импорт альбомов из CSV'),
                'attr' => [
                    'data-url' => RouterManager::obj()->getAdminUrl('importCsv', ['schema' => 'photogalleries-album', 'referer' => $this->url->selfUri()], 'main-csv'),
                    'class' => 'crud-add'
                ]
            ],
        ]), 'import');


        //Опишем фильтр, который следует добавить
        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container( [ //Контейнер визуального фильтра
                'Lines' =>  [
                    new Filter\Line( ['Items' => [ //Одна линия фильтров
                            new Filter\Type\Text('id','№', ['attr' => ['class' => 'w50']]), //Фильтр по ID
                            new Filter\Type\Text('title',t('Название'), ['searchType' => '%like%']), //Фильтр по названию производителя
                    ]
                    ]),
                ]
            ]),
            'Caption' => t('Поиск')
        ]));

        return $helper;
    }


    /**
     * Открытие окна добавления и редактирования сервиса и услуги
     *
     * @param integer $primaryKeyValue - первичный ключ товара(если товар уже создан)
     * @param boolean $returnOnSuccess - Если true, то будет возвращать true при успешном сохранении, иначе будет вызов стандартного _successSave метода
     * @param CrudCollection $helper - текущий хелпер
     *
     * @return bool|\RS\Controller\Result\Standard
     */
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $obj = $this->api->getElement();    
        
        if ($primaryKeyValue == null){
            $obj['public']    = 1; 
            $obj['parent_id'] = $this->request('dir', TYPE_INTEGER, 0); //Укажем категорию, если она выбрана 
            $obj->setTemporaryId();
            $this->getHelper()->setTopTitle(t('Добавить альбом'));
        } else {
            $this->getHelper()->setTopTitle(t('Редактировать альбом').' {title}');
        }
        
        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }

    /**
    * Переключение флага публичности
    * 
    */
    function actionAjaxTogglePublic()
    {
        $id = $this->url->get('id', TYPE_STRING);
        
        $album = $this->api->getOneItem($id);
        if ($album) {
            $album['public'] = !$album['public'];
            $album->update();
        }
        return $this->result->setSuccess(true);
    }
}