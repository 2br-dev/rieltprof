<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Application\Compress;

/**
* Соединяет переданные файлы в один файл, оптимизирует его и записывает в кэш папку.
* Оптимизация и слияние происходит только если были изменения в исходных файлах.
*/
abstract class AbstractCompress
{
    const
        NO_COMPRESS = 0;

    protected
        $uniqName = '',
        $return_list = [],
        $file_list = [],
        $group_list = [],
        $checked_file_list = [],
        $output_path,
        $extension,
        $file_time = 0; //Самая максимальная дата модификации файла CSS
    
    function __construct(array $file_list, $output_path, $extension)
    {
        $this->file_list = $file_list;
        $this->output_path = $output_path;
        $this->extension = $extension;
        \RS\File\Tools::makePath(\Setup::$PATH.$this->output_path);
    }
    
    /**
    * Объединяет  файлы, оптимизирует их.
    * @return array список с одним оптимизированным файлом или исходный список в случае ошибки
    */
    protected function merge($group, $compress_type)
    {        
        $uniq_t = ['file' => $group['uniq']['file'].'?t'.$group['time']]  //Добавляем параметр (чтобы браузер не использовал кэшированный файл)
                    + $group['files'][0];

        if (file_exists(\Setup::$PATH.$group['uniq']['file']) && filemtime(\Setup::$PATH.$group['uniq']['file']) == $group['time']) {
            return [$uniq_t];
        }
        $merged_css = '';
        foreach($group['files'] as $filearr) {
            $file = $filearr['file'];
        	$pfilename = strtok($file,'?');
            $file_content = $this->getContent($pfilename);
            $merged_css .= $file_content."\n";
        }
      
        if ($this->compress($merged_css, \Setup::$PATH.$group['uniq']['file'], $compress_type))
        {
            touch(\Setup::$PATH.$group['uniq']['file'], $group['time']);
            return [$uniq_t];
        }
        return $group['files'];
    }
    
    /**
    * Возвращает содержимое исходного файла перед объединением
    * 
    * @param string $file путь к файлу
    * @return string
    */
    function getContent($file)
    {
        return file_get_contents(\Setup::$PATH.$file);
    }    
    
    /**
    * Минимизирует файлы
    * 
    * @param string $source данные для сжатия
    * @param string $output_file имя файла, в который следует записать минимизированные данные
    * @param integer $compress_type тип сжатия. 0 - не сжимать, 1 - объединять, 2 - сжимать
    */
    abstract function compress($source, $output_file, $compress_type);

        
    /**
     * Возвращает массив с одним файлом в списке. Файл - объединенный, оптимизированный
     *
     * @param array $no_compress - Массив несжатых файлов
     * @param int $compress_type - тип сжатия: 0 - не сжимать, 1 - объединять, 2 - объединять, сжимать
     * @return array
     */
    public function getCompressed($no_compress = [], $compress_type)
    {
        if ($compress_type == self::NO_COMPRESS)
            return $no_compress;

        $no_compress = array_flip($no_compress);
        //Форируем группы файлов. Если какой то файл не открывается, то закрываем группу, вставляем его, начинаем новую группу.
        //Это необходимо для нормальной работы CSS файлов, где важна очередность их подключения
        $groups = [];
        clearstatcache();
        $i = 0;
        foreach ($this->file_list as $name => $filearr)
        {
            $filename = $filearr['file'];
        	$pfilename = strtok($filename,'?');
            $params = array_diff_key($filearr['params'], ['unshift' => true, 'endGroup' => true, 'header' => true, 'footer' => true]);
            //В группу могут попасть только файлы с идентичными параметрами
            if ((isset($current_params) && $current_params != $params) || !empty($filearr['params']['endGroup']) ) {
                $i++;
            }
            $current_params = $params;
            
            if (!isset($no_compress[$name])
                && strpos($pfilename, '://') === false
                && file_exists(\Setup::$PATH.$pfilename))
            {
                if (!isset($groups[$i])) {
                    $groups[$i] = [
                        'files' => [],
                        'uniq_src' => '',
                        'time' => 0
                    ];
                }
                $groups[$i]['files'][] = $filearr;
                $groups[$i]['uniq_src'] .= $filename;
                $mtime = filemtime(\Setup::$PATH.$pfilename);
                if ($mtime > $groups[$i]['time']) $groups[$i]['time'] = $mtime;
            } else {
                $groups[$i+1]['uniq'] = $filearr;
                $i += 2;
            }
        }
        
        foreach ($groups as $group)
        {
            if (!isset($group['uniq'])) {
                $group['uniq'] = 
                    ['file' => $this->output_path.'/'.sprintf("%u", crc32($group['uniq_src'])).$this->extension]
                    + $group['files'][0];
            }
            
            if (isset($group['files'])) $this->return_list = array_merge($this->return_list, $this->merge($group, $compress_type));
                else $this->return_list[] = $group['uniq'];
        }
        
        return $this->return_list;
    }
}
