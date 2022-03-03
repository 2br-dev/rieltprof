<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Controller\Block;

use Catalog\Model\FavoriteApi;
use RS\Controller\StandartBlock;

/**
 * Блок-контроллер збранные товары
 */
class Favorite extends StandartBlock
{
    protected static $controller_title = 'Избранное';
    protected static $controller_description = 'Отображает избранные товары';

    protected $default_params = [
        'indexTemplate' => 'blocks/favorite/favorite.tpl',
    ];

    function actionIndex()
    {
        $favorite_api = FavoriteApi::getInstance();

        //Если включено кэширование блоков, то необходимо передавать
        // сведения обо всех товарах в избранном в виде json в массиве global,
        // чтобы обновлять на JS состояние кнопок "В избранное"
        if (\Setup::$CACHE_BLOCK_ENABLED) {
            $product_ids = array_map('intval', array_values($favorite_api->loadInFavoriteList()));
            $this->app->addJsVar('favoriteProducts', $product_ids);
        }

        $countFavorites = $favorite_api->getFavoriteCount();
        $this->view->assign('countFavorite', $countFavorites);

        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
