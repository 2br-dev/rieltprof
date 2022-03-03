<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;

use RS\AccessControl\Rights;
use Main\Config\ModuleRights;

/**
* API по работе с виджетами в админке
*/
class Widgets
{
    const
        MODE_ONE_COLUMN   = 1,
        MODE_TWO_COLUMN   = 2,
        MODE_THREE_COLUMN = 3,

        DEFAULT_MODE2_COLUMN = 1,
        DEFAULT_MODE3_COLUMN = 2;
        
    protected
        $cur_user_id,
        $default_add_column = 'center', //Добавляем в колонку 1 по умолчанию, нумерация начинается с 0.
        $obj = '\Main\Model\Orm\Widgets',
        $obj_instance,
        $site_id,
        $widget_folder = 'controller/admin/widget';
    
    function __construct()
    {
        $this->obj_instance = new $this->obj();
        $this->setSiteId(\RS\Site\Manager::getSiteId());
        $this->setUserId(\RS\Application\Auth::getCurrentUser()->id);
    }

    /**
     * Устанавливает сайт, в рамках которого будут строиться выборки
     *
     * @param integer $site_id ID сайта
     * @return void
     */
    function setSiteId($site_id)
    {
        $this->site_id = $site_id;
    }
    
    /**
    * Устанавливает текущего пользователя. Выборки будут это учитывать.
    */
    function setUserId($user_id)
    {
        $this->cur_user_id = $user_id;
    }
    
    /**
    * Возвращает список виджетов на главную страницу
    * 
    * @param integer $total - возвращает общее количество виджетов
    * @return array
    */
    function getMainList(&$total)
    {
        $list = \RS\Orm\Request::make()
            ->from($this->obj_instance)
            ->where([
                'site_id' => $this->site_id,
                'user_id' => $this->cur_user_id
            ])
            ->objects(null, 'class');
        
        $total = count($list);
        $result = [];
            
        foreach($list as $widget) {
            $full_class_name = Orm\Widgets::staticGetFullClass($widget['class']);
            if (!in_array($widget['class'], \Setup::$DISABLE_WIDGETS) && class_exists($full_class_name)) {
                $result[] = $widget;                
            } else {
                //Удаляем с рабочего стола несуществующие виджеты
                $this->removeWidget($widget['class']);
            }
        }
        return $result;
    }

    /**
     * Добавляет виджет. В режиме $mode добавляет виджет в заданную колонку и позицию.
     * Во всех остальных режимах добавляет виджет в колонку по умолчанию в нулевую позицию (самая верхняя).
     *
     * @param $wclass - сокращенный идентификатор виджета
     * @param $column - номер колонки для вставки, начиная с 1
     * @param $position - позиция в колонке, начиная с 0. Если null, то виджет будет добавлен в конец
     * @param $mode - режим колоночности
     * @return \Main\Model\Orm\Widgets
     */
    function insertWidget($wclass, $column, $position = null, $mode = self::MODE_THREE_COLUMN)
    {
        if ($this->issetWidget($wclass)) {
            if ($position === null)
                $position = $this->getNextPos($column, $mode);

            $widget = new $this->obj();
            $widget['site_id'] = $this->site_id;
            $widget['user_id'] = $this->cur_user_id;
            $widget['class'] = $wclass;

            //Записываем в позиции по умолчанию
            $default_mode2_column = $column > 2 ? self::DEFAULT_MODE2_COLUMN : $column;

            $widget['mode2_column'] = $default_mode2_column;
            $widget['mode3_column'] = self::DEFAULT_MODE3_COLUMN;

            $widget['mode1_position'] = $this->getNextPos(null, self::MODE_ONE_COLUMN);
            $widget['mode2_position'] = $this->getNextPos($default_mode2_column, self::MODE_TWO_COLUMN);
            $widget['mode3_position'] = $this->getNextPos(self::DEFAULT_MODE3_COLUMN, self::MODE_THREE_COLUMN);

            if ($mode != self::MODE_ONE_COLUMN) {
                $widget[$this->getColumnFieldName($mode)] = $column;
                $widget[$this->getPositionFieldName($mode)] = $this->getNextPos($column, $mode);
            }

            //Если есть права на вставку виджетов
            if (!$this->noWriteRights()) {
                $widget->insert();
                //Сортируем виджеты в колонке
                $this->sortInColumn($widget, $column, $position, $mode);
            }

            $widget[$this->getPositionFieldName($mode)] = $position;

            return $widget;
        }
    }

