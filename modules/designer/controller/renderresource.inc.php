<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Controller;

use Designer\Model\RenderApi;
use RS\Controller\Front;

/**
 * Class RenderResource для рендеринга CSS и JS файлов
 * @package Designer\Controller\Front
 */
class RenderResource extends Front
{

    /**
     * Рендер файлов CSS и JS и их сохранения для блока дизайнера
     *
     * @throws \RS\Exception
     */
    function actionIndex()
    {
        $this->wrapOutput(false);
        $block_id = $this->request('id', TYPE_STRING, null);
        $type     = $this->request('type', TYPE_STRING, null);
        $ext      = $this->request('ext', TYPE_STRING, null);

        header('Content-type: text/css');

        switch ($type){
            case "mmenu": //Если нужно для мобильного меню
                echo RenderApi::getInstance()->createCSSFileForDesignerMMenu();
                break;
            case "block": //Если нужно для блока
            default:
                echo RenderApi::getInstance()->createCSSFileForDesignerBlock($block_id);
                break;
        }
    }
}

