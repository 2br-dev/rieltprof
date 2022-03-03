<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model;

use Designer\Model\AtomApis\ImageApi;
use Designer\Model\DesignAtoms;
use RS\Module\AbstractModel\BaseModel;
use Templates\Model\Orm\Section;
use Templates\Model\Orm\SectionContainer;
use Templates\Model\Orm\SectionModule;

/**
* Класс для работы с блоками дизайнера на странице
*
*/
class BlocksApi extends BaseModel
{
    public static $inst = null;

    /**
     * Возвращает текущий объект Апи
     *
     * @return BlocksApi|null
     */
    static public function getInstance()
    {
        if (self::$inst == null) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    protected $attrs = [ //Аттрибуты структуры
        'class' => [
            'name'  => 'class',
            'title' => "Класс",
            'type'  => 'text',
            'value' => '',
            'visible' => false,
        ],
        'style' => [
            'name'  => 'style',
            'title' => "Стиль",
            'type'  => 'textareastyle',
            'value' => '',
            'visible' => false
        ]
    ];
    protected static $section_sizes = ['_', '_xs', '_sm', '_lg', '_xl'];
    protected $resource_cache_folder; //Папка с файлами ресурсового кэша

    protected $css_item = [ //Аттрибуты CSS структуры
        'title' => "Класс",
        'name' => "class",
        'type' => "text",
        'value' => ""
    ];

    function __construct()
    {
        $this->resource_cache_folder = \Setup::$PATH.\Setup::$MODULE_FOLDER."/designer/cache/";
    }

    /**
     * Возвращает структуру аттрибутов сущности с нужными заменами
     *
     * @param string $class - класс для назначения
     * @param array $change_params - массив ключ => значение для подмены на нужное
     * @return array
     */
    private function getStructureAttrs($class, $change_params = [])
    {
        $this->attrs['class']['value'] = $class;
        return array_merge($this->attrs, $change_params);
    }

    /**
     * Возвращает массив объектов структур сущностей, для последующей модификации или добавления новых элементов на клиетской часта
     *
     * @return array
     */
    function getStructuresJSON()
    {
        //Наши доступные свойства для колонок и пресетов
        $background_preset   = new DesignAtoms\CSSPresets\Background();
        $background_info_css = $background_preset->getCSSData();
        $background_info     = $background_preset->getData();

        $marin_bottom = new DesignAtoms\CSSProperty\Size('--designer-row-margin-bottom', t('Нижний внешний отступ'), '0px');
        $marin_bottom->setVisible(false);
        $marin_bottom_info = $marin_bottom->getPropertyInfo();

        $max_width      = new DesignAtoms\CSSProperty\Size('max-width', t('Максимальная ширина'), '');
        $max_width_info = $max_width->getPropertyInfo();

        $align_items      = new DesignAtoms\CSSProperty\VAlignItems('justify-content', t('Вертикальное позиционирование'), 'flex-start');
        $align_items_info = $align_items->getPropertyInfo();

        $column_class      = new DesignAtoms\CSSProperty\SelectColumnClass('class', t('Размер'), 'd-col-sm-12');
        $column_class_info = $column_class->getPropertyInfo();

        $row_css = array_merge_recursive($background_info_css, [
            "{$marin_bottom_info['name']}" => $marin_bottom_info,
            "{$max_width_info['name']}" => $max_width_info,
        ]);
        $row_presets[] = $background_info;

        $margin_top_column = new DesignAtoms\CSSProperty\Size('--designer-column-margin-top', t('Внешний верхний отступ'));
        $margin_column_top_info = $margin_top_column->getPropertyInfo();

        $margin_bottom_column = new DesignAtoms\CSSProperty\Size('--designer-column-margin-bottom', t('Внешний нижний отступ'));
        $margin_column_bottom_info = $margin_bottom_column->getPropertyInfo();

        $padding_column_top = new DesignAtoms\CSSProperty\Size('--designer-column-padding-top', t('Верхний внутренний отступ'), '0vh');
        $padding_column_top->setVisible(false);
        $padding_column_top_info = $padding_column_top->getPropertyInfo();

        $padding_column_bottom = new DesignAtoms\CSSProperty\Size('--designer-column-padding-bottom', t('Нижний внутренний отступ'), '0vh');
        $padding_column_bottom->setVisible(false);
        $padding_column_bottom_info = $padding_column_bottom->getPropertyInfo();

        $column_css = array_merge_recursive($background_info_css, [
            "{$align_items_info['name']}" => $align_items_info,
            "{$margin_column_top_info['name']}" => $margin_column_top_info,
            "{$margin_column_bottom_info['name']}" => $margin_column_bottom_info,
            "{$padding_column_top_info['name']}" => $padding_column_top_info,
            "{$padding_column_bottom_info['name']}" => $padding_column_bottom_info,
        ]);
        $column_presets[] = $background_info;

        return [
            'row' => [ //Строка
                'id' => "",
                'type' => "row",
                'additional_class' => '',
                'attrs' => $this->getStructureAttrs('d-row'),
                'name' => t('Блок {n}'),
                'background_fullwidth' => false, //Задний фон на всю ширину
                'css' => $row_css,
                'presets' => $row_presets,
                'hidden' => [
                    'xs' => false
                ],
                'childs' => []
            ],
            'column' => [ //Колонка
                'id' => "",
                'type' => "column",
                'additional_class' => '',
                'attrs' => $this->getStructureAttrs('d-col-md-12', [
                    'class' => $column_class_info
                ]),
                'name' => t('Колонка {n}'),
                'css'  => $column_css,
                'presets' => $column_presets,
                'hidden' => [
                    'xs' => false
                ],
                'childs' => []
            ],
            'atoms' => AtomsApi::getStorageDataForAtoms(),
        ];
    }

    /**
     * Загружает данные полей ORM объекта в виде ключ=>значение
     *
     * @param string $atom_type - тип атома
     * @param string $attr_name - ключ аттрибута с нужным полем
     * @param integer $id - id ORM объекта
     *
     * @return array
     */
    function loadOrmFieldsInfo($atom_type, $attr_name, $id)
    {
        $atom_class = "\Designer\Model\DesignAtoms\Items\\".$atom_type;
        /**
         * @var \Designer\Model\DesignAtoms\AbstractAtom $atom
         */
        $atom = new $atom_class();

        /**
         * @var  \Designer\Model\DesignAtoms\Attrs\OrmFields $attr
         */
        $attr = $atom->getAttr($attr_name);
        $orm_class = $attr['data']['entity'];
        /**
         * @var \RS\Orm\AbstractObject $orm
         */
        $orm = new $orm_class();
        $orm->load($id);

        $fields = array_keys($attr['data']['orm_fields']);
        $arr = [];
        if (!empty($fields)){
            foreach ($fields as $field){
                $arr[$field] = $orm[$field];
            }
        }

        return $arr;
    }

    /**
     * Возвращает блок по идентификатору или false, если он не найден
     *
     * @param string $block_id - id блока
     * @return bool|\RS\Controller\StandartBlock|SectionModule
     */
    function getBlockById($block_id)
    {
        $first_letter = mb_substr($block_id, 0, 1);
        if ($first_letter == 'T'){ //Блок из шаблона
            $api = new \Templates\Model\TemplateModuleApi();

            $block_id = mb_substr($block_id, 1, mb_strlen($block_id) - 1);

            /** @var \RS\Controller\StandartBlock $block */
            $block = $api->getBlockFromCache($block_id, 'designer-block-designer');

            if (!$block) { //Подгрузим блок
                $this->addError(t('Блок не найден'));
                return false;
            }
        }else{ //Блок по сетке
            $block = new \Templates\Model\Orm\SectionModule($block_id);
            if (!$block['id']){
                $this->addError(t('Блок не найден'));
                return false;
            }
        }
        return $block;
    }

    /**
     * Фильтрует секцию CSS отсекая лишние данные для сохранения
     *
     * @param array $data - массив данных
     * @return array
     */
    private function filterSaveCSSSection($data)
    {
        if (isset($data['title'])){
            unset($data['title']);
        }
        if (isset($data['name'])) {
            unset($data['name']);
        }
        if (isset($data['type'])) {
            unset($data['type']);
        }
        if (isset($data['data'])) {
            unset($data['data']);
        }
        if (isset($data['css_for_wrapper'])) {
            unset($data['css_for_wrapper']);
        }
        if (isset($data['debug_event'])) {
            unset($data['debug_event']);
        }
        if (isset($data['orm_fields'])) {
            unset($data['orm_fields']);
        }
        if (isset($data['attr_groups'])) {
            unset($data['attr_groups']);
        }
        if (isset($data['hint'])) {
            unset($data['hint']);
        }
        if (isset($data['visible'])) {
            unset($data['visible']);
        }
        if (isset($data['hover']) && empty($data['hover'])){ //Если свойств hover нет
            unset($data['hover']);
        }

        if (is_string($data['value'])){
            $data['value'] = str_replace("&#039;", "'", $data['value']);
        }
        return $data;
    }


    /**
     * Проходится по дереву пресета и подготавляет изображения, чтобы с ним можно было потом работать в редактире изображения
     *
     * @param string $block_id - идентификатор блока
     * @param array $data - массив данных
     * @return array
     */
    private function prepareImagesFromPreset($block_id, $data)
    {
        if ($data['type'] == 'atom' && $data['atomType'] == 'Image'){
            //Сохраним правильно оригинал и само фото
            $image = $data['attrs']['src']['value'];

            $data['attrs']['src']['value']      = ImageApi::getInstance()->copyImagesFromPreset($block_id, $data['id'], $image, 'src');
            $data['attrs']['original']['value'] = ImageApi::getInstance()->copyImagesFromPreset($block_id, $data['id'], $image, 'original');
        }

        if (!empty($data['childs'])){ //Если есть потомки, то тоже отфильтруем
            foreach ($data['childs'] as &$child){
                $child = $this->prepareImagesFromPreset($block_id, $child);
            }
        }
        return $data;
    }


    /**
     * Фильтрует данные, которые будут сохраняться в базу, отбрасывая лишний данные от междинга на публичной части и возвращает
     *
     * @param array $data - массив данных
     * @return array
     */
    private function filterSaveData($data)
    {
        switch($data['type']){
            case 'row': //Строка
                if (isset($data['is_can_be_background_full_width'])){
                    unset($data['is_can_be_background_full_width']);
                }
                if (isset($data['grid'])){
                    unset($data['grid']);
                }
                break;
            case 'col': //Колонка
                unset($data['title']);
                break;
            case 'atom': //Атом
            case 'subatom': //Подэлемент
                if (isset($data['formData'])){
                    unset($data['formData']);
                }
                if (isset($data['paid'])){
                    unset($data['paid']);
                }
                unset($data['image']);
                unset($data['tags']);
                unset($data['title']);
                unset($data['hint']);
                unset($data['html_type']);
                unset($data['reset_attrs']);
                //Если есть виртуальные аттрибуты, то вырежем
                if (isset($data['virtual_attrs']) && !empty($data['virtual_attrs'])){
                    foreach ($data['virtual_attrs'] as $virtual_attr){
                        unset($data['attrs'][$virtual_attr]);
                    }
                }
                unset($data['virtual_attrs']);
                break;
        }
        if (!empty($data['presets'])){
            unset($data['presets']);
        }
        if (!empty($data['css'])){
            foreach ($data['css'] as &$css){
                $css = $this->filterSaveCSSSection($css);
            }
        }
        if (!empty($data['attrs'])){
            foreach ($data['attrs'] as $key=>&$attr){
                if (isset($attr['type'])){
                    switch ($attr['type']){
                        case "directlink":
                        case "ormfields":
                        case "selectfieldvaluefromjsondata":
                        case "selectfieldvalueastree":
                            unset($data['attrs'][$key]);
                            continue 2;
                            break;
                    }
                }

                $attr = $this->filterSaveCSSSection($attr);
            }
        }
        if (!empty($data['childs'])){ //Если есть потомки, то тоже отфильтруем
            foreach ($data['childs'] as &$child){
                $child = $this->filterSaveData($child);
            }
        }
        return $data;
    }

    /**
     * Записывает данные в блок
     *
     * @param \RS\Controller\StandartBlock|SectionModule $block
     * @param mixed $data - данные для записи
     */
    private function writeDataToBlock($block, $data)
    {
        $block->setParam('settings', $data);
        if ($block instanceof SectionModule){ //Блок по сетке
            $block->update();
            //Сбросим кэш
            \RS\Cache\Manager::obj()->invalidateByTags([CACHE_TAG_BLOCK_PARAM]);
        }else{ //Блок в шаблоне
            $object = $block->getParamObject();
            $object->getFromArray($block->getParam());

            $api = new \Templates\Model\TemplateModuleApi();
            $api->saveBlockValues($block, $object);
        }
    }

    /**
     * Сохраняет настройки блока с данными
     * Данные хранятся к ключе settings
     *
     * @param string $block_id - id блока
     * @param array $data - данные для установки
     *
     * @return array
     * @throws \RS\Event\Exception
     */
    function saveBlocksDataFromPreset($block_id, $data)
    {
        if ($block = $this->getBlockById($block_id)){
            //Удалим данные по картинкам ранее загруженным
            $settings = $block->getParam('settings');
            if (isset($settings['row'])){
                $this->deleteImagesFromBlockData($settings['row']);
            }

            //Фильтруем данные для сохранения
            $data['row'] = $this->filterSaveData($data['row']);
            $data['row'] = $this->prepareImagesFromPreset($block_id, $data['row']);
            if (self::isCanDesignerHaveFullBackground($block_id)){
                $data['row']['is_can_be_background_full_width'] = true;
            }else{
                $data['row']['is_can_be_background_full_width'] = false;
                $data['row']['background_fullwidth'] = false; //Сбросим всегда для переместившегося
            }
            $this->writeDataToBlock($block, $data);
            $settings = $block->getParam('settings');

            return [
                'row' => $settings['row']
            ];
        }
        return [];
    }

    /**
     * Сохраняет настройки блока с данными
     * Данные хранятся к ключе settings
     *
     * @param string $block_id - id блока
     * @param array $data - данные для установки
     * @throws \RS\Event\Exception
     */
    function saveBlocksData($block_id, $data)
    {
        if ($block = $this->getBlockById($block_id)){
            $data['row'] = $this->filterSaveData($data['row']);
            $this->writeDataToBlock($block, $data);
        }
    }

    /**
     * Очищает папку с файлами кэша для ресурсов CSS и JS файлов автогенерируемый
     */
    function clearResourceCacheFolder()
    {
        \RS\File\Tools::deleteFolder($this->resource_cache_folder);
    }

    /**
     * Удаляет настройки блока с данными
     * Данные хранятся к ключе settings
     *
     * @param string $block_id - id блока
     * @param array $data - данные для установки
     * @throws \RS\Event\Exception
     */
    function deleteBlocksData($block_id)
    {
        if ($block = $this->getBlockById($block_id)){
            //Наши доступные свойства для колонок и пресетов
            $background_preset   = new DesignAtoms\CSSPresets\Background();
            $background_info_css = $background_preset->getCSSData();
            $background_info     = $background_preset->getData();

            $marin_bottom      = new DesignAtoms\CSSProperty\Size('margin-bottom', t('Нижний внешний отступ'), '0px');
            $marin_bottom->setVisible(false);
            $marin_bottom_info = $marin_bottom->getPropertyInfo();

            $max_width      = new DesignAtoms\CSSProperty\Size('max-width', t('Максимальная ширина'), '');
            $max_width_info = $max_width->getPropertyInfo();

            $row_css = array_merge_recursive($background_info_css, [
                "{$marin_bottom_info['name']}" => $marin_bottom_info,
                "{$max_width_info['name']}"    => $max_width_info,
            ]);
            $row_presets[] = $background_info;

            $data['row'] = [
                'id' => $block_id,
                'type' => "row",
                'attrs' => $this->getStructureAttrs('d-row'),
                'name' => t('Блок {n}'),
                'background_fullwidth' => false, //Задний фон на всю ширину
                'css' => $row_css,
                'presets' => $row_presets,
                'childs' => []
            ];
            $this->writeDataToBlock($block, $data);
        }
    }

    /**
     * Возвращает информацию по атому
     *
     * @param string $block_id - id блока
     * @param string $atom_id - id атома
     *
     * @return array
     */
    function getAtomInfo($block_id, $atom_id)
    {
        $info = [];
        if ($block = $this->getBlockById($block_id)){
            $settings = $block->getParam('settings');

            if (isset($settings['row']['childs'])){ //Если есть колонки
                foreach ($settings['row']['childs'] as $col){
                    if (isset($col['childs'])){
                        foreach ($col['childs'] as $atom){
                            if ($atom['id'] == $atom_id){
                                $info = $atom;
                                break(2);
                            }
                        }
                    }
                }
            }
        }
        return $info;
    }

    /**
     * Возвращает информацию по атому по его id
     *
     * @param string $atom_id - id атома
     *
     * @return array|false
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     */
    function getAtomById($atom_id)
    {
        $blocks = \RS\Orm\Request::make()
                        ->from(new SectionModule())
                        ->where([
                            'module_controller' => 'designer\controller\block\designer'
                        ])->objects();

        if (!empty($blocks)){
            foreach ($blocks as $block){
                $atom = $this->getAtomInfo($block['id'], $atom_id);
                if (!empty($atom)){
                    return $atom;
                    break;
                }
            }
        }
        return false;
    }


    /**
     * Сохраняет настройки блока с данными для атома
     * Данные хранятся к ключе settings
     *
     * @param string $block_id - id блока
     * @param array $atomdata - данные атома для установки
     * @throws \RS\Event\Exception
     */
    function saveAtomData($block_id, $atomdata)
    {
        if ($block = $this->getBlockById($block_id)){
            $settings = $block->getParam('settings');

            //Найдем атом и обновим
            if (!empty($settings)){
                if (!empty($settings['row']['childs'])){
                    foreach ($settings['row']['childs'] as &$col){
                        if (!empty($col['childs'])){
                            foreach ($col['childs'] as &$atom){
                                if ($atom['id'] == $atomdata['id']){
                                    $atom = $atomdata;
                                    $this->saveAtomOrmFieldsIfNeeded($atom);
                                }
                            }
                        }
                    }
                }
            }

            $this->saveBlocksData($block_id, $settings);
        }
    }

    /**
     * Сохраняет данные ORM полей атома если это необходимо. Проходится по аттрибутам и ищет аттрибут с типом ormfields
     *
     * @param array $atomdata - данные атома
     */
    function saveAtomOrmFieldsIfNeeded($atomdata)
    {
        if (!empty($atomdata['attrs'])){
            foreach ($atomdata['attrs'] as $attr_key=>$attr){
                if ($attr_key == 'style' || $attr_key == 'class'){
                    continue;
                }
                if ($attr['type'] == 'ormfields'){ //Тип поля ORM объекта
                    $key = $attr['data']['atom_id_field'];
                    $orm_class = $attr['data']['entity'];
                    $orm = new $orm_class();
                    $orm->load($atomdata[$key]);
                    if (!empty($attr['value'])){
                        foreach ($attr['value'] as $k=>$val){
                            $orm[$k] = $val;
                        }
                        $orm->update();
                    }
                }
            }
        }
    }


    /**
     * Сохраняет общие настройки блока с данными для атома
     *
     * @param array $atomdata - данные атома для установки
     * @throws \RS\Exception
     */
    function saveAtomDataForSettings($atomdata)
    {
        $config = \RS\Config\Loader::byModule('designer');

        $designer_settings = !empty($config['designer_settings']) ? json_decode($config['designer_settings'], true) : null;
        $atomdata = $this->filterSaveData($atomdata);
        if (!$designer_settings){
            $designer_settings = [
                'block_id' => 'settings',
                'childs' => !empty($designer_settings) ? $designer_settings : [
                    'row' => [
                        'id' => 'settings',
                        'type' => 'row',
                        'childs' => [[
                            'id' => 'column',
                            'type' => 'column',
                            'childs' => [$atomdata],
                        ]],
                        'hidden' => [
                            'xs' => false
                        ]
                    ]
                ]
            ];
        }else{
            $designer_settings['childs']['row']['childs'][0]['childs'][0] = $atomdata;
        }

        $config['designer_settings'] = json_encode($designer_settings);
        $config->update();
    }


    /**
     * Сохраняет настройки строки с данными
     * Данные хранятся к ключе settings
     *
     * @param string $block_id - id блока
     * @param array $rowdata - данные строки для установки
     * @throws \RS\Event\Exception
     */
    function saveRowData($block_id, $rowdata)
    {
        if ($block = $this->getBlockById($block_id)){
            $settings = $block->getParam('settings');

            //Найдем строку и обновим
            if (!empty($settings)){
                if (!empty($settings['row']) && ($settings['row']['id'] == $rowdata['id'])){
                    $settings['row'] = $rowdata;
                }
            }
            $this->saveBlocksData($block_id, $settings);
        }
    }


    /**
     * Сохраняет настройки колонки с данными
     * Данные хранятся к ключе settings
     *
     * @param string $block_id - id блока
     * @param array $columndata - данные колонки для установки
     * @throws \RS\Event\Exception
     */
    function saveColumnData($block_id, $columndata)
    {
        if ($block = $this->getBlockById($block_id)){
            $settings = $block->getParam('settings');

            //Найдем колонку и обновим
            if (!empty($settings)){
                if (!empty($settings['row']['childs'])){
                    foreach ($settings['row']['childs'] as &$col){
                        if ($col['id'] == $columndata['id']){
                            $col = $columndata;
                        }
                    }
                }
            }

            $this->saveBlocksData($block_id, $settings);
        }
    }

    /**
     * Добавляет новый атом в колонку
     *
     * @param string $block_id - id блока
     * @param string $col_id - id колонки
     * @param integer $index - позиция для вставки
     * @param array $atomdata - данные атома для установки
     * @throws \RS\Event\Exception
     */
    function addAtomToColumn($block_id, $col_id, $index, $atomdata)
    {
        if ($block = $this->getBlockById($block_id)){
            $settings = $block->getParam('settings');

            //Найдем атом и обновим
            if (!empty($settings)){
                if (!empty($settings['row']['childs'])){
                    foreach ($settings['row']['childs'] as &$col){

                        if ($col['id'] == $col_id){

                            if (!empty($col['childs'])){
                                array_splice($col['childs'], $index, 0, [$atomdata]);
                            }else{
                                $col['childs'][] = $atomdata;
                            }
                        }
                    }
                }
            }

            $this->saveBlocksData($block_id, $settings);
        }
    }

    /**
     * Добавляет новую колонку
     *
     * @param string $block_id - id блока
     * @param integer $index - позиция для вставки
     * @param array $coldata - данные колонки для установки
     * @throws \RS\Event\Exception
     */
    function addColumn($block_id, $index, $coldata)
    {
        if ($block = $this->getBlockById($block_id)){
            $settings = $block->getParam('settings');

            //Найдем атом и обновим
            if (!empty($settings)){
                if (!empty($settings['row']['childs'])){
                    array_splice($settings['row']['childs'], $index, 0, [$coldata]);
                }else{
                    $settings['row']['childs']['childs'][] = $coldata;
                }
            }

            $this->saveBlocksData($block_id, $settings);
        }
    }

    /**
     * Перемещает атом в колонке
     *
     * @param string $block_id - id блока
     * @param string $col_id - id колонки
     * @param integer $new_position - новая позиция
     * @param integer $old_position - старая позиция
     * @throws \RS\Event\Exception
     */
    function moveAtomInColumn($block_id, $col_id, $new_position, $old_position)
    {
        if ($block = $this->getBlockById($block_id)){
            $settings = $block->getParam('settings');

            //Найдем атом и обновим
            if (!empty($settings)){
                if (!empty($settings['row']['childs'])){
                    foreach ($settings['row']['childs'] as &$col){

                        if ($col['id'] == $col_id){

                            if (!empty($col['childs'])){
                                $childs = $col['childs'];
                                $atomdata = $childs[$old_position];
                                unset($childs[$old_position]);
                                array_splice($childs, $new_position, 0, [$atomdata]);
                                $col['childs'] = [];

                                foreach ($childs as $child){
                                    $col['childs'][] = $child;
                                }
                            }
                        }
                    }
                }
            }

            $this->saveBlocksData($block_id, $settings);
        }
    }

    /**
     * Удаляет атомы картинок из данных блока
     *
     * @param array $data - массив данных блока
     * @throws \RS\Event\Exception
     */
    function deleteImagesFromBlockData($data)
    {
        if (isset($data['type']) && $data['type'] == 'atom' && $data['atomType'] == 'Image'){
            $atomClass = new \Designer\Model\DesignAtoms\Items\Image();
            $atomClass->beforeDelete($data);
        }

        if (!empty($data['childs'])){ //Если есть потомки, то тоже отфильтруем
            foreach ($data['childs'] as $child){
                $this->deleteImagesFromBlockData($child);
            }
        }
    }

    /**
     * Удаляет атом из блока
     *
     * @param string $block_id - id блока
     * @param string $atom_id - id атома
     * @throws \RS\Event\Exception
     */
    function deleteAtom($block_id, $atom_id)
    {
        if ($block = $this->getBlockById($block_id)) {
            $settings = $block->getParam('settings');

            //Найдем атом и обновим
            if (!empty($settings)) {
                if (!empty($settings['row']['childs'])) {
                    foreach ($settings['row']['childs'] as &$col) {
                        if (!empty($col['childs'])) {
                            foreach ($col['childs'] as $k=>$atom){
                                if ($atom['id'] == $atom_id){
                                    //Вызовем функцию по удалению данных
                                    $atomClassName = "\\Designer\\Model\\DesignAtoms\\Items\\".$atom['atomType'];
                                    /**
                                     * @var \Designer\Model\DesignAtoms\AbstractAtom $atomClass
                                     */
                                    $atomClass = new $atomClassName();
                                    $atomClass->beforeDelete($atom);

                                    $childs = $col['childs'];
                                    unset($childs[$k]);

                                    $col['childs'] = [];

                                    foreach ($childs as $child){
                                        $col['childs'][] = $child;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $this->saveBlocksData($block_id, $settings);
        }
    }

    /**
     * Удаляет колонку из блока
     *
     * @param string $block_id - id блока
     * @param integer $id - id колонки
     * @throws \RS\Event\Exception
     */
    function deleteColumn($block_id, $id)
    {
        if ($block = $this->getBlockById($block_id)) {
            $settings = $block->getParam('settings');

            //Найдем колонку и обновим
            if (!empty($settings)) {
                if (!empty($settings['row']['childs'])) {
                    foreach ($settings['row']['childs'] as $k=>$col) {
                        if ($col['id'] == $id){
                            unset($settings['row']['childs'][$k]);
                        }
                    }
                    if (!empty($settings['row']['childs'])){
                        $settings['row']['childs'] = array_combine(range(0, count($settings['row']['childs'])-1), array_values($settings['row']['childs']));
                    }
                    if (empty($settings['row']['childs'])){
                        if (!empty($settings['row']['css']['max-width']['value'])){ //Сохраним старую ширину, чтобы не пропадала
                            $old_max_width = $settings['row']['css']['max-width']['value'];
                            $settings['row']['css'] = [];
                            $settings['row']['css']['max-width']['value'] = $old_max_width;
                        }else{
                            $settings['row']['css']   = null;
                        }
                        $settings['row']['attrs'] = null;
                    }
                }
            }
            $this->saveBlocksData($block_id, $settings);
        }
    }

    /**
     * Возвращает true, если у блока есть атом яндекс карты
     *
     * @param array $settings - массив данных блока
     *
     * @return bool
     */
    public static function isHaveYandexMapAtomInBlock($settings)
    {
        if (!empty($settings['row']['childs'])) {
            foreach ($settings['row']['childs'] as $col){
                if (!empty($col['childs'])){
                    foreach ($col['childs'] as $atom){
                        if ($atom['atomType'] == 'YandexMap'){
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Возвращает true, если у блока есть атом меню
     *
     * @param array $settings - массив данных блока
     *
     * @return bool
     */
    public static function isHaveMenuAtomInBlock($settings)
    {
        if (!empty($settings['row']['childs'])) {
            foreach ($settings['row']['childs'] as $col){
                if (!empty($col['childs'])){
                    foreach ($col['childs'] as $atom){
                        if ($atom['atomType'] == 'Menu'){
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Возвращает true, если есть возможность фон у блока сделать на всю ширину
     *
     * @param integer $block_id - id блока дизайнера
     *
     * @return bool
     */
    public static function isCanDesignerHaveFullBackground($block_id)
    {
        $block   = new SectionModule($block_id);
        $section = new Section($block['section_id']);
        $grid = \RS\Theme\Manager::getCurrentThemeGrid();

        //Смотрм на какой системе тема, чтобы определиться
        if ($grid == 'bootstrap' || $grid == 'bootstrap4'){
            $row = new Section($section['parent_id']);
            if ($row['element_type'] != 'row'){ //Если вы под секции то нет
                return false;
            }
            $only_full_size = true;
            foreach (self::$section_sizes as $section_size){
                $key = 'width'.$section_size;
                if (isset($section[$key])){
                    if ($section[$key] && ($section[$key] < 12)){
                        $only_full_size = false;
                    }
                }
            }

            if ($only_full_size){
                return true;
            }
            return false;
        }

        if ($grid == 'gs960'){
            if ($section['width'] == 12 && !$section['width_sm'] && !$section['width_lg'] && !$section['width_xl'] && !$section['width_xs']){
                return true;
            }
        }

        return false;
    }

    /**
     * Возвращает настройки по умолчанию для атома мобильного меню
     *
     * @return array
     */
    public static function getMMenuDefaultSettings()
    {
        return [
            'block_id' => 'settings',
            'childs' => [
                'row' => [
                    'id' => 'settings',
                    'type' => 'row',
                    'childs' => [[
                        'id' => 'column',
                        'type' => 'column',
                        'childs' => [[
                            'id' => 'mobilemenu',
                            'additional_class' => '',
                            'atomType' => 'MMenu',
                            'attrs' => [
                                'class' => [
                                    'value' => 'd-mobile-mmenu'
                                ],
                            ],
                            'type' => 'atom',
                        ]],
                    ]]
                ]
            ]
        ];
    }
}