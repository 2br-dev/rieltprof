<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model;

use Designer\Model\DesignAtoms\Items\Form;
use Main\Model\NoticeSystem\InternalAlerts;
use RS\Module\AbstractModel\BaseModel;

/**
* Класс для перевода массива структур в готовый HTML код
*
*/
class RenderApi extends BaseModel
{
    const LEAF_TYPE_ROW    = "row";
    const LEAF_TYPE_COLUMN = "column";
    const LEAF_TYPE_ATOM   = "atom";


    protected $atoms_list = []; //Список атомов
    protected $styles_with_css_prefixes = [ //Массив стилей которые должны идти с префиксами
        "background-size",
        "justify-content",
        "flex-direction",
        "align-items",
        "align-self"
    ];
    protected $view; //джижок для рендера

    private static
        $instance;

    private $cache_resource_folder_css = "";
    
    function __construct()
    {
        $this->cache_resource_folder_css = self::getCacheResourcesFolder();
        \RS\File\Tools::makePath($this->cache_resource_folder_css);
    }

    /**
     * Возвращает экземпляр текущего класса (Singleton)
     *
     * @return $this
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Дополняет стили доп. префиксами стилей, для всех браузеров
     *
     * @param array $styles - массив стилей для обработки
     * @return array
     */
    private function appendCSSStylePrefixes($styles)
    {
        $arr = [];
        foreach ($styles as $key=>$value){
            $arr[$key] = $value;
            if (in_array($key, $this->styles_with_css_prefixes)){
                $arr["-webkit-".$key] = $value;
                $arr["-moz-".$key] = $value;
            }
        }
        return $arr;
    }

    /**
     * Скрепляет стили в единой целое из массива
     *
     * @param array $styles - массив стилей
     * @return string
     */
    private function glueStyles($styles)
    {
        $css = [];
        foreach ($styles as $key=>$value){
            if (!empty($value)){
                $css[] = "\t".$key.": ".$value;
            }
        }
        return implode(";\n", $css);
    }


    /**
     * Подготавливает CSS стиль, если значение свойства в виде массива
     *
     * @param string $title - название CSS свойства
     * @param array $value - массив значений
     *
     * @return string|array
     */
    private function prepareCSSStyleElementFromValueAsArray($title, $value)
    {
        $prepared_value = [];

        if (isset($value['border-type'])){ //Тип обводки
            $arr = [
                'top',
                'left',
                'bottom',
                'right'
            ];
            foreach ($arr as $key){
                if ($value[$key]){
                    $prepared_value['border-'.$key] = $value[$key]." ".$value['border-type']." ".$value['border-color'];
                }
            }
        }else{
            if (isset($value['top']) && !empty($value['top'])){
                $prepared_value = implode(" ", $value);
            }
        }
        return $prepared_value;
    }


    /**
     * Подготавливает часть массива стилей CSS по значению
     *
     * @param string $key - ключ CSS
     * @param mixed $value - значение CSS стиля
     * @param array $styles - массив ранее созданых стилей
     *
     * @return array
     */
    private function prepareCSSStyleArrayPartFromValue($key, $value, $styles)
    {
        if (is_array($value)){
            //TODO: Дописать подборка для размеров экрана
            $prepared_value = $this->prepareCSSStyleElementFromValueAsArray($key, $value);
            if (!empty($prepared_value)){
                if (is_array($prepared_value)){
                    foreach ($prepared_value as $k=>$val){
                        $styles[$k] = $val;
                    }
                }else{
                    $styles[$key] = $prepared_value;
                }
            }
        }else{
            if (!empty($value)){
                if ($key == "background-image"){
                    $styles[$key] = "url(".$value.")";
                }else{
                    $styles[$key] = $value;
                }
            }
        }
        return $styles;
    }

    /**
     * Возвращает обёрнутые стили для обертки атома
     *
     * @param array $element - данные элемента
     * @param string $block_class - id классов блока
     * @return string
     */
    private function getWrappedStylesForAtomInstance($element, $block_class)
    {
        $class_styles = "";

        /**
         * @var \Designer\Model\DesignAtoms\Items\Button $check_atom
         */
        $check_atom = '\Designer\Model\DesignAtoms\Items\\'.$element['atomType'];

        $wrap_styles = $check_atom::getAtomWrapperStyles($element);

        if (!empty($wrap_styles)){
            $class_styles .= $block_class." .d-atom-instance".$element['id']."{\n";
            $class_styles .= $this->glueStyles($wrap_styles);
            $class_styles .= "\n}\n\n";
        }

        return $class_styles;
    }

