<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\Orm;
use \RS\Orm\Type;

/**
 * Объект Push уведомления для отправки пользователям
 * --/--
 * @property string $title Заголовок сообщения
 * @property string $body Краткий текст
 * @property string $_send_type_ Тип уведомления
 * @property string $send_type Тип уведомления
 * @property string $message Текст для отправки пользователям
 * @property integer $product_id Товар для показа пользователю
 * @property integer $category_id Категория для показа пользователю
 * --\--
 */
class PushTokenMessage extends \RS\Orm\FormObject
{
    const //Типы сообщений
        TYPE_SIMPLE   = "Simple",
        TYPE_PAGE     = "Page",
        TYPE_PRODUCT  = "Product",
        TYPE_CATEGORY = "Category";
    
    function __construct()
    {
        parent::__construct(new \RS\Orm\PropertyIterator(
            [
                'title' => new Type\Varchar([
                    'description' => t('Заголовок сообщения'),
                ]),
                'body' => new Type\Varchar([
                    'description' => t('Краткий текст'),
                ]),
                '_send_type_' => new Type\Varchar([
                    'description' => t('Тип уведомления'),
                    'template' => '%pushsender%/form/pushtokenmessage/type.tpl',
                    'runtime' => true
                ]),
                'send_type' => new Type\Varchar([
                    'description' => t('Тип уведомления'),
                    'listFromArray' => [[
                        self::TYPE_SIMPLE => t('Текcтовое сообщение'),
                        self::TYPE_PAGE => t('Страница с текстом'),
                        self::TYPE_PRODUCT => t('Переход на товар'),
                        self::TYPE_CATEGORY => t('Переход в директорию')
                    ]],
                    'visible' => false
                ]),
                'message' => new Type\Richtext([
                    'description' => t('Текст для отправки пользователям'),
                    'visible' => false
                ]),
                'product_id' => new Type\Integer([
                    'description' => t('Товар для показа пользователю'),
                    'template' => '%pushsender%/form/pushtokenmessage/product_id.tpl',
                    'visible' => false
                ]),
                'category_id' => new Type\Integer([
                    'description' => t('Категория для показа пользователю'),
                    'template' => '%pushsender%/form/pushtokenmessage/category_id.tpl',
                    'visible' => false
                ])
            ]
        ));
    }
}                    
