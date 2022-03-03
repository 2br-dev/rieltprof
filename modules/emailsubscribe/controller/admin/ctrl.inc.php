<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace EmailSubscribe\Controller\Admin;
 
use \RS\Html\Table\Type as TableType,
    \RS\Html\Toolbar\Button as ToolbarButton,
    \RS\Html\Filter,
    \RS\Html\Table;
     
/**
* Контроллер Списка E-mail для рассылки
*/
class Ctrl extends \RS\Controller\Admin\Crud
{
    function __construct()
    {
        //Устанавливаем, с каким API будет работать CRUD контроллер
        parent::__construct(new \EmailSubscribe\Model\Api());
    }
     
    function helperIndex()
    {
        $helper = parent::helperIndex(); //Получим helper по-умолчанию
        $helper->setTopTitle(t('Список E-mail для рассылки')); //Установим заголовок раздела
        $helper->setTopHelp(t('Данный раздел содержит список емайлов пользователей, которые дали согласие на e-mail рассылку.<br>
                               Вы можете экспортировать данный список в формат CSV, для использования в сторонних сервисах рассылки,<br>
                               или для создания рассылки, с помощью RS модуля - <a href="https://marketplace.readyscript.ru/addons/mailsender/">Обычные и триггерные рассылки</a>'));//Информационное окно
        $helper->setTopToolbar($this->buttons(['add'], ['add' => t('добавить E-mail для рассылки')]));
        $helper->addCsvButton('emailsubscribe-email');
        //Установим, какие кнопки отобразить в нижней панели инструментов
        $helper->setBottomToolbar($this->buttons(['multiedit', 'delete']));
        
         
        //Опишем колонки табличного представления данных
        $helper->setTable(new Table\Element([
            'Columns' => [
                new TableType\Checkbox('id', ['showSelectAll' => true]), //Отображаем флажок "выделить элементы на всех страницах"
                new TableType\Text('email', 'E-mail', ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Datetime('dateof', t('Дата подписки'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\StrYesno('confirm', t('Подтверждён?'), ['Sortable' => SORTABLE_BOTH, 'href' => $this->router->getAdminPattern('edit', [':id' => '@id']), 'LinkAttr' => ['class' => 'crud-edit']]),
                new TableType\Text('id', '№', ['TdAttr' => ['class' => 'cell-sgray']]),
                new TableType\Actions('id', [
                    //Опишем инструменты, которые нужно отобразить в строке таблицы пользователю
                    new TableType\Action\Edit($this->router->getAdminPattern('edit', [':id' => '~field~']), null, [
                        'attr' => [
                            '@data-id' => '@id'
                        ]]),
                ],
                    //Включим отображение кнопки настройки колонок в таблице
                    ['SettingsUrl' => $this->router->getAdminUrl('tableOptions')]
                ),
            ],

        ]));
         
        //Опишем фильтр, который следует добавить
        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container( [ //Контейнер визуального фильтра
                'Lines' =>  [
                    new Filter\Line( ['Items' => [ //Одна линия фильтров
                            new Filter\Type\Text('id','№'), //Фильтр по ID
                            new Filter\Type\Text('email','E-mail', ['searchType' => '%like%']), //Фильтр по названию производителя
                            new Filter\Type\Select('confirm', t('Подтверждён?'), [
                                '' => t("-Не важно-"),
                                1 => t("Да"),
                                0 => t("Нет"),
                            ]), //Фильтр по названию производителя
                    ]
                    ]),
                ]
            ]),
            'Caption' => t('Поиск по E-mail')
        ]));
         
        return $helper;
    }
    
    
    /**
    * Открытие окна добавления и редактирования товара
    * 
    * @param integer $primaryKeyValue - первичный ключ товара(если товар уже создан)
    * @return string
    */
    function actionAdd($primaryKeyValue = null, $returnOnSuccess = false, $helper = null)
    {
        $obj = $this->api->getElement();    
        
        if ($primaryKeyValue == null){
            $obj['public'] = 1; 
            $this->getHelper()->setTopTitle(t('Добавить E-mail'));
        } else {
            $this->getHelper()->setTopTitle(t('Редактировать ').' {email}');
        }
        
        return parent::actionAdd($primaryKeyValue, $returnOnSuccess, $helper);
    }
    
}