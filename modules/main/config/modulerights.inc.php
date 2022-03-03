<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Config;

use RS\AccessControl\AutoCheckers\AutoCheckerInterface;
use RS\AccessControl\AutoCheckers\ControllerChecker;
use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Right;
use RS\AccessControl\RightGroup;

class ModuleRights extends DefaultModuleRights
{
    const RIGHT_WIDGET_CONTROL = 'widget_control';
    const RIGHT_DEBUG_MODE = 'debug_mode';
    const RIGHT_VIEW_LOGS = 'view_logs';

    const RIGHT_TRANSLATE_GENERATION = 'translate_generation';
    const RIGHT_TRANSLATE_UPDATE = 'translate_change';
    const RIGHT_TRANSLATE_DELETE = 'translate_delete';


    protected function getSelfModuleRights()
    {
        return [
            new RightGroup('main_general', t('Разное'), [
                new Right(self::RIGHT_READ, t('Чтение')),
                new Right(self::RIGHT_CREATE, t('Создание')),
                new Right(self::RIGHT_UPDATE, t('Изменение')),
                new Right(self::RIGHT_DELETE, t('Удаление')),
                new Right(self::RIGHT_WIDGET_CONTROL, t('Управление виджетами')),
                new Right(self::RIGHT_DEBUG_MODE, t('Возможность включить режим отладки')),
                new Right(self::RIGHT_VIEW_LOGS, t('Просмотр логов')),
            ]),

            new RightGroup('main_translate', t('Переводы'), [
                new Right(self::RIGHT_TRANSLATE_GENERATION, t('Генерация фраз')),
                new Right(self::RIGHT_TRANSLATE_UPDATE, t('Изменение перевода, импорт')),
                new Right(self::RIGHT_TRANSLATE_DELETE, t('Удаление фраз/языков')),
            ]),
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
            new ControllerChecker('main-admin-logview', '*', '*', [], self::RIGHT_VIEW_LOGS),
        ];
    }
}
