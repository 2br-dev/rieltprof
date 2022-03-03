<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Sitemap\Model;
use RS\Router\Manager as RouterManager;

/**
 * Класс содержит методы для генерации sitemap файлов
 */
class Api
{
    const
        ELEMENT_NAME_KEY = 'element_name',
        ELEMENT_MAP_TYPE_KEY = 'element_map_type';
    
    protected
        $site_id,
        $map_type,
        $allowed_map_types = ['google'],
        $folder = '/storage/sitemap',
        $full_filename;

    /**
     * Конструктор
     *
     * @param integer $site_id
     * @param string $map_type - Тип sitemap файла
     * @param bool $gzip - Если true, то будут генерироваться .gz файлы
     */
    function __construct($site_id = null, $map_type = null, $gzip = false)
    {
        if ($site_id) {
            \RS\Site\Manager::setCurrentSite(new \Site\Model\Orm\Site($site_id));
        }
        $this->site_id = \RS\Site\Manager::getSiteId();
        $this->gzip = $gzip;

        if ($this->gzip) {
            $this->folder .= '/gz';
        }

        // имя файла "sitemap[_{map_type}]-{site_id}.xml[.gz]"
        $filename = 'sitemap';
        if ($map_type && in_array($map_type, $this->allowed_map_types)) {
            $this->map_type = $map_type;
            $filename .= "_{$this->map_type}";
        }

        //Если есть партнёрские сайты и мы на нём, то добавим в нему ещё идентификатор
        if (\RS\Module\Manager::staticModuleExists('partnership') && \RS\Module\Manager::staticModuleEnabled('partnership')) {
            $partner = \Partnership\Model\Api::getCurrentPartner();
            /**
             * @var \Partnership\Model\Orm\Partner $partner
             */
            if ($partner) {
                $filename .= "_partner{$partner['id']}";
            }
        }
        $filename .= "-{$this->site_id}.xml".($gzip ? '.gz' : '');
        $this->full_filename = \Setup::$PATH.$this->folder.'/'.$filename;
    }
        
    /**
    * Отдает актуальный файл sitemap.xml на вывод
    * @return void
    */
    function sitemapToOutput()
    {
        if (!$this->checkActual()) {
            $this->generateSitemap();
        }
        $app = \RS\Application\Application::getInstance();

        if (file_exists($this->full_filename)) {

            $content_type = $this->gzip ? 'application/x-gzip' : 'text/xml';

            $app->headers
                ->addHeader('Content-Type', $content_type)
                ->sendHeaders();
            readfile($this->full_filename);
        } else {
            $app->showException(404, t('Файл не найден'));
        }
    }

    /**
     * Отдает составные части sitemap
     *
     * @return void
     */
    function sitemapChunkToOutput($chunk)
    {
        $app = \RS\Application\Application::getInstance();
        $chunk_file = $this->getChunkFilepath($chunk);

        if (file_exists($chunk_file)) {

            $content_type = $this->gzip ? 'application/x-gzip' : 'text/xml';

            $app->headers
                ->addHeader('Content-Type', $content_type)
                ->sendHeaders();
            readfile($chunk_file);
        } else {
            $app->showException(404, t('Файл не найден'));
        }
    }

    /**
     * Формирует имя файла на диске для составной части sitemap
     *
     * @param integer $chunk Номер части
     * @param bool|null $gzip Добавлять или нет .gz в конце. null - по умолчанию
     * @return string
     */
    function getChunkFilepath($chunk, $gzip = null)
    {
        if ($gzip === null) {
            $gzip = $this->gzip;
        }
        return $this->getFilenameWithoutExt()."-".(int)$chunk.".xml".($gzip ? '.gz' : '');
    }
    
    /**
    * Возвращает true, если файл sitemap существует и он актуальный
    * 
    * @return bool
    */
    function checkActual()
    {
        $config = \RS\Config\Loader::byModule($this);
        $expire_time = time() - $config['lifetime']*60;
        return $config['lifetime'] != 0 && (file_exists($this->full_filename) && filemtime($this->full_filename) > $expire_time);
    }
    
