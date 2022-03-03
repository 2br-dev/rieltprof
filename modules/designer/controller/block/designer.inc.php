<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Controller\Block;
use Designer\Model\BlocksApi;
use Designer\Model\PresetApi;
use Designer\Model\RenderApi;
use Main\Model\ModuleLicenseApi;
use RS\Controller\Block;
use RS\Orm\ControllerParamObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;

/**
 * Блок - дизайнер. Позволяет пользователю настраивать данный блок в удобной форме.
 * Предназначен для статических данных
 */
class Designer extends Block
{
    const TEMPLATE_CREATED_PREFIX = 'T';
    const YA_MAP_API_KEY = 'f0779502-abe1-46cb-9772-eacb78d13768';

    protected static $controller_title = 'Дизайнер';       //Краткое название контроллера
    protected static $controller_description = 'Позволяет создать свой блок информации на сайте';  //Описание контроллера

    protected static $blocks_is_loaded  = false;
    protected static $yamap_js_loaded   = false; //Яндекс карта подгружена?
    protected static $mobile_css_loaded = false; //CSS стиль подгружен?

    /**
     * Возвращает ORM объект, содержащий настриваемые параметры или false в случае,
     * если контроллер не поддерживает настраиваемые параметры
     *
     * @return \RS\Orm\ControllerParamObject | false
     */
    public function getParamObject()
    {
        return new ControllerParamObject(new PropertyIterator([
            '__information__' => new Type\MixedType( [
                'description' => '',
                'visible' => true,
                'template' => '%designer%/blocks/designer/designer_info.tpl'
            ]),
            'settings' => new Type\ArrayList([
                'description' => t('Настройки блока дизайнер'),
                'visible' => false,
                'default' => [            //Стандартная структура информации о компоненте
                    'params' => [         //Пареметры компонента
                        //Здесь будут храниться данные блока на странице
                    ]
                ]
            ])
        ]));
    }

    /**
     * Метод возвращает ID SectionModule, если данный контроллер добавлен по сетке
     * или T{block_id}, если данный контроллер был добавлен через moduleinsert в шаблоне
     *
     * @return string
     */
    public function getModuleId()
    {
        if ($this->getParam('generate_by_template')) {
            return self::TEMPLATE_CREATED_PREFIX.$this->getBlockId();
        }
        return $this->getBlockId();
    }

