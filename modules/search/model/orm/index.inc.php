<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Search\Model\Orm;
use \RS\Orm\Type;

/**
 * ORM объект - универсальный поисковый индекс.
 * --/--
 * @property string $result_class Класс результата
 * @property integer $entity_id id сущности
 * @property string $title Заголовок результата
 * @property string $indextext Описание сущности (индексируемый)
 * @property string $dateof Дата добавления в индекс
 * --\--
 */
class Index extends \RS\Orm\AbstractObject
{
    protected static
        $table = 'search_index';
    
    function _init()
    {
        $this->getPropertyIterator()->append([
            'result_class' => new Type\Varchar([
                'maxLength' => '100',
                'description' => t('Класс результата'),
                'allowEmpty' => false
            ]),
            'entity_id' => new Type\Integer([
                'description' => t('id сущности'),
                'allowEmpty' => false
            ]),
            'title' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Заголовок результата'),
            ]),
            'indextext' => new Type\Text([
                'description' => t('Описание сущности (индексируемый)'),
            ]),
            'dateof' => new Type\Datetime([
                'description' => t('Дата добавления в индекс'),
            ]),
        ]);
        
        $this
            ->addIndex(['result_class', 'entity_id'], self::INDEX_PRIMARY, 'result_class-entity_id')
            ->addIndex(['title', 'indextext'], self::INDEX_FULLTEXT);
    }
    
    /**
    * Возвращает имя свойства, которое помечено как первичный ключ.
    * 
    * @return string
    */
    public function getPrimaryKeyProperty()
    {
        return ['result_class', 'entity_id'];
    }    
}