    /**
     * Оборачивает готовый массив свойств нужными классами и возвращает готовые конструкции CSS
     *
     * @param array $element - данные элемента
     * @param string $block_class - id классов блока
     * @param array $styles - массив стилей элемента
     * @param array $hover_styles - массив стилей элемента для HOVER состояния
     *
     * @return string
     */
    private function wrapStylesWithClasses($element, $block_class, $styles, $hover_styles)
    {
        $class_name       = "";
        $hover_class_name = "";

        switch($element['type']){
            case "row":
                //Посмотрим, нужно ли ограничивать строку
                foreach ($styles as $k=>$style){
                    if ($k == 'max-width'){ //Ограничим строку

                        $class_name .= $block_class." .d-row{\n";
                        $class_name .= "\t".$k.":".$style.";";
                        $class_name .= "}\n";
                        unset($styles[$k]);
                    }
                }

                $class_name .= $block_class."{\n";
                if (!empty($hover_styles)) {
                    $hover_class_name = $block_class.":hover{\n";
                }
                break;
            case "atom":
                //Смотрим для обертки
                /**
                 * @var \Designer\Model\DesignAtoms\Items\Button $check_atom
                 */
                $check_atom = '\Designer\Model\DesignAtoms\Items\\'.$element['atomType'];
                $css_for_wrapper = $check_atom::$css_for_wrapper;

                if (!empty($css_for_wrapper)){
                    $class_name .= $this->getWrappedStylesForAtomInstance($element, $block_class);

                    foreach ($css_for_wrapper as $css_item){
                        if (isset($styles[$css_item])){
                            unset($styles[$css_item]);
                        }
                    }
                }

                //Смотрим для самого атома
                $atom_class = " .d-".$element['type']."-".$element['id'];
                $class_name .= $block_class.$atom_class."{\n";
                if (!empty($hover_styles)) {
                    $hover_class_name .= $block_class . $atom_class.":hover{\n";
                }
                break;
            case "subatom":
                $class_name .= $block_class . " .".$element['attrs']['class']['value']."{\n";
                if (!empty($hover_styles)) {
                    $hover_class_name .= $block_class . " .".$element['attrs']['class']['value'].":hover{\n";
                }
                break;
            case "column":
            default:
                $class_name = $block_class." .d-".$element['type']."-".$element['id']."{\n";
                if (!empty($hover_styles)) {
                    $hover_class_name = $block_class." .d-".$element['type']."-".$element['id'].":hover{\n";
                }
                break;
        }

        $class_name .= $this->glueStyles($styles);
        $class_name .= ";\n}\n\n";
        if (!empty($hover_styles)) {
            $hover_class_name .= $this->glueStyles($hover_styles);
            $hover_class_name .= ";\n}\n\n";

            $class_name .= $hover_class_name;
        }
        return $class_name;
    }

    /**
     * Возвращает готовый CSS для элемента дерева
     *
     * @param string $block_class - класс самого блока
     * @param array $element - массив данных этой ветки дерева
     *
     * @return string
     */
    private function getElementCSSStyles($block_class, $element)
    {
        $styles       = []; //массив стилей
        $hover_styles = []; //массив стилей для HOVER

        //Смотрим стили в CSS
        if (!empty($element['css'])){
            foreach ($element['css'] as $key=>$data){
                if (!empty($element['css_for_wrapper']) && in_array($key, $element['css_for_wrapper'])){ //Проскочим, то, что нужно для обёртки
                    continue;
                }

                $styles = $this->prepareCSSStyleArrayPartFromValue($key, $data['value'], $styles);

                if (!empty($data['hover'])){
                    $hover_styles = $this->prepareCSSStyleArrayPartFromValue($key, $data['hover'], $hover_styles);
                }
            }
        }

        $class_name = "";
        if (!empty($styles)){
            $styles = $this->appendCSSStylePrefixes($styles);

            if (!empty($hover_styles)){
                $hover_styles = $this->appendCSSStylePrefixes($hover_styles);
            }
            if (!empty($element['attrs']['class']['value']) && ($element['attrs']['class']['value'] == 'd-mobile-mmenu-fog')){
                $block_class = "";
            }
            $class_name .= $this->wrapStylesWithClasses($element, $block_class, $styles, $hover_styles);
        }
        //Пройдемся по потомкам
        if (!empty($element['childs'])){
            if ($element['type'] == 'atom'){
                $class = ".d-".$element['type']."-".$element['id'];
                if ($element['id'] == 'mobilemenu'){ //Если это мобильное меню
                    $class = ".d-mobile-mmenu";
                }
                $block_class = $block_class." ".$class;
            }
            foreach ($element['childs'] as $child){
                $class_name .= $this->getElementCSSStyles($block_class, $child);
            }
        }
        return $class_name;
    }


