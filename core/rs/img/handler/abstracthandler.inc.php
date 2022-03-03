<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Img\Handler;

use RS\Controller\ExceptionPageNotFound;
use RS\Controller\IController;
use RS\Http\Request;
use RS\Img\Core;
use RS\Img\Exception;

/**
* Базовый абстрактный класс для всех обработчиков изображений
*/
abstract class AbstractHandler implements IController
{
    protected
        $width,
        $height,
        $scale,
        $pic_id,
        $ext_detail = [], //[0] - оригинал [1] - доп. расширение
        $hash,
        
        $url,
        //Должны быть заданы у наследника
        $srcFolder,  //папка с оригиналами изображений (относительный путь от корня)
        $dstFolder;  //папка с измененными изображениями(относительный путь от корня)


    /**
    * Конструктор, вызываемый из роутера
    * 
    * @param mixed $type_section - секция URL, в которой содержится информация о размере и типе масштабирования изображения
    * @param mixed $picid_section - секция URL, в которой содержится id изображения
    * @return CImg_Handler_Interface
    */        
    function __construct()
    {
        if (!isset($this->srcFolder) || !isset($this->dstFolder)) {
            throw new Exception(t("У класса %0 не заданы свойства srcFolder или dstFolder", [get_class($this)]));
        }
        $this->url = Request::commonInstance();
    }
    
    function presetAct($act)
    {}
    
    function parseParameters()
    {
        if (preg_match('/^(.+?)_(\d+)x(\d+)$/', $this->url->get('type', TYPE_STRING), $match)) {
            $this->scale = $match[1];
            $this->width = $match[2];
            $this->height = $match[3];
        } else {
            throw new Exception(t('Неверный URL картинки'));
        }
        
        if (preg_match('/^(.+?)_(.+?)$/', $this->url->get('picid', TYPE_STRING), $match)) {
            $ext = $this->url->get('ext', TYPE_STRING);
            $this->ext_detail = explode('.',$ext);// [0] - оригинал [1] - доп. расширение

            $this->pic_id = $match[1].'.'.$ext;
            $this->hash = $match[2];
        } else {
            throw new Exception(t('Неверный URL или неверная подпись ссылки'));
        }        
    }
    
    function exec()
    {
        $this->parseParameters();

        try {
            $img = new Core(\Setup::$ROOT, \Setup::$FOLDER.$this->srcFolder, \Setup::$FOLDER.$this->dstFolder);
            $img->toOutput($this->pic_id, $this->width, $this->height, $this->scale, $this->hash);
        } catch (Exception $e) {
            throw new ExceptionPageNotFound($e->getMessage(), get_class($this));
        }
        
        return true;        
    }
}

