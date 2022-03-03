<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model;

use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;

/**
* Класс предоставляет восможности по импорту фотографий из ZIP архива
*/
class ImportPhotosApi extends \RS\Module\AbstractModel\BaseModel
{
    protected
        $data = [],
        $photo_api,
        $timeout,
        $allow_ext = ['png', 'jpg', 'gif'],
        $zip_name = 'photos.zip',
        $log_rel = '/storage/tmp/importphotos/log.txt',
        $zip_folder,
        $zip_folder_rel = '/storage/tmp/importphotos',
        $extract_folder,
        $extract_folder_rel = '/storage/tmp/importphotos/unpack',
        $touch_products = [],
        $csv_file,
        $csv_file_rel = '/storage/tmp/importphotos/unpack/names.csv',
        $names_replace = [],
        $tmp_data_file = '/data.srz';
    
    function __construct()
    {
        $this->zip_folder = \Setup::$PATH.$this->zip_folder_rel;
        $this->extract_folder = \Setup::$PATH.$this->extract_folder_rel;
        $this->csv_file = \Setup::$PATH.$this->csv_file_rel;
        $this->timeout = \RS\Config\Loader::byModule($this)->import_photos_timeout;
        $this->tmp_data_file = $this->zip_folder.$this->tmp_data_file;        
        $this->loadParams();
        $this->photo_api = new \Photo\Model\PhotoApi();
    }
    
    /**
    * Загружает временные данные, создаваемые во время выполнения импорта
    * @return void
    */
    function loadParams()
    {
        if (file_exists($this->tmp_data_file)) {
            $this->data = @unserialize(file_get_contents($this->tmp_data_file));
        }
    }
    
    /**
    * Записывает значение параметра во временое хранилище
    * 
    * @param mixed $key - ключ параметра
    * @param mixed $value - значение
    * @return void
    */
    function setParam($key, $value)
    {
        if ($key === null && is_array($value)) {
            $this->data = array_merge($this->data, $value);
        } else {
            if (is_array($key)) {
                $this->data = $key;
            } else {
                $this->data[$key] = $value;
            }
        }
        file_put_contents($this->tmp_data_file, serialize($this->data));
    }
    
