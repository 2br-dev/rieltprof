<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Model;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

class CloneThemeApi extends \RS\Module\AbstractModel\BaseModel
{
    /**
    * Клонирует тему оформления 
    * 
    * @param string $source_theme - идентификатор исходной темы
    * @param string $new_name - новый идентификатор темы
    * @param string $new_title - новое название темы
    * @param string $new_author - новый автор темы
    * @param string $new_descr - новое описание темы
    * @param bool $set_new_theme - Если true, то новая тема будет установлена в системе, иначе будет просто создана новая тема
    */
    function cloneTheme($source_theme, $new_name, $new_title, $new_author, $new_descr, $set_new_theme = false)
    {
        if ($acl_err = Rights::CheckRightError($this, DefaultModuleRights::RIGHT_CREATE)) {
            return $this->addError($acl_err);
        }
        
        $this->cleanErrors();
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $new_name)) {
            $this->addError(t('Неверно задан новый идентификатор'), t('Новый идентификатор (Англ. яз)'), 'new_name');   
        } else {
            $new_theme_path = \Setup::$SM_TEMPLATE_PATH.$new_name;
            if (file_exists($new_theme_path)) {
                $this->addError(t('Тема с таким идентификатором уже существует'), t('Новый идентификатор (Англ. яз)'), 'new_name');
            }
        }
        if (!trim($new_title)) {
            $this->addError(t('Укажите название новой темы оформления'), t('Новое название темы'), 'new_title');
        }
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $source_theme)) {
            $this->addError(t('Недопустимые символы в идентификаторе исходной темы'), t('Исходная тема'), 'source_theme');
        } else {
            $source_theme_path = \Setup::$SM_TEMPLATE_PATH.$source_theme;
        }
        
        if (!$this->hasError()) {
            //Клонируем тему
            \RS\File\Tools::moveWithReplace($source_theme_path, $new_theme_path, false, true);
            
            //Корректируем theme.xml
            $theme = new \RS\Theme\Item($new_name);
            $xml = $theme->getThemeXml();
            $xml->general->name = $new_title;
            $xml->general->author = $new_author;
            $xml->general->description = $new_descr;
            $xml->asXML($theme->getThemeXmlFilename());
            
            //Устанавливаем тему
            if ($set_new_theme) {
                if ($shades = array_keys($theme->getShades())) {
                    $theme->init($new_name."({$shades[0]})");
                }
                $theme->setThisTheme();        
            }
            return true;
        }
        
        return false;
    }
}