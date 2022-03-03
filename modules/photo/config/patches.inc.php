<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Photo\Config;


use Photo\Model\Orm\Image;
use RS\Module\AbstractPatches;

class Patches extends AbstractPatches
{
    function init()
    {
        return [
            '400'
        ];
    }

    /**
     * Высчитывает md5-hash для всех фотографий
     */
    function beforeUpdate400()
    {
        $page_size = 1000;
        $offset = 0;

        $image = new Image();
        $folders = $image->getFolders();

        $q = \RS\Orm\Request::make()
            ->select('id, servername')
            ->from($image)
            ->where('hash IS NULL')
            ->limit($page_size);

        while($rows = $q->offset($offset)->exec()->fetchAll()) {
            $parts = [];
            foreach($rows as $row) {
                $path  = \Setup::$ROOT.$folders['srcFolder'].'/'.$row['servername'];

                if (file_exists($path)) {
                    $hash = md5(file_get_contents($path));
                    $parts[] = "({$row['id']}, '{$hash}')";
                }
            }

            if ($parts) {
                $sql = "INSERT INTO {$image->_getTable()} (id, hash) VALUES " . implode(',', $parts) . " ON DUPLICATE KEY UPDATE hash=VALUES(hash)";
                \RS\Db\Adapter::sqlExec($sql);
            }

            $offset += $page_size;
        }
    }
}