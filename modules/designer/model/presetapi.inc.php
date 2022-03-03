<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model;

use Main\Model\ModuleLicenseApi;
use RS\Module\AbstractModel\BaseModel;

/**
* Класс для работы с блоками пресетами на странице
*
*/
class PresetApi extends BaseModel
{
    //Методы отправки
    const METHOD_GET  = 'GET';
    const METHOD_POST = 'POST';

    //Методы АПИ
    const API_GET_PRESET_DIRS = 'designer.getPresetCategoryList';
    const API_GET_PRESET_LIST = 'designer.getPresetList';
    const API_GET_PRESET      = 'designer.getPreset';

    /**
     * Подготавливает идентификаторы для внутренних ветвей информации
     *
     * @param array $data - массив данных из ветви
     * @return array
     */
    function preparePresetIds($data)
    {
        if (!empty($data)){
            if (key_exists("type", $data)){
                $data['id'] = \RS\Helper\Tools::generatePassword(12, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789");
            }
            if (!empty($data['childs'])){
                foreach ($data['childs'] as $key=>$child){
                    $data['childs'][$key] = $this->preparePresetIds($child);
                }
            }
        }
        if (isset($data['row'])){
            $data['row'] = $this->preparePresetIds($data['row']);
        }
        return $data;
    }

    /**
     * Проверяет все ли атомы доступны из тех, что указаны в пресете.
     *
     * @param array $block_data - данные блока
     * @throws \RS\Exception
     */
    function checkAllAtomsExists($block_data)
    {
        if (isset($block_data['row']) && !empty($block_data['row']['childs'])){
            foreach ($block_data['row']['childs'] as $column){
                if (!empty($column['childs'])){
                    foreach ($column['childs'] as $atom){
                        $type = mb_strtolower($atom['atomType']); //Тип атома

                        //Посмотрим, какие атомы исключить если модудли выключены
                        if ($type == 'form' && (!\RS\Module\Manager::staticModuleExists('feedback') || !\RS\Module\Manager::staticModuleEnabled('feedback'))){
                            $this->addError(t('Необходимо включить модуль Обратной связи'));
                        }
                        if ($type == 'gallery' && (!\RS\Module\Manager::staticModuleExists('photogalleries') || !\RS\Module\Manager::staticModuleEnabled('photogalleries'))){
                            $this->addError(t('Необходимо включить модуль Фотогалереи'));
                        }
                        if ($type == 'slider' && (!\RS\Module\Manager::staticModuleExists('banners') || !\RS\Module\Manager::staticModuleEnabled('banners'))){
                            $this->addError(t('Необходимо включить модуль Баннеры'));
                        }

                        $atom_class = "\Designer\Model\DesignAtoms\Items\\".$type;
                        try{
                            $atom_item = new $atom_class();
                        }catch (\Exception $e){
                            $this->addError($e->getMessage());
                        }
                    }
                }
            }
        }
    }


    /**
     * Устанавливает данные атома данныи по умолчанию для вставки
     *
     * @param array $block_data - данные пресета
     * @throws \RS\Exception
     */
    function setAtomsDefaults(&$block_data)
    {
        if (isset($block_data['settings']['row']) && !empty($block_data['settings']['row']['childs'])){
            foreach ($block_data['settings']['row']['childs'] as &$column){
                if (!empty($column['childs'])){
                    foreach ($column['childs'] as &$atom){
                        $type = $atom['atomType']; //Тип атома
                        $atom_class = "\Designer\Model\DesignAtoms\Items\\".$type;

                        /**
                         * @var \Designer\Model\DesignAtoms\AbstractAtom $atom_class
                         */
                        $atom_class::setDefaultsAfterPresetInsert($atom, $block_data);
                    }
                }
            }
        }
    }

    /**
     * Идентификатор пресета для получения
     *
     * @param integer $id - идентификатор пресета
     * @return array|false
     * @throws \RS\Exception
     */
    function getPreset($id)
    {
        $response = self::makeApiRequest(self::API_GET_PRESET, self::METHOD_GET, [
            'id' => $id
        ]);
        if (!ModuleLicenseApi::isLicenseRenewalActive()){
            $this->addError(t('Отсутствует Pro подписка'));
            return false;
        }

        if (isset($response['success']) && $response['success']){
            //Скопирем файл и распакуем
            $zip_folder = \Setup::$PATH.\Setup::$MODULE_FOLDER."/designer".\Setup::$MODULE_TPL_FOLDER."/presets/".$response['category']."/".$id."/";
            $zip_file   = $zip_folder."data.zip";
            \RS\File\Tools::makePath($zip_file, true);

            if (@copy($response['file'], $zip_file)){
                $zip = new \ZipArchive();

                if ($zip->open($zip_file) === TRUE) {
                    $zip->extractTo($zip_folder);
                    $zip->close();

                    $data_file = $zip_folder."data.json"; //Файл с данными настроек
                    if (file_exists($data_file)){

                        $data = @json_decode(file_get_contents($data_file), true);

                        if (!empty($data['settings'])){
                            $this->checkAllAtomsExists($data['settings']);
                            if (!$this->hasError()){
                                $this->setAtomsDefaults($data);
                                $data = $this->preparePresetIds($data['settings']);
                            }else{
                                return false;
                            }
                        }
                        return $data;
                    }
                }
            }
        }else{
            if (!empty($response['error'])){
                $this->addError($response['error']);
            }
        }

        return false;
    }

    /**
     * Возвращает массив категорий пресетов в виде плоского
     *
     * @param array $first - первый элемент
     * @return array
     */
    public static function getCategoryStaticList($first = [])
    {
        $list = self::getCategoryList();
        $arr = [];
        $arr = array_merge($arr, $first);
        foreach ($list as $item){
            $arr[$item['id']] = $item['title'];
        }
        return $arr;
    }

    /**
     * Возвращает массив категорий пресетов
     *
     * @return array
     */
    public static function getCategoryList()
    {
        static $catlist;
        if ($catlist === null){
            $response = self::makeApiRequest(self::API_GET_PRESET_DIRS, self::METHOD_GET);

            if (isset($response['list'])){
                $catlist = $response['list'];
                return $catlist;
            }
        }
        return [];
    }



    /**
     * Возвращает плоский список выбора пресетов
     *
     * @param array $first - первый элемент
     * @return array
     */
    public static function getPresetsStaticList($first = [])
    {
        $list = self::getPresets();
        $arr = [];
        $arr = array_merge($arr, $first);
        foreach ($list as $item){
            $arr[$item['alias']] = $item['title'];
        }
        return $arr;
    }

    /**
     * Возвращает массив пресетов
     *
     * @return array
     */
    public static function getPresets()
    {
        static $list;
        if ($list === null){
            $response = self::makeApiRequest(self::API_GET_PRESET_LIST, self::METHOD_GET);

            if (isset($response['list'])){
                $list = $response['list'];
                return $list;
            }
        }

        return [];
    }

    /**
     * Возвращает массив пресетов с ключами состоящими из идентификаторов категорий
     *
     * @return array
     */
    public static function getPresetsWithCategoryInKeys()
    {
        $list = self::getPresets();

        $arr = [];
        if (!empty($list)){
            foreach ($list as $item){
                $arr[$item['parent']][] = $item;
            }
        }

        return $arr;
    }

    /**
     * Возвращает блок по идентификатору или false, если он не найден
     *
     * @param string $block_id - id блока
     * @return bool|\Templates\Model\Orm\SectionModule
     */
    function getBlockById($block_id)
    {
        $block = new \Templates\Model\Orm\SectionModule($block_id);
        if (!$block['id']){
            $this->addError(t('Блок не найден'));
            return false;
        }
        return $block;
    }

    /**
     * Делает запрос к АПИ ReadyScript и возвращает ответ
     *
     * @param string $method - метод АПИ
     * @param string $type - тип запроса
     * @param array $data - массив данных для передачи
     *
     * @return array
     */
    public static function makeApiRequest($method, $type = 'GET', $data = [])
    {
        $params = [];
        $data['license'] = \RS\Helper\RSApi::getAuthParams();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($type == 'GET'){
            $params = http_build_query($data);
        }else{
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $url = \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$RS_SERVER_DOMAIN.'/api/methods/'.$method;

        if (!empty($params)){
            $url = $url."?".$params;
        }


        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        if ($result){
            return json_decode($result, true);
        }
        return [];
    }
}