    /**
     * Возвращает CSS блока дизайнера
     *
     * @param integer $block_id - id блока
     * @param array $settings - массив настроек
     * @return string
     * @throws \RS\Exception
     */
    function renderBlockCSS($block_id, $settings)
    {
        if (!isset($settings['row'])){
            return "";
        }
        $this->atoms_list = AtomsApi::getStorageDataForAtoms();
        $block_id = "#d-".$settings['row']['type']."-".$block_id;
        return $this->getElementCSSStyles($block_id, $settings['row']);
    }

    /**
     * Возвращает отрендеренные стили для мобильного меню
     *
     * @return string
     * @throws \RS\Exception
     */
    function renderMobileMenuCSS()
    {
        $config = \RS\Config\Loader::byModule('designer');
        $designer_settings = json_decode($config['designer_settings'], true);
        return $this->getElementCSSStyles('body', $designer_settings['childs']['row']);
    }

    /**
     * Возвращает готовый JS код для элементов
     *
     * @param array $element - массив данных этой ветки дерева
     *
     * @return string
     */
    private function getElementJSCodes($element)
    {
        $js_code = "";
        if (isset($element['attrs']['onclick']['value']) && !empty($element['attrs']['onclick']['value'])){
            $id = "#d-".$element['type']."-".$element['id'];
            $js_code .= "\n
            document.querySelector('".$id."').addEventListener('click', (e)=>{
                ".$element['attrs']['onclick']['value']."
            });\n";
        }

        if (!empty($element['childs'])){
            foreach($element['childs'] as $child){
                $js_code .= $this->getElementJSCodes($child);
            }
        }
        return $js_code;
    }

    /**
     * Возвращает JS коды блока дизайнера
     *
     * @param array $settings - массив настроек
     * @return string
     * @throws \RS\Exception
     */
    function renderBlockJSCodes($settings)
    {
        if (!isset($settings['row'])){
            return "";
        }

        $js_code = $this->getElementJSCodes($settings['row']);
        if (!empty($js_code)){
            $js_code = "
            document.addEventListener('DOMContentLoaded', (e) => {
                $js_code
            });\n";
        }
        return $js_code;
    }

    /**
     * Возвращает JS файлы для элементов
     *
     * @param array $element - массив данных этой ветки дерева
     * @param array $arr - массив ранее установленных JS-ок
     *
     * @return array
     */
    private function getElementJS($element, &$arr)
    {
        if ($element['type'] == 'subatom'){
            return;
        }
        if (!empty($element['childs'])){
            foreach ($element['childs'] as $child){
                $this->getElementJS($child, $arr);
            }
        }
        if ($element['type'] == 'atom'){ //Проверим только для атомов
            /**
             * @var Form $atom
             */
            $atom = '\Designer\Model\DesignAtoms\Items\\' . $element['atomType'];
            if (!empty($atom::$public_js)){
                foreach ($atom::$public_js as $public_js){
                    if (!in_array($public_js, $arr)){ //Если ранее не добавляли JS
                        $arr[] = $public_js;
                    }
                }
            }
        }
    }

    /**
     * Возвращает JS файлы блока дизайнера для подключения
     *
     * @param array $settings - массив настроек
     * @return array
     * @throws \RS\Exception
     */
    function getBlockAtomsJS($settings)
    {
        if (!isset($settings['row'])){
            return [];
        }
        $arr = [];
        $this->getElementJS($settings['row'], $arr);
        return $arr;
    }



