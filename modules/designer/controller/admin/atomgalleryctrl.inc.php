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
 * Контроллер, позволяющий работать с компонентом меню
 */
class AtomGalleryCtrl extends Front
{
    /**
     * Возвращает комплектации для нужного товара
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     */
    function actionGetAlbumById()
    {
        $album_id     = $this->request('album_id', TYPE_INTEGER, 0);
        $photo_params = $this->request('photo', TYPE_ARRAY, []);
        $album = new \Photogalleries\Model\Orm\Album($album_id);
        if (empty($photo_params)){
            return $this->result->setSuccess(false)->addEMessage(t('Не переданы данные по размера в параметре photo'));
        }
        if (!$album['id']){
            return $this->result->setSuccess(false)->addEMessage(t('Альбом не найден проверьте переданный идентификатор'));
        }
        if (!$album['public']){
            return $this->result->setSuccess(false)->addEMessage(t('Альбом является скрытым'));
        }
        $photos = $album->fillImages();
        $images = [];
        $k = 0;
        foreach ($photos as $photo){
            $k++;
            $photo_info['medium'] = $photo->getUrl($photo_params['width'], $photo_params['height'], $photo_params['type']);
            $photo_info['big']    = $photo->getUrl($photo_params['big_width'], $photo_params['big_height'], $photo_params['type']);
            $photo_info['title']  = $photo['title'] ? $photo['title'] : t('Фото №%0', [$k]);
            $images[] = $photo_info;
        }
        $info = \ExternalApi\Model\Utils::extractOrm($album);
        $info['images'] = $images;
        return $this->result->setSuccess(true)->addSection('album', $info);
    }
}
