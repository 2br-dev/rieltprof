<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Controller\Admin\Helper;

/**
* Класс отвечает за панель, расположенную в шапке административной панели
*/
class HeaderPanel
{
    private static $instance;
    private static $public_instance;

    private $items = [];
    
    /**
    * Получить экземпляр можно через статический метод getInstance()
    */
    protected function __construct()
    {}
    
    /**
    * Возвращает экземпляр текущего класса для административной части
     *
    * @return self
    */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Возвращает экземпляр текущего класса для публичной части
     *
     * @return self
     */
    public static function getPublicInstance()
    {
        if (!isset(self::$public_instance)) {
            self::$public_instance = new self();
        }
        return self::$public_instance;
    }
    
    /**
    * Добавляет элемент
    * 
    * @param string $title - Подпись к ссылке
    * @param string $href - ссылка
    * @param mixed $attr - другие атрибуты. 
    * Зарезервированные ключи для атрибутов:
    * - icon - укажет класс для иконки кнопки rs-icon-<КЛАСС ИКОНКИ>
    * 
    * @param mixed $key - уникальный идентификатор элемента
    * @return self
    */
    public function addItem($title, $href, array $attr = [], $key = null)
    {
        if ($href !== null) {
            $attr = array_merge($attr, ['href' => $href]);
        }
        
        $item = [
                    'title' => $title,
                    'attr' => $attr
        ];
        
        if ($key !== null) {
            $this->items[$key] = $item;
        } else {
            $this->items[] = $item;
        }
        
        return $this;
    }
    
    /**
    * Удаляет элемент
    * 
    * @return self
    */
    public function removeItem($key)
    {
        unset($this->items[$key]);
    }
    
    /**
    * Возвращает список элементов
    * 
    * @return array
    */
    public function getItems()
    {
        return $this->items;
    }
}
