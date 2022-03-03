<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExtCsv\Config;

use RS\Orm\ConfigObject;
use RS\Orm\Type;

/**
 * Конфигурационный файл модуля
 */
class File extends ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'csv_id_fields' => new Type\ArrayList([
                'runtime' => false,
                'description' => t('Поля для идентификации товара при импорте (удерживая CTRL можно выбрать несколько полей)'),
                'hint' => t('Во время импорта данных из CSV файла, система сперва будет обновлять товары, у которых будет совпадение значений по указанным здесь колонкам. В противном случае будет создаваться новый товар'),
                'list' => [['\ExtCsv\Model\CsvSchema\Product', 'getPossibleIdFields']],
                'size' => 7,
                'attr' => [['multiple' => true]]
            ]),
            'csv_recommended_id_field' => new Type\Varchar([
                'description' => t('Поле идентификации рекомендуемых товаров'),
                'list' => [['\ExtCsv\Model\CsvSchema\Product', 'getPossibleIdFields']]
            ]),
            'csv_concomitant_id_field' => new Type\Varchar([
                'description' => t('Поле идентификации сопутствующих товаров'),
                'list' => [['\ExtCsv\Model\CsvSchema\Product', 'getPossibleIdFields']]
            ]),
        ]);
    }
}
