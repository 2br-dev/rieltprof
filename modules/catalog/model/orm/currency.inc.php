<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model\Orm;
use \RS\Orm\Type;

/**
 * Объект - валюта
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Трехсимвольный идентификатор валюты (Ан. яз)
 * @property string $stitle Символ валюты
 * @property integer $is_base Это базовая валюта?
 * @property double $ratio Коэффициент относительно базовой валюты
 * @property integer $public Видимость
 * @property integer $default Выбирать по-умолчанию
 * @property integer $reconvert Пересчитать все цены
 * @property double $percent Увеличивать/уменьшать курс на %
 * --\--
 */
class Currency extends \RS\Orm\OrmObject
{
    protected static
        $table = 'currency';
        
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar([
                    'maxLength' => '3',
                    'description' => t('Трехсимвольный идентификатор валюты (Ан. яз)'),
                    'checker' => ['chkEmpty', t('Идентификатор - обязательное поле')]
                ]),
                'stitle' => new Type\Varchar([
                    'maxLength' => 10,
                    'description' => t('Символ валюты')
                ]),
                'is_base' => new Type\Integer([
                    'description' => t('Это базовая валюта?'),
                    'hint' => t('Флажок в данном поле означает, что цены товаров в системе указаны в данной валюте'),
                    'checkboxview' => [1,0]
                ]),
                'ratio' => new Type\Real([
                    'description' => t('Коэффициент относительно базовой валюты')
                ]),
                'public' => new Type\Integer([
                    'description' => t('Видимость'),
                    'checkboxview' => [1,0]
                ]),
                'default' => new Type\Integer([
                    'description' => t('Выбирать по-умолчанию'),
                    'checkboxview' => [1,0]
                ]),
                'reconvert' => new Type\Integer([
                    'description' => t('Пересчитать все цены'),
                    'checkboxview' => [1,0],
                    'runtime' => true,
                    'appVisible' => false
                ]),
            t('Обновление курсов'),
                'percent' => new Type\Real([
                    'description' => t('Увеличивать/уменьшать курс на %'),
                    'hint' => t('Можно указывать число как с положительно так и отрицательное. Действует при нажатии "Получить курс ЦБ РФ".'),
                    'default' => 0,
                    'appVisible' => false
                ])

        ]);
        $this->addIndex(['title', 'site_id'], self::INDEX_UNIQUE);
    }
    
    function beforeWrite($flag)
    {

    }
    
    /**
    * Действие после сохраниния объектра
    * 
    * @param string $flag - строковый флаг текущей оперпции (insert,update)
    */
    function afterWrite($flag)
    {
        if ($this['default'] == 1) {
            //Валюты по-умолчанию может быть только одна
            \RS\Orm\Request::make()->update($this)->set([
                'default' => 0
            ])->where(['site_id' => $this['site_id']])
                ->where("id != ".$this['id'])
                ->exec();
        }
        if ($this['reconvert']) {
            \Catalog\Model\CostApi::recalculateCosts($this['site_id'], $this);
        }
    }
    
    function delete()
    {
        $count = \RS\Orm\Request::make()->from($this)
            ->where(['site_id' => \RS\Site\Manager::getSiteId()])->count();
        
        if ($count>1) {
            return parent::delete();
        } else {
            return $this->addError(t('Должна присутствовать хотя бы одна валюта'));
        }
        
    }
}

