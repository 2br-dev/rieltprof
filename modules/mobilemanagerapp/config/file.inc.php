<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Config;
use \RS\Orm\Type;

/**
* Настройки модуля
*/
class File extends \RS\Orm\ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'allow_user_groups' => new Type\ArrayList([
                'runtime' => false,            
                'description' => t('Группы пользователей, для которых доступно данное приложение'),
                'list' => [['\Users\Model\GroupApi','staticSelectList']],
                'size' => 7,
                'attr' => [['multiple' => true]]
            ]),
            'push_enable' => new Type\Integer([
                'description' => t('Включить Push уведомления для данного приложения?'),
                'checkboxView' => [1,0]
            ])
        ]);
    }
}
