<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Orm\Property;

use Catalog\Model\PropertyApi;
use Catalog\Model\PropertyValueApi;
use RS\Cache\Manager as CacheManager;
use RS\Event\Manager as EventManager;
use RS\Exception as RSException;
use RS\Http\Request as HttpRequest;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\View\Engine as ViewEngine;

/**
 * Core-объект характеристика товара
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название характеристики
 * @property string $alias Англ. псевдоним
 * @property string $type Тип
 * @property string $description Описание
 * @property integer $sortn Сорт. индекс
 * @property integer $parent_sortn Сорт. индекс группы
 * @property string $unit Единица измерения
 * @property string $unit_export Размерная сетка
 * @property string $name_for_export Имя, выгружаемое на Яндекс маркет
 * @property string $xml_id Идентификатор товара в системе 1C
 * @property integer $group_id 
 * @property integer $product_id 
 * @property double $interval_from Минимальное значение
 * @property double $interval_to Максимальное значение
 * @property double $step Шаг
 * @property integer $parent_id Группа
 * @property integer $int_hide_inputs Скрывать поля ввода границ диапазона
 * @property integer $hidden Не отображать в карточке товара
 * @property integer $no_export Не экспортировать
 * @property integer $public 
 * @property integer $is_expanded 
 * @property integer $useval 
 * @property array $allowed_values 
 * @property array $allowed_values_objects 
 * @property string $values Возможные значения
 * --\--
 */
class Item extends OrmObject
{
    // Стандартные типы характеристик
    const TYPE_NUMERIC = 'int';
    const TYPE_STRING = 'string';
    const TYPE_LIST = 'list';
    const TYPE_RADIO_LIST = 'radio';
    const TYPE_BOOL = 'bool';
    const TYPE_COLOR = 'color';
    const TYPE_IMAGE = 'image';
    // Поля БД, для записи значений характеристик
    const FIELD_FLOAT = 'val_int';
    const FIELD_STRING = 'val_str';
    const FIELD_LIST = 'val_list_id';

    protected static $table = 'product_prop';

    protected $_cache_is_list;
    protected $before_this;
    protected $slider_data;

    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar([
                    'maxLength' => '255',
                    'index' => true,
                    'description' => t('Название характеристики'),
                    'checker' => ['chkEmpty', t('Название характеристики не должно быть пустым')],
                    'meVisible' => false,
                    'attr' => [[
                        'data-autotranslit' => 'alias'
                    ]]
                ]),
                'alias' => new Type\Varchar([
                    'description' => t('Англ. псевдоним'),
                    'checker' => ['chkalias', null],
                    'hint' => t('Используется при включенной функции "Включить ЧПУ фильтры?" в настройках модуля каталог')
                ]),
                'type' => new Type\Varchar([
                    'maxLength' => '10',
                    'description' => t('Тип'),
                    'Attr' => [['size' => 1]],
                    'List' => [[__CLASS__, 'getAllowTypeValues']],
                    'meVisible' => false,
                    'template' => '%catalog%/form/property/type.tpl',
                    'default' => self::TYPE_STRING
                ]),
                'description' => new Type\Text([
                    'description' => t('Описание'),
                ]),
                'sortn' => new Type\Integer([
                    'maxLength' => '11',
                    'description' => t('Сорт. индекс'),
                    'visible' => false,
                ]),
                'parent_sortn' => new Type\Integer([
                    'description' => t('Сорт. индекс группы'),
                    'visible' => false,
                ]),
                'unit' => new Type\Varchar([
                    'maxLength' => '30',
                    'description' => t('Единица измерения'),
                ]),

                'unit_export' => new Type\Varchar([
                    'maxLength' => '30',
                    'description' => t('Размерная сетка'),
                ]),
                'name_for_export' => new Type\Varchar([
                    'maxLength' => '30',
                    'description' => t('Имя, выгружаемое на Яндекс маркет'),
                ]),
                'xml_id' => new Type\Varchar([
                    'maxLength' => '255',
                    'description' => t('Идентификатор товара в системе 1C'),
                    'visible' => false,
                ]),
                'group_id' => new Type\Integer([
                    'maxLength' => '11',
                    'runtime' => true,
                    'visible' => false,
                ]),
                'product_id' => new Type\Integer([
                    'maxLength' => '11',
                    'runtime' => true,
                    'visible' => false,
                ]),

