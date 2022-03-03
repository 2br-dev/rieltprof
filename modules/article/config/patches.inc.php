<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Config;

use Article\Model\Orm\Article;
use RS\Orm\Request;

/**
 * Патчи к ядру
 */
class Patches extends \RS\Module\AbstractPatches
{
    /**
     * Возвращает список имен существующих патчей
     */
    function init()
    {
        return [
            '3018',
        ];
    }

    /**
     * Патч, исправляет дублирующиеся alias в таблице статей
     *
     * @throws \RS\Orm\Exception
     */
    function beforeUpdate3018()
    {
        $articles = Request::make()
            ->select()
            ->from(new Article())
            ->exec()->fetchSelected('alias', 'id', true);

        foreach ($articles as $alias => $ids) {
            if (count($ids) > 1) {
                foreach ($ids as $n => $id) {
                    if ($n == 0) continue;
                    $new_alias = $alias . '-' . uniqid();
                    Request::make()
                        ->update(new Article())
                        ->set([
                            'alias' => $new_alias
                        ])
                        ->where([
                            'id' => $id
                        ])
                        ->exec();
                }
            }
        }
    }
}