<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Config;

use RS\Config\Loader as ConfigLoader;
use RS\Module\AbstractPatches;

class Patches extends AbstractPatches
{
    function init()
    {
        return [
            '20052',
            '300',
            '4038',
        ];
    }

    public function afterUpdate4038()
    {
        $config = ConfigLoader::byModule('main');
        $config['yandex_js_api_geocoder'] = $config['api_key_geocoder'];
        $config->update();
    }
    
    function beforeUpdate20052()
    {
        //Читаем старые значения позиций виджетов
        $this->data = \RS\Orm\Request::make()
                    ->select("id, CONCAT(site_id, '.', user_id) as hash, col, position")
                    ->from(new \Main\Model\Orm\Widgets())
                    ->orderby('col, position')
                    ->exec()
                    ->fetchSelected('hash', null, true);
    }
    
    /**
    * Транформируем струю систему виджетов в новую.
    * В новой системе нужно распределить виджеты по 1,2-х и 3-х колоночной сетке
    * В каждой сетке будет собственная сортировка
    */
    function afterUpdate20052()
    {
        //Читаем новые значения
        if ($this->data) {
            $widget = new \Main\Model\Orm\Widgets();
            
            $col_mapping = [
                'left' => 1,
                'center' => 2,
                'right' => 3
            ];
            
            foreach($this->data as $hash => $data) {
                $tmp = [
                    'mode1_pos' => 0,
                    'mode2_left_pos' => 0,
                    'mode2_center_pos' => 0
                ];
                
                foreach($data as $item) {
                    if (!isset($col_mapping[ $item['col'] ])) continue;
                    
                    if ($item['col'] == 'left' || $item['col'] == 'right') {
                        //Переносим все виджеты из 3 колонки в первую
                        $mode2_col = 'left';
                        $tmp['mode2_left_pos']++;
                    } else {
                        $mode2_col = 'center';
                        $tmp['mode2_center_pos']++;
                    }
                    
                    $tmp['mode1_pos']++;
                    
                    \RS\Orm\Request::make()
                        ->update($widget)
                        ->set([
                            'mode2_column' => $col_mapping[ $mode2_col ],
                            'mode3_column' => $col_mapping[ $item['col'] ],
                        
                            'mode1_position' => $tmp['mode1_pos']-1,
                            'mode2_position' => $tmp["mode2_{$mode2_col}_pos"]-1,
                            'mode3_position' => $item['position']
                        ])
                        ->where([
                            'id' => $item['id']
                        ])
                        ->exec();
                }
            }
        }
    }

    /**
     * Удаляем виджет "Новости компании ReadyScript", "Проверка обновлений", в RS 3.0 они встроен в админ. панель
     */
    function beforeUpdate300()
    {
        //Удаляем виджет "Новости компании ReadyScript"
        @unlink(\Setup::$PATH.\Setup::$MODULE_FOLDER.'/main/controller/admin/widget/readyscriptnews.inc.php');

        //Удалим виджет "Проверка обновлений" при переходе к ReadyScript 3.0
        @unlink(\Setup::$PATH.\Setup::$MODULE_FOLDER.'/siteupdate/controller/admin/widget/checkupdates.inc.php');
    }
}