    /**
    * Возвращает сохраненный раннее параметр из врменного хранилища
    * 
    * @param string | null $key - ключ параметра
    * @param mixed $default - значение по умолчанию
    * @return mixed
    */
    function getParam($key = null, $default = null)
    {
        if ($key === null) return $this->data;
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
    
    /**
    * Возвращает список полей товара, с которыми возможно сравнение с именами 
    * файлов изображений
    * 
    * @return array
    */
    function getCompareProductFields()
    {
        $product = new Orm\Product();
        $fields = ['barcode', 'title', 'alias', 'xml_id', 'id', 'maindir', 'brand_id'];
        $list = [];
        foreach($fields as $field) {
            $list[$field] = $product['__'.$field]->getDescription();
        }
        return $list;
    }
    
    /**
    * Возвращает относительную от корня сайта ссылку на отчет об импорте
    * 
    * @param bool $absolute - если true, то возвращает абсолютную ссылку
    * @return string
    */
    function getLogUrl($absolute = false)
    {
        $rel_path = \Setup::$FOLDER.'/'.$this->log_rel;
        return $absolute ? \RS\Site\Manager::getSite()->getAbsoluteUrl($rel_path) : $rel_path;
    }
    
    /**
    * Удаляет файл с отчетом об импорте
    * 
    * @return ImportPhotosApi
    */
    private function cleanLog()
    {
        @unlink(\Setup::$ROOT.$this->getLogUrl());
        return $this;
    }
    
    /**
    * Добавляет в файл отчета сообщение
    * 
    * @param string $message - сообщение
    * @return ImportPhotosApi
    */
    private function writeLog($message)
    {
        file_put_contents(\Setup::$ROOT.$this->getLogUrl(), $message."\n", FILE_APPEND);
        return $this;
    }
    
    /**
    * Возвращает true, если имеются распакованные изображения для импорта
    * 
    * @return bool
    */
    function issetUnpackedFiles()
    {
        $result = false;
        $dir = $this->extract_folder;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..') continue;
                    $result = true;
                    break;
                }
                closedir($dh);
            }
        }
        return $result;
        
    }
    
    /**
    * Загружает файл во временную папку для дальнейших действий
    * 
    * @param mixed $file_arr
    */
    function uploadFile($file_arr)
    {
        \RS\File\Tools::makePath($this->zip_folder);
        \RS\File\Tools::deleteFolder($this->zip_folder, false);        
        $uploader = new \RS\File\Uploader(['zip'], $this->zip_folder_rel);
        $uploader->setRightChecker([$this, 'checkWriteRights']);
        
        if ($uploader->setUploadFilename($this->zip_name)->uploadFile($file_arr)) {
            $zip = new \ZipArchive;
            $zip_status = $zip->open($this->zip_folder.'/'.$this->zip_name);
            if ($zip_status === true) {
                return true;
            }
            $error = t('Не удается открыть архив. Ошибка: %0', [$zip_status]);
        } else {
            $error = $uploader->getErrorsStr();
        }
        return $this->addError($error, t('Zip архив с изображениями'), 'zipfile');
    }
    
    function checkWriteRights()
    {
        return Rights::CheckRightError($this, DefaultModuleRights::RIGHT_CREATE);
    }
    
    /**
    * Распаковывает загруженный zip файл пошагово
    * 
    * @param integer $start_pos - индекс файла, с которого начнется распаковка
    * @return bool | integer - Возвращает true, в случае полной распаковки 
    * или порядковый номер последнего распакованного файла
    */
    function extractFile($start_pos = 0)
    {
        if ($error = $this->checkWriteRights()) return $this->addError($error);
        
        $zip_filename = $this->zip_folder.'/'.$this->zip_name;
        $zip = new \ZipArchive;
        if ($zip->open($zip_filename) === true) {
            if ($start_pos == 0) {
                $this->setParam('zip_num_files', $zip->numFiles);
                $this->setParam('zip_done_percent', 0);
                
                \RS\File\Tools::makePath($this->extract_folder);
                \RS\File\Tools::deleteFolder($this->extract_folder, false);
            }
            $time_start = microtime(true);
            for($i = $start_pos; $i < $zip->numFiles; $i++) {
                $file = $zip->getNameIndex($i);
                
                //Распаковываем только файлы допустимых расширений и папки
                if (substr($file,-1) == '/' || in_array(strtolower(substr($file, -3)), array_merge($this->allow_ext, ['csv']) )) {
                    if (!$zip->extractTo($this->extract_folder, [$file])) {
                        return $this->addError(t('Не удалось распаковать файл %0', $file));
                    }
                }
                //sleep(2);

                if (microtime(true)-$time_start > $this->timeout) break; //Останаливаемся, чтобы не превышать лимит по времени выполнения скрипта                
            }
            
            //Расчет процентов выполнения
            $percent = $zip->numFiles ? (($i+1) / $zip->numFiles) : 1;
            if ($percent>1) $percent = 1;
            $this->setParam('zip_done_percent', round($percent * 100));
            
            $zip->close();            
        }
        
        return ($i >= $this->getParam('zip_num_files')-1) ? true : $i+1;
    }
    
    /**
    * Обнуляет информацию о процентах выполнения импорта
    * 
    * @return void
    */
    function resetStatistic()
    {
        $this->setParam(null, [
            'import_done_percent' => 0,
            'zip_done_percent' => 0
        ], true);
    }
    
    /**
    * Импортирует распакованные фотографии пошагово
    * 
    * @param integer $start_pos - индекс файла, с которого начнется импорт
    * @return bool | integer - Возвращает true или порядковый номер последнего импортированного файла
    */
    function importPhoto($start_pos = 0, $field, $separator)
    {
        if ($error = $this->checkWriteRights()) return $this->addError($error);

        if ($start_pos == 0) {
            $this->loadNamesCsv();
            $this->setParam('statistic', [
                'touch_images' => 0,
                'touch_products' => 0,
                'images_imported' => 0,
                'no_match_images' => 0
            ]);
            $this->setParam('import_done_percent', 0);
            $this->setParam('touch_products', []);
            $this->cleanLog();
            $this->writeLog('Import start...');
        }
        
        $dir = $this->extract_folder;
        $current_pos = $this->importRecursive($dir, 0, $start_pos, ['field' => $field, 'separator' => $separator], microtime(true));
        
        $this->setParam('touch_products', $this->getParam('touch_products')+$this->touch_products);
        
        $percent = $this->getParam('zip_num_files') ? ( ($current_pos+1) / $this->getParam('zip_num_files')) : 1;
        if ($percent>1) $percent = 1;
        $this->setParam('import_done_percent', round($percent * 100));
        $result = (is_integer($current_pos)) ? true : $current_pos+1;
        if ($result === true) {
            $this->writeLog('Import complete.');
        }
        $this->setParam('statistic', ['touch_products' => count($this->getParam('touch_products'))] + $this->getParam('statistic'));
        return $result;
    }
    
    /**
    * Обходит папку с распакованными фотографиями и вызывает функцию импорта для каждой фотографии
    * 
    * @param string $dir - директория для обхода
    * @param integer $i - начальный номер обхода
    * @param integer $start_pos - позиция, с которой необходимо импортировать фото
    * @param array $params - параметры для импорта
    * @param float $start_time - время начала операции импорта
    * @return integer | string - Если число, значит обход завершен штатно, если строка - по наступлению таймаута
    */
    private function importRecursive($dir, $i, $start_pos, $params, $start_time)
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach($files as $file) {
                if ($file == '.' || $file == '..') continue;
                if (is_dir($dir.'/'.$file)) {
                    $i = $this->importRecursive($dir.'/'.$file, $i+1, $start_pos, $params, $start_time);
                    if (is_string($i)) return $i;
                } else {
                    if ($i >= $start_pos) {
                        //Испортируем фото к товару
                        $this->importPhotoFile($dir.'/', $file, $params);
                        if (microtime(true) - $start_time > $this->timeout)  return (string)"$i";
                    }
                    $i++;
                }
            }
        }
        return (int)$i;
    }
    
    /**
    * Импортирует один файл с изображением
    * 
    * @param string $dir - директория изображения
    * @param string $file - имя файла-изображения
    * @param array $params - параметры импорта
    * @return bool
    */
    private function importPhotoFile($dir, $file, $params)
    {
        list($filename, $ext) = \RS\File\Tools::parseFileName($file, true);
        if (in_array(strtolower($ext), $this->allow_ext)) {
            $search_term = strtok($filename, $params['separator']);
            
            //Подменяем название файла на то, что указано было в CSV (если таковой был)
            if (isset($this->names_replace[$search_term])) {
                $search_terms = $this->names_replace[$search_term];
            } else {
                $search_terms = (array)$search_term;
            }
            
            $products_ids = [];
            foreach($search_terms as $term) {
                $products_ids += $this->searchProducts($term, $file, $params['field']);
            }
            if ($products_ids) {
                $this->touch_products += $products_ids;
                if ($this->photo_api->addFromUrl($dir.$file, 'catalog', $products_ids, true) === false) {
                    $this->writeLog("Image $file - error importing (bad image type or enough disk space)");
                }
            }            
        }
        return true;
    }
    
    /**
    * Ищет товары, которые соответствуют названию файла-изображения
    * 
    * @param string $term - название файла (до символа разделителя)
    * @param mixed $file - полное имя файла
    * @param mixed $field - поле, по которому ищется соответствие имени файла и товара
    * @return array of product id - возвращает ID товаров
    */
    private function searchProducts($term, $file, $field)
    {
        $q = \RS\Orm\Request::make()
                ->select('P.id')
                ->from(new Orm\Product(), 'P')
                ->where(['P.site_id' => \RS\Site\Manager::getSiteId()]);
                
        switch($field) {
            case 'maindir': $q->join(new Orm\Dir(), 'D.id = P.maindir', 'D')->where(['D.name' => $term]); break;
            case 'brand_id': $q->join(new Orm\Brand(), 'B.id = P.brand_id', 'B')->where(['B.title' => $term]); break;
            default: $q->where([$field => $term]);
        }
        
        $found = $q->exec()->fetchSelected('id', 'id');
        $stat = $this->getParam('statistic');        
        
        if ($found) {
            //Отсекаем товары, которым загружалось раннее данное фото            
            $exclude = \RS\Orm\Request::make()
                ->from(new \Photo\Model\Orm\Image())
                ->where(['filename' => $file, 'type' => 'catalog'])
                ->whereIn('linkid', $found)
                ->exec()->fetchSelected('linkid', 'linkid');
            $found = array_diff_key($found, $exclude);            
            if ($found) {
                $stat['images_imported']++;
            }
        } else {
            $exclude = [];
            //Пишем в статистику, что к фотографии не найдено товаров
            $stat['no_match_images']++;
        }
        $stat['touch_images']++;
        $this->setParam('statistic', $stat);
        $this->writeLog('Image '.$file.' added to products id: '.($found ? implode(',', $found) : '-').($exclude ? ' skip products id: '.implode(',', $exclude) : '') );
                    
        return $found;
    }
    
    
    /**
    * Очищает временную папку (zip архив и распакованные данные) для импорта фото
    * 
    * @return void
    */
    function cleanTemporaryDir()
    {
        \RS\File\Tools::deleteFolder($this->zip_folder, true);
    }
    
    /**
    * Загружает CSV файл с сопоставлениями имен, если таковой имелся в архиве 
    * @return void
    */
    private function loadNamesCsv()
    {
        if (file_exists($this->csv_file) && ($fp = fopen($this->csv_file, "r")) !== false) {
            $csv_charset = \RS\Config\Loader::byModule('main')->csv_charset;
            $csv_delimiter = \RS\Config\Loader::byModule('main')->csv_delimiter;
            $csv_enclosure = '"';
            
            if ($csv_charset == 'windows-1251') {
                setlocale(LC_CTYPE, 'ru_RU.cp1251');
            }        
            @ini_set('auto_detect_line_endings', true);
            
            while (($row = fgetcsv($fp, null, $csv_delimiter, $csv_enclosure)) !== false) {
                if ($csv_charset != 'utf-8') {
                    array_walk($row, function(&$value, $key, $in_charset) {
                            $value = iconv($in_charset, 'utf-8', $value);
                        }, $csv_charset);
                }
                if (count($row)>=2) {
                    @$this->names_replace[$row[0]][] = $row[1];
                }
            }
            fclose($fp);
        }
    }
    
}