    /**
     * Добавляет служеные адреса общие, для работы дизайнера
     *
     * @param array $designer_variables - массив данных ресурсов
     * @throws \RS\Exception
     */
    private function addServiceUrlsForDesigner(&$designer_variables)
    {
        $config = \RS\Config\Loader::byModule($this);
        $designer_variables['getCategoriesUrl']      = $this->router->getAdminUrl('getCategories', ['ajax' => 1], 'designer-componentsctrl');
        $designer_variables['getPresetUrl']          = $this->router->getAdminUrl('getPreset', ['ajax' => 1], 'designer-blocksctrl');
        $designer_variables['getComponentUrl']       = $this->router->getAdminUrl('getComponent', [], 'designer-componentsctrl');
        $designer_variables['getComponentUploadUrl'] = $this->router->getAdminUrl('componentUpload', ['ajax' => 1], 'designer-componentsctrl');
        $designer_variables['getUploadedImagesUrl']  = $this->router->getAdminUrl('getUploadedImages', ['ajax' => 1], 'designer-uploadctrl');
        $designer_variables['getUploadedFilesUrl']   = $this->router->getAdminUrl('getUploadedFiles', ['ajax' => 1], 'designer-uploadctrl');
        $designer_variables['pixabayImageSearchUrl'] = $this->router->getAdminUrl('pixabayImageSearch', ['ajax' => 1], 'designer-uploadctrl');

        $designer_variables['saveBlockDataUrl']      = $this->router->getAdminUrl('saveData', ['ajax' => 1], 'designer-blocksctrl');
        $designer_variables['deleteBlockDataUrl']    = $this->router->getAdminUrl('deleteData', ['ajax' => 1], 'designer-blocksctrl');
        $designer_variables['saveRowDataUrl']        = $this->router->getAdminUrl('saveRowData', ['ajax' => 1], 'designer-blocksctrl');
        $designer_variables['saveColumnDataUrl']     = $this->router->getAdminUrl('saveColumnData', ['ajax' => 1], 'designer-blocksctrl');
        $designer_variables['saveAtomDataUrl']       = $this->router->getAdminUrl('saveAtomData', ['ajax' => 1], 'designer-blocksctrl');
        $designer_variables['addNewAtomToColumnUrl'] = $this->router->getAdminUrl('addNewAtomToColumn', ['ajax' => 1], 'designer-blocksctrl');
        $designer_variables['moveAtomInColumnUrl']   = $this->router->getAdminUrl('moveAtomInColumn', ['ajax' => 1], 'designer-blocksctrl');
        $designer_variables['deleteAtomUrl']         = $this->router->getAdminUrl('deleteAtom', ['ajax' => 1], 'designer-blocksctrl');
        $designer_variables['getOrmFieldsDataUrl']   = $this->router->getAdminUrl('getOrmFieldsData', ['ajax' => 1], 'designer-blocksctrl');

        $designer_variables['deleteColumnUrl'] = $this->router->getAdminUrl('deleteColumn', ['ajax' => 1], 'designer-blocksctrl');
        $designer_variables['deleteFileUrl']   = $this->router->getAdminUrl('deleteFile', ['ajax' => 1], 'designer-uploadctrl');
        $designer_variables['deleteImageUrl']  = $this->router->getAdminUrl('deleteImage', ['ajax' => 1], 'designer-uploadctrl');
        $designer_variables['saveImageUrl']    = $this->router->getAdminUrl('uploadImage', ['ajax' => 1], 'designer-uploadctrl');
        $designer_variables['saveImageByUrl']  = $this->router->getAdminUrl('uploadImageByUrl', ['ajax' => 1], 'designer-uploadctrl');
        $designer_variables['saveFileUrl']     = $this->router->getAdminUrl('uploadFile', ['ajax' => 1], 'designer-uploadctrl');
        $designer_variables['svgIconsURL']     = $this->router->getAdminUrl('getSVGIcons', ['ajax' => 1], 'designer-uploadctrl');

        $designer_variables['instrumentPanelImageUrl'] = \Setup::$FOLDER.\Setup::$MODULE_FOLDER."/designer/view/img/instrumentspanel/";
        $designer_variables['imageDefaultPicture']     = \Setup::$FOLDER.\Setup::$MODULE_FOLDER."/designer/view/img/defaultdata/default_image.png";
        $designer_variables['tinymce_js_url']          = \Setup::$RES_JS_FOLDER."/tiny_mce/tinymce.min.js";
        $designer_variables['yandexmap']['api_key']    = !empty($config['ya_map_api_key']) ? $config['ya_map_api_key'] : self::YA_MAP_API_KEY;
        $designer_variables['video']['videoExists']    = $this->router->getAdminUrl('videoExists', ['ajax' => 1], 'designer-atomvideoctrl');

        $designer_variables['ace_js_url']      = \Setup::$RES_JS_FOLDER."/ace-master/ace/ace.js";
        $designer_variables['ace_html_js_url'] = \Setup::$RES_JS_FOLDER."/ace-master/ace/mode-html.js";
    }


    /**
     * Добавляет служеные адреса для атома меню, для работы дизайнера
     *
     * @param array $designer_variables - массив данных ресурсов
     */
    private function addMenuUrlsForDesigner(&$designer_variables)
    {
        $designer_variables['menu']['getMenuListUrl']     = $this->router->getAdminUrl('getMenuList', ['ajax' => 1], 'designer-atommenuctrl');
        $designer_variables['menu']['getCategoryListUrl'] = $this->router->getAdminUrl('getCategoryList', ['ajax' => 1], 'designer-atommenuctrl');
        $designer_variables['menu']['menuAddUrl']         = $this->router->getAdminUrl(null, [], 'menu-ctrl');
        $designer_variables['menu']['categoryAddUrl']     = $this->router->getAdminUrl(null, [], 'catalog-ctrl');
        $designer_variables['mobilemenu_js_url']          = \Setup::$MODULE_FOLDER."/designer".\Setup::$MODULE_TPL_FOLDER."/js/mobilemenu/dist/mobilemenu.js";
    }


    /**
     * Добавляет служеные адреса для атома списка товаров, для работы дизайнера
     *
     * @param array $designer_variables - массив данных ресурсов
     */
    private function addProductsListUrlsForDesigner(&$designer_variables)
    {
        $designer_variables['productslist']['getCategoryListUrl'] = $this->router->getAdminUrl('getCategoryList', ['ajax' => 1], 'designer-atomproductslistctrl');
        $designer_variables['productslist']['getProductsListUrl'] = $this->router->getAdminUrl('getProductsList', ['ajax' => 1], 'designer-atomproductslistctrl');
        $designer_variables['productslist']['categoryAddUrl']     = $this->router->getAdminUrl(null, [], 'catalog-ctrl');
    }