    /**
    * Создает файл sitemap.xml
     *
    * @return void
    */
    function generateSitemap()
    {
        $event_result = \RS\Event\Manager::fire('getpages', []);
        $pagelist = $event_result->getResult();
        $config = \RS\Config\Loader::byModule($this);
        
        $additional_urls = $config['add_urls'];
        foreach(explode("\n", $additional_urls) as $url) {
            if ($url = trim($url)) {
                $pagelist[] = [
                    'loc' => $url
                ];
            }
        }

        $default_page = [
            'priority' => $config['priority'],
        ];
        if ($config['set_generate_time_as_lastmod']) {
            $default_page['lastmod'] = date('c');
        }
        if ($config['changefreq'] != 'disabled') {
            $default_page['changefreq'] = $config['changefreq'];
        }

        $this->cleanSitemaps();

        if (count($pagelist) > $config->max_chunk_item_count) {
            //Создаем sitemapIndex и отдельно sitemap
            $pagelist_chunks = array_chunk($pagelist, $config->max_chunk_item_count);

            $chunk_filenames = [];
            foreach($pagelist_chunks as $n => $chunk) {
                $chunk_filenames[] = $this->createSitemapItemsFile($chunk, $default_page, $n+1);
            }

            $this->createSitemapIndexFile($chunk_filenames);
        } else {
            //Создаем sitemap
            $this->createSitemapItemsFile($pagelist, $default_page);
        }
    }

