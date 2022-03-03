<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\AccessControl;

use RS\AccessControl\AutoCheckers\AutoCheckerInterface;
use RS\AccessControl\AutoCheckers\ControllerChecker;

/**
 * Объект прав модуля по умолчанию
 */
class DefaultModuleRights extends AbstractModuleRights
{
    const RIGHT_READ = 'read';
    const RIGHT_CREATE = 'create';
    const RIGHT_UPDATE = 'update';
    const RIGHT_DELETE = 'delete';

    /**
     * Возвращает древовидный список собственных прав модуля
     *
     * @return (Right|RightGroup)[]
     */
    protected function getSelfModuleRights()
    {
        return [
            new Right(self::RIGHT_READ, t('Чтение')),
            new Right(self::RIGHT_CREATE, t('Создание')),
            new Right(self::RIGHT_UPDATE, t('Изменение')),
            new Right(self::RIGHT_DELETE, t('Удаление')),
        ];
    }

    /**
     * Возвращает список собственных инструкций для автоматических проверок прав
     *
     * @return AutoCheckerInterface[]
     */
    protected function getSelfAutoCheckers()
    {
        return [
            new ControllerChecker('', '*', '*', [], self::RIGHT_READ, true),
        ];
    }
}