    /**
     * Добавляет служеные адреса для атома формы, для работы дизайнера
     *
     * @param array $designer_variables - массив данных ресурсов
     */
    private function addFormUrlsForDesigner(&$designer_variables)
    {
        $site_config = \RS\Config\Loader::getSiteConfig();
        $designer_variables['form']['formAddUrl']        = $this->router->getAdminUrl('categoryAdd', [], 'feedback-ctrl');
        $designer_variables['form']['getFormsListUrl']   = $this->router->getAdminUrl('getFormsList', ['ajax' => 1], 'designer-atomformctrl');
        $designer_variables['form']['getFormByIdUrl']    = $this->router->getAdminUrl('getFormById', ['ajax' => 1], 'designer-atomformctrl');
        $designer_variables['form']['formAgreementLink'] = $this->router->getUrl('site-front-policy-agreement');
        $designer_variables['form']['formAgreement']     = $site_config['enable_agreement_personal_data'] ? true : false;
        $designer_variables['form']['formsList']         = \Feedback\Model\FormApi::staticSelectList([ //Список доступных форм
            0 => t('Не выбрано')
        ]);
    }

    /**
     * Добавляет служеные адреса для атома баннеров, для работы дизайнера
     *
     * @param array $designer_variables - массив данных ресурсов
     */
    private function addFormUrlsForBanners(&$designer_variables)
    {
        $designer_variables['banners']['zoneAddUrl']     = $this->router->getAdminUrl('categoryAdd', [], 'banners-ctrl');
        $designer_variables['banners']['getZoneByIdUrl'] = $this->router->getAdminUrl('getZoneById', ['ajax' => 1], 'designer-atombannerctrl');
        $designer_variables['banners']['zonesList']      = \Banners\Model\ZoneApi::staticSelectList([ //Список доступных зон
            0 => t('Не выбрано')
        ]);
    }

    /**
     * Добавляет служеные адреса для атома картинки, для работы дизайнера
     *
     * @param array $designer_variables - массив данных ресурсов
     */
    private function addImagesUrlsForDesigner(&$designer_variables)
    {
        $designer_variables['image']['saveAtomCroppedImageUrl'] = $this->router->getAdminUrl('saveAtomCroppedImage', ['ajax' => 1], 'designer-atomimagectrl');
        $designer_variables['image']['createDefaultAtomImageUrl'] = $this->router->getAdminUrl('createDefaultAtomImage', ['ajax' => 1], 'designer-atomimagectrl');
        $designer_variables['image']['changeImageForAtomImageByRatioUrl'] = $this->router->getAdminUrl('changeImageForAtomImageByRatio', ['ajax' => 1], 'designer-atomimagectrl');
        $designer_variables['image']['changeImageForAtomImageByHeightAndWidthUrl'] = $this->router->getAdminUrl('changeImageForAtomImageByHeightAndWidth', ['ajax' => 1], 'designer-atomimagectrl');
        $designer_variables['image']['saveAtomUploadedImageByUrl'] = $this->router->getAdminUrl('saveAtomUploadedImageByUrl', ['ajax' => 1], 'designer-atomimagectrl');
    }


    /**
     * Добавляет служеные адреса для атома галлереи, для работы дизайнера
     *
     * @param array $designer_variables - массив данных ресурсов
     */
    private function addGalleryUrlsForDesigner(&$designer_variables)
    {
        $designer_variables['gallery']['albumAddUrl']     = $this->router->getAdminUrl('add', [], 'photogalleries-ctrl');
        $designer_variables['gallery']['getAlbumByIdUrl'] = $this->router->getAdminUrl('getAlbumById', ['ajax' => 1], 'designer-atomgalleryctrl');
        $designer_variables['gallery']['albumsList']      = \Photogalleries\Model\AlbumApi::staticSelectList([ //Список доступных форм
            0 => t('Не выбрано')
        ]);
    }


