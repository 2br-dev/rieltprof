<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Config;

use Crm\Model\Orm\Deal;
use Crm\Model\Orm\Task;
use RS\Config\Loader;
use RS\Module\AbstractPatches;
use RS\Orm\Request;

class Patches extends AbstractPatches
{
    /**
     * Возвращает массив имен патчей.
     * В классе должны быть пределены методы:
     * beforeUpdate<ИМЯ_ПАТЧА> или
     * afterUPDATE<ИМЯ_ПАТЧА>
     *
     * @return array
     */
    function init()
    {
        return [
            '405',
            '4013'
        ];
    }

    /**
     * Устанавливаем сортировочный индекс по умолчанию
     */
    function afterUpdate405()
    {
        Request::make()
            ->update(new Task)
            ->set('board_sortn = id')
            ->where('board_sortn IS NULL')
            ->exec();

        Request::make()
            ->update(new Deal)
            ->set('board_sortn = id')
            ->where('board_sortn IS NULL')
            ->exec();
    }

    /**
     * Устанавливает секретный ключ по умолчанию
     */
    function afterUpdate4013()
    {
        $config = Loader::byModule($this);
        if (!$config['tel_secret_key']) {
            $config['tel_secret_key'] = md5(\Setup::$SECRET_KEY . '- TELEPHONY SECRET -' . \Setup::$SECRET_SALT);
            $config->update();
        }
    }
}