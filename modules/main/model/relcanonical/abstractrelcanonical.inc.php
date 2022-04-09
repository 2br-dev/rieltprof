<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Main\Model\RelCanonical;

use RS\Config\Loader as ConfigLoader;
use RS\Event\Manager as EventManager;

/**
 * Абстрактный класс канонических ссылок
 */
abstract class AbstractRelCanonical
{
    /**
     * Возвращает название класса канонических ссылок
     *
     * @return string
     */
    abstract public function getTitle(): string;

    /**
     * Возвращает идентификатор класса канонических ссылок
     *
     * @return string
     */
    abstract public function getId(): string;

    /**
     * Возвращает описание класса канонических ссылок
     *
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * Действия перед рендерингом HTML
     * В этом методе добавляются канонические ссылки
     *
     * @return void
     */
    abstract public function onControllerBeforeWrap(): void;

    /**
     * Возвращает текущий класс канонических ссылок
     *
     * @return AbstractRelCanonical
     */
    public static function getRealCanonicalClass(): AbstractRelCanonical
    {
        $config = ConfigLoader::byModule('main');
        $classes = self::getClasses();
        return $classes[$config['rel_canonical_class']] ?? new RelCanonicalStub();
    }

    /**
     * Возвращает список доступных классов канонических ссылок
     *
     * @return string[]
     */
    public static function getList(): array
    {
        $list = [];
        foreach (self::getClasses() as $id => $class) {
            $list[$id] = [
                'title' => $class->getTitle(),
                'description' => $class->getDescription(),
            ];
        }
        return $list;
    }

    /**
     * Возвращает доступные классы канонических ссылок
     *
     * @return AbstractRelCanonical[]
     */
    public static function getClasses(): array
    {
        static $classes;
        if ($classes === null) {
            $classes = [];

            /** @var AbstractRelCanonical[] $classes */
            $list = EventManager::fire('getrelcanonicallist', [])->getResult();

            foreach ($list as $class) {
                $classes[$class->getId()] = $class;
            }
        }
        return $classes;
    }
}
