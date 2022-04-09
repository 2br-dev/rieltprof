<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Model\OrmType;

use RS\Orm\Type;

/**
 * Класс, отвечающий
 */
class ImageSelect extends Type\Varchar
{
    protected $image_path = '/theme/settings'; //Путь к изображениям относительно корня сайта
    protected $form_template = '%templates%/admin/ormtype/image_select.tpl';
    protected $ext_priority = ['svg', 'png', 'jpg', 'gif'];

    /**
     * Возвращает список изображений для списка
     */
    function getFrames()
    {
        $frames = [];
        $theme = \RS\Theme\Manager::getCurrentTheme('theme');
        $theme_relative = \Setup::$SM_RELATIVE_TEMPLATE_PATH.'/'.$theme.$this->image_path;

        foreach($this->getList() as $key => $value) {
            foreach($this->ext_priority as $ext) {
                $filepath = $theme_relative.'/'.$this->getName().'/'.$key.'.'.$ext;
                if (file_exists(\Setup::$ROOT.$filepath)) {
                    $filepath_relative = $filepath;
                }
            }

            $frames[] = [
                'value' => $key,
                'title' => $value,
                'image' => $filepath_relative ?? ''
            ];
        }

        return $frames;
    }

    /**
     * Устанавливает расширение для изображений
     *
     * @param $ext
     */
    function setImageExtension($ext)
    {
        $this->ext = $ext;
    }

    /**
     *
     * @param bool $multiedit
     * @return string
     */
    public function getRenderTemplate($multiedit = false)
    {
        return $this->form_template;
    }
}