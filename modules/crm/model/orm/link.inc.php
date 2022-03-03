<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Orm;
use Crm\Model\Links\Type\AbstractType;
use RS\Orm\Request;
use \RS\Orm\Type;

/**
 * ORM объект - связь между объектами
 * --/--
 * @property string $source_type Тип объекта источника
 * @property integer $source_id ID объекта источника
 * @property string $link_type Тип связываемого объекта 
 * @property integer $link_id ID связываемого объекта
 * --\--
 */
class Link extends \RS\Orm\AbstractObject
{
    protected static
        $table = 'crm_link';

    function _init() //инициализация полей класса. конструктор метаданных
    {
        $this->getPropertyIterator()->append([
            'source_type' => new Type\Varchar([
                'description' => t('Тип объекта источника'),
                'maxLength' => 50
            ]),
            'source_id' => new Type\Integer([
                'description' => t('ID объекта источника')
            ]),
            'link_type' => new Type\Varchar([
                //Здесь идентификатор объекта, наследника Crm\Model\Links\Type\AbstractType
                //Например: order (результат Crm\Model\Links\Type\LinkTypeOrder::getId())
                'description' => t('Тип связываемого объекта '),
                'maxLength' => 50
            ]),
            'link_id' => new Type\Integer([
                'description' => t('ID связываемого объекта')
            ])
        ]);

        $this->addIndex(['source_type', 'source_id', 'link_type', 'link_id'], self::INDEX_UNIQUE);
    }

    /**
     * Возвращает имя свойства, которое помечено как первичный ключ.
     *
     * @return array
     */
    public function getPrimaryKeyProperty()
    {
        return [
            'source_type',
            'source_id',
            'link_type',
            'link_id'
        ];
    }

    /**
     * Возвращает инициализированный объект типа связи
     *
     * @return \Crm\Model\Links\Type\AbstractType
     */
    function getLinkTypeObject()
    {
        $link_type = AbstractType::makeById($this['link_type']);
        $link_type->init($this['link_id']);

        return $link_type;
    }

    /**
     * Загружает объект source_object
     *
     * @param AbstractObject $source_object
     * @return mixed
     */
    function loadSourceObject($source_object)
    {
        $source_object->load($this['source_id']);
        return $source_object;
    }

    /**
     * Удаляет связь, а в случае, если данная связь была последней, то удаляет и объект source
     *
     * @return bool - возвращает true, в случае успешного выполнения операции
     */
    function deleteWithSource($source_object)
    {
        if ($this->delete()) {
            $link_count = Request::make()
                ->from($this)
                ->where([
                    'source_id' => $this['source_id'],
                    'source_type' => $this['source_type']
                ])->count();

            if (!$link_count) { //Если больше нет ссылок на объект, то удаляем исходный объект
                if ($source_object->load($this['source_id'])) {
                    $source_object->delete();
                }
            }
            return true;
        }

        return false;
    }
}