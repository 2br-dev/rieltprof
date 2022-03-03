<?php

namespace rieltprof\Model;

use RS\Module\AbstractModel\EntityList;

/**
 * Класс для организации выборок ORM объекта.
 * В этом классе рекомендуется также реализовывать любые дополнительные методы, связанные с заявленной в конструкторе моделью
 */
class ModelApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Model);
    }
}
