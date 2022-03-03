<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Filter;

use RS\Html\AbstractHtml;
use RS\View\Engine;

/**
* Абстрактный класс контейнера для фильтров. 
* Контейнер для фильтров может содержать Линии.
* Линия - объект, отвечающий за отображение одной строки с поисковыми формами
*/
abstract class AbstractContainer extends AbstractHtml
{
  public 
        $uniq,    
        $open = false;
        
    protected
        $tpl,
        $lines = [];
    
    protected static 
        $inc = 0; //Необходим для установки уникального номера на странице        
        
    /**
    * Конструктор контейнера для фильтров 
    * 
    * @param array $options - массив для быстрой инициализации класса. 
    * Ключи массива - это имена методов текущего класса, без префикса set... или add...
    * Значения массива будут переданы первым аргуметом в соответствующий метод
    * array(
    *   'Lines' => null
    * )
    * вызовет $this->setLines(null)
    * 
    * @return AbstractContainer
    */
    function __construct(array $options = [])
    {
        $this->uniq = self::getNextInc();
        parent::__construct($options);
    }
    
    
    /**
    * Возвращает следующий уникальный номер для контейнера
    * 
    * @return integer
    */
    public static function getNextInc()
    {
        self::$inc++;
        return self::$inc;
    }    

    /**
    * Устнавливает флаг "открытости контейнера". 
    * Если true, то контейнер визуально будет отображаться.
    * Если false, то контейнер визуально будет скрыт.
    * 
    * @param bool $is_open
    * @return AbstractContainer
    */
    function setOpen($is_open)
    {
        $this->open = $is_open;
        return $this;
    }    
    
    /**
    * Добавляет массив с объектами линий.
    * Линия - это объект, который отображает одну строку с формами.
    * 
    * @param array of Line $lines - массив линий
    * @return AbstractContainer
    */
    function addLines(array $lines)
    {
        foreach($lines as $line) {
            $this->addLine($line);
        }
        return $this;
    }    
            
    /**
    * Добавляет одну линию в контейнер.
    * 
    * @param Line $line
    * @return AbstractContainer
    */
    function addLine(Line $line)
    {
        $this->lines[] = $line;
        return $this;
    }
    
    /**
    * Возвращает массив добавленных линий к контейнеру
    * 
    * @return Line[]
    */
    function getLines()
    {
        return $this->lines;
    }
    
    /**
    * Возвращает HTML код контейнера
    * @return string
    */
    function getView()
    {
        $tpl = new Engine();
        $tpl->assign('fcontainer', $this);
        return $tpl->fetch($this->tpl);
    }
}

