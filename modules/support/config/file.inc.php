<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Config;
use \RS\Orm\Type;

class File extends \RS\Orm\ConfigObject
{
    
    function _init()
    {
        parent::_init()->append([
            'send_admin_message_notice' => new Type\Varchar([
                'maxLength' => '1',
                'description' => t('Уведомлять о личном сообщении администратора'),
                'listfromarray' => [[
                        'Y'    => t('Да'), 
                        'N'    => t('Нет'),
                ]
                ],
            ]),
            'send_user_message_notice' => new Type\Varchar([
                'maxLength' => '1',
                'description' => t('Уведомлять о личном сообщении пользователя'),
                'listfromarray' => [[
                        'Y'    => t('Да'), 
                        'N'    => t('Нет'),
                ]
                ],
            ]),
        ]);
    }               
}