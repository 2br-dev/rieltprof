<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Catalog\Model;

use RS\Application\Auth;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;
use RS\Site\Manager as SiteManager;

/**
 * Класс определяет объект - избранные товары
 */
class FavoriteApi extends EntityList
{
    protected $user_id;
    protected $guest_id;
    protected $cache;

    function __construct()
    {
        parent::__construct(new Orm\Favorite, [
            'multisite' => true,
            'defaultOrder' => 'id dasc',
        ]);
    }

    /**
     * Добавляет товар в избранное
     *
     * @param int $product_id - id товара
     * @return void
     */
    function addToFavorite($product_id)
    {
        if (!$this->alreadyInFavorite($product_id)) {
            $favorite = new Orm\Favorite();
            $favorite['guest_id'] = self::getGuestId();
            $favorite['user_id'] = self::getUserId();
            $favorite['product_id'] = $product_id;
            $favorite->insert();

            $this->cleanLocalCache();
        }
    }

    /**
     * Загружает список товаров в избранном у данного пользователя
     *
     * @return array
     */
    public function loadInFavoriteList()
    {
        if (!isset($this->cache)) {
            $this->cache = OrmRequest::make()
                ->from(new Orm\Favorite(), 'F')
                ->where("(F.guest_id = '#guest' OR F.user_id = '#user')", [
                    'guest' => $this->getGuestId(),
                    'user' => $this->getUserId(),
                ])
                ->exec()->fetchSelected('product_id', 'product_id');
        }

        return $this->cache;
    }

    /**
     * Возвращает true если данный продукт уже в избранном
     *
     * @param $product_id
     * @return bool
     */
    public function alreadyInFavorite($product_id)
    {
        $this->loadInFavoriteList();
        return isset($this->cache[$product_id]);
    }

    /**
     * Возвращает установленный $guest_id
     * если $guest_id не установлен - возврщает идентификатор текущего гостя
     *
     * @return string
     */
    public function getGuestId()
    {
        return ($this->guest_id === null) ? Auth::getGuestId() : $this->guest_id;
    }

    /**
     * Возвращает установленный $user_id
     * если $user_id не установлен - возврщает id текущего пользователя
     *
     * @return int
     */
    public function getUserId()
    {
        return ($this->user_id === null) ? Auth::getCurrentUser()['id'] : $this->user_id;
    }

    /**
     * Возвращает количество товаров в избранном
     *
     * @return int
     */
    public function getFavoriteCount()
    {
        $res = OrmRequest::make()
            ->select('count(F.id) AS count')
            ->from(new Orm\Product(), 'P')
            ->join(new Orm\Favorite(), 'P.id = F.product_id', 'F')
            ->where("(F.guest_id = '#guest' OR F.user_id = '#user') AND F.site_id ='#site' AND P.public = 1", [
                'guest' => $this->getGuestId(),
                'user' => $this->getUserId(),
                'site' => SiteManager::getSiteId(),
            ])
            ->exec()->fetchRow();

        return $res['count'];
    }

    /**
     * Возвращает список товаров, находящихся в списке избранных
     *
     * @param integer $page номер страницы
     * @param integer $pageSize количество элементов на странице
     * @return array
     */
    function getFavoriteList($page = 1, $pageSize = null)
    {
        $q = OrmRequest::make()
            ->select("P.*, '1' as isInFavorite")
            ->from(new Orm\Product(), 'P')
            ->join(new orm\Favorite(), 'P.id = F.product_id', 'F')
            ->where("(F.guest_id = '#guest' OR F.user_id = '#user') AND F.site_id = '#site' AND P.public = 1", [
                'guest' => self::getGuestId(),
                'user' => self::getUserId(),
                'site' => SiteManager::getSiteId(),
            ])
            ->orderby('F.id DESC');
        if ($pageSize) {
            $q->limit(($page - 1) * $pageSize, $pageSize);
        }

        return $q->objects();
    }

    /**
     * Сливает избранные товары гостя с избранным пользователя
     *
     * вызывается при авторизации
     */
    public function mergeFavorites()
    {
        \RS\orm\Request::make()
            ->update(new Orm\Favorite(), true)
            ->set(['user_id' => $this->getUserId()])
            ->where(['guest_id' => $this->getGuestId()])
            ->exec();

        $this->cleanLocalCache();
    }

    /**
     * Удаляет товар из избранного
     *
     * @param int $product_id - id товара
     */
    function removeFromFavorite($product_id)
    {
        OrmRequest::make()
            ->delete()
            ->from(new Orm\Favorite())
            ->where("(guest_id = '#guest' OR user_id = '#user') AND product_id = '#product'", [
                'guest' => self::getGuestId(),
                'user' => self::getUserId(),
                'product' => $product_id,
            ])
            ->exec();

        $this->cleanLocalCache();
    }

    /**
     * Устанавливает $guest_id для дальнейшего использования
     *
     * @param int $guest_id - id гостя
     */
    public function setGuestId($guest_id)
    {
        $this->guest_id = $guest_id;
        $this->cleanLocalCache();
    }

    /**
     * Устанавливает $user_id для дальнейшего использования
     *
     * @param int $user_id - id пользователя
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        $this->cleanLocalCache();
    }

    /**
     * Очищает локальный кэш
     * Кэш применяется для ускорения метода alreadyInFavorite
     */
    public function cleanLocalCache()
    {
        $this->cache = null;
    }
}