    /**
     * Добавляет служеные адреса для атома товара, для работы дизайнера
     *
     * @param array $designer_variables - массив данных ресурсов
     */
    private function addProductUrlsForDesigner(&$designer_variables)
    {
        $designer_variables['product']['productAddUrl']           = $this->router->getAdminUrl('add', [], 'catalog-ctrl');
        $designer_variables['product']['getProductByIdUrl']       = $this->router->getAdminUrl('getProductById', ['ajax' => 1], 'designer-atomproductctrl');
        $designer_variables['product']['getOffersByProductIdUrl'] = $this->router->getAdminUrl('getOffersByProductId', ['ajax' => 1], 'designer-atomproductctrl');

        $designer_variables['dialogs']['catalog']['getChildCategoryUrl'] = $this->router->getAdminUrl('getChildCategory' , [], 'catalog-dialog');
        $designer_variables['dialogs']['catalog']['getProductsUrl']      = $this->router->getAdminUrl('getProducts' , [], 'catalog-dialog');
        $designer_variables['dialogs']['catalog']['dialog']              = $this->router->getAdminUrl(null , [], 'catalog-dialog');

        $designer_variables['productselect_js_url']  = \Setup::$MODULE_FOLDER."/catalog/view/js/selectproduct.js";
        $designer_variables['productselect_css_url'] = \Setup::$MODULE_FOLDER."/catalog/view/css/selectproduct.css";
        $designer_variables['lightgallery_js_url']   = \Setup::$RES_JS_FOLDER."/lightgallery/lightgallery-nojquery.min.js";
        $designer_variables['lightgallery_css_url']  = \Setup::$RES_CSS_FOLDER."/common/lightgallery/css/lightgallery.min.css";
        $designer_variables['swiper_js_url']         = \Setup::$RES_JS_FOLDER."/swiper/swiper.min.js";
        $designer_variables['swiper_css_url']        = \Setup::$RES_CSS_FOLDER."/common/swiper/swiper.min.css";
    }

    /**
     * Наполняет переменные данными для атомов
     *
     * @param array $designer_variables - массив данных ресурсов
     * @throws \RS\Exception
     */
    private function addResourcesForAtoms(&$designer_variables)
    {
        //Для атома картинки
        $this->addImagesUrlsForDesigner($designer_variables['designer']);
        if (\RS\Module\Manager::staticModuleExists('feedback') || \RS\Module\Manager::staticModuleEnabled('feedback')){//Для атома формы
            $this->addFormUrlsForDesigner($designer_variables['designer']);
        }
        if (\RS\Module\Manager::staticModuleExists('banners') || \RS\Module\Manager::staticModuleEnabled('banners')){//Для атома баннеров
            $this->addFormUrlsForBanners($designer_variables['designer']);
        }
        //Для атома меню
        $this->addMenuUrlsForDesigner($designer_variables['designer']);
        //Для атома списка товаров
        $this->addProductsListUrlsForDesigner($designer_variables['designer']);
        //Для атома товара
        $this->addProductUrlsForDesigner($designer_variables['designer']);
        if (\RS\Module\Manager::staticModuleExists('photogalleries') || \RS\Module\Manager::staticModuleEnabled('photogalleries')) {
            //Для атома галлереи
            $this->addGalleryUrlsForDesigner($designer_variables['designer']);
        }
    }

    /**
     * Добавляет переменные для проверки действия PRO подписки
     *
     * @param array $designer_variables - массив данных ресурсов
     */
    private function addProAccountVariables(&$designer_variables)
    {
        $pro_type = 1; //Тип Pro подписки. Если 1 - действует, 0 - не действует
        if (!ModuleLicenseApi::isLicenseRenewalActive()){ //Подписка истекла
            $pro_type = 0;
        }

        $pro_account_sign = crc32($pro_type.'designer_pro_account');
        $designer_variables['designer']['pro_account'] = [
            'type' => $pro_type,
            'pro_account_sign' => $pro_account_sign
        ];
    }

    /**
     * Добавляет необходимые ресурсы на страницу для работы блока дизайнера
     *
     * @throws \RS\Exception
     */
    private function appendResources()
    {
        static $is_appended; //Используем статику для только первого подключения
        if (!$is_appended){
            //Подключим библиотеки и левое меню, чтобы можно было взаимодействовать
            $this->app->addCss('%designer%/app/bootstrap4.css');
            $this->app->addCss('%designer%/app/designer.css');
            $this->app
                ->addJs('%designer%/app/dist/vendor.bundle.js', null, null, true, [
                    'footer' => true,
                    'type' => 'module',
                    'defer' => 'defer'
                ])
                ->addJs('%designer%/app/dist/app.bundle.js', null, null, true, [
                    'footer' => true,
                    'type' => 'module',
                    'defer' => 'defer'
                ]);

            $blocksApi = new BlocksApi();
            $presetApi = new PresetApi();

            $designer_variables['designer'] = [
                 //Секция для блока дизайнеров
                'structures' => $blocksApi->getStructuresJSON(), //JSON сущностей системы
                'presets' => [
                    'categories' => $presetApi->getCategoryList(),
                    'list' => $presetApi->getPresetsWithCategoryInKeys(),
                ],
            ];
            //Служебные адреса
            $this->addServiceUrlsForDesigner($designer_variables['designer']);
            $this->addResourcesForAtoms($designer_variables);

            //Добавим константу о том, что подписка истекла.
            $this->addProAccountVariables($designer_variables);

            $this->app->addJsVar($designer_variables);

            if (\RS\Module\Manager::staticModuleExists('designeradmin') && \RS\Module\Manager::staticModuleEnabled('designeradmin')){
                $this->app->addJsVar([
                    'designer_admin' => [ //Секция для блока дизайнеров
                        'deletePresetUrl' => $this->router->getAdminUrl('deletePreset', ['ajax' => 1], 'designeradmin-presetctrl'),
                    ]
                ]);
            }

            $is_appended = true;
        }
    }

