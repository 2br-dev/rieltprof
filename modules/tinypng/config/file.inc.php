<?php
namespace TinyPNG\Config;
use \RS\Orm\Type;

class File extends \RS\Orm\ConfigObject
{
    
    function _init()
    {
        parent::_init()->append(array(
            'portion_count' => new Type\Integer(array(
                'description' => t('Количество фото для обработки за раз'),
                'hint' => t('Чем больше тем больше ресурсов нужна сжатие'),
            )),
            'photo_count_in_stack' => new Type\Varchar(array(
                'description' => t('Количество необработанных фото'),
                'runtime' => true,   
                'template' => "%tinypng%/form/config/photo_count_in_stack.tpl",
            )),
            'quality' => new Type\Integer(array(
                'description' => t('Качество сжатия'),
                'hint' => t('от 1 до 100. По умолчанию - 92'),
            )),
        ));
    }
    
    /**
    * Возвращает значения свойств по-умолчанию
    * 
    * @return array
    */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + array(           
            'tools' => array(
                array(
                    'url' => \RS\Router\Manager::obj()->getAdminUrl('ajaxAddThemeImages', array(), 'tinypng-tools'),
                    'title' => t('Добавить фото из текущей темы для сжатия'),
                    'description' => t('Добавляет фото из текущей темы для сжатия в очередь')
                ),
                array(
                    'url' => \RS\Router\Manager::obj()->getAdminUrl('ajaxRestart', array(), 'tinypng-tools'),
                    'title' => t('Запустить заново компрессию'),
                    'description' => t('Заново начнёт отправлять все фото на сжатие'),
                    'confirm' => t('Вы действительно хотите?')
                )
            )
        );
    }
}