<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Links;

use Crm\Model\Orm\Link;
use RS\Db\Result;
use RS\Orm\Request;

class LinkManager
{
    /**
     * Сохраняет связь объекта с другими объектами
     *
     * @param string $source_type
     * @param integer $source_id
     * @param array $links
     * @return bool;
     */
    public static function saveLinks($source_type, $source_id, $links)
    {
        if (self::removeLinks($source_type, $source_id)) {

            foreach ($links as $link_type => $link_ids) {
                foreach ($link_ids as $link_id) {
                    $link = new Link();
                    $link['source_type'] = $source_type;
                    $link['source_id'] = $source_id;
                    $link['link_type'] = $link_type;
                    $link['link_id'] = $link_id;
                    $link->insert();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Удаляет все связи с объектом
     *
     * @param string $source_type
     * @param integer $source_id
     * @return bool
     */
    public static function removeLinks($source_type, $source_id)
    {
        Request::make()
            ->delete()
            ->from(new Link())
            ->where([
                'source_type' => $source_type,
                'source_id' => $source_id
            ])->exec();

        return true;
    }

    /**
     * Обновляет ID связи объекта
     *
     * @param $tmp_id
     * @param $new_id
     * @param $link_type
     * @return Result
     */
    public static function updateLinkId($tmp_id, $new_id, $link_type)
    {
        return Request::make()
            ->update(new Link())
            ->set([
                'link_id' => $new_id
            ])
            ->where([
                'link_id' => $tmp_id,
                'link_type' => $link_type
            ])
            ->exec();
    }

    /**
     * Возвращает количество связанных объектов
     *
     * @param string $source_type
     * @param string $link_type
     * @param integer $link_id
     * @return integer
     */
    public static function getLinkedSourceObjectCount($source_type, $link_type, $link_id)
    {
        return Request::make()
            ->from(new Link())
            ->where([
                'source_type' => $source_type,
                'link_type' => $link_type,
                'link_id' => $link_id
            ])->count();
    }
}