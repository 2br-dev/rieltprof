<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Comments\Config;
use \RS\Orm\Type;

class File extends \RS\Orm\ConfigObject
{
	function _init()
	{
		parent::_init()->append([
            'need_moderate' => new Type\Varchar([
                'maxLength' => '1',
                'description' => t('Необходима премодерация до публикации отзыва'),
                'ListFromArray' => [[
                    'Y' => t('Да'), 
                    'N' => t('Нет')]],
                'Attr' => [['size' => 1]],
            ]),
            'allow_more_comments' => new Type\Integer([
                'maxLength' => '1',
                'description' => t('Разрешить несколько комментариев с одного IP адреса'),
                'CheckboxView' => [1,0]
            ]),
            'need_authorize' => new Type\Varchar([
                'maxLength' => '1',
                'description' => t('Только авторизованные пользователи могут оставить отзыв'),
                'ListFromArray' => [[
                    'Y' => t('Да'), 
                    'N' => t('Нет')
                ]],
                'Attr' => [['size' => 1]],
            ]),
            'widget_newlist_pagesize' => new Type\Integer([
                'maxLength' => '11',
                'description' => t('Количество последних комментариев отображаемых на виджете'),
            ]),
        ]);
	}
}
