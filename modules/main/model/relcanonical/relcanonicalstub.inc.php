<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Main\Model\RelCanonical;

/**
 * Класс-заглушка для канонических ссылок
 */
class RelCanonicalStub extends AbstractRelCanonical
{
    /**
     * Возвращает название класса канонических ссылок
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('- Не использовать -');
    }

    /**
     * Возвращает идентификатор класса канонических ссылок
     *
     * @return string
     */
    public function getId(): string
    {
        return 'stub';
    }

    /**
     * Возвращает описание класса канонических ссылок
     *
     * @return string
     */
    public function getDescription(): string
    {
        return t('Канонические ссылки не добавляются.');
    }

    /**
     * Действия перед рендерингом HTML
     * В этом методе добавляются канонические ссылки
     *
     * @return void
     */
    public function onControllerBeforeWrap(): void
    {
    }
}
