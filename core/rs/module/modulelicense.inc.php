<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Module;

use RS\Orm\AbstractObject;
use RS\Orm\Type;

/**
 * Класс описывает ORM объект - лицензии модулей
 */
class ModuleLicense extends AbstractObject
{
    protected static $table = 'module_license';

    protected function _init()
    {
        $this->getPropertyIterator()->append([
            'module' => (new Type\Varchar())
                ->setDescription(t('Имя модуля')),
            'data' => (new Type\Blob())
                ->setDescription(t('Данные лицензии')),
        ]);

        $this->addIndex('module', self::INDEX_PRIMARY);
    }
}
