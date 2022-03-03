<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\FilterType;

/**
 * Класс обеспечивает фильтрацию по номеру абонента в звонках
 */
class NotEmptyString extends \RS\Html\Filter\Type\Select
{
    protected $search_type = 'custom';

    function __construct($key, $title, $options = [])
    {
        parent::__construct($key, $title, [
            '' => t('Не важно'),
            '1' => t('Есть'),
            '0' => t('Нет')
        ], $options);
    }

    /**
     * Возвращает условие для выборки
     *
     * @return string
     */
    public function where_custom()
    {
        switch($this->getValue()) {
            case '1': return $this->getSqlKey()." != ''";
            case '0': return $this->getSqlKey()." = ''";

            default: return '';
        }
    }
}