<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Event;

/**
 * Абстрактный класс обработчиков событий
 */
abstract class HandlerAbstract
{
    /**
     * Здесь должна происходить подписка на события
     */
    abstract public function init();

    /**
     * Подписывает обработчик на событие. Сокращенный синтаксис
     *
     * @param string|string[] $events - Событие или массив событий
     * @param callback|object|string $callback_class - Имя класса обработчика события или callback для вызова, если null, то подставляется $this
     * @param string $callback_method - Имя статического метода класса обработчика события
     * @param integer $priority - Приоритет выполнения события, чем выше, тем раньше будет выполнен обработчик
     * @return HandlerAbstract
     */
    function bind($events, $callback_class = null, $callback_method = null, $priority = 10)
    {
        if ($callback_class === null) {
            $callback_class = $this;
        }
        foreach ((array)$events as $event) {
            Manager::bind($event, [$callback_class, $callback_method], $priority);
        }
        return $this;
    }
}
