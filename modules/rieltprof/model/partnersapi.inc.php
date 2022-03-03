<?php

namespace rieltprof\Model;

use rieltprof\Model\Orm\Partners;
use RS\Module\AbstractModel\EntityList;

/**
 * Класс для организации выборок ORM объекта.
 * В этом классе рекомендуется также реализовывать любые дополнительные методы, связанные с заявленной в конструкторе моделью
 */
class PartnersApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Partners());
    }
}
