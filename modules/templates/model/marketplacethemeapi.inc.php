<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Templates\Model;
use PushSender\Model\ExternalApi\Push\getList;

/**
 * Класс позволяет получать темы оформления, доступные в Marketplace ReadyScript
 */
class MarketplaceThemeApi extends \RS\Module\AbstractModel\BaseModel
{
    protected
        $api_getlist_url = '/themesapi/getlist/';

    /**
     * Возвращает список тем оформления, доступных в Marketplce ReadyScript
     *
     * @param bool $exclude_mine
     */
    function getMarketplaceThemes($exclude_installed = true)
    {
        $url = \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$MARKETPLACE_DOMAIN.$this->api_getlist_url;
        $context = stream_context_create([
            'http'=> [
                'method'=>"POST",
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    'lang' => \RS\Language\Core::getCurrentLang(),
                    'version' => \Setup::$VERSION,
                    'product' => defined('CLOUD_UNIQ') ? 'cloud' : 'box',
                    'script_type' => \Setup::$SCRIPT_TYPE
                ]),
                'timeout' => 5
            ]
        ]);
        $response = @file_get_contents($url, null, $context);
        if (!$response) {
            return $this->addError(t('Не удалось соединиться с сервером ReadyScript'));
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return $this->addError(t('Получен некорректный ответ от сервера ReadyScript'));
        }

        //Исключаем уже установленные темы оформления
        if ($exclude_installed) {
            $theme_manager = new \RS\Theme\Manager();
            $my_themes = $theme_manager->getList();

            $data['response']['themes'] = array_diff_key($data['response']['themes'], $my_themes);
        }

        return $this->extractThemeInfo($data);
    }

    /**
     *
     * @param $data
     */
    function extractThemeInfo($data)
    {
        $result = [];
        foreach($data['response']['themes'] as $item) {
            try {
                $xml = new \SimpleXMLElement($item['theme_xml'], 0, true);
                $theme_item = [
                    'title' => (string)$xml->general->name,
                    ] + $item;

                if (isset($xml->shades)) {
                    $shades = [];
                    foreach($xml->shades->shade as $one_shade) {
                        $shades[] = [
                            'id' => (string)$one_shade->id,
                            'title' => (string)$one_shade->title,
                            'color' => (string)$one_shade->color,
                            'preview' => $this->getPreviewUrl($item['img_prefix'], (string)$one_shade->id)
                        ];
                    }

                    $theme_item['preview'] = $this->getPreviewUrl($item['img_prefix'], $shades[0]['id']);
                    $theme_item['default_shade_id'] = $shades[0]['id'];
                    $theme_item['shades'] = $shades;
                } else {
                    $theme_item['preview'] = $this->getPreviewUrl($item['img_prefix']);
                }

            } catch(\Exception $e) {
                //Пропускаем ошибочную тему оформления
                $this->addError(t('Не удалось разобрать XML файл %0', [$item['theme_xml']]));
            }

            $result[] = $theme_item;
        }

        return $result;
    }

    /**
     *
     *
     * @param $theme_item
     * @param $xml_item
     */
    private function getPreviewUrl($prefix, $shade_id = '')
    {
        $shade = $shade_id ? '_'.$shade_id : '';
        return $prefix."/preview{$shade}.jpg";
    }
}