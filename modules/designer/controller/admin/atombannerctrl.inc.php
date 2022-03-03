<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Controller\Admin;

use RS\Controller\Admin\Front;

/**
 * Контроллер, позволяющий работать с компонентом баннеров
 */
class AtomBannerCtrl extends Front
{
    /**
     * Возвращает комплектации для нужного товара
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     */
    function actionGetZoneById()
    {
        $zone_id      = $this->request('zone', TYPE_STRING);
        $thumb_width  = $this->request('thumb_width', TYPE_STRING, 30);
        $thumb_height = $this->request('thumb_height', TYPE_STRING, 30);

        if (empty($zone_id)){
            return $this->result->setSuccess(false)->addEMessage(t('Не переданы данные в параметре zone для баннера'));
        }
        $zone_api = new \Banners\Model\ZoneApi();
        $zone     = $zone_api->getById($zone_id);
        $banners  = $zone->getBanners();

        $images = [];
        $thumbs = [];
        foreach ($banners as $banner){
            /**
             * @var \Banners\Model\Orm\Banner $banner
             */
            $photo_info['link']  = $banner['link'];
            $photo_info['image'] = $banner->getBannerUrl($zone['width'], $zone['height']);
            $photo_info['title'] = $banner['title'];
            $photo_info['blank'] = $banner['targetblank'] ? true : false;
            $thumbs[] = $banner->getBannerUrl((int)$thumb_width, (int)$thumb_height);
            $images[] = $photo_info;
        }
        $info = \ExternalApi\Model\Utils::extractOrm($zone);
        $info['images'] = $images;
        $info['thumbs'] = $thumbs;
        return $this->result->setSuccess(true)->addSection('zone', $info);
    }
}