    /**
     * Создает sitemap файл с ссылками на страницы
     *
     * @param array $pagelist Массив страниц с массивами параметров для sitemap
     * @param array $default_page Шаблон параметров по-умолчанию для одной страницы
     * @param string|null $sitemap_filepath Путь к файлу, который необходимо создать ***.xml , обязательн с расширением xml
     * @return array Возвращает массив с абсолютной ссылкой и ссылкой на файл sitemap на диске
     */
    public function createSitemapItemsFile($pagelist, $default_page, $chunk = null)
    {
        if ($chunk === null) {
            $sitemap_filepath = $this->getFilenameWithoutExt().'.xml';
        } else {
            $sitemap_filepath = $this->getChunkFilepath($chunk, false);
        }

        \RS\File\Tools::makePath($sitemap_filepath, true);

        $xml = new \XMLWriter();
        $xml->openUri($sitemap_filepath);
        $xml->startDocument('1.0', 'utf-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->writeAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

        $config = \RS\Config\Loader::byModule($this);
        $exclude_urls = !empty($config['exclude_urls']) ? preg_split("/(\r\n|\n|\r)/", $config['exclude_urls']) : []; //url которые надо исключить

        $base_url = rtrim(\RS\Site\Manager::getSite()->getRootUrl(true, false), '/');
        //Если это парнёрский сайт
        if (\RS\Module\Manager::staticModuleExists('partnership') && \RS\Module\Manager::staticModuleEnabled('partnership')) {
            $partner = \Partnership\Model\Api::getCurrentPartner();

            /**
             * @var \Partnership\Model\Orm\Partner $partner
             */
            if ($partner) {
                $base_url = rtrim($partner->getRootUrl(true, false), '/');
            }
        }

        foreach($pagelist as $page_data) {
            $page = array_diff_key($page_data, array_flip(['custom_data'])) + $default_page;

            if (substr($page['loc'], 0, 4) != 'http') {
                $page['loc'] = $base_url.$page['loc'];
            }

            foreach ($exclude_urls as $exclude_url) {
                if (preg_match("/".trim(str_replace('/', '\/', $exclude_url))."/ui", $page['loc'])){
                    continue 2;
                }
            }

            if (isset($page['loc'])) {
                $xml->startElement('url');
                $this->writeItemContent($page, $xml);
                $xml->endElement();
            }
        }
        $xml->endDocument();
        $xml->flush();
        unset($xml);

        if ($this->gzip && ($new_filename = $this->gzipFile($sitemap_filepath))) {
            $sitemap_filepath = $new_filename;
        }

        return [
            'url' => $this->getSitemapUrl($chunk, $base_url),
            'path' => $sitemap_filepath
        ];
    }

    function getSitemapUrl($chunk = null, $base_url)
    {
        $params = [
            'site_id' => $this->site_id,
        ];

        if ($chunk !== null) {
            $params['chunk'] = $chunk;
        }

        if ($this->map_type) {
            $params['type'] = $this->map_type;
        }

        if ($this->gzip) {
            $params['pack'] = 'gz';
        }

        return $base_url.RouterManager::obj()->getUrl('sitemap-front-sitemap', $params);
    }

    /**
     * Создает .gz архив с файлом $source
     *
     * @param string $source Путь к исходному файлу
     * @param int $level Уровень сжатия
     * @return bool|string Возвращает путь к новому .gz файлу
     */
    public function gzipFile($source, $level = 9)
    {
        $dest = $source . '.gz';
        $mode = 'wb' . $level;
        $error = false;
        if ($fp_out = gzopen($dest, $mode)) {
            if ($fp_in = fopen($source,'rb')) {
                while (!feof($fp_in))
                    gzwrite($fp_out, fread($fp_in, 1024 * 512));
                fclose($fp_in);
            } else {
                $error = true;
            }
            gzclose($fp_out);
        } else {
            $error = true;
        }
        if ($error)
            return false;
        else
            return $dest;
    }

    /**
     * Создает SitemapIndex файл с ссылками на Sitemap файлы
     *
     * @param $chunk_filenames
     * @return void
     */
    public function createSitemapIndexFile($chunk_filenames)
    {
        $sitemap_filepath = $this->getFilenameWithoutExt().'.xml';

        $xml = new \XMLWriter();
        $xml->openUri($sitemap_filepath);
        $xml->startDocument('1.0', 'utf-8');
        $xml->startElement('sitemapindex');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach($chunk_filenames as $file_data) {
            $xml->startElement('sitemap');
            $xml->writeElement('loc', $file_data['url']);
            $xml->writeElement('lastmod', date('c', filemtime($file_data['path'])));
            $xml->endElement();
        }
        $xml->endDocument();
        $xml->flush();
        unset($xml);

        if ($this->gzip) {
            $this->gzipFile($sitemap_filepath);
        }
    }

    /**
     * Возвращает имя главного файла sitemap без разширения
     *
     * @return string
     */
    private function getFilenameWithoutExt()
    {
        return str_replace(['.gz', '.xml'], '', $this->full_filename);
    }

    /**
     * Очищает старые файлы sitemap
     *
     * @return void
     */
    public function cleanSitemaps()
    {
        $files = glob($this->getFilenameWithoutExt().'*');
        foreach($files as $file) {
            unlink($file);
        }
    }
    
    /**
    * Рекурсивно записывает переданный массив элементов карты в xml
    * 
    * @param array $content - содержимое элемента
    * @param \XMLWriter $xml - объект XML документа
    */
    protected function writeItemContent(array $content = [], $xml)
    {
        foreach ($content as $key=>$item) {
            if (is_array($item)) {
                // не записываем элементы, предназначенные для типов sitemap, отличных от текущего
                if (!empty($item[self::ELEMENT_MAP_TYPE_KEY]) && $item[self::ELEMENT_MAP_TYPE_KEY] != $this->map_type) {                    
                    continue;
                }
                $xml->startElement( (isset($item[self::ELEMENT_NAME_KEY])) ? $item[self::ELEMENT_NAME_KEY] : $key);
                $this->writeItemContent($item, $xml);
                $xml->endElement();
            } elseif (!in_array($key, [self::ELEMENT_NAME_KEY, self::ELEMENT_MAP_TYPE_KEY])) {
                $xml->writeElement($key, $item);
            }
        }
    }
}