    /**
     * Возвращает CSS файлы для элементов
     *
     * @param array $element - массив данных этой ветки дерева
     * @param array $arr - массив ранее установленных JS-ок
     *
     * @return array
     */
    private function getElementCSS($element, &$arr)
    {
        if ($element['type'] == 'subatom'){
            return;
        }
        if (!empty($element['childs'])){
            foreach ($element['childs'] as $child){
                $this->getElementCSS($child, $arr);
            }
        }
        if ($element['type'] == 'atom'){ //Проверим только для атомов
            /**
             * @var Form $atom
             */
            $atom = '\Designer\Model\DesignAtoms\Items\\' . $element['atomType'];
            if (!empty($atom::$public_css)){
                foreach ($atom::$public_css as $public_css){
                    if (!in_array($public_css, $arr)){ //Если ранее не добавляли CSS
                        $arr[] = $public_css;
                    }
                }
            }
        }
    }

    /**
     * Возвращает CSS файлы блока дизайнера для подключения
     *
     * @param array $settings - массив настроек
     * @return array
     * @throws \RS\Exception
     */
    function getBlockAtomsCSS($settings)
    {
        if (!isset($settings['row'])){
            return [];
        }
        $arr = [];
        $this->getElementCSS($settings['row'], $arr);
        return $arr;
    }



    /**
     * Подготавливает аттрибуты HTML элемента
     *
     * @param array $element - массив данных этой ветки дерева
     * @return array
     */
    private function prepareHTMLAttributesOfElement($element)
    {
        $attrs = [];
        foreach ($element['attrs'] as $key=>$data){
            switch ($key){
                case "onclick":
                case "href":
                    if ($element['type'] == 'subatom'){ //Если это подэлемент атома
                        $attrs[$key] = $data['value'];
                    }
                    continue 2;
                    break;
                case "class":
                    if ($element['type'] == 'subatom'){ //Если это подэлемент атома
                        $attrs[$key] = $data['value'];
                    }else{ //Если это сам атом
                        $attrs['id'] = "d-".$element['type']."-".$element['id'];
                        $attrs[$key] = (!empty($data['value']) ? $data['value']." " : "")."d-".$element['type']."-".$element['id'];

                        if (!empty($element['hidden'])){
                            foreach ($element['hidden'] as $size=>$is_enabled){
                                if ($is_enabled){
                                    $attrs[$key] .= " hidden-".$size;
                                }
                            }
                        }
                    }
                    break;
                default:
                    if ($key != 'style' && !empty($data['value'])){
                        if ($element['type'] == 'atom'){
                            $k = "data-".str_replace("_", "-", $key);
                            if (is_array($data['value'])){
                                if (isset($data['value']['href'])){ //Если это сведения о адресе
                                    $attrs[$k] .= $data['value']['protocol'].$data['value']['href'];
                                }else{ //обычный массив
                                    $attrs[$k] = json_encode($data['value'], JSON_UNESCAPED_UNICODE);
                                }
                            }else{
                                $attrs[$k] = $data['value'];
                            }
                        }else{
                            $attrs[$key] = $data['value'];
                        }
                    }elseif($key == 'style' && !empty($data['value']) && $element['type'] == 'subatom'){
                        $attrs['style'] = $data['value'];
                    }
                    break;
            }
        }
        return $attrs;
    }

    /**
     * Возвращает данные по потомкам самого атома
     *
     * @param array $element - данные атома
     * @param Form $atom - объект атома
     * @param string $childs - данные по потомках, установленных ранее
     *
     * @return string
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     * @throws \SmartyException
     */
    private function getElementChildsHTML($element, $atom, $childs)
    {
        if (method_exists($atom, 'getFillChildsDataForRender')){
            $atom_childs = $atom::getFillChildsDataForRender($element);

            if (is_string($atom_childs)){
                $childs .= $atom_childs;
            }else if (!empty($atom_childs)){
                foreach ($atom_childs as $atom_child){
                    $childs .= $this->getElementHTML($atom_child);
                }
            }
        }else{
            foreach ($element['childs'] as $child){
                $childs .= $this->getElementHTML($child);
            }
        }
        return $childs;
    }