    /**
     * Удаляет виджет по короткому идентификатору виджета
     *
     * @param $widget_controller - сокращенный идентификатор виджета
     * @return void
     */
    function removeWidget($widget_controller)
    {
        if ($this->noWriteRights()) return false;
        
        \RS\Orm\Request::make()->delete()
            ->from($this->obj_instance)
            ->where([
                'class' => $widget_controller,
                'site_id' => $this->site_id,
                'user_id' => $this->cur_user_id
            ])->exec();

        $this->reCalculatePositions();
    }
    
    /**
    * Возвращает название поля для колонки для режима $mode
    * 
    * @param integer $mode - режим колоночности
    * @return string
    */
    function getColumnFieldName($mode)
    {
        return "mode".(int)$mode."_column";
    }
    
    /**
    * Возвращает название поля в БД для позиции в колонке для режима $mode
    * 
    * @param integer $mode
    * @return string
    */
    function getPositionFieldName($mode)
    {
        return "mode".(int)$mode."_position";
    }
    
    /**
    * Перемещение виджета по рабочему столу
    * 
    * @param integer $id - id виджета
    * @param integer $to_column - имя новой колонки
    * @param integer $to_pos - порядковый номер виджета в колонке, начиная с нуля
    * @param integer $mode - количество колонок в сетке (режим: 1,2 или 3)
    * @return void
    */
    function moveWidget($id, $to_column, $to_pos, $mode)
    {
        if ($this->noWriteRights()) return false;
        
        $column_field_name = $this->getColumnFieldName($mode);
        $position_field_name = $this->getPositionFieldName($mode);
        
        $cur_widget = $this->getWidgetRecord($id);
        
        if (!$cur_widget 
            || ( ($mode == self::MODE_ONE_COLUMN || $cur_widget[$column_field_name] == $to_column)
                 && $cur_widget[$position_field_name] == $to_pos)) return false;
        
        $next_position = $this->moveToOtherColumn($cur_widget, $to_column, $mode);
        
        if (!isset($next_position) || $next_position != 0) { //Если виджет не один в колонке
            $this->sortInColumn($cur_widget, $to_column, $to_pos, $mode);
        }
    }
    
    /**
    * Перемещает виджет в новую колонку в конец
    * 
    * @param \Main\Model\Orm\Widgets $cur_widget - перемещаемый виджет
    * @param integer $to_column - колонка назначения
    * @param integer $mode - режим колоночности
    * @return mixed - возвращает позицию виджета в новой колонке
    */    
    protected function moveToOtherColumn($cur_widget, $to_column, $mode)
    {
        $next_position = null;
        $column_field_name = $this->getColumnFieldName($mode);
        $position_field_name = $this->getPositionFieldName($mode);                
        
        if ($mode != self::MODE_ONE_COLUMN && $cur_widget[$column_field_name] != $to_column) { //У виджета сменилась колонка
            
            //Обновляем порядковые номера в связи с уходом виджета из колонки
            \RS\Orm\Request::make()->update($this->obj_instance)
                    ->set("{$position_field_name} = {$position_field_name} - 1")
                    ->where([
                        'site_id' => $this->site_id,
                        'user_id' => $this->cur_user_id,
                        $column_field_name => $cur_widget[$column_field_name]
                    ])
                    ->where("{$position_field_name} > '#cur_pos'", ['cur_pos' => $cur_widget[$position_field_name]])
                    ->exec();

            //Последняя позиция в новой колонке            
            $next_position = $this->getNextPos($to_column, $mode);

            //Вставляем виджет в самый низ новой колонки
            \RS\Orm\Request::make()
                ->update($this->obj_instance)
                ->set([
                    $position_field_name => $next_position, 
                    $column_field_name => $to_column])
                ->where([
                    'site_id' => $this->site_id,
                    'user_id' => $this->cur_user_id,
                    'id' => $cur_widget['id']
                ])->exec();

            $cur_widget[$position_field_name] = $next_position;
            $cur_widget[$column_field_name] = $to_column;
        }
        return $next_position;
    }
    
