<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model;

use RS\Module\AbstractModel\EntityList;

/**
 * API для виджета "Ссылки"
 */
class FastLinkApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\FastLink(), [
            'multisite' => true,
            'titleField' => 'title',
            'sortField' => 'sortn',
            'defaultOrder' => 'sortn'
        ]);
    }
}