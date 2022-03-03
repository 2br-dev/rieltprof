<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\AccessControl;

use RS\Application\Auth;
use RS\Config\Loader as ConfigLoader;
use RS\Module\Item as ModuleItemr;
use RS\Module\Manager as ModuleManager;
use Users\Config\File as UsersConfig;

/**
 * Отвечает за проверку прав доступа
 */
class Rights
{
    /** Идентификатор системного модуля */
    protected static $default_module = 'main';

    /**
     * Возвращает false - если у модуля $mod_name имеется разрешение на $right иначе текст ошибки
     *
     * @param object|string $module - сокращенное имя модуля или любой объект модуля
     * @param string $right - идентификатор права
     * @param bool $ignore_missing_rights - не считать ошибкой отсутствие в модуле проверяемого права
     * @return bool|string
     * @throws \RS\Exception
     */
    public static function CheckRightError($module, $right, $ignore_missing_rights = false)
    {
        if (gettype($module) == 'object') { //Извлекаем из имени класса имя модуля
            //Если из имени класса не удалось извлечь имя модуля, то уравниваем его права с системным модулем
            $module = ModuleItemr::nameByObject($module, self::$default_module);
        }
        //Если у модуля нет конфигурационного файла, то уравниваем его права с системным модулем
        if (!ModuleManager::staticModuleExists($module)) {
            $module = self::$default_module;
        }

        list($right, $ignore_missing_rights) = self::compatibilityTransform($right, $ignore_missing_rights); // Для совместимости со старой системой прав (08.18)

        $module_config = ConfigLoader::byModule($module);
        $module_rights = $module_config->getModuleRightObject();
        if (!$ignore_missing_rights && !$module_rights->hasRight($right)) {
            return t('Проверка несуществующего права "%right" к модулю "%module"', [
                'right' => $right,
                'module' => $module_config['name'],
            ]);
        }

        $user = Auth::getCurrentUser();
        if (!$user->checkModuleRight($module, $right)) {
            $error = t('Недостаточно прав. Необходимы права на "%right" к модулю "%module"', [
                'right' => $module_rights->getRightTitleWithPath($right),
                'module' => $module_config['name'],
            ]);
            return $error;
        }
        return false;
    }

    /**
     * Возвращает true, если есть разрешение на указанное право $right. Иначе - false.
     * Применяется, если нет необходимости выводить текст ошибки.
     *
     * @param object|string $module - сокращенное имя модуля или любой объект модуля
     * @param string $right - идентификатор права
     * @param bool $ignore_missing_rights - игнорировать отсутствие проверяеиого права
     * @return bool
     * @throws \RS\Exception
     */
    public static function hasRight($module, $right, $ignore_missing_rights = false)
    {
        return self::CheckRightError($module, $right, $ignore_missing_rights) === false;
    }

    /**
     * Для совместимости со старой системой прав (08.18)
     * Преобразовывает идентификатор проверяемого права
     *
     * @param int|string $right - идентификатор права
     * @param bool $ignore_missing_rights - игнорировать отсутствие проверяеиого права
     * @return array
     */
    private static function compatibilityTransform($right, $ignore_missing_rights)
    {
        if (!isset(UsersConfig::$access_system_version)) {
            if (is_numeric($right)) {
                if ($right == ACCESS_BIT_READ) {
                    $right = DefaultModuleRights::RIGHT_READ;
                }
                if ($right == ACCESS_BIT_WRITE) {
                    $right = DefaultModuleRights::RIGHT_UPDATE;
                }
                $ignore_missing_rights = true;
            } else {
                $right = ($right == DefaultModuleRights::RIGHT_READ) ? ACCESS_BIT_READ : ACCESS_BIT_WRITE;
            }
        }

        return [$right, $ignore_missing_rights];
    }
}
