<?php
namespace Rieltprof\Model\Behavior;

use Users\Model\Orm\User;

/**
* Объект - Расширения пользователя
*/
class UsersUser extends \RS\Behavior\BehaviorAbstract
{
    /**
     * Проверяет поле ФОТО на заполненность
     *
     * @param User $_this
     * @param mixed $value
     * @param string $error_text
     * @param string $field
     *
     * @return bool|string
     */
    public static function checkUserPhotoField($_this, $value, $error_text)
    {
        if ((empty($_this['photo']) && $value == '')) {
            return $error_text;
        }
        return true;
    }

    /**
     * Возвращает все объявления пользователя
     * @param $user_id
     * @return bool|\RS\Orm\AbstractObject[]
     */
    public static function getAllAds($user_id){
        $ads = \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Product())
            ->where([
                'owner' => $user_id,
                'public' => 1
            ])->objects();
        return $ads ? $ads : false;
    }

    /**
     * Возвращает количесво объявлений пользователя
     * @param $user_id
     * @return int
     */
    public function getCountAds($user_id){
        $ads = \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Product())
            ->where([
                'owner' => $user_id,
                'public' => 1
            ])->count();
        return $ads ? $ads : 0;
    }

    /**
     * Возвращает все категории в которых у Риелтора есть объявления.
     * @return array|bool
     */
    public function getUniqCategoryAds()
    {
        $orm = $this->owner;
        $parent_dir = [];
        $categories = \RS\Orm\Request::make()
            ->select('DISTINCT `maindir`')
            ->from(new \Catalog\Model\Orm\Product())
            ->where([
                'owner' => $orm['id']
            ])->exec()->fetchSelected(null, 'maindir');
        $result = [];
        foreach ($categories as $key => $value){
            $dir_obj = new \Catalog\Model\Orm\Dir($value);
            $result[$key]['parent'] = $dir_obj->getParentDir()->name;
            $result[$key]['name'] = $dir_obj->name;
            $result[$key]['id'] = $value;
        }
        return $result;
    }

    /**
     * Возвращает все объявления риелтора из определенной категории по id категории
     * @param $id
     * @return bool|\RS\Orm\AbstractObject[]
     */
    public function getAdsFromCategoryById($id){
        $orm = $this->owner;
        $ads = \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Product())
            ->where([
                'maindir' => $id,
                'owner' => $orm['id']
            ])
            ->orderby('dateof DESC')->objects();
        return $ads ? $ads : false;
    }

    /**
     * Проверяет возможно ли оставить отзыв. Пользователь может оставлять только один отзыв на одного владельца объявления
     * @param $from
     * @param $to
     */
    public function canSendReview($from, $to){
        $check = \RS\Orm\Request::make()
            ->from(new \Rieltprof\Model\Orm\Review())
            ->where([
                'user_from' => $from,
                'user_to' => $to
            ])->exec()->fetchAll();
        if(count($check)){
            return false;
        }
        return true;
    }

    /**
     * Функция возвращает количество отзывов о пользователе
     * @return int
     */
    public function getCountReviews()
    {
        $orm = $this->owner;
        $count_reviews = \RS\Orm\Request::make()
            ->from(new \Rieltprof\Model\Orm\Review())
            ->where([
                'user_to' => $orm['id']
            ])->count();
        return $count_reviews;
    }

}

