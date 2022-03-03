<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Tags\Model\Orm;
use \RS\Orm\Type;
                  
/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $word_id ID тега
 * @property string $type Тип связи
 * @property integer $link_id ID объекта, с которым связан тег
 * --\--
 */
class Link extends \RS\Orm\OrmObject
{
    protected static
        $table = 'tags_links';
        
    function _init()
    {
        parent::_init();
        $this->getPropertyIterator()->append([
            'word_id' => new Type\Bigint([
                'description' => t('ID тега')
            ]),
            'type' => new Type\Varchar([
                'description' => t('Тип связи'),
                'maxlength' => 20
            ]),
            'link_id' => new Type\Integer([
                'description' => t('ID объекта, с которым связан тег')
            ])
        ]);
        
        $this->addIndex(['word_id', 'type', 'link_id'], self::INDEX_UNIQUE);
    }
    
}

