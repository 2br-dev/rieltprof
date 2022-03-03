<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Items;

use \Designer\Model\DesignAtoms;

/**
 * Class Video - класс атома видео
 */
class Video extends DesignAtoms\AbstractAtom {
    protected $title        = "Видео"; //Название компонента
    protected $tag          = "div";//Тег с помощью которого будет формироваться содержимое
    protected $image        = "video.svg"; //Картинка компонента

    protected static $ratio_classes = [ //Классы разрешения
        '21:9' => 'ratio21by9',
        '16:9' => 'ratio16by9',
        '4:3'  => 'ratio4by3',
        '1:1'  => 'ratio1by1'
    ];

    public static $reset_attrs = [ //CSS который сбрасывается при обнулении
        'type',
        'href',
        'ratio',
    ];
    public static $public_js       = [//Массив дополнительных JS, которые нужно подключить в публичной части
        '%designer%/atoms/video.js'
    ];

    /**
     * Конструктор класса
     */
    function __construct()
    {
        parent::__construct();

        $preset = new DesignAtoms\CSSPresets\AbstractCssPreset();
        $this->addMaxWidthAndAlignSelfCSSToPreset($preset);
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-size' => 'cover',
            'background-repeat' => 'no-repeat',
            'background-position' => 'center center'
        ]);
        $this->addCSSPreset([
            $background,
            new DesignAtoms\CSSPresets\Border(),
            $preset
        ]);

        $youtube = new \Designer\Model\DesignAtoms\Attrs\AttrSelect('type', t('Тип'), 'youtube');
        $youtube->setOptions([
            'youtube' => 'Youtube',
            'mp4' => t('Файл mp4'),
        ]);
        $ratio = new \Designer\Model\DesignAtoms\Attrs\Text('ratio', t('Соотношение сторон видео'));
        $ratio->setVisible(false);
        $this->setAttr([
            $youtube,
            $ratio,
            new \Designer\Model\DesignAtoms\Attrs\Link('src', t('Ссылка на видео')),
            new \Designer\Model\DesignAtoms\Attrs\ToggleCheckbox('autoplay', t('Автопроигрывание'), 1),
            new \Designer\Model\DesignAtoms\Attrs\ToggleCheckbox('loop', t('Зациклить видео'), 0)
        ]);
    }


    /**
     * Возвращает массив данных параметров для названия альбома
     *
     * @return array
     */
    public static function getChildParamsDataForPlayButton()
    {
        //Заголовки
        $background = new DesignAtoms\CSSPresets\Background();
        $background->setDefaults([
            'background-image' => '/modules/designer/view/img/iconsset/action/play.svg',
            'background-size' => '100px 100px',
            'background-repeat' => 'no-repeat',
            'background-position' => 'center center',
        ]);

        $field = new DesignAtoms\Items\SubAtom();
        $field->setTag('a')
            ->setTitle(t('Кнопка проигрывания'))
            ->setClass('d-atom-video-play')
            ->addCSSPreset([
                $background,
                new DesignAtoms\CSSPresets\Border()
            ]);
        return $field->getData();
    }


    /**
     * Возвращает массив данных параметров детей внутри составного элемента
     *
     * @return array
     */
    public static function getChildParamsData()
    {
        static $childs;
        if (!$childs){
            $childs['play_button'] = self::getChildParamsDataForPlayButton();
        }
        return $childs;
    }

    /**
     * Возвращает массив данных детей внутри составного элемента для отображения в публичной части
     *
     * @param string $video_url - адрес видео
     * @param string $type - тип видео
     * @param array $data - массив данных элемента
     * @return array|string
     */
    public static function getVideoPublicChilds($video_url, $type, $data)
    {
        if ($type == 'youtube'){
            if ($data['attrs']['autoplay']['value']){
                $video_url .= "?autoplay=1";
            }
            return self::getEmptySubAtomForRender('', 'iframe', [
                'src' => $video_url,
                'frameborder' => '0',
                'allow' => 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture',
                'allowfullscreen' => 'allowfullscreen',
            ]);
        }else{
            $child = self::getEmptySubAtomForRender('', 'source', [
                'src' => $video_url,
                'type' => 'video/mp4',
            ]);
            $video = self::getEmptySubAtomForRender('', 'video');
            if (!empty($data['css']['background-image']['value'])){
                $video['attrs']['poster']['value'] =  $data['css']['background-image']['value'];
            }
            if ($data['attrs']['autoplay']['value']){
                $video['attrs']['muted']['value']    = 'muted';
                $video['attrs']['autoplay']['value'] = 'autoplay';
                $video['attrs']['controls']['value'] = 'controls';
            }

            $video['childs'][] = $child;
            return $video;
        }
    }

    /**
     * Возвращает массив данных детей внутри составного элемента для отображения в публичной части
     *
     * @param array $data - массив данных элемента
     * @return array|string
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     */
    public static function getFillChildsDataForRender($data)
    {
        $children = "<p class='d-no-element'>" . t('Видео не назначено') . "</p>";

        if ($data['attrs']['src']['value']) { //Если видео назначено
            $src  = $data['attrs']['src']['value'];
            $type = $data['attrs']['type']['value'];

            $video_url = $src['protocol'].$src['href'];

            $children = [];
            $children_params = self::getChildParamsData();
            $play_wrapper = self::getEmptySubAtomForRender('d-atom-video-play-wrapper', 'div', [
                'style' => "background-image: url('".$data['css']['background-image']['value']."')"
            ]);
            $play_button = $children_params['play_button'];
            $v = explode("/", $video_url);
            if ($type == 'youtube') {
                $play_button['attrs']['data-video-id']['value'] = array_pop($v);
            }

            $play_wrapper['childs'][] = $play_button;

            $inner_class = 'd-atom-video-inner';
            if ($type == 'youtube'){
                $inner_class .= " ".self::$ratio_classes[$data['attrs']['ratio']['value']];
            }
            $wrapper_inner = self::getEmptySubAtomForRender($inner_class);
            if (!$data['attrs']['autoplay']['value']){
                $wrapper_inner['childs'][] = $play_wrapper;
            }
            $wrapper_inner['childs'][] = self::getVideoPublicChilds($video_url, $type, $data);

            $children[] = $wrapper_inner;
        }
        return $children;
    }
}