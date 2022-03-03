<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Site\Model;

use RS\Helper\IdnaConvert;
use RS\Router\Manager as RouterManager;
use Site\Model\Orm\Site;

/**
 * Класс управляет файлом Robots.txt в рамках одного сайта
 */
class RobotsTxtApi
{
    private $htaccess_file;
    /** @var Site */
    private $current_site;

    /**
     * Конструктор класса, работающего с robots.txt файлами
     *
     * @param Orm\Site $site сайт
     */
    function __construct(Orm\Site $site)
    {
        $this->setCurrentSite($site);
        $this->htaccess_file = \Setup::$PATH . '/.htaccess';
    }

    /**
     * Устанавливает текущий сайт
     *
     * @param Orm\Site $site
     * @return RobotsTxtApi
     */
    function setCurrentSite(Orm\Site $site)
    {
        $this->current_site = $site;
        return $this;
    }

    /**
     * Автоматически создает стандартный robots.txt для сайта.
     * Добавляет в .htaccess необходимую запись в случае мультисайтовости
     *
     * @return void
     */
    function AutoCreateSiteRobotsTxt()
    {
        $domain = $this->getMainHost();
        $protocol = ($this->current_site['redirect_to_https']) ? 'https://' : '';
        $sitemap_url = RouterManager::obj()->getUrl('sitemap-front-sitemap', ['site_id' => $this->current_site['id']], true);
        if ($domain == '') return;

        $content = "User-agent: *\n";
        $content .= "Host: $protocol{$domain}\n";
        $content .= "Sitemap: $sitemap_url\n";

        $this->writeRobotsTxt($content);
        $this->writeHtaccess();
    }

    /**
     * Возвращает содержимое файла robots.txt для текущего сайта
     *
     * @return string
     */
    function getRobotsTxtContent()
    {
        $filename = $this->getRobotsFilename(true);
        if (file_exists($filename)) {
            return file_get_contents($filename);
        }
        return '';
    }

    /**
     * Записывает новое содержимое в файл robots.txt для текущего сайта
     *
     * @param string $newContent - новое содержимое
     *
     * @return int Возвращает количество записанных байт в файл
     */
    function writeRobotsTxt($newContent)
    {
        return file_put_contents($this->getRobotsFilename(true), $newContent);
    }

    /**
     * Добавляет к htaccess правило для успешной отдачи файла ВАШ_ДОМЕН/robots.txt
     * на любом из мультисайтов
     *
     * @return void
     */
    function writeHtaccess()
    {
        //Для сайта по умолчанию записи в .htaccess не нужны
        if ($this->current_site['default']) return;

        $data = $this->getHtaccessRules();
        $section = 'RewriteEngine On';

        $before = $after = '';
        if (file_exists($this->htaccess_file)) {
            $start_write_pos = filesize($this->htaccess_file);
            $htaccess_content = file_get_contents($this->htaccess_file);

            if (stripos($htaccess_content, $data) !== false) {
                //Необходимые строки уже присутствуют в файле, ничего не делаем
                return;
            }

            //Ищем позицию 'RewriteEngine On' в файле
            $section_pos = stripos($htaccess_content, $section);
            if ($section_pos !== false) {
                $start_write_pos = $section_pos;
                $htaccess_content = substr($htaccess_content, 0, $start_write_pos) . substr($htaccess_content, $start_write_pos + strlen($section));
            }
            $before = substr($htaccess_content, 0, $start_write_pos);
            $after = substr($htaccess_content, $start_write_pos);
        }

        $new_htaccess_content = rtrim($before) . "\r\n" . $section . "\r\n" . $data . ltrim($after);
        return file_put_contents($this->htaccess_file, $new_htaccess_content);
    }

    /**
     * Возвращает правила, которые необходимо добавить в htaccess для успешного
     * открытия в robots.txt
     *
     * @return string
     */
    function getHtaccessRules()
    {
        $domain = str_replace('.', '\.', $this->getMainHost());
        $filename = $this->getRobotsFilename();

        $data = "RewriteCond %{HTTP_HOST} {$domain}$\r\n" .
            "RewriteRule ^(robots.txt)$ /{$filename} [L]\r\n";

        return $data;
    }

    /**
     * Возвращает имя файла robots.txt для текущего сайта
     *
     * @param bool $include_path - добавить путь к корневому каталогу системы
     * @return string
     */
    function getRobotsFilename($include_path = false)
    {
        $postfix = $this->current_site['default'] ? '' : '-' . $this->current_site['id'];
        $filename = "robots{$postfix}.txt";
        return $include_path ? \Setup::$PATH . '/' . $filename : $filename;
    }

    /**
     * Возвращает главный(первый по списку) домен для текущего сайта
     * Если домен интернациональный, то он сразу будет в punycode
     *
     * @return string|null
     */
    protected function getMainHost()
    {
        $domains = $this->current_site->getDomainsList();
        if ($domains) {
            $idna = new IdnaConvert();
            return $idna->encode($domains[0]);
        }
        return null;
    }

    /**
     * Удаляет файл robots.txt и соответствующие записи из .htaccess
     *
     */
    function deleteRobotsTxt()
    {
        $filename_robots = $this->getRobotsFilename(true);
        if (file_exists($filename_robots)) {
            unlink($filename_robots);
        }

        if (file_exists($this->htaccess_file)) {
            $htaccess_content = file_get_contents($this->htaccess_file);
            $data = $this->getHtaccessRules();
            $htaccess_content = str_replace($data, '', $htaccess_content);
            file_put_contents($this->htaccess_file, $htaccess_content);
        }
    }

}

