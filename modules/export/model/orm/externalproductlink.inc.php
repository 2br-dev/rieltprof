<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\Orm;

use RS\Orm\AbstractObject;
use RS\Orm\Type;
use RS\Orm\Request;

/**
 * ORM объект, отвечает за присвоение товару и комплектации внешнего ID в рамках профиля экспорта.
 * Также содержит флаг необходимности повторного экспорта товара по API
 * --/--
 * @property integer $profile_id id профиля экспорта
 * @property integer $product_id id товара на сайте
 * @property integer $offer_id id комплектации
 * @property integer $ext_id id товара во внешней системе
 * @property string $ext_data произвольные json данные связи
 * @property array $ext_data_array произвольные json данные связи, массив
 * @property integer $has_changed флаг изменения товара
 * @property string $hash Хэш от последних выгруженных данных
 * --\--
 */
class ExternalProductLink extends AbstractObject
{
    protected static
        $table = 'export_external_link';

    const
        EXPORT_ITEM_PRODUCT = 'product_id',
        EXPORT_ITEM_OFFER = 'offer_id',

        HAS_CHANGED_YES = 1,
        HAS_CHANGED_NO = 0;

    function _init()
    {
        $this->getPropertyIterator()->append([
            'profile_id' => new Type\Integer([
                'description' => t('id профиля экспорта')
            ]),
            'product_id' => new Type\Integer([
                'description' => t('id товара на сайте')
            ]),
            'offer_id' => new Type\Integer([
                'description' => t('id комплектации')
            ]),
            'ext_id' => new Type\Integer([
                'description' => t('id товара во внешней системе')
            ]),
            'ext_data' => new Type\Varchar([
                'maxLength' => 4000,
                'description' => t('произвольные json данные связи')
            ]),
            'ext_data_array' => new Type\ArrayList([
                'description' => t('произвольные json данные связи, массив')
            ]),
            'has_changed' => new Type\Integer([
                'description' => t('флаг изменения товара'),
                'checkboxView' => [1,0]
            ]),
            'hash' => new Type\Varchar([
                'maxLength' => 50,
                'description' => t('Хэш от последних выгруженных данных')
            ])
        ]);

        $this->addIndex(['profile_id', 'product_id', 'offer_id'], self::INDEX_UNIQUE);
    }

    /**
     * Обработчик загрузки ORM объекта
     *
     * @return void
     */
    public function afterObjectLoad()
    {
        $this['ext_data_array'] = @json_decode($this['ext_data'], true) ?: [];
    }

    /**
     * Обработчик сохранения ORM объекта
     *
     * @param bool $flag
     * @return void
     */
    public function beforeWrite($flag)
    {
        $this['ext_data'] = json_encode($this['ext_data_array'], JSON_UNESCAPED_UNICODE || JSON_UNESCAPED_SLASHES);
    }

    /**
     * Обновляет флаг необходимости экспорта товара/комплектации в ВК
     *
     * @param array $id - ID товара или комплектации
     * @param string $export_item - self::EXPORT_ITEM_PRODUCT или self::EXPORT_ITEM_OFFER - тип перданного $id
     * @param integer $profile_id - ID профиля экспорта
     *
     * @throws \RS\Db\Exception
     */
    public static function activateExport($id, $export_item = self::EXPORT_ITEM_PRODUCT, $profile_id = null)
    {
        $q = Request::make()
            ->update(new static)
            ->set([
                'has_changed' => self::HAS_CHANGED_YES
            ])
            ->where([
                $export_item => $id
            ]);

        if ($profile_id) {
            $q->where([
                'profile_id' => $profile_id
            ]);
        }

        $q->exec();
    }


    /**
     * Отмечает товар флагом о том, что его не нужно экспортировать
     *
     * @param array $id - ID товара или комплектации
     * @param string $export_item - self::EXPORT_ITEM_PRODUCT или self::EXPORT_ITEM_OFFER - тип перданного $id
     * @param integer $profile_id - ID профиля экспорта
     * @return void
     * @throws \RS\Db\Exception
     *
     */
    public static function deactivateExport($id, $export_item = self::EXPORT_ITEM_PRODUCT, $profile_id = null)
    {
        $q = Request::make()
            ->update(new static)
            ->set([
                'has_changed' => self::HAS_CHANGED_NO
            ])
            ->where([
                $export_item => $id
            ]);

        if ($profile_id) {
            $q->where([
                'profile_id' => $profile_id
            ]);
        }

        $q->exec();
    }

    /**
     * Устанавливает id товара во внешней системе
     *
     * @param $profile_id
     * @param $ext_id
     * @param $product_id
     * @param int $offer_id
     * @return bool
     */
    public static function setExternalId($profile_id, $ext_id, $product_id, $offer_id)
    {
        $link = new static();
        $link['profile_id'] = $profile_id;
        $link['ext_id'] = $ext_id;
        $link['product_id'] = $product_id;
        $link['offer_id'] = $offer_id;
        $link['has_changed'] = 0;
        return $link->replace();
    }

    /**
     * Получает id товара во внешней системе
     *
     * @param $profile_id
     * @param $product_id
     * @param $offer_id
     * @return mixed
     * @throws \RS\Db\Exception
     * @throws \RS\Exception
     */
    public function getExternalId($profile_id, $product_id, $offer_id)
    {
        $vk_id = Request::make()
            ->from(new static)
            ->where([
                'profile_id' => $profile_id,
                'product_id' => $product_id,
                'offer_id' => $offer_id
            ])
            ->exec()
            ->getOneField('vk_id');

        return $vk_id;
    }
}