<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Search\Model\Engine;

use Main\Model\Requester\ExternalRequest;
use RS\Config\Loader as ConfigLoader;
use RS\Orm\Request as OrmRequest;

/**
 * Полнотекстовый поиск средствами Mysql
 */
class Mysql extends AbstractEngine
{
    const EXTERNAL_REQUEST_ID = 'search';

    /**
     * Ссылка на сервис Яндекс.Спеллер
     * @var string
     */
    protected $yandex_spell_checker_url = 'https://speller.yandex.net/services/spellservice.json/checkText';

    /**
     * Возвращает название поискового сервиса
     *
     * @return string
     */
    public function getTitle()
    {
        return t('MySQL');
    }

    /**
     * Возвращает поисковый запрос, подготовленный для использования в выражении like
     *
     * @return string
     */
    protected function getQueryForLike()
    {
        $stemmer = new \Search\Model\Stem\Ru();
        return '%' . $stemmer->stemWord(str_replace('%', '', $this->query)) . '%';
    }

    /**
     * Возвращает поисковый запрос в нужной форме для поиска без учета окончаний
     *
     * @return string
     */
    protected function getStemmedQuery()
    {
        //Если в поисковой строке найдены кавычки,
        //не применяем эвристических методов улучшения результатов.
        //Считаем, что пользователь опытный, сам составляет запрос.
        if (strpos($this->query, "\"") !== false) return $this->query;


        $words = preg_split('/[\s,]+/u', $this->query, -1, PREG_SPLIT_NO_EMPTY);
        $stemmer = new \Search\Model\Stem\Ru();

        $query = $this->query;
        foreach ($words as $word) {
            //Если перед словом не будет задан спец-символ, ставим + (слово обязательно должно присутствовать в результате)
            if (!preg_match('/[+\-"~(<>]/', mb_substr($word, 0, 1))) {
                $query = str_replace($word, '+' . $word, $query);
            }

            $stemmed = $stemmer->stemWord($word);
            if (mb_strlen($stemmed) > 3) {//Если после стеминга слово не стало менее 4-х символов, то
                $query = str_replace($word, $stemmed . '*', $query);
            }
        }

        return $query;
    }

    /**
     * Добавляет базовое условие поиска к объекту запроса $q
     *
     * @param OrmRequest $q
     * @return OrmRequest
     */
    protected function getBaseRequest($q = null)
    {
        if ($q == null) $q = new OrmRequest();
        $q->from(new \Search\Model\Orm\Index())->asAlias('A')
            ->where("MATCH(A.`title`, A.`indextext`) AGAINST('#query' IN BOOLEAN MODE)", [
                'query' => $this->getStemmedQuery()
            ]);
        if (!empty($this->filters)) $q->where($this->filters);
        return $q;
    }

    /**
     * Выполняет поиск по заранее заданным параметрам
     *
     * @return boolean|\Search\Model\Orm\Index[] - если поиск выполнен, в случае ошибки false
     */
    public function search(OrmRequest $q = null)
    {
        if (empty($this->query)) {
            $this->addError(t('Введите поисковый запрос'));
            return false;
        }

        $q = $this->getBaseRequest($q);
        $this->total = $q->count();
        $results = new \RS\Orm\Request();
        if ($this->total) {
            if ($this->page_size) {
                $offset = (($this->page - 1) * $this->page_size);
                $q->limit($offset, $this->page_size);
            }

            if ($this->order_type == self::ORDER_RELEVANT) {
                $q->select("*, MATCH(A.`title`, A.`indextext`) AGAINST('" . \RS\Db\Adapter::escape($this->query) . "' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) as rank")
                    ->orderby('rank DESC');
            } else {
                $q->orderby($this->order);
            }

            $results = $q->objects();
        }
        return $results;
    }

    /**
     * Модифицирует объект запроса $q, добавляя в него условия для поиска
     *
     * @param OrmRequest $q - объект запроса
     * @param mixed $alias_product - псевдоним для таблицы товаров
     * @param mixed $alias - псевдоним для индексной таблицы
     * @return OrmRequest
     */
    public function joinQuery(OrmRequest $q, $alias_product = 'A', $alias = 'B')
    {
        if ($this->config['search_type'] == 'like') {
            $this->joinQueryLike($q, $alias_product, $alias);
        } elseif ($this->config['search_type'] == 'likeplus') {
            $this->joinQueryLikePlus($q, $alias_product, $alias);
        } else {
            $this->joinQueryFulltext($q, $alias_product, $alias);
        }

        if (!empty($this->filters)) $q->where($this->filters);

        return $q;
    }

