<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Config;

/**
 * Патчи к модулю
 */
class Patches extends \RS\Module\AbstractPatches
{
    /**
     * Возвращает список имен существующих патчей
     */
    function init()
    {
        return [
            '20062',
        ];
    }


    /**
     * Отключаем опцию "Избранное" в темах оформления после обновления,
     * так как необходимо добавить блоки в конструктор сайта для использования данной опции
     */
    function afterUpdate20062()
    {
        $template_contexts = \RS\Orm\Request::make()
            ->from(new \Templates\Model\Orm\SectionContext())
            ->objects();

        if ($template_contexts) {
            //Обновляем имеющиеся настройки темы оформления
            foreach ($template_contexts as $context) {
                $options = $context['options_arr'];
                $options['enable_favorite'] = 0;

                \RS\Orm\Request::make()
                    ->update($context)
                    ->set([
                        'options' => serialize($options)
                    ])
                    ->where([
                        'site_id' => $context['site_id'],
                        'context' => $context['context']
                    ])
                    ->exec();
            }
        } else {
            //Добавляем настройки темы, если их не было на момент обновления
            $theme = \RS\Theme\Item::makeByContext('theme');
            $context = $theme->getContextOptions();
            $options = $context['options_arr'];
            $options['enable_favorite'] = 0;
            $context['options_arr'] = $options;
            $context->insert();
        }
    }
}