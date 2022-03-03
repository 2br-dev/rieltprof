<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Sitemap\Controller\Front;

class Sitemap extends \RS\Controller\Front
{
    public
        $site_id,
        $map_type,
        $gzip,
        $chunk;

    function init()
    {
        $this->site_id = $this->url->request('site_id', TYPE_INTEGER);
        $this->map_type = $this->url->request('type', TYPE_STRING);
        $this->gzip = $this->url->request('pack', TYPE_STRING) == 'gz';
        $this->chunk = $this->url->request('chunk', TYPE_STRING);

        $this->wrapOutput(false);
    }

    /**
     * Генерирует и возвращает главный файл sitemap.xml
     */
    function actionIndex()
    {
        $api = new \Sitemap\Model\Api($this->site_id, $this->map_type, $this->gzip);
        if ($this->chunk != '') {
            $api->sitemapChunkToOutput((int)$this->chunk);
        } else {
            $api->sitemapToOutput();
        }
        return;
    }
}
