<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Shop\Model\Marking;

use RS\Exception as RSException;

class MarkingException extends RSException
{
    const ERROR_SINGLE_CODE_PARSE = 1; // ошибка одного кода УИТ
    const ERROR_ORDER_ITEM_CODES_PARSE = 2; // ошибка в кодах УИТ товарной позиции, в data
}