    /**
    * Сортирует виджеты в рамках одной колонки
    * 
    * @param \Main\Model\Orm\Widgets $cur_widget - перемещаемый виджет
    * @param integer $to_column - колонка назначения
    * @param integer $to_pos - позиция назначения
    * @param integer $mode - режим колоночности
    * @return void
    */
    protected function sortInColumn($cur_widget, $to_column, $to_pos, $mode)
    {
        $column_field_name = $this->getColumnFieldName($mode);
        $position_field_name = $this->getPositionFieldName($mode);                
                
        //Определяем направлене перемещения 
        if ($cur_widget[$position_field_name] < $to_pos) {
            //Вниз
            $q = \RS\Orm\Request::make()
                    ->update($this->obj_instance)
                    ->set("{$position_field_name} = {$position_field_name}-1")
                    ->where([
                        'site_id' => $this->site_id,
                        'user_id' => $this->cur_user_id,
                    ])
                    ->where("{$position_field_name} > '#cur_pos' AND {$position_field_name} <= '#to_pos'",
                        ['cur_pos' => $cur_widget[$position_field_name], 'to_pos' => $to_pos]);
        } else { 
            //Вверх
            $q = \RS\Orm\Request::make()
                    ->update($this->obj_instance)
                    ->set("{$position_field_name} = {$position_field_name}+1")
                    ->where([
                        'site_id' => $this->site_id,
                        'user_id' => $this->cur_user_id,
                    ])
                    ->where("{$position_field_name} >= '#to_pos' AND {$position_field_name} < '#cur_pos'",
                        ['cur_pos' => $cur_widget[$position_field_name], 'to_pos' => $to_pos]);
        }
        
        if ($mode != self::MODE_ONE_COLUMN) {
            $q->where([
                $column_field_name => $to_column
            ]);
        }

        $q->exec();
        
        \RS\Orm\Request::make()
            ->update($this->obj_instance)
            ->set([
                $position_field_name => $to_pos
            ])
            ->where([
                'site_id' => $this->site_id,
                'user_id' => $this->cur_user_id,
                'id' => $cur_widget['id']
            ])
            ->exec();
    }
    
    /**
    * Возвращает следующую позицию в колонке
    * 
    * @param integer $column - номер колонки, начиная от 1
    * @param integer $mode - режим колоночности
    * 
    * @return void
    */
    function getNextPos($column, $mode)
    {
        $q = \RS\Orm\Request::make()
            ->select('MAX('.$this->getPositionFieldName($mode).')+1 as max')
            ->from($this->obj_instance)
            ->where([
                'site_id' => $this->site_id,
                'user_id' => $this->cur_user_id
            ]);

        if ($mode != self::MODE_ONE_COLUMN) {
            $q->where([
                $this->getColumnFieldName($mode) => $column
            ]);
        }

        return $q->exec()->getOneField('max', 0);
    }
    
    /**
    * Возвращает запись о виджете на рабочем столе
    * 
    * @param integer $id
    * @return \Main\Model\Orm\Widgets
    */
    function getWidgetRecord($id)
    {
        $widget = \RS\Orm\Request::make()->select('*')
            ->from($this->obj_instance)
            ->where([
                'id' => $id,
                'user_id' => $this->cur_user_id,
                'site_id' => $this->site_id,
            ])
            ->object();
        return $widget;
    }
    
    /**
    * Возвраает список всех виджетов в системе.
    * 
    * @param bool $appendInfo - если true, то будет добавлена дополнительная информация к виджетам.
    * @return array
    */
    function getFullList($appendInfo = false, $return_only_unused = false)
    {
        $module_folder_list = scandir(\Setup::$PATH.\Setup::$MODULE_FOLDER);
        $widgets = [];
        foreach($module_folder_list as $item) {
            if ($item != '.' && $item != '..' && $item != '.svn') {
                $widgets += $this->moduleWidgets($item);
            }
        }
        if ($appendInfo || $return_only_unused)
            $widgets = $this->appendInfo($widgets);

        if ($return_only_unused) {
            foreach ($widgets as $class => $widget) {
                if (!empty($widget['use']))
                    unset($widgets[$class]);
            }
        }

        return $widgets;
    }
    
    /**
    * Добавляет информацию из базы(используется ли виджет) к списку виджетов.
    * 
    * @param array $widget_list - список записей о виджетах из базы
    * @return array
    */
    protected function appendInfo(array $widget_list)
    {
        if (count($widget_list)) {
            $res = \RS\Orm\Request::make()->select("class") //Проверяем какие классы присутствуют у пользователя на "рабочем столе"
                    ->from($this->obj_instance)
                    ->where(['user_id' => $this->cur_user_id, 'site_id' => $this->site_id])
                    ->whereIn('class', array_keys($widget_list))
                    ->exec();
            
            while ($row = $res->fetchRow()) {
                $widget_list[$row["class"]]['use'] = 1;
            }
        }
        return $widget_list;
    }
    
