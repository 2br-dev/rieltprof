<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Attrs;

/**
 * Class OrmFields - аттрибут типа поля из ORM объкта для чтения и редактирования
 */
class OrmFields extends AbstractAttr {

    protected $orm = null; //ORM объект с которым работаем
    protected $orm_fields = []; //массив полей ORM объекта

    /**
     * AbstractCssProperty constructor.
     *
     * @param string $attribute - название аттрибута
     * @param string $title - имя аттрибута
     * @param mixed $value - значение аттрибута
     */
    function __construct($attribute, $title, $value = "")
    {
        if (empty($value)){
            $value = [];
        }
        parent::__construct($attribute, $title, $value);
    }

    /**
     * Создаёт экземпляр класса для получения данных из ORM объекта
     *
     * @param \RS\Orm\AbstractObject $orm_object - ORM объект
     * @param string $title - название заголовка для левой панели
     * @param string $atom_id_field - поле атома в котором содержится идентификатор подгружаемого ORM объекта
     * @return $this
     */
    public static function from($orm_object, $title, $atom_id_field)
    {
        $class = get_class($orm_object);
        $ex_class = explode('\\', $class);
        $ex_class = array_pop($ex_class);
        $attribute_name = mb_strtolower($ex_class);

        $atom = new self($attribute_name, $title);
        $atom->setEntity($orm_object);
        $atom->setAdditionalDataByKey('atom_id_field', $atom_id_field);

        return $atom;
    }

    /**
     * Устнавливает объект ORM объекта с которым работает аттрибут
     *
     * @param \RS\Orm\AbstractObject $orm_object - ORM объект
     * @return $this
     */
    function setEntity($orm_object)
    {
        $this->orm = $orm_object;
        $this->setAdditionalDataByKey('entity', get_class($orm_object));
        return $this;
    }

    /**
     * Возвращает ORM объект с которым работает аттрибут
     *
     * @return \RS\Orm\AbstractObject
     */
    function getEntity()
    {
        $orm_class = $this->getAdditionalDataByKey('entity');
        return new $orm_class();
    }

    /**
     * возвращает масстав нужных полей ORM объекта
     *
     * @return array
     */
    function getOrmNeededFields()
    {
        return $this->orm_fields;
    }

    /**
     * Устанавливает массив полей
     *
     * @param array $orm_fields - массив полей, которые нужно подгружать у ORM объекта
     * @throws \RS\Exception
     * @return $this
     */
    function setNeededFields(array $orm_fields)
    {
        $this->orm_fields = $orm_fields;
        $this->setFieldsInfoFromOrmObject();
        return $this;
    }

    /**
     * Возвращает информацию по значениям полей ORM объекта, в виде ключ=>[type=>тип, title=>название поля]
     *
     * @return array
     * @throws \RS\Exception
     */
    function setFieldsInfoFromOrmObject()
    {
        $orm = $this->getEntity();
        if (!($orm instanceof \RS\Orm\AbstractObject)){
            throw new \RS\Exception(t('Необходим ORM объект'));
        }
        $arr = [];
        foreach ($this->orm_fields as $orm_field){
            /**
             * @var \RS\Orm\Type\AbstractType $field
             */
            $field = "__".$orm_field;
            $class = get_class($orm[$field]);
            $ex_class = explode("\\", $class);
            $ex_class = array_pop($ex_class);
            $arr[mb_strtolower($orm_field)] = [
                'type' => mb_strtolower($ex_class),
                'title' => $orm[$field]->getDescription()
            ];
        }

        $this->setAdditionalDataByKey('orm_fields', $arr);
        return $arr;
    }
}