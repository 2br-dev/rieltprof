<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Orm;

use Catalog\Model\CostApi;
use RS\Config\Loader as ConfigLoader;
use RS\Event\Exception as EventException;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\Site\Manager as SiteManager;

/**
 * Класс типов цены
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $xml_id Идентификатор в системе 1C
 * @property string $title Название
 * @property string $type Тип цены
 * @property string $val_znak Знак значения
 * @property double $val Величина увеличения стоимости
 * @property string $val_type Тип увеличения стоимости
 * @property integer $depend Цена, от которой ведется расчет
 * @property float $round Округление
 * @property integer $old_cost Старая(зачеркнутая) цена
 * --\--
 */
class Typecost extends OrmObject
{
    const TYPE_MANUAL = 'manual'; //Тип цен вручную
    const TYPE_AUTO = 'auto';   //Автоматический тип цен

    protected static $table = 'product_typecost';

    protected static $xcost;

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'xml_id' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Идентификатор в системе 1C'),
                'visible' => false,
            ]),
            'title' => new Type\Varchar([
                'maxLength' => '150',
                'description' => t('Название'),
            ]),
            'type' => new Type\Enum([self::TYPE_MANUAL, self::TYPE_AUTO], [
                'listfromarray' => [[
                    self::TYPE_MANUAL => 'manual',
                    self::TYPE_AUTO => 'auto'
                ]],
                'default' => self::TYPE_MANUAL,
                'description' => t('Тип цены'),
                'hint' => t('Допустимо использование отрицательные и дробные значения. <br>Например: -35.5%'),
                'template' => '%catalog%/form/typecost/typecost.tpl'
            ]),
            'val_znak' => new Type\Varchar([
                'description' => t('Знак значения'),
                'maxLength' => '1',
                'Attr' => [['size' => '1']],
                'ListFromArray' => [[
                    '+' => '+',
                    '-' => '-'
                ]],
                'visible' => false,
            ]),
            'val' => new Type\Real([
                'maxLength' => '11',
                'description' => t('Величина увеличения стоимости'),
                'Attr' => [['size' => '5']],
                'visible' => false,
            ]),
            'val_type' => new Type\Enum(['sum', 'percent'], [
                'description' => t('Тип увеличения стоимости'),
                'ListFromArray' => [[
                    'sum' => t('единиц'),
                    'percent' => '%'
                ]],
                'visible' => false,
            ]),
            'depend' => new Type\Integer([
                'maxLength' => '11',
                'description' => t('Цена, от которой ведется расчет'),
                'List' => [['\Catalog\Model\CostApi', 'getManualCostList']],
                'visible' => false,
            ]),
            'round' => new Type\Decimal([
                'description' => t('Округление'),
                'hint' => t('Дробная часть указывается через точку<br/>
                            Округление происходит <b>в большую сторону</b>,<br/>
                            результат округления кратен значению:<br/>
                            <b>1</b> - округлять до целых (13,5678 = 14)<br/>
                            <b>0.1</b> - до десятых (13,5678 = 13,6)<br/>
                            <b>10</b> - до десятков (13,5678 = 20)<br/>
                            <b>5</b> - до кратного пяти (13,5678 = 15).<br/><br/>'),
                'appVisible' => false,
                'decimal' => 2,
            ]),
            'old_cost' => new Type\Integer([
                'description' => t('Старая(зачеркнутая) цена'),
                'list' => [['\Catalog\Model\CostApi', 'staticSelectList'], [0 => t('- По умолчанию -')]],
                'default' => 0,
                'allowEmpty' => false,
            ]),
            '_calcvalue' => new Type\MixedType([
                'description' => t('Расчитанное значение цены для товара')
            ]),
        ]);

        $this->addIndex(['site_id', 'xml_id'], self::INDEX_UNIQUE);
    }

    /**
     * Возвращает можно ли сделать данный тип цен автоматически вычисляемым
     *
     * @return bool
     */
    function mayBecomeAuto()
    {
        $api = new CostApi();
        $api->setFilter([
            'type' => self::TYPE_MANUAL,
        ]);
        if (!empty($this['id'])) {
            $api->setFilter([
                'id:!=' => $this['id'],
            ]);
        }
        return $api->getListCount() > 0;
    }

    /**
     * Функция срабатывает после записи объекта
     *
     * @param string $saveflag - флаг текущего действия. update или insert.
     * @return void
     */
    function afterWrite($saveflag)
    {
        if ($saveflag == self::UPDATE_FLAG) {
            if ($this['type'] == self::TYPE_AUTO) $this->checkDepends();
        }

        // Если цена по умолчанию в настройках модуля не задана 
        //или задана неверно, то устанавливаем эту ($this) цену по молчинию
        $config = ConfigLoader::byModule($this);
        $is_cost_correct = $this->exists($config['default_cost']);
        if (!$is_cost_correct) {
            $config['default_cost'] = $this['id'];              // Сохраняем эту ($this) цену в конфиг модуля
            $config->update();
        }
    }

    /**
     * Удаляет объект из хранилища
     *
     * @return bool
     */
    function delete()
    {
        $site_id = SiteManager::getSiteId();
        $count = OrmRequest::make()->from($this)
            ->where(['site_id' => $site_id])->count();

        if ($count > 1) {
            OrmRequest::make()
                ->delete()
                ->from(new Xcost())
                ->where(['cost_id' => $this['id']])
                ->exec();

            // Изменим цены в настройках модулей
            $catalog_config = ConfigLoader::byModule('catalog');
            $shop_config = ConfigLoader::byModule('shop');
            if ($catalog_config['default_cost'] == $this['id']) {
                $catalog_config['default_cost'] = OrmRequest::make()
                    ->from($this)
                    ->exec()
                    ->getOneField('id');
                $update = true;
            }
            if ($catalog_config['old_cost'] == $this['id']) {
                $catalog_config['old_cost'] = 0;
                $update = true;
            }
            if ($shop_config['source_cost'] == $this['id']) {
                $shop_config['source_cost'] = 0;
                $shop_config->update();
            }
            if ($update ?? false) {
                $catalog_config->update();
            }

            //Перед удалением, переводим зависимые элементы к типу "Задается вручную"
            $this->checkDepends();
            return parent::delete();
        } else {
            return $this->addError(t('Должен присутствовать хотя бы один тип цен'));
        }
    }

    /**
     * Переводит зависимые элементы к типу "Задается вручную", если еобходимо
     *
     * @return void
     */
    function checkDepends()
    {
        OrmRequest::make()
            ->update($this)
            ->set(['type' => self::TYPE_MANUAL])
            ->where(['depend' => $this['id']])
            ->exec();
    }

    /**
     * Возвращает значение, округленное до параметров, заданное для данного типа цен.
     *
     * @param float $value
     * @param string $round_type
     * @return float|int
     */
    public function getRounded($value, $round_type = CostApi::CEIL)
    {
        $round = (float)$this['round'];

        if ($round) {
            switch ($round_type) {
                case CostApi::CEIL:
                    return ceil($value / $round) * $round;
                case CostApi::ROUND:
                    return round($value / $round, 0) * $round;
                case CostApi::FLOOR:
                    return floor($value / $round) * $round;
            }
        }

        return $value;
    }


    /**
     * Возвращает цену, от которой зависит текущая цена
     *
     * @return TypeCost
     */
    function getDependCost()
    {
        return new self($this['depend']);
    }

    /**
     * Возвращает клонированный объект оплаты
     *
     * @return Typecost
     * @throws EventException
     */
    function cloneSelf()
    {
        $clone = parent::cloneSelf();
        $clone['xml_id'] = null;
        return $clone;
    }

    /**
     * Исключает цену $cost_id из списка в поле depend
     * Используется в настройке зависимой цены
     *
     * @param integer $cost_id
     * @return void
     */
    function excludeCostFromDepend($cost_id)
    {
        $list = $this['__depend']->getList();
        foreach($list as $id => $title) {
            if ($id == $cost_id) {
                unset($list[$id]);
            }
        }

        $this['__depend']->setList(null);
        $this['__depend']->setListFromArray($list);
    }
}
