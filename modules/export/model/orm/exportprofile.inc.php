<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\Orm;

use Catalog\Model\Orm\Property\Dir as PropertyDir;
use Catalog\Model\ProductDialog;
use Catalog\Model\PropertyApi;
use Export\Model\Api as ExportApi;
use Export\Model\ExportType\AbstractType as AbstractExportType;
use Export\Model\Orm\Vk\VkCategory;
use Export\Model\Orm\Vk\VkCategoryLink;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;
use Shop\Model\Orm\Order;

/**
 * ORM объект - профиль экспорта товаров
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название
 * @property string $alias URL имя
 * @property string $class Класс экспорта
 * @property integer $life_time Период экспорта
 * @property string $url_params Дополнительные параметры для ссылки на товар
 * @property string $_serialized 
 * @property array $data 
 * @property integer $is_exporting Флаг незавершенного экспорта
 * @property integer $is_enabled Включен
 * --\--
 */
class ExportProfile extends OrmObject
{
    protected static $table = 'export_profile';

    protected $order;
    protected $type_object;

    function __construct($id = null, $cache = true, Order $order = null)
    {
        parent::__construct($id, $cache);
        $this->order = $order;
    }

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'title' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Название'),
            ]),
            'alias' => new Type\Varchar([
                'maxLength' => '150',
                'description' => t('URL имя'),
                'hint' => t('Могут использоваться только английские буквы, цифры, знак подчеркивания, запятая, точка и минус'),
                'meVisible' => false,
            ]),
            '__gate_url__' => new Type\MixedType([
                'visible' => true,
                'description' => t('URL для экспорта'),
                'template' => '%export%/form/profile/url.tpl'
            ]),
            'class' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Класс экспорта'),
                'list' => [['\Export\Model\Api', 'getTypesAssoc']],
                'visible' => false,
            ]),
            'life_time' => new Type\Integer([
                'description' => t('Период экспорта'),
                'listfromarray' => [[
                    0 => t('При каждом обращении'),
                    180 => t('3 часа'),
                    360 => t('6 часов'),
                    720 => t('12 часов'),
                    1440 => t('1 день'),
                    7200 => t('5 дней'),
                    14000 => t('10 дней'),
                    28000 => t('20 дней'),
                ]],
            ]),
            'url_params' => new Type\Varchar([
                'maxLength' => 255,
                'description' => t('Дополнительные параметры<br/> для ссылки на товар'),
                'hint' => t('Необязательно. Параметры будут добавлены после знака ? в ссылке на товар. Данное поле можно использовать, например, для добавления utm метки к ссылке на товар.'),
            ]),
            '_serialized' => new Type\Text([
                'visible' => false,
            ]),
            'data' => new Type\ArrayList([
                'visible' => false
            ]),
            'is_exporting' => new Type\Integer([
                'description' => t('Флаг незавершенного экспорта'),
                'visible' => false,
            ]),
            'is_enabled' => new Type\Integer([
                'description' => t('Включен'),
                'default' => 1,
                'maxLength' => 1,
                'checkboxView' => [1,0]
            ])
        ]);
    }

    function beforeWrite($flag)
    {
        $this['_serialized'] = serialize($this['data']);
    }

    /**
     * @throws \Exception
     */
    function afterObjectLoad()
    {
        $this['data'] = @unserialize($this['_serialized']);
        $this->initClass();
    }

    /**
     * Возвращает объект профиля экспорта
     *
     * @return AbstractExportType|null
     * @throws \RS\Exception
     */
    public function getTypeObject()
    {
        if ($this->type_object === null) {
            if ($this->type_object = ExportApi::getTypeByShortName($this['class'])) {
                $this->type_object->getFromArray((array)$this['data']);
                $this->type_object->setExportProfile($this);
            }
        }
        return $this->type_object;
    }

    /**
     * @throws \Exception
     */
    function initClass()
    {
        $type = ExportApi::getTypeByShortName($this['class']);
        if ($type) {
            $type->setExportProfile($this);
            $this->getPropertyIterator()->appendPropertyIterator($type->getPropertyIterator()
                ->arrayWrap('data')
                ->setPropertyOptions(['runtime' => true]),
                true
            );

            foreach ((array)$this['data'] as $key => $value) {
                $this[$key] = $value;
            }
        }
    }

    /**
     * Возвращает готовый HTML для ячейки с ссылкой на обмен таблицы
     *
     * @param ExportProfile $export
     * @return string
     */
    public function getExportActionCell()
    {
        return $this->getTypeObject() ? $this->getTypeObject()->getExportActionCell($this) : null;
    }


    /**
     * Возвращает HTML код для блока "список товаров"
     */
    function getProductsDialog()
    {
        return new ProductDialog('data[products]', false, @(array)$this['data']['products']);
    }

    /**
     * Возвращает все категории свойств товаров
     *
     * @return PropertyDir[]
     */
    function getAllPropertyGroups()
    {
        return PropertyApi::getAllGroups();
    }

    /**
     * Удаляет объект из хранилища
     *
     * @return boolean - true, в случае успеха
     * @throws \RS\Exception
     */
    public function delete()
    {
        if ($result = parent::delete()) {
            //Удаляем связи
            Request::make()
                ->delete()
                ->from(new VkCategory())
                ->where([
                    'profile_id' => $this['id']
                ])->exec();

            Request::make()
                ->delete()
                ->from(new VkCategoryLink())
                ->where([
                    'profile_id' => $this['id']
                ])->exec();

            Request::make()
                ->delete()
                ->from(new ExternalProductLink())
                ->where([
                    'profile_id' => $this['id']
                ])->exec();
        }
        return $result;
    }

    /**
     * Производит валидацию текущих данных в свойствах
     *
     * @return bool Возвращает true, если нет ошибок, иначе - false
     */
    function validate()
    {
        //Решение проблемы валидации полей при создании нового профиля экспорта
        foreach ((array)$this['data'] as $key => $value) {
            $this[$key] = $value;
        }

        return parent::validate();
    }
}
