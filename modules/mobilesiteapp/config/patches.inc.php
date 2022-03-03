<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileSiteApp\Config;
use Shop\Model\Orm\Order;

/**
 * Патчи к модулю
 */
class Patches extends \RS\Module\AbstractPatches
{
    /**
     * Возвращает массив патчей.
     */
    function init()
    {
        return [
            '3017'
        ];
    }

    /**
     * Сделаем цвет по умолчанию
     */
    function afterUpdate3017()
    {
        if (class_exists('Shop\Model\Orm\Order')) {
            \RS\Orm\Request::make()
                ->update(new Order())
                ->set([
                    'mobile_background_color' => '#E0E0E0'
                ])
                ->where([
                    'mobile_background_color' => '#fff'
                ])->exec();
        }
    }

}