<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model;

use Designer\Model\DesignAtoms;

/**
* Класс для работы с атомами (элементами отображения)
*
*/
class AtomsApi
{
    public static $atoms_list; //Массив компонентов (элементов для отображения)
    public static $atoms_ignore_list = []; //Массив компонентов для исключения из списка

    /**
     * Возвращает список доступных элементов компонентов
     *
     * @return DesignAtoms\AbstractAtom[]
     * @throws \RS\Exception
     */
    public static function getAtoms()
    {
        if (self::$atoms_list === null){
            //Посмотрим, какие атомы исключить если модудли выключены
            if (!\RS\Module\Manager::staticModuleExists('feedback') || !\RS\Module\Manager::staticModuleEnabled('feedback')){
                self::$atoms_ignore_list[] = 'feedback';
            }
            if (!\RS\Module\Manager::staticModuleExists('photogalleries') || !\RS\Module\Manager::staticModuleEnabled('photogalleries')){
                self::$atoms_ignore_list[] = 'gallery';
            }
            if (!\RS\Module\Manager::staticModuleExists('faq') || !\RS\Module\Manager::staticModuleEnabled('faq')){
                self::$atoms_ignore_list[] = 'faq';
            }
            if (!\RS\Module\Manager::staticModuleExists('banners') || !\RS\Module\Manager::staticModuleEnabled('banners')){
                self::$atoms_ignore_list[] = 'slider';
            }

            //Подключим нужные нам атомы
            $folder = \Setup::$ROOT.\Setup::$FOLDER.\Setup::$MODULE_FOLDER.'/designer/model/designatoms/items/'; //Категория для поиска
            self::$atoms_list = [];
            foreach (glob($folder."*.*") as $path_to_atom){
                $atom_name = basename($path_to_atom, '.inc.php');
                if (!in_array($atom_name, self::$atoms_ignore_list)){
                    $atom = '\Designer\Model\DesignAtoms\Items\\'.$atom_name;
                    self::$atoms_list[] = new $atom();
                }
            }
        }
        return self::$atoms_list;
    }

    /**
     * Возвращает информацию по компонентам в системе
     *
     * @return array
     * @throws \RS\Exception
     */
    public static function getStorageDataForAtoms()
    {
        $atoms = self::getAtoms();
        $data = [];
        if (!empty($atoms)){
            foreach ($atoms as $atom){
                $item = $atom->getData();
                $data[$item['atomType']] = $item;
            }
        }

        return $data;
    }
}