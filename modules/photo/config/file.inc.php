<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Photo\Config;
use \RS\Orm\Type;

/**
* Конфигурационный файл модуля
*/
class File extends \RS\Orm\ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'original_photos_resize' => new Type\Integer([
                'description' => t('Изменять размер оригинальной фотографии при загрузке'),
                'checkboxview' => [1,0],
                'hint' => t('Включение данной опции позволит увеличить безопасность системы, а также увеличить скорость генерации изображений в последующем')
            ]),
            'original_photos_width' => new Type\Integer([
                'description' => t('Максимальная ширина оригинала фотографии')
            ]),
            'original_photos_height' => new Type\Integer([
                'description' => t('Максимальная высота оригинала фотографии')
            ]),
            'product_sort_photo_desc' => new Type\Integer([
                'maxLength' => 1,
                'default' => 0,
                'description' => t('Сортировать добавленные фото в обратном порядке?'),
                'checkboxview' => [1,0],
            ]),
        ]);
    }

    /**
    * Возвращает значения свойств по-умолчанию
    * 
    * @return array
    */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl('ajaxDelUnlinkPhotos', [], 'photo-tools'),
                    'title' => t('Удалить несвязанные фото'),
                    'description' => t('Удаляет оригиналы и миниатюры фотографий, на которые нет ссылок в базе'),
                    'confirm' => t('Вы действительно хотите удалить несвязанные фото?')
                ],
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl('ajaxDelPreviewPhotos', [], 'photo-tools'),
                    'title' => t('Удалить миниатюры фотографий'),
                    'description' => t('Удаляет автоматически сгенерированные по требованию шаблонов миниатюры фотографий'),
                    'confirm' => t('Вы действительно хотите удалить миниатюры всех фото?')
                ],
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl('ajaxDelDoublesPhotos', [], 'photo-tools'),
                    'title' => t('Удалить дубли фотографий в рамках одного товара'),
                    'description' => t('Удаляет у товара дубли фотографий (дублем считаются фотографии, у которых высчитан одинаковый хэш)'),
                    'confirm' => t('Вы действительно хотите удалить дубли фотографий?')
                ],
            ]
            ];
    }       
}