    /**
     * Модифицирует объект запроса $q, добавляя в него условия для полнотекстового поиска
     *
     * @param OrmRequest $q - объект запроса
     * @param mixed $alias_product - псевдоним для таблицы товаров
     * @param mixed $alias - псевдоним для индексной таблицы
     */
    protected function joinQueryFulltext(OrmRequest $q, $alias_product = 'A', $alias = 'B')
    {
        //Полнотекстовый поиск
        $q->join(new \Search\Model\Orm\Index(), "$alias.entity_id = $alias_product.id", $alias)
            ->where("MATCH($alias.`title`, $alias.`indextext`) AGAINST('#query' IN BOOLEAN MODE)", [
                'query' => $this->getStemmedQuery()
            ]);

        if ($this->order_type == self::ORDER_RELEVANT) {
            $q->select("MATCH($alias.`title`, $alias.`indextext`) AGAINST('" . \RS\Db\Adapter::escape($this->query) . "' IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION) as rank")
                ->orderby('rank DESC');
        }
    }

    /**
     * Модифицирует объект запроса $q, добавляя в него условия для поиска для like
     *
     * @param OrmRequest $q - объект запроса
     * @param mixed $alias_product - псевдоним для таблицы товаров
     * @param mixed $alias - псевдоним для индексной таблицы
     */
    protected function joinQueryLike(OrmRequest $q, $alias_product = 'A', $alias = 'B')
    {
        $query = $this->getQueryForLike();
        $q->join(new \Search\Model\Orm\Index(), "$alias.entity_id = $alias_product.id", $alias)
            ->where("($alias.`title` like '#query' OR $alias.`indextext` like '#query' OR $alias.`title` like '#psquery' OR $alias.`indextext` like '#psquery')", [
                'query' => $query,
                'psquery' => $this->puntoSwitcher($query)
            ])
            ->orderby('INSTR(#0.title, "#1") > 0 desc, INSTR(#0.title, "#1")', [$alias, $this->query]);
    }


    /**
     * Модифицирует объект запроса $q, добавляя в него условия для поиска для like+
     *
     * @param \RS\Orm\Request $q - объект запроса
     * @param mixed $alias_product - псевдоним для таблицы товаров
     * @param mixed $alias - псевдоним для индексной таблицы
     */
    protected function joinQueryLikePlus(\RS\Orm\Request $q, $alias_product = 'A', $alias = 'B')
    {
        $q->join(new \Search\Model\Orm\Index(), "$alias.entity_id = $alias_product.id", $alias);
        $words = explode(" ", $this->prepareLikePlusString($this->query));
        $words_switch = $this->puntoSwitcher($words);
        $words_speller = []; //слова с исправленными ошибками

        //Исправляем ошибки с помощью Яндекс.SpellChecker
        if ($this->config['search_type_likeplus_spell_checker_enable']) {
            $requester = new ExternalRequest(static::EXTERNAL_REQUEST_ID, $this->yandex_spell_checker_url);
            $requester->setParams(['text' => $this->query]);
            $requester->setTimeout(4);
            $speller = $requester->executeRequest()->getResponseJson();
            if ($speller) {
                foreach ($speller as $variant) {
                    $words_speller[$variant['word']] = $variant['s'];
                }
            }
        }

        $stemmer = new \Search\Model\Stem\Ru();
        foreach ($words as $key => $word) {
            if ($word != '') {
                $search_word_masks = [
                    $stemmer->stemWord($word),
                    $stemmer->stemWord($words_switch[$key])
                ];

                if (isset($words_speller[$word])) {
                    //добавляем исправленные слова
                    foreach($words_speller[$word] as $word_checked) {
                        $stemmed = $stemmer->stemWord($word_checked);
                        if (!in_array($stemmed, $search_word_masks)) {
                            $search_word_masks[$stemmed] = $stemmed;
                        }
                    }
                }
                $q->openWGroup();
                foreach($search_word_masks as $stemmed_word) {
                    $q->where("$alias.`indextext` like '%#term%'", [
                        'term' => $stemmed_word
                    ], 'OR');
                }
                $q->closeWGroup();
            }
        }
    }

    /**
     * Преобразует индекс для likeplus поиска
     *
     * @param mixed $search_item
     */
    function onUpdateSearch($search_item)
    {
        if ($this->config['search_type'] == 'likeplus') {
            //Объединяет все слова в одну непрерывную строку
            $search_item['indextext'] = str_replace(' ', '', $this->prepareLikePlusString($search_item['title'] . $search_item['indextext']));
        }
    }

    /**
     * Возвращает подготовленную для поиска likePlus строку
     *
     * @param string $query
     * @return string
     */
    protected function prepareLikePlusString($query)
    {
        $config = ConfigLoader::byModule('search');
        $dis = preg_split('//u', html_entity_decode($config['search_type_likeplus_ignore_symbols']), -1, PREG_SPLIT_NO_EMPTY);

        return str_replace($dis, ' ', mb_strtolower($query));
    }

    /**
     * Punto switcher для поиска
     *
     * @param string|array $query
     * @return string|array
     */
    protected function puntoSwitcher($query)
    {
        if (is_array($query)) {
            foreach ($query as $key => $word) {
                $query[$key] = \RS\Helper\Transliteration::puntoSwitchWord($word);
            }
        } else {
            $query = \RS\Helper\Transliteration::puntoSwitchWord($query);
        }
        return $query;
    }
}
