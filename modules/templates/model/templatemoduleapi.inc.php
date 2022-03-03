<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Templates\Model;

use RS\Controller\Block;
use RS\Controller\Block as ControllerBlock;
use RS\Controller\StandartBlock;
use RS\Orm\ControllerParamObject;
use RS\Orm\Request as OrmRequest;
use Templates\Model\Orm\SectionModule;

/**
 * Апи блочного контроллера вставленного через moduleinsert в шаблоне
 */
class TemplateModuleApi
{
    /**
     * Возвращает полное имя класса контроллера по маршруту
     *
     * @param string $block_url
     * @return string
     */
    function getBlockClassByUrlName($block_url)
    {
        if (preg_match('/^([^\-]+?)\-(.*)$/', $block_url, $match)) {
            return str_replace('-', '\\', "-{$match[1]}-controller-{$match[2]}");
        }
        return '';
    }

    /**
     * Возращает блок с классом по его id в кэше
     * Если блока не находит, возражает false
     *
     * @param integer $cache_id - id в кэше блоков
     * @param string $url_name - сокращённое url имя блока
     *
     * @return object|false
     */
    function getBlockFromCache($cache_id, $url_name)
    {
        $block_class = $this->getBlockClassByUrlName($url_name); //Получим класс

        // параметры пришли, то обработаем
        if ($cache_id && !empty($block_class)) {
            $context = \RS\Theme\Manager::getCurrentTheme('blocks_context'); //Текущий контекст темы оформления

            $block = new $block_class([
                '_block_id' => $cache_id,
                'generate_by_template' => true,
                Block::BLOCK_INSERT_CONTEXT => $context
            ], $cache_id);

            return $block;
        }

        return false;
    }

    /**
     * Возвращает блоки, добавленные через moduleinsert
     *
     * @param string $context - контекст
     * @return SectionModule[]
     */
    public static function getBlocks($context)
    {
        /** @var SectionModule[] $modules */
        $modules = OrmRequest::make()
            ->from(new SectionModule())
            ->where('page_id is NULL and context = "#0"', [$context])
            ->objects();

        return $modules;
    }

    /**
     * Сохраняет новые значения блока в шаблоне
     *
     * @param StandartBlock $block - блочный контроллер из кэша
     * @param ControllerParamObject $values - значения параметров
     */
    function saveBlockValues($block, $values)
    {
        $block_properties = $block->getParamObject()->getPropertyIterator();
        $params = $block->getParam();
        $store_params = $block->getStoreParams(); //Ключи параметров которые должны быть подставлены
        //Получаем параметры со значениями для замены
        if (!empty($store_params)) {
            $values = $values->getValues();
            foreach ($store_params as $key) {
                if (isset($block_properties[$key]) && !in_array($key, $block->getNotReplaceableParams())) {
                    $value = $values[$key];
                    if (($value !== false) && ($value !== null)) {
                        $params[$key] = $value;
                    }
                }
            }
        }

        //Данные для поиска в шаблоне
        $module_info['name'] = get_class($block);                                        //Имя класса, блока
        $module_info['block_id'] = $block->getParam(ControllerBlock::BLOCK_ID_PARAM);  //Позиция, начиная с 1-цы
        $module_info['context'] = $block->getParam(ControllerBlock::BLOCK_INSERT_CONTEXT);

        if ($module_info['block_id']) { //Поиск если все параметры есть
            $this->saveParamsInDbByModule($module_info, $params);
        }
    }

    /**
     * Сохраняет параметры блока в БД (используется для блоков не по сетке)
     *
     * @param array $module_info
     * @param array $params
     * @return void
     */
    protected function saveParamsInDbByModule($module_info, $params)
    {
        $section_module = SectionModule::loadByWhere([
            'template_block_id' => $module_info['block_id'],
        ]);
        $section_module['template_block_id'] = $module_info['block_id'];
        $section_module['context'] = $module_info['context'];
        $section_module['module_controller'] = strtolower($module_info['name']);
        $section_module->setParams($params);

        if ($section_module['id']) {
            $section_module->update();
        } else {
            $section_module->insert();
        }
    }

    /**
     * Удаляет сохранённые в БД параметры блока
     *
     * @param integer $module_id - id модуля в БД
     * @return int
     */
    public static function deleteSavedParamsByModule($module_id)
    {
        return (new OrmRequest())
            ->delete()
            ->from(SectionModule::_getTable())
            ->where(['id' => $module_id])
            ->where("template_block_id != ''")
            ->exec()->affectedRows();
    }
}
