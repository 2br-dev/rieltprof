<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm;
use \RS\Orm\Type;

/**
 * Реквизиты компании, принимающей платежи.
 * --/--
 * @property string $firm_name Наименование организации
 * @property string $firm_inn ИНН организации
 * @property string $firm_kpp КПП организации
 * @property string $firm_bank Наименование банка
 * @property string $firm_bik БИК
 * @property string $firm_rs Расчетный счет
 * @property string $firm_ks Корреспондентский счет
 * @property string $firm_director Фамилия, инициалы руководителя
 * @property string $firm_accountant Фамилия, инициалы главного бухгалтера
 * --\--
 */
class Company extends \RS\Orm\AbstractObject
{
    function _init()
    {
        $this->getPropertyIterator()->append([
            'firm_name' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Наименование организации'),
            ]),
            'firm_inn' => new Type\Varchar([
                'maxLength' => '12',
                'description' => t('ИНН организации'),
                'Attr' => [['size' => 20]],
            ]),
            'firm_kpp' => new Type\Varchar([
                'maxLength' => '12',
                'description' => t('КПП организации'),
                'Attr' => [['size' => 20]],
            ]),
            'firm_bank' => new Type\Varchar([
                'maxLength' => '255',
                'description' => t('Наименование банка'),
            ]),
            'firm_bik' => new Type\Varchar([
                'maxLength' => '10',
                'description' => t('БИК'),
            ]),
            'firm_rs' => new Type\Varchar([
                'maxLength' => '20',
                'description' => t('Расчетный счет'),
                'Attr' => [['size' => 25]],
            ]),
            'firm_ks' => new Type\Varchar([
                'maxLength' => '20',
                'description' => t('Корреспондентский счет'),
                'Attr' => [['size' => 25]],
            ]),
            'firm_director' => new Type\Varchar([
                'maxLength' => '70',
                'description' => t('Фамилия, инициалы руководителя'),
            ]),
            'firm_accountant' => new Type\Varchar([
                'maxLength' => '70',
                'description' => t('Фамилия, инициалы главного бухгалтера'),
            ])
        ]);
    }        
    
    /**
    * Возвращает объект хранилища
    * 
    * @return \RS\Orm\Storage\AbstractStorage
    */
    protected function getStorageInstance()
    {
        return new \RS\Orm\Storage\Stub($this);
    }
    
}
