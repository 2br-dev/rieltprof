<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Model\Orm;
use \RS\Orm\Type;

/**
 * Объект описывает
 * --/--
 * @property integer $site_id ID сайта
 * @property string $context Контекст темы оформления
 * @property string $hook_name Идентификатор хука
 * @property string $module Идентификатор модуля
 * @property string $sortn Порядковый номер
 * --\--
 */
class TemplateHookSort extends \RS\Orm\AbstractObject
{        
    protected static
        $table = 'tpl_hook_sort';
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite(),
            'context' => new Type\Varchar([
                'description' => t('Контекст темы оформления'),
                'maxLength' => 100
            ]),
            'hook_name' => new Type\Varchar([
                'description' => t('Идентификатор хука'),
                'maxLength' => 100
            ]),
            'module' => new Type\Varchar([
                'description' => t('Идентификатор модуля'),
                'maxLength' => 50
            ]),
            'sortn' => new Type\Varchar([
                'description' => t('Порядковый номер'),
                'index' => true
            ])
        ]);
        
        $this->addIndex(['site_id', 'context', 'hook_name', 'module'], self::INDEX_UNIQUE);
    }
    
}