                'interval_from' => new Type\Real([
                    'description' => t('Минимальное значение'),
                    'Attr' => [['size' => 8]],
                    'visible' => false,
                    'runtime' => true
                ]),
                'interval_to' => new Type\Real([
                    'description' => t('Максимальное значение'),
                    'Attr' => [['size' => 8]],
                    'visible' => false,
                    'runtime' => true
                ]),
                'step' => new Type\Real([
                    'description' => t('Шаг'),
                    'Attr' => [['size' => 3]],
                    'visible' => false,
                    'runtime' => true
                ]),

                'parent_id' => new Type\Integer([
                    'description' => t('Группа'),
                    'allowEmpty' => false,
                    'List' => [['\Catalog\Model\PropertyDirApi', 'selectList']],
                ]),
                'int_hide_inputs' => new Type\Integer([
                    'description' => t('Скрывать поля ввода границ диапазона'),
                    'checkboxView' => [1, 0],
                    'maxLength' => 1,
                    'default' => 0
                ]),
                'hidden' => new Type\Integer([
                    'description' => t('Не отображать в карточке товара'),
                    'checkboxView' => [1, 0],
                    'maxLength' => 1,
                    'default' => 0
                ]),
                'no_export' => new Type\Integer([
                    'description' => t('Не экспортировать'),
                    'checkboxView' => [1, 0],
                    'maxLength' => 1,
                    'default' => 0
                ]),
                'value' => new Type\MixedType([
                    'runtime' => true,
                    'visible' => false
                ]),
                'is_my' => new Type\MixedType([
                    'runtime' => true,
                    'visible' => false
                ]),
                'public' => new Type\Integer([
                    'runtime' => true,
                    'visible' => false
                ]),
                'is_expanded' => new Type\Integer([
                    'runtime' => true,
                    'visible' => false
                ]),
                'useval' => new Type\Integer([
                    'runtime' => true,
                    'visible' => false
                ]),
                'allowed_values' => new Type\ArrayList([
                    'visible' => false
                ]),
                'allowed_values_objects' => new Type\ArrayList([
                    'visible' => false
                ]),
                'values' => new Type\Text([
                    'runtime' => true,
                    'description' => t('Возможные значения'),
                    'visible' => false
                ]),
            t('Значения'),
                '__property_values__' => new Type\UserTemplate('%catalog%/form/property/value_items.tpl'),
        ]);

        $this['__id']->setVisible(true);
        $this['__id']->setMeVisible(false);
        $this['__id']->setHidden(true);

        $this->addIndex(['site_id', 'alias'], self::INDEX_UNIQUE);
        $this->addIndex(['site_id', 'xml_id'], self::INDEX_UNIQUE);
    }

    /**
     * Действия перед записью характеристики
     *
     * @param string $flag - insert или update
     * @return false|null|void
     */
    function beforeWrite($flag)
    {
        if ($this['id'] < 0) {
            $this['_tmpid'] = $this['id'];
            unset($this['id']);
        }

        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = OrmRequest::make()
                    ->select('MAX(sortn) as max')
                    ->from($this)
                    ->exec()->getOneField('max', 0) + 1;
        }

        if ($this['parent_id'] !== null) {
            if ($this['parent_id'] > 0) {
                $parent_sortn = OrmRequest::make()
                    ->select('sortn')
                    ->from(new Dir())
                    ->where([
                        'id' => $this['parent_id']
                    ])->exec()->getOneField('sortn', 0);
            } else {
                $parent_sortn = 0;
            }
            $this['parent_sortn'] = $parent_sortn; //Сохраняем родительский сорт. индекс            
        }

        if ($this['alias'] == '') {
            unset($this['alias']);
        }

        if ($flag == self::UPDATE_FLAG) {
            $this->before_this = new self($this['id']);
        }
    }

    /**
     * Функция срабатывает после сохранения характеристики
     *
     * @param string $flag - update или insert
     */
    function afterWrite($flag)
    {
        if ($flag == self::UPDATE_FLAG) {
            CacheManager::obj()->invalidateByTags(CACHE_TAG_UPDATE_CATEGORY);
        }

        if ($this['_tmpid'] < 0) {
            OrmRequest::make()
                ->update(new ItemValue())
                ->set(['prop_id' => $this['id']])
                ->where([
                    'prop_id' => $this['_tmpid']
                ])->exec();
        }

        if ($this->isModified('values')) {
            //Для совместимости добавляем значения по старому
            $property_api = new PropertyValueApi();
            $property_api->addSomeValues([
                'prop_id' => $this['id'],
                'values' => $this['values']
            ]);
        }

        if (isset($this->before_this)) {
            if ($this['type'] != $this->before_this->type) {
                //Конвертируем значния при смене типа характеристики
                $property_value_api = new PropertyValueApi();
                $property_value_api->convertPropertyType($this, $this->before_this->type, $this['type']);
            }
        }
    }

    /**
     * Возвращает псевдоним объекта или его id
     *
     * @return integer|string
     */
    function getAliasOrId()
    {
        return (!empty($this['alias'])) ? $this['alias'] : $this['id'];
    }

    /**
     * Возвращает массив с возможными значениями характеристики
     *
     * @param array $value_ids - список ID, значения для которых нужно вернуть
     * @param mixed $cache - Если true, то включено кэширование результатов
     * @return array
     */
    function valuesArr(array $value_ids = null, $cache = true)
    {
        static $cached;

        if (!$cache || !isset($cached[$this['id']])) {
            $cached[$this['id']] = OrmRequest::make()
                ->from(new ItemValue())
                ->where([
                    'prop_id' => $this['id']
                ])
                ->where("value != ''")
                ->orderby('sortn')
                ->exec()->fetchSelected('id', 'value');
        }

        return $value_ids ? array_intersect_key($cached[$this['id']], array_flip($value_ids)) : $cached[$this['id']];
    }

    /**
     * Возвращает объект группы, в которой состояит характеристика
     *
     * @return Dir
     */
    function getDir()
    {
        $dir = new Dir($this['parent_id']);
        if (!$this['parent_id']) {
            $dir['title'] = t('Без группы');
        }
        return $dir;
    }

    /**
     * Возвращает список имеющихся значений характеристики на основе предварительно загруженных сведений.
     * В случае, если данные не были загружены, то возвращаются все возможные значения
     *
     * @return array
     */
    function getAllowedValues()
    {
        return isset($this['allowed_values']) ? $this['allowed_values'] : $this->valuesArr();
    }

    /**
     * Возвращает список объектов имеющихся значений характеристики на основе предварительно загруженных сведений.
     * В случае, если данные не были загружены, то возвращаются все возможные значения
     *
     * @return ItemValue[]
     */
    function getAllowedValuesObjects()
    {
        if (isset($this['allowed_values_objects'])) {
            return $this['allowed_values_objects'];
        }

        /** @var ItemValue[] $result */
        $result = OrmRequest::make()
            ->from(new ItemValue())
            ->where([
                'prop_id' => $this['id']
            ])
            ->orderby('sortn')
            ->objects(null, 'id');

        return $result;
    }

    /**
     * Сортирует значения списковой характеристики
     *
     */
    function sortValues()
    {
        //Развернём значения
        $values = array_flip($this->getAllowedValues());
        $key_values = array_keys($values);
        //Отсортируем по ключам
        natsort($key_values);

        //Пройдемся по массиву для обновления
        $m = 0;
        foreach ($key_values as $key) {
            $m++;
            OrmRequest::make()
                ->update()
                ->from(new ItemValue())
                ->set([
                    'sortn' => $m
                ])->where([
                    'id' => $values[$key]
                ])->exec();
        }
    }

    /**
     * Возвращает HTML-форму для выбора значения характеристики в административной части
     *
     * @param mixed $disabled
     * @return string
     * @throws \SmartyException
     */
    function valView($disabled = null)
    {
        $tpl = new ViewEngine();
        $tpl->assign('self', $this);
        $tpl->assign('value', is_null($this['value']) ? $this['defval'] : $this['value']);
        $tpl->assign('disabled', ($disabled) ? 'disabled="disabled"' : '');
        return $tpl->fetch('%catalog%property_val.tpl');
    }

    /**
     * Возвращает читабельное значение характеристики
     *
     * @param boolean $list_available - флаг отвечаеющий за то, что если характеристика списковая, то будут показаны только те хар-ки, которые есть в наличии у комплектаций
     *
     * @return string
     */
    function textView($list_available = false)
    {
        $val = is_null($this['value']) ? $this['defval'] : $this['value'];

        if ($this['type'] == self::TYPE_BOOL) {
            return $val ? t('есть') : t('нет');
        }

        if ($this->isListType()) {
            return implode(', ', !$list_available ? (array)$this['value_in_string'] : (array)$this['available_value_in_string']);
        }
        return $val;
    }

    /**
     * Удаляет текущую характеристику
     *
     * @return bool Возвращает true, в случае успеха
     */
    function delete()
    {
        //При удалении свойства, удаляем все связи
        OrmRequest::make()
            ->delete()
            ->from(new Link())
            ->where([
                'prop_id' => $this['id']
            ])
            ->exec();

        //Удаляем все значения характеристик
        OrmRequest::make()
            ->delete()
            ->from(new ItemValue())
            ->where([
                'prop_id' => $this['id']
            ])
            ->exec();

        return parent::delete();
    }

    /**
     * Если тип характеристики - да/нет (checkbox), возвращает true, если отмечен
     */
    function checked()
    {
        if ($this['type'] != 'bool') return false;
        return $this['value'] == 1;
    }

    /**
     * Возвращает единицы измерения для ползунка в JavaScript
     */
    function getUnit()
    {
        return '&nbsp;' . $this['unit'];
    }

    /**
     * Возвращает данные для потроения шкалы ползунка в JavaScript
     */
    function getScale()
    {
        $data = $this->sliderData();
        return $data['scale'];
    }

    /**
     * Возвращает связку процент/значение для шкалы позунка в JavaScript
     */
    function getHeterogeneity()
    {
        $data = $this->sliderData();
        return $data['heterogeneity'];
    }

    /**
     * Возвращает порядок округления
     */
    function getRound()
    {
        $dec = strstr($this['step'], ".");
        return ($dec === false) ? 0 : strlen($dec) - 1;
    }

    /**
     * Расчитывает данные для ползунка фильтра
     */
    function sliderData()
    {
        if (!isset($this->slider_data)) {
            $step = ($this['step'] != 0) ? $this['step'] : 1;

            $delta = ($this['interval_to'] - $this['interval_from']); //разница значений
            $points = floor($delta / $step); //количество значений.

            $scale = []; //шкала
            $heterogeneity = []; //связь процентов (0-100) со значениями.
            if ($points) {
                $max = 5;
                if ($points < $max) $max = $points;

                for ($i = 0; $i <= $max; $i++) {
                    $percent = (100 / $max * $i);
                    $point = round(($percent / 100) * $delta + $this['interval_from'], $this->getRound());
                    $scale[] = $point;
                    if ($percent > 0 && $percent < 100) {
                        $heterogeneity[] = "\"$percent/$point\"";
                    }
                }
            }

            $this->slider_data = [
                'scale' => implode(',', $scale),
                'heterogeneity' => implode(',', $heterogeneity),
                'delta' => $delta,
                'points' => $points
            ];
        }
        return $this->slider_data;
    }

    /**
     * Возвращает Url для установки или снятия данного фильтра
     *
     * @param string $filter_var - Массив с установленными фильтрами
     * @param array $value - значение фильтра, на которое нужна ссылка
     * @return string
     * @throws RSException
     */
    function getUrl($filter_var, $value)
    {
        $url = HttpRequest::commonInstance();
        $api = new PropertyApi();
        $filter = $url->get($filter_var, TYPE_ARRAY);
        $filter = $api->cleanNoActiveFilters($filter);
        $my_filter = isset($filter[$this['id']]) ? $filter[$this['id']] : [];

        if ($this['type'] == 'list') {
            $key = array_search($value, $my_filter);
            if ($key !== false) {
                //Снимаем фильтр
                unset($my_filter[$key]);
            } else {
                $my_filter[] = $value;
            }
        }

        return $url->replaceKey([$filter_var => [$this['id'] => $my_filter] + $filter]);
    }

    /**
     * Возвращает имя поля в объекте Link, в котором находится актуальное значение для текущей характеристики
     *
     * @return string
     */
    function getValueLinkField()
    {
        if ($this->isListType()) {
            return 'val_list_id';
        } elseif ($this['type'] == self::TYPE_BOOL
            || $this['type'] == self::TYPE_NUMERIC) {
            return 'val_int';
        } else {
            return 'val_str';
        }
    }

    /**
     * Возвращает true, если характеристика является списковой
     *
     * @return bool
     */
    function isListType()
    {
        if ($this->_cache_is_list === null) {
            $types = self::getAllowTypeData();
            $this->_cache_is_list = $types[$this['type']]['is_list'];
        }
        return $this->_cache_is_list;
    }

    /**
     * Возвращает массив типов характеристик, которые являются списковыми
     *
     * @return array
     */
    public static function getListTypes()
    {
        static
        $result;

        if ($result === null) {
            $result = [];
            foreach (self::getAllowTypeData() as $key => $type) {
                if ($type['is_list']) {
                    $result[] = $key;
                }
            }
        }
        return $result;
    }

    /**
     * Возвращает список возможных типов характеристик
     *
     * @return array
     */
    public static function getAllowTypeValues()
    {
        $result = [];
        foreach (self::getAllowTypeData() as $key => $data) {
            $result[$key] = $data['title'];
        }

        return $result;
    }

    /**
     * Возвращает расширенную информацию о типах списков
     *
     * @return array
     */
    public static function getAllowTypeData()
    {
        static
        $local_cache;

        if ($local_cache === null) {
            $base_types = [
                self::TYPE_NUMERIC => [
                    'title' => t('Число'),
                    'is_list' => false,
                    'save_field' => self::FIELD_FLOAT
                ],
                self::TYPE_STRING => [
                    'title' => t('Строка'),
                    'is_list' => false,
                    'save_field' => self::FIELD_STRING
                ],
                self::TYPE_BOOL => [
                    'title' => t('Да/Нет'),
                    'is_list' => false,
                    'save_field' => self::FIELD_FLOAT
                ],
                self::TYPE_LIST => [
                    'title' => t('Список'),
                    'is_list' => true,
                    'save_field' => self::FIELD_LIST
                ],
                self::TYPE_RADIO_LIST => [
                    'title' => t('Список(радиокнопки)'),
                    'is_list' => true,
                    'save_field' => self::FIELD_LIST
                ],
                self::TYPE_COLOR => [
                    'title' => t('Список цветов'),
                    'is_list' => true,
                    'save_field' => self::FIELD_LIST
                ],
                self::TYPE_IMAGE => [
                    'title' => t('Список изображений'),
                    'is_list' => true,
                    'save_field' => self::FIELD_LIST
                ]
            ];

            //Из сторонних содулей можно дополнить список, обработав событие
            $event_result = EventManager::fire('property.gettypes', []);
            $local_cache = $base_types + $event_result->getResult();
        }

        return $local_cache;
    }

    /**
     * Возвращает клонированный объект характеристики
     * @return self
     */
    function cloneSelf()
    {
        $clone = parent::cloneSelf();
        unset($clone['xml_id']);
        return $clone;
    }
}