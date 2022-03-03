<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Controller\Front;

/**
 * Контроллер RSS канала категории статей
 */
class Rss extends \RS\Controller\Front
{
    public
        $api,
        $cat_api;

    function init()
    {
        $this->api = new \Article\Model\Api();
        $this->cat_api = new \Article\Model\Catapi();
    }

    function actionIndex()
    {
        $category = $this->url->get('category', TYPE_STRING);
        $dir = $this->cat_api->getById($category);
        $cache_key = 'rss_cache_'.crc32($dir['id']);
        $cache = \RS\Cache\Manager::obj();

        if (!$cache->validate($cache_key) || !($rss_xml = $cache->read($cache_key)))
        {
            $pageSize = 50; //Отображать 50 последних новостей в RSS

            if ($category > 0) {
                $child_ids = $this->cat_api->getChildsId($dir['id']);
                $this->api->setFilter('parent', $child_ids, 'in');
            }

            $this->api->setFilter('public', 1);

            $list = $this->api->getList(1, $pageSize);

            $site = \RS\Site\Manager::getSite();
            $site_config = \RS\Config\Loader::getSiteConfig();

            //Формируем RSS-XML
            $rss=new \DomDocument('1.0','utf-8');
            $rss_root = $rss->appendChild( $rss->createElement('rss') );
            $rss_root->setAttribute('version', '2.0');
            $channel = $rss_root->appendChild( $rss->createElement('channel') );
            $title = $rss->createElement('title');
            $title->appendChild($rss->createTextNode( $site['full_title'] ));

            $link = $rss->createElement('link');
            $link->appendChild( $rss->createTextNode($site->getRootUrl('true')) );


            $description = $rss->createElement('description');
            $description->appendChild( $rss->createCDATASection($site_config['slogan']) );

            $channel->appendChild( $title );
            $channel->appendChild( $link );
            $channel->appendChild( $description );

            if (!empty($site_config['logo'])) {
                $image = $rss->createElement('image');

                $url = $rss->createElement('url');

                $url->appendChild(
                    $rss->createTextNode($site_config['__logo']->getUrl(250, 250, 'xy', true))
                );

                $titleimg = $rss->createElement('title');
                $titleimg->appendChild($rss->createTextNode( $site['full_title'] ));
                $linkimg = $rss->createElement('link');
                $linkimg->appendChild( $rss->createTextNode($site->getRootUrl('true')) );

                $image->appendChild( $url );
                $image->appendChild( $titleimg );
                $image->appendChild( $linkimg );
                $channel->appendChild( $image );
            }

            foreach($list as $article) {
                $item = $rss->createElement('item');
                $title = $rss->createElement('title');
                $title->appendChild( $rss->createCDATASection($article['title']) );

                $description = $rss->createElement('description');
                $description->appendChild( $rss->createCDATASection($article->getPreview()) );
                $link = $rss->createElement('link');

                $link->appendChild( $rss->createTextNode( $article->getUrl(true) ) );

                $guid = $rss->createElement('guid');
                $guid->appendChild( $rss->createTextNode( $article->getUrl(true) ) );

                $item->appendChild( $title );
                $item->appendChild( $description );

                $item->appendChild( $link );
                $item->appendChild( $guid );
                $channel->appendChild($item);
            }

            $rss_xml = $rss->saveXML();
            $cache->write($cache_key, $rss_xml);
        }

        $this->app->headers->addHeader('Content-Type', 'application/rss+xml');
        $this->wrapOutput(false);
        return $rss_xml;
    }
}

