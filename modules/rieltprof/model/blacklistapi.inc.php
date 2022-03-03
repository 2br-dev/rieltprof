<?php

namespace rieltprof\Model;

use rieltprof\Model\Orm\BlackList;
use RS\Module\AbstractModel\EntityList;

/**
 * Класс для организации выборок ORM объекта.
 * В этом классе рекомендуется также реализовывать любые дополнительные методы, связанные с заявленной в конструкторе моделью
 */
class BlackListApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new BlackList());
    }
}