    /**
    * Возвращает список виджетов у модуля или пустой массив, если модулей нет.
    * 
    * @param string $module - Имя модуля
    * @return array
    */
    function moduleWidgets($module)
    {
        $widget_folder = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.$module.'/'.$this->widget_folder;
        $widgets = [];
        if (is_dir($widget_folder)) {
            $files = scandir($widget_folder);
            foreach($files as $file)
                if ($file != '.' && $file != '..' && $file != '.svn') {
                    $filename = basename($file);
                    $widget_name = str_replace(['.my.'.\Setup::$CLASS_EXT, '.'.\Setup::$CLASS_EXT], '', $filename);
                    $widget_class = $module.'\\'.str_replace('/', '\\', $this->widget_folder).'\\'.$widget_name;

                    if ($this->issetWidget($widget_class)) {
                        $instance = new $widget_class();
                        $info = $instance->getWidgetInfo();
                        if (!in_array($info['short_class'], \Setup::$DISABLE_WIDGETS)) {
                            $widgets[$info['short_class']] = $info;
                        }
                        unset($instance);
                    }
                }
        }
        return $widgets;
    }
    
    /**
    * Возвращает HTML виджета, готовый для отображения
    * 
    * @param string $widget_controller - строковый идентификатор виджета
    * @param array $param
    */
    function getWidgetOut($widget_controller, $param = [])
    {
        //т.к. $widget_controller может быть получен из GET, проверяем родителя класса.
        if ($full_class_name = $this->issetWidget($widget_controller)) {
            $com = new $full_class_name($param);
            return $com->exec();
        }
        return false;
    }
    
    /**
    * Возвращает полное имя класса контроллера виджета или false, если контроллера не существует
    * 
    * @param string $widget_controller - строковый идентификатор виджета
    * @return string | false
    */
    function issetWidget($widget_controller)
    {
        $full_class_name = Orm\Widgets::staticGetFullClass($widget_controller);
        if (class_exists($full_class_name) && is_subclass_of($full_class_name, '\RS\Controller\Admin\Widget')) {
            return $full_class_name;
        }
        return false;
    }
    
    /**
    * Возвращает false, в случае если не ошибок, связанных с правами доступа,
    * в противном случае возвращает текст ошибки
    * 
    * @return bool(false) | string
    */
    function noWriteRights()
    {
        if (!\Setup::$INSTALLED) {
            return false;
        }
        return Rights::CheckRightError($this, ModuleRights::RIGHT_WIDGET_CONTROL);
    }
    
    /**
    * Возвращает объект виджета по названию класса
    * 
    * @param string $wclass
    * @return Orm\Widgets
    */
    function getWidgetByWClass($wclass)
    {
        return \RS\Orm\Request::make()
                    ->from($this->obj_instance)
                    ->where([
                        'class' => $wclass,
                        'site_id' => $this->site_id
                    ])->object();
    }

    /**
     * Возвращает возможные колоночные режимы
     *
     * @return array
     */
    function getColumnsMode()
    {
        return [
            self::MODE_ONE_COLUMN,
            self::MODE_TWO_COLUMN,
            self::MODE_THREE_COLUMN
        ];
    }

    /**
     * Пересчитывает порядковые номера всех виджетов во всех режимах колоночности
     * в рамках текущего пользователя и сайта
     * Актуально при удалении виджета
     *
     * @return bool
     */
    function reCalculatePositions()
    {
        $to_update = [];

        foreach($this->getColumnsMode() as $mode) {
            //Перебираем режимы колоночности
            $column_field_name = $this->getColumnFieldName($mode);
            $position_field_name = $this->getPositionFieldName($mode);

            for($column = 1; $column <= $mode; $column++) {
                //Перебираем колонки режима
                $q = \RS\Orm\Request::make()
                    ->select("id")
                    ->from($this->obj_instance)
                    ->orderby($position_field_name)
                    ->where([
                        'site_id' => $this->site_id,
                        'user_id' => $this->cur_user_id,
                    ]);

                if ($column > 1) {
                    $q->where([$column_field_name => $column]);
                }
                $original_sort = $q->exec()->fetchSelected(null, 'id');

                $new_sortn = 0;
                foreach($original_sort as $id) {
                    $to_update[$id][$position_field_name] = $new_sortn;
                    $new_sortn++;
                }
            }
        }

        foreach($to_update as $widget_id => $update_fields) {
            \RS\Orm\Request::make()
                ->update($this->obj_instance)
                ->set($update_fields)
                ->where([
                    'id' => $widget_id
                ])->exec();
        }

        return true;
    }
}