    /**
     * Устанавливает флан того, что блоки на фронте инициализированы
     */
    function isBlocksIsLoaded()
    {
        return self::$blocks_is_loaded;
    }

    /**
     * Устанавливает флан того, что блоки на фронте инициализированы
     */
    function setBlocksIsLoaded()
    {
        self::$blocks_is_loaded = true;
    }

    /**
     * Возвращает готовый JSON с параметрами блока
     *
     * @return array
     */
    function getJSONParams()
    {
        $settings = $this->getParam('settings');
        if (empty($settings)){
            return [];
        }

        $data = [
            'block_id' => $this->getModuleId(),
            'childs' => $this->getParam('settings')
        ];
        return json_encode($data, (defined('JSON_UNESCAPED_UNICODE')) ? JSON_UNESCAPED_UNICODE : 0);
        //return json_encode(BlocksApi::getStorageDataForBlocks(), (defined('JSON_UNESCAPED_UNICODE')) ? JSON_UNESCAPED_UNICODE : 0);
    }

    /**
     * Добавляет кнопки для блокадиацнера в режиме отладки
     *
     * @param \RS\Debug\Group $debug_group - панель отладки
     * @param array $settings - настройки блока
     * @throws \RS\Exception
     */
    private function addDebugButtonsToBlock($debug_group, $settings)
    {
        $button_d_hide = "d-hide";
        if (\RS\Module\Manager::staticModuleExists('designeradmin') && \RS\Module\Manager::staticModuleEnabled('designeradmin')){
            $debug_group->addTool('blocksave', new \RS\Debug\Tool\Edit($this->router->getAdminUrl('savePreset', ['block_id' => $this->getModuleId()], 'designeradmin-blocksctrl'), t('Сохранение блока'), [
                'attr' => [
                    'class' => 'debug-icon-save crud-edit d-module-button '.((empty($settings)) ? $button_d_hide : ""),
                    'data-title' => t('Сохранение блока'),
                    'data-block-id' => $this->getModuleId()
                ]
            ]));
        }
        $debug_group->addTool('blocksettings', new \RS\Debug\Tool\Create('', t('Настройки блока'), [
            'attr' => [
                'class' => 'debug-icon-options open-designer-row-settings d-module-button '.((empty($settings)) ? $button_d_hide : ""),
                'data-title' => t('Настройки дизайнера'),
                'data-block-id' => $this->getModuleId()
            ]
        ]));
        $debug_group->addTool('blockoptions', new \RS\Debug\Tool\Create('', t('Инструменты дизайна'), [
            'attr' => [
                'class' => 'debug-icon-create open-designer d-module-button '.((empty($settings)) ? $button_d_hide : ""),
                'data-title' => t('Инструменты дизайна'),
                'data-block-id' => $this->getModuleId()
            ]
        ]));

        //Добавляем ссылку в контекстное меню зоны блока. Будет отображаться при нажатии правой кнопки мыши на свободной зоне блока.
        if (empty($settings)){ //Если код компонента не назначен, то разрешим выбрать компнонент
            $debug_group->addTool('create', new \RS\Debug\Tool\Create('', t('Инструменты дизайна'), [
                'attr' => [
                    'class' => 'debug-icon-create open-designer d-module-button',
                    'data-title' => t('Инструменты дизайна'),
                    'data-block-id' => $this->getModuleId()
                ]
            ]));
        }
    }


