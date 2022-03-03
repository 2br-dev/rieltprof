<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\Orm\Vk;

use RS\Orm\AbstractObject;
use RS\Orm\Request;
use RS\Orm\Type;

/**
 * ORM объект описывает связь категории RS с категорией VK в разрезе профилей экспорта
 * --/--
 * @property integer $dir_id ID категории RS
 * @property integer $profile_id ID профиля
 * @property integer $vk_cat_id ID категории ВКонтакте
 * --\--
 */
class VkCategoryLink extends AbstractObject
{
    protected static
        $table = 'export_vk_cat_link';

    function _init()
    {
        $this->getPropertyIterator()->append([
            'dir_id' => new Type\Integer([
                'description' => t('ID категории RS')
            ]),
            'profile_id' => new Type\Integer([
                'description' => t('ID профиля')
            ]),
            'vk_cat_id' => new Type\Integer([
                'description' => t('ID категории ВКонтакте')
            ])
        ]);

        $this->addIndex(['dir_id', 'profile_id', 'vk_cat_id'], self::INDEX_PRIMARY);
    }

    /**
     * Возвращает имя свойства, которое помечено как первичный ключ.
     * Для совместимости с предыдущими версиями, метод ищет первичный ключ в свойствах.
     *
     * С целью увеличения производительности необходимо у наследников реализовать явное
     * возвращение свойств, отвечающих за первичный ключ.
     *
     * @return string | array | false - false в случае отсутствия такого свойства
     */
    public function getPrimaryKeyProperty()
    {
        return ['dir_id', 'profile_id', 'vk_cat_id'];
    }

    /**
     * Возвращает ID категории ВКонтакте для категории RS в рамках профиля экспорта
     *
     * @param integer $profile_id - ID профиля
     * @param integer $site_category_id - ID категории RS
     * @return bool|mixed
     * @throws \RS\Db\Exception
     */
    public static function getVkId($profile_id, $site_category_id)
    {
        $vk_cat_id = Request::make()
            ->from(new self())
            ->where([
                'dir_id' => $site_category_id,
                'profile_id' => $profile_id
            ])
            ->exec()
            ->getOneField('vk_cat_id', false);

        return $vk_cat_id;
    }

    /**
     * Сохраняет связь категорий RS с категориями VK
     *
     * @param array|integer $dir_ids - ID одной или нескольких категории RS
     * @param array $profile_data - массив [['ID профиля'] => ID категории VK]
     * @throws \RS\Db\Exception
     *
     * @return bool
     */
    public static function saveLinks($dir_ids, $profile_data)
    {
        $dir_ids = (array)$dir_ids;

        if ($dir_ids) {
            Request::make()
                ->delete()
                ->from(new self())
                ->whereIn('dir_id', $dir_ids)
                ->exec();

            foreach ($profile_data as $profile_id => $vk_id) {
                if ($vk_id > 0) {
                    foreach($dir_ids as $dir_id) {
                        $link = new self();
                        $link['dir_id'] = $dir_id;
                        $link['profile_id'] = $profile_id;
                        $link['vk_cat_id'] = $vk_id;
                        $link->insert();
                    }
                }
            }
        }
    }
}