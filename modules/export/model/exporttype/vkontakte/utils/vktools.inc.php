<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Vkontakte\Utils;

use Export\Model\ExportType\Vkontakte\Vkontakte;
use Export\Model\Orm\ExportProfile;
use Export\Model\Orm\Vk\VkCategory;
use Export\Model\Orm\Vk\VkCategoryLink;
use RS\Orm\Request;
use RS\Router\Manager;

/**
 * Класс содержит инструменты для работы
 */
class VkTools
{
    const
        ROOT_CAT_ID_OFFSET = 100000; //Смещение ID корневой категории VK, чтобы не было пересечений с дочерними

    /**
     * Загружает/Обновляет категории ВКонтакте для заданного профиля
     *
     * @param ExportProfile $profile - Профиль экспорта ВКонтакте
     */
    public function loadCategoryForProfile(ExportProfile $profile)
    {
        $token = $profile->getTypeObject()->getToken();
        if (!$token) {
            $profiles_url = Manager::obj()->getAdminUrl(false, [], 'export-ctrl');

            return t('Не получен AccessToken в профиле экспорта. Перейдите в раздел <a href="%href" target="_blank">Товары -> Экспорт</a> и нажмите на кнопку "Получить access Token"', [
                'href' => $profiles_url
            ]);
        }

        $request_params = [
            'access_token' => $token,
            'count' => 1000
        ];

        $vk = new VkQuery(5.95);
        $result = $vk->query($request_params, 'market.getCategories');

        $exists_ids = [];
        $exists_vk_ids = [];

        foreach($result['items'] as $item) {
            $parent_vk_name = $item['section']['name'];
            $parent_vk_id = self::ROOT_CAT_ID_OFFSET + $item['section']['id'];
            $parent_id = $this->getCategoryIdByVkId($profile['id'], $parent_vk_id, $parent_vk_name, 0);
            $exists_ids[$parent_id] = $parent_id;
            $exists_vk_ids[$parent_vk_id] = $parent_vk_id;

            $vk_name = $item['name'];
            $vk_id = $item['id'];
            $id = $this->getCategoryIdByVkId($profile['id'], $vk_id, $vk_name, $parent_id);
            $exists_ids[$id] = $id;
            $exists_vk_ids[$vk_id] = $vk_id;
        }

        $this->removeUnexistsCategory($profile['id'], $exists_ids, $exists_vk_ids);
        return true;
    }

    /**
     * Возвращает ID категории из VK в ReadyScript. Создает или возвращает ID существующей записи
     *
     * @param integer $profile_id ID Профиля экспорта
     * @param integer $vk_id ID категории из справочника ВК
     * @param string $title Название категории из справочника ВК
     * @param integer $parent_id ID родительской категории в справочнике RS
     */
    private function getCategoryIdByVkId($profile_id, $vk_id, $title, $parent_id)
    {
        static $cache = [];

        if (!isset($cache[$profile_id])) { //Только при вызове первый раз загружаем кэш
            $rows = Request::make()
                ->from(new VkCategory())
                ->where([
                    'profile_id' => $profile_id
                ])->exec()->fetchAll();

            $cache[$profile_id] = [];
            foreach($rows as $row) {
                $cache[$profile_id][$row['vk_id']] = $row['id'];
            }
        }

        if (!isset($cache[$profile_id][$vk_id])) { //Создаем запись
            $vk_category = new VkCategory();
            $vk_category['profile_id'] = $profile_id;
            $vk_category['title'] = $title;
            $vk_category['vk_id'] = $vk_id;
            $vk_category['parent_id'] = $parent_id;
            $vk_category->insert();

            $cache[$profile_id][$vk_id] = $vk_category['id'];
        }

        return $cache[$profile_id][$vk_id];
    }

    /**
     * Удаляет все категории, которые теперь стали отсутствовать во ВКонтакте
     *
     * @param [] $exists_id Список ID категорий, которые не нужно удалять
     * @param [] $exists_vk_ids Список VK_ID категорий связи с которыми не нужно удалять
     *
     * @return integer Возвращает количество удаленных категорий
     */
    private function removeUnexistsCategory($profile_id, $exists_ids, $exists_vk_ids)
    {
        if ($exists_ids && $exists_vk_ids) {
            $delete_count = Request::make()
                ->delete()
                ->from(new VkCategory())
                ->where([
                    'profile_id' => $profile_id
                ])
                ->where('id NOT IN (' . implode(',', $exists_ids) . ')')
                ->exec()->affectedRows();

            Request::make()
                ->delete()
                ->from(new VkCategoryLink())
                ->where([
                    'profile_id' => $profile_id
                ])
                ->where('vk_cat_id NOT IN (' . implode(',', $exists_vk_ids) . ')')
                ->exec();

            return $delete_count;
        }

        return 0;
    }


    /**
     * Возвращает данные о связях категории с категориями ВК в разделе профилей экспорта
     * Если передан $vk_profile_id, то будут возвращены данные только по одному профилю
     *
     * @param integer $dir_id
     * @param integer $profile_id
     * @return array
     * @throws \RS\Db\Exception
     */
    public function getCategoryLinksData($dir_id, $vk_profile_id = null)
    {
        $link_data = [
            'vk_profiles' => []
        ];

        //Заполняем секцию с профилями
        $profiles = $this->getVKProfilesList();

        //Заполняем данные о существующих линках
        foreach ($profiles as $profile) {
            $parent_list = $this->getParentCategoriesList($profile['id']);
            $categories = $this->getFinallyCategoriesList($parent_list);

            $profile_array = [
                'profile' => $profile,
                'profile_vk_categories' => $categories,
            ];

            $link_data['vk_profiles'][$profile['id']] = $profile_array;
        }

        return $vk_profile_id ?  $link_data['vk_profiles'][$vk_profile_id] : $link_data;
    }

    /**
     * Возвращает список профилей экспорта Вконтакте
     *
     * @return array
     * @throws \RS\Db\Exception
     */
    private function getVKProfilesList()
    {
        $vk_profile = new Vkontakte();

        $profiles = \RS\Orm\Request::make()
            ->from(new ExportProfile())
            ->where([
                'class' => $vk_profile->getShortName()
            ])
            ->objects();

        return $profiles;
    }

    /**
     * Возвращает список секций(родительских категорий) ВК
     *
     * @param $profie_id
     * @return array
     * @throws \RS\Db\Exception
     */
    private function getParentCategoriesList($profie_id)
    {
        $parent_categories = Request::make()
            ->from(new VkCategory())
            ->where([
                'profile_id' => $profie_id,
                'parent_id' => 0
            ])
            ->exec()
            ->fetchSelected('id', 'title' );

        return $parent_categories;
    }

    /**
     * Возвращает список потомсков для секции
     *
     * @param $parent_list
     * @return array
     * @throws \RS\Db\Exception
     */
    private function getFinallyCategoriesList($parent_list)
    {
        $categories = [];
        foreach ($parent_list as $id => $title) {
            $buffer = Request::make()
                ->from(new VkCategory())
                ->where([
                    'parent_id' => $id
                ])
                ->exec()
                ->fetchSelected('vk_id', 'title');

            $categories[$title] = $buffer;
        }

        return $categories;
    }
}