<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ModControl\Model;

use RS\Module\Manager;

/**
 * Поиск по настройкам всех модулей
 */
class SearchOptionsApi
{
    /**
     * Производит поиск по всем настройкам всех модулей
     *
     * @param $term
     * @return array
     */
    public function search($term)
    {
        $all_settings = $this->getAllModulesSettings();

        foreach($all_settings as $module => $tabs) {
            foreach($tabs['items'] as $tab => $fields) {
                foreach($fields['items'] as $n => $field) {
                    if ($term && (mb_stripos($field['field_title'], $term) !== false
                                    || mb_stripos($field['field_hint'], $term) !== false)) {

                        $all_settings[$module]['items'][$tab]['items'][$n]['field_title'] = $this->highlight($term, $field['field_title']);
                        $all_settings[$module]['items'][$tab]['items'][$n]['field_hint'] = $this->highlight($term, $field['field_hint']);
                    } else {
                        unset($all_settings[$module]['items'][$tab]['items'][$n]);
                    }
                }

                if (empty($all_settings[$module]['items'][$tab]['items'])) {
                    unset($all_settings[$module]['items'][$tab]);
                }
            }

            if (empty($all_settings[$module]['items'])) {
                unset($all_settings[$module]);
            }
        }

        return $all_settings;
    }

    /**
     * Подсвечивает найденное вхождение в строку
     *
     * @param $term
     * @param $string
     * @return string
     */
    public function highlight($term, $string)
    {
        $term_preg = preg_quote($term);
        return preg_replace('/('.$term_preg.')/iu', '<b>\1</b>', $string);
    }


    /**
     * Возвращает список всех опций всех модулей
     *
     * @param bool $cache - Если true, то данные будут возвращены из кэша
     * @return array
     */
    public function getAllModulesSettings($cache = true)
    {
        if ($cache) {
            return \RS\Cache\Manager::obj()
                ->request([$this, __FUNCTION__], false);
        } else {
            $settings = [];
            $router = \RS\Router\Manager::obj();

            $modules_api = new Manager();
            $modules = $modules_api->getList();

            foreach ($modules as $module) {
                $config = $module->getConfig();
                $groups = $config->getPropertyIterator()->getGroups(false);

                $module_config_url = $router->getAdminUrl('edit', ['mod' => $module->getName()], 'modcontrol-control');

                $groups_data = [];
                foreach ($groups as $n => $data) {
                    /**
                     * @var $field \RS\Orm\Type\AbstractType
                     */
                    $fields_data = [];
                    foreach ($data['items'] as $field) {
                        $fields_data[] = [
                            'field_title' => $field->getDescription(),
                            'field_hint' => $field->getHint(),
                            'tab_index' => $n,
                            'module' => $module->getName(),
                            'url' => $module_config_url.'#tab-'.$n.'-'.urlencode($field->getName()),
                            'is_tool' => false
                        ];
                    }

                    if ($fields_data) {
                        $groups_data[$data['group']] = [
                            'title' => $data['group'],
                            'url' => $module_config_url.'#tab-'.$n,
                            'items' => $fields_data
                        ];
                    }
                }

                if ($config['tools']) {
                    $fields_data = [];
                    foreach($config['tools'] as $n => $tool) {
                        $fields_data[] = [
                            'field_title' => isset($tool['title']) ? $tool['title'] : t('Нет названия'),
                            'field_hint' => isset($tool['description']) ? $tool['description'] : '',
                            'is_tool' => true,
                            'module' => $module->getName(),
                            'url' => $module_config_url.'#tab-0-action~'.$n,
                        ];
                    }

                    if ($fields_data) {
                        $group_name = t('Утилиты');
                        $groups_data[$group_name] = [
                            'title' => $group_name,
                            'url' => $module_config_url.'#actions',
                            'items' => $fields_data
                        ];
                    }
                }

                if ($groups_data) {
                    $settings[$module->getName()] = [
                        'title' => $config['name'],
                        'url' => $module_config_url,
                        'items' => $groups_data
                    ];
                }
            }

            return $settings;
        }
    }
}