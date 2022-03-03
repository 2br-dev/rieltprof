<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Application\Compress;

/**
* Данный класс предназначен для оптимизации списка css файлов.
* Соединяет переданные CSS в один файл, оптимизирует его с помощью TidyCSS и записывает в кэш папку.
* Оптимизация и слияние происходит только если были изменения в исходных файлах.
*/
class Css extends AbstractCompress
{
    const
        COMPRESS_TIDY = 2;
    
    function __construct(array $css_list)
    {
        parent::__construct($css_list, \Setup::$COMPRESS_CSS_PATH, '.css');
    }
    
    /**
    * Возвращает содержимое CSS файла, с измененными ссылками на все ресурсы.
    * 
    * @param string $file - путь к CSS файлу относительно корня движка
    */
    function getContent($file)
    {
        $data = file_get_contents(\Setup::$PATH.$file);
        //Заменим ссылки на ресурсы на относительные от корня сайта
        
        $path = dirname(\Setup::$FOLDER.$file); //Директория CSS файла
        $new_data = preg_replace_callback('/url\((.*?)\)/um', function($match) use($path) {
            
            $rel_url = trim($match[1],'"\''); 
            //Если url абсолютный или относительный от корня, то не изменяем путь
            if (preg_match('/^(\/|http|https|data\:image\/)/u', $rel_url)) return $match[0];
            
            $levels = substr_count($rel_url, '../'); //Если присутствуют относительные пути, узнаем количество уровней, на которое следует подняться выше.
            
            //Высчитываем путь, относительно от корня, опираясь на $levels
            $new_path = $path;    
            if ($levels) {
                $rel_url = str_replace('../', '', $rel_url);
                $i = 0;
                while($i < $levels) {
                    if (($pos = strrpos($new_path, '/')) !== false) {
                        $new_path = substr($new_path, 0, $pos);
                    } else {
                        //Оставляем путь без изменения, если невозможно подняться выше корневой папки сайта
                        $new_path = $path;
                        break;
                    }
                    $i++;
                }
            }
            
            return 'url('.$new_path.'/'.ltrim($rel_url, '/').')';
        }, $data);
        
        return $new_data;
    }

    /**
     * Минимизирует файлы
     *
     * @param string $source данные для сжатия
     * @param string $output_file имя файла, в который следует записать минимизированные данные
     * @param integer $compress_type тип сжатия. 0 - не сжимать, 1 - объединять, 2 - сжимать
     */
    function compress($css_source, $output_file, $compress_type)
    {
        if ($compress_type == self::COMPRESS_TIDY) {
            //Оптимизируем CSS контент
            $css = new \csstidy();
            $css->set_cfg('merge_selectors', 2);
            $css->load_template('highest_compression');
            $result = $css->parse($css_source);
            $css_source = $css->print->plain();
        }
       
       return file_put_contents($output_file, $css_source);
    }

}

