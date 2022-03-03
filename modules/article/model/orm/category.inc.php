<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Article\Model\Orm;

use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название
 * @property string $alias Псевдоним(Ан.яз)
 * @property integer $parent Родительская категория
 * @property integer $public Показывать на сайте?
 * @property integer $sortn Сортировочный индекс
 * @property integer $use_in_sitemap Добавлять в sitemap
 * @property string $meta_title Заголовок
 * @property string $meta_keywords Ключевые слова
 * @property string $meta_description Описание
 * --\--
 */
class Category extends OrmObject
{
    protected static
        $table = "article_category";

    protected function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'site_id' => new Type\CurrentSite(),
                'title' => new Type\Varchar([
                    'maxLength' => '150',
                    'description' => t('Название'),
                    'Checker' => ['chkEmpty', t('Необходимо заполнить поле название')],
                ]),
                'alias' => new Type\Varchar([
                    'maxLength' => '150',
                    'description' => t('Псевдоним(Ан.яз)'),
                ]),
                'parent' => new Type\Integer([
                    'description' => t('Родительская категория'),
                    'tree' => [['\Article\Model\CatApi', 'staticTreeList'], 0, ['' => t('- Верхний уровень -')]],
                ]),
                'public' => new Type\Integer([
                    'description' => t('Показывать на сайте?'),
                    'maxLength' => 1,
                    'default' => 1,
                    'checkboxView' => [1, 0]
                ]),
                'sortn' => new Type\Integer([
                    'description' => t('Сортировочный индекс'),
                    'maxLength' => '11',
                    'visible' => false,
                ]),
                'use_in_sitemap' => new Type\Integer([
                    'description' => t('Добавлять в sitemap'),
                    'checkboxView' => [1, 0]
                ]),
                t('Мета тэги'),
                'meta_title' => new Type\Varchar([
                    'maxLength' => 1000,
                    'description' => t('Заголовок'),
                ]),
                'meta_keywords' => new Type\Varchar([
                    'maxLength' => 1000,
                    'description' => t('Ключевые слова'),
                ]),
                'meta_description' => new Type\Varchar([
                    'maxLength' => 1000,
                    'viewAsTextarea' => true,
                    'description' => t('Описание'),
                ]),
        ]);

        $this->addIndex(['parent', 'site_id', 'alias'], self::INDEX_UNIQUE);
    }

    /**
     * Действия перед записью объекта
     *
     * @param string $save_flag - insert или update
     * @return null
     * @throws \RS\Db\Exception
     */
    function beforeWrite($save_flag)
    {
        if ($save_flag == self::INSERT_FLAG) {
            $this['sortn'] = OrmRequest::make()
                ->select('MAX(sortn)+1 last_sortn')
                ->from($this)
                ->exec()
                ->getOneField('last_sortn', 1);
        }

        if ($save_flag == self::UPDATE_FLAG) {
            if ($this['parent'] == $this['id']) {
                $this->addError(t('Не верно указана родительская категория'));
                return false;
            }
        }

        if ($this['alias'] === '') {
            $this['alias'] = null;
        }
        return true;
    }

    /**
     * Возвращает статьи привязанные к категории статей
     *
     */
    function getArticles()
    {
        return OrmRequest::make()->select('*')
            ->from(new Article())
            ->where(['parent' => $this['id']])
            ->objects();
    }

    /**
     * Возвращает alias, а если он не задан, то id
     *
     * @return mixed
     */
    function getUrlId()
    {
        return $this['alias'] ?: $this['id'];
    }

    /**
     * Возвращает путь к списку статей данной категории на сайте
     *
     * @param bool $absolute - Если true, то будет возвращен абсолютный путь
     * @return string
     */
    function getUrl($absolute = false)
    {
        return RouterManager::obj()->getUrl('article-front-previewlist', ['category' => $this->getUrlId()], $absolute);
    }

    /**
     * Возвращает родительскую категорию
     *
     * @return self
     */
    function getParent()
    {
        return new self($this['parent']);
    }

    function delete()
    {
        //При удалении категории удаляем все статьи, котое находятся в ней
        OrmRequest::make()
            ->delete()
            ->from(new Article())
            ->where(['parent' => $this['id']])
            ->exec();
        parent::delete();
    }
}
