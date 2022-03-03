<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Menu\Config;

use Article\Model\Orm\Article;
use Menu\Model\Orm\Menu;
use RS\Module\AbstractPatches;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;

/**
 * Патчи к модулю
 */
class Patches extends AbstractPatches
{
    /**
     * Возвращает массив имен патчей.
     *
     * @return array
     */
    function init()
    {
        return [
            '20038',
            '20035',
            '20023',
            '402',
        ];
    }

    /**
     * Сбрасываем предыдущее закэшированное состояние объекта меню
     */
    function beforeUpdate20038()
    {
        $menu = new Menu();
        $menu->getPropertyIterator()->append([
            'content' => new Type\Richtext([
                'description' => t('Статья'),
            ]),
        ]);
        $menu->dbUpdate();
    }

    /**
     * Переносим привязаные статьи в объекты Меню
     *
     * @throws \RS\Db\Exception
     */
    function afterUpdate20038()
    {
        //Загружаем статьи
        $linked_articles = OrmRequest::make()
            ->from(new Article())
            ->where("alias like '#menu_%'")
            ->exec()
            ->fetchAll();

        foreach ($linked_articles as $item) {
            if (preg_match('/#menu_(\d+)/', $item['alias'], $match)) {
                OrmRequest::make()
                    ->update(new Menu())
                    ->set(['content' => $item['content']])
                    ->where([
                        'id' => $match[1]
                    ])->exec();
            }
        }
    }

    /**
     * Добавляем отсутствующее поле
     */
    function beforeUpdate20035()
    {
        $menu = new Menu();
        $menu->getPropertyIterator()->append([
            'link_template' => new Type\Template([
                'description' => t('Шаблон'),
                'visible' => false
            ]),
        ]);
        $menu->dbUpdate();
    }

    /**
     * Добавляем отсутствующее поле
     */
    function beforeUpdate20023()
    {
        $menu = new Menu();
        $menu->getPropertyIterator()->append([
            'target_blank' => new Type\Integer([
                'maxLength' => 1,
                'description' => t('Открывать ссылку в новом окне'),
                'checkboxView' => [1, 0],
                'default' => 0,
                'visible' => false
            ])
        ]);
        $menu->dbUpdate();
    }

    /**
     * Удаляем пункты меню типа "разделитель"
     */
    function beforeUpdate402()
    {
        OrmRequest::make()
            ->delete()
            ->from(new Menu())
            ->where(['typelink' => 'separator'])
            ->exec();
    }
}
