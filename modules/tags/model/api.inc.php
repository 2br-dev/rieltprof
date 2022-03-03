<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Tags\Model;

class Api 
{
    protected
        $obj_instance,
        $obj_link_instance;
    
    /**
    * Разбивает строку на массив слов. Разбиваются слова с запятыми
    *     
    * @param string $keywords - строка с ключевыми стовами через знак "," - запятая
    */
    function parseWords($keywords)
    {
        $word_list = preg_split('[,]', $keywords, -1, PREG_SPLIT_NO_EMPTY);
        return $word_list;
    }
    
    function addWords($words, $type, $linkid)
    {
        if (!is_array($words)) $words = $this->parseWords($words);
        if (empty($words)) return false;
        
        $values = [];
        $links = [];
        foreach($words as $word_str) {
            $word_str = $this->prepareWord($word_str);
            if (empty($word_str)) continue;
            
            $stemmed = $this->stemWord($word_str);
            $id = $this->getWordId($stemmed);
            
            $word = new Orm\Word();
            $word['id'] = $id;
            $word['stemmed'] = $stemmed;
            $word['word'] = $word_str;
            $word->insert(true);
            
            $link = new Orm\Link();
            $link['word_id'] = $id;
            $link['type'] = $type;
            $link['link_id'] = $linkid;
            $link->replace();
        }
    }
    
    function getWordId($word)
    {
        return sprintf('%u', crc32($word));
    }
    
    function prepareWord($word)
    {
        return mb_strtolower(trim($word));
    }
    
    function stemWord($word)
    {
        //Здесь может быть механизм обработки слов - например стемминг.
        return $word;
    }
    
    function getWords($type, $linkid)
    {
        return \RS\Orm\Request::make()
            ->select('W.*, L.id as lid')
            ->from(new Orm\Word())->asAlias('W')
            ->join(new Orm\Link(), 'W.id = L.word_id', 'L')
            ->where([
                'type' => $type,
                'link_id' => $linkid
            ])
            ->orderby('L.id')
            ->exec()
            ->fetchAll();
    }
    
    function getList(){
        return \RS\Orm\Request::make()
                ->from(new \Tags\Model\Orm\Link())
                ->objects();
    }
    
    function getHelpList($beg_word)
    {
        $beg_word = $this->prepareWord($beg_word);
        return \RS\Orm\Request::make()
            ->from(new Orm\Word())
            ->where("stemmed LIKE '#word%'", ['word' => $this->stemWord($beg_word)])
            ->exec()
            ->fetchAll();
    }
    
    /**
    * Удаляет связь со словом
    * @param mixed $id - id строки со связью
    * @param mixed $link_id id объекта с которым связано слово. Нужно, для защиты от удаления произвольных связей
    * @return int
    */
    function delLink($id, $link_id = null)
    {
        $q = \RS\Orm\Request::make()
            ->delete()
            ->from(new Orm\Link())
            ->where(['id' => $id]);
        
        if (isset($link_id)) $q->where(['link_id' => $link_id]);
        return $q->exec()->affectedRows();
    }
    
    /**
    * Удаляет привязку слов по id ссылки и тип объекта
    * 
    * @param integer $link_id - id ссылки на объект
    * @param string $type     - тип объекта. Например 'article'
    */
    function delByLinkAndType($link_id,$type){
       $q = \RS\Orm\Request::make()
            ->from(new \Tags\Model\Orm\Link())
            ->where([
                'link_id' => $link_id,
                'type' => $type,
            ])
            ->delete()
            ->exec(); 
    }
    
    /**
    * Возвращает связанные id по ключевым словам
    * 
    * @param string|array $wordlist - строка с ключевыми стовами через знак "," - запятая, либо массив ключевых слов для поиска
    * @param string $type           - тип объектов для подгрузки
    */
    function getIdByTags($wordlist, $type = null)
    {
        if (is_string($wordlist)){
           $wordlist = $this->parseWords($wordlist); 
        }
        
        $w_ids    = [];
        foreach ($wordlist as $word)
        {
            $word    = $this->prepareWord($word);
            $w_ids[] = $this->getWordId($this->stemWord($word));
        }
        if (empty($w_ids)) return false;
        
        $q = \RS\Orm\Request::make()
            ->select('link_id')
            ->from(new Orm\Link())
            ->groupby('link_id')
            ->whereIn('word_id', $w_ids);
        
        if ($type !== null) {
            $q->where(['type' => $type]);
        }
        return $q->exec()->fetchSelected(null, 'link_id');
    }
    
    /**
    * Проверяет есть такой тег, по его alias и если есть предлогает новое название и возвращает его
    * 
    * @param string $alias - английское название тега
    * @param integer $number - номер который будет дописан к концу alias, если такой alias найден
    */
    function checkAliasByAlias($alias,$number=1){
        $word = \RS\Orm\Request::make()
            ->from(new \Tags\Model\Orm\Word())
            ->where([
                'alias' => $alias
            ])
            ->object();
        if ($word){ //Если слово с таким alias существует
            $alias = $alias.$number;
            return $this->checkAliasByAlias($alias,$number+1);
        }
        return $alias;
    }
}