    /**
     * Возвращает готовый HTML код для элемента дерева
     *
     * @param array $element - массив данных этой ветки дерева
     * @return string
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     * @throws \SmartyException
     */
    function getElementHTML($element)
    {
        $view = new \RS\View\Engine();
        $tag  = "div"; //Тег по умолчанию

        if (isset($element['tag'])){ //Если, есть тэг
            $tag = $element['tag'];
        }else{
            $element['tag'] = $tag;
        }

        $wrapper_tag   = "";
        $wrapper_attrs = [];
        $attrs         = [];

        if (!empty($element['attrs'])){
            if (!empty($element['attrs']['href']['value']['href'])){ //Если ссылка указана
                $href = $element['attrs']['href'];
                if ($href['value']['blank']){
                    $attrs['target'] = $wrapper_attrs['target'] = '_blank';
                }
                if ($href['value']['nofollow']){
                    $attrs['rel'] = $wrapper_attrs['rel'] = 'nofollow';
                }

                $wrapper_tag = "a";
                $wrapper_attrs['href'] = $href['value']['protocol'].$href['value']['href'];
            }
            $attrs = $this->prepareHTMLAttributesOfElement($element);
        }

        $childs = "";
        if (isset($element['type']) && $element['type'] == 'atom'){ //Дополним непосредственно атом аттрибутами
            $view->assign([
                'atom_type' => mb_strtolower($element['atomType'])
            ]);

            /**
             * @var Form $atom
             */
            $atom = '\Designer\Model\DesignAtoms\Items\\' . $element['atomType'];
            $attrs = $atom::addAdditionalAttributesToAtom($element, $attrs);
            $attrs = $atom::checkPaidAtomAvailableAndAddClass($element, $attrs);
        }

        if (!empty($element['childs'])){
            if (isset($element['type']) && $element['type'] == 'atom') {
                $childs = $this->getElementChildsHTML($element, $atom, $childs);
            }else{
                foreach ($element['childs'] as $child){
                    $childs .= $this->getElementHTML($child);
                }
            }
        }else{
            $childs = $element['html'];
        }

        if ($element['type'] == 'column'){
            $attrs['class'] .= " d-col-12 d-column";
        }

        $view->assign([
            'element' => $element,
            'tag' => $tag,
            'attrs' => $attrs,
            'wrapper_tag' => $wrapper_tag,
            'wrapper_attrs' => $wrapper_attrs,
            'childs' => $childs
        ]);
        return $view->fetch("%designer%/render/item_child.tpl");
    }

    /**
     * Переводит настройки блока в HTML код
     *
     * @param integer $block_id - id блока
     * @param array $settings - массив настроек модуля
     * @return string
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     * @throws \SmartyException
     */
    function renderBlockHtml($block_id, $settings)
    {
        if (!isset($settings['row'])){
            return "";
        }
        $element = $settings['row'];
        $attrs = [
            'id' => 'd-row-'.$block_id,
            'class' => 'd-row-wrapper'
        ];

        if ($element['type'] == 'row' && $element['background_fullwidth']){ //Если нужно развернуть на всю ширину
            $attrs['class'] .= " d-full-width";
        }

        $view = new \RS\View\Engine();
        $view->assign([
            'block_id' => $block_id,
            'element' => $element,
            'attrs' => $attrs,
            'html' => $this->getElementHTML($element)
        ]);
        return $view->fetch("%designer%/render/block.tpl");
    }

    /**
     * Возвращает папку в которой должны лежать кэшируемые файлы
     *
     * @param string $type - тип файлов (css|js)
     *
     * @return string
     */
    public static function getCacheResourcesFolder($type = 'css')
    {
        return \Setup::$PATH.\Setup::$MODULE_FOLDER."/designer/cache/".$type."/";
    }

    /**
     * Создаёт CSS файл для блока дизайнера с отрендеренным контентом для публичной части и возращает содержимое
     *
     * @param string $block_id - id блока
     * @return string
     * @throws \RS\Exception
     */
    function createCSSFileForDesignerBlock($block_id)
    {
        $block    = BlocksApi::getInstance()->getBlockById($block_id);
        $settings = $block->getParam('settings');

        $css = $this->renderBlockCSS($block_id, $settings);
        file_put_contents($this->cache_resource_folder_css."block-".mb_strtolower($block_id).".css", $css);
        return $css;
    }

    /**
     * Создаёт CSS файл для блока дизайнера с отрендеренным контентом для публичной части и возращает содержимое
     *
     * @return string
     * @throws \RS\Exception
     */
    function createCSSFileForDesignerMMenu()
    {
        $css = $this->renderMobileMenuCSS();
        file_put_contents($this->cache_resource_folder_css."mmenu.css", $css);
        return $css;
    }
}