    /**
     * Добавляет JS и CSS файлы из настроек блока
     *
     * @param array $settings - настройки блока
     * @throws \RS\Exception
     */
    private function addBlocksJSAndCSSFiles($settings)
    {
        $renderApi = RenderApi::getInstance();
        //Получение js
        $js_codes = $renderApi->renderBlockJSCodes($settings);
        if (!empty($js_codes)){
            $this->app->addJsCode('designer'.$this->getModuleId(), $js_codes);
        }
        $jss = $renderApi->getBlockAtomsJS($settings);
        if (!empty($jss)){
            foreach ($jss as $js){
                $basepath = null;
                if (mb_stripos($js, '%') === false){
                    $basepath = BP_ROOT;
                }
                $this->app->addJs($js, null, $basepath, false, [
                    'footer' => true,
                    'defer' => 'defer'
                ]);
            }
        }
        //Получение css
        $csss = $renderApi->getBlockAtomsCSS($settings);
        if (!empty($csss)){
            foreach ($csss as $css){
                $basepath = null;
                if (mb_stripos($css, '%') === false){
                    $basepath = BP_ROOT;
                }
                $this->app->addCss($css, null, $basepath);
            }
        }
    }

    /**
     * Инициализирует работу блока в режиме отладки
     *
     * @param \RS\Debug\Group | null $debug_group - группа инструментов отладки
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Exception
     */
    private function initDebugDesigner($debug_group)
    {
        $settings = $this->getParam('settings');
        $this->addDebugButtonsToBlock($debug_group, $settings);
        $this->appendResources();

        if ($this->isConstructorBlock()){ //Если это блок из конструктора, то добавим дополнительные данные адреса
            if (BlocksApi::isCanDesignerHaveFullBackground($this->getModuleId())){
                $settings['row']['is_can_be_background_full_width'] = true;
            }else{
                $settings['row']['is_can_be_background_full_width'] = false;
                $settings['row']['background_fullwidth'] = false; //Сбросим всегда для переместившегося
            }
            $this->setParam('settings', $settings);
        }

        $this->view->append([
            'settings' => $settings
        ]);
        return $this->result->setTemplate('blocks/designer/designer_debug.tpl');
    }

    /**
     * Инициализирует работу блока в публичной части
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     * @throws \SmartyException
     */
    private function initPublicDesigner()
    {
        $settings = $this->getParam('settings');
        $this->app->addCss('%designer%/app/bootstrap4.css');
        $this->app->addCss('%designer%/app/designer_front.css');
        $this->app->addCss(\Setup::$MODULE_FOLDER.'/designer/cache/css/block-'.$this->getModuleId().'.css', null, BP_ROOT);

        $renderApi = RenderApi::getInstance();
        if (BlocksApi::isHaveYandexMapAtomInBlock($settings)){
            if (!self::$yamap_js_loaded){
                $config = \RS\Config\Loader::byModule($this);
                $url = 'https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey='.($config['ya_map_api_key'] ? $config['ya_map_api_key'] : self::YA_MAP_API_KEY);
                $this->app->addJs($url, null, BP_ROOT);
                self::$yamap_js_loaded = true;
            }
        }
        if (BlocksApi::isHaveMenuAtomInBlock($settings) && !self::$mobile_css_loaded){
            $this->app->addCss(\Setup::$MODULE_FOLDER.'/designer/cache/css/mmenu.css', null, BP_ROOT);
            self::$mobile_css_loaded = true;
        }

        $this->addBlocksJSAndCSSFiles($settings);
        $this->view->assign([
            'html' => $renderApi->renderBlockHtml($this->getModuleId(), $settings)
        ]);
        return $this->result->setTemplate('blocks/designer/designer.tpl');
    }

    /**
     * Блок дизайнера
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     * @throws \SmartyException
     */
    function actionIndex()
    {
        //Назначим нужные нам стили
        $this->app->addJs('%designer%/designer.js', null, null, false, [
            'footer' => true,
            'defer' => 'defer'
        ]);

        if ($debug_group = $this->getDebugGroup()) { //Если включен режим отладки
            return $this->initDebugDesigner($debug_group);
        }
        return $this->initPublicDesigner();
    }


    /**
     * Возвращает true, если метод processResult должен дополнять HTML
     *
     * @return bool
     */
    protected function canProcessResult()
    {
        return $this->getDebugGroup()
               && !($this instanceof \Main\Controller\Block\MainContent);
    }

    /**
     * Возвращает true, если включен режим отладки
     *
     * @return bool
     */
    protected function isDebugModeEnabled()
    {
        return \RS\Debug\Mode::isEnabled();
    }
}