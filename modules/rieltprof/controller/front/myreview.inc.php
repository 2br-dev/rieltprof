<?php
namespace Rieltprof\Controller\Front;

use Catalog\Model\Api as ProductApi;
use Catalog\Model\Dirapi;
use RS\Application\Auth as AppAuth;
use RS\Controller\Front;
use RS\Orm\Type\Varchar;
use Users\Model\Orm\User;
use Users\Model\Verification\Action\TwoStepProfile;

/**
* Просмотр данных владельца объявления
*/
class MyReview extends Front
{
    /** Поля, которые следует ожидать из POST */
    public $use_post_keys = [
        'is_company', 'company', 'company_inn', 'name', 'surname', 'midname', 'sex', 'passport', 'phone', 'e_mail',
        'openpass', 'current_pass', 'openpass_confirm', 'captcha', 'data', 'changepass', 'birthday'
    ];

    protected $id;
    protected $lastpage;
    
    /** @var ProductApi */
//    public $api;
    /** @var Dirapi */
//    public $dirapi;
//    public $config;
    
    function init()
    {
//        $this->id     = $this->url->get('id', TYPE_STRING);
//        $this->api    = new ProductApi();
//        $this->dirapi = new Dirapi();
//        $this->config = ConfigLoader::byModule($this);
    }
    
    /**
    * Обычный просмотр товара
    */
    function actionIndex()
    {
        /**
        * @var \Catalog\Model\Orm\Product $item
        */
//        $user = \RS\Application\Auth::getCurrentUser();
        $user = $this->getUserForProfile();
        $reviews = \RS\Orm\Request::make()
            ->from(new \Rieltprof\Model\Orm\Review())
            ->where([
                'user_to' => $user['id']
            ])->objects();
        if (!$user){
            $this->e404(t('Для просмотра этой страницы необходимо авторизоваться'));
        }
        $this->view->assign('user', $user);
        $this->view->assign('reviews', $reviews);
        //Пишем лог
//        UserLogApi::appendUserLog(new LogtypeShowProduct(), $item['id'], null, $item['id']);
        return $this->result->setTemplate( '%rieltprof%/myreview.tpl' );
    }

    /**
     * Возвращает объект пользователя, подготовленный для редактирования профиля
     *
     * @return User
     */
    protected function getUserForProfile()
    {
        $user = clone AppAuth::getCurrentUser();
        $user->usePostKeys($this->use_post_keys);

        //Добавим объекту пользователя 2 виртуальных поля
        $user->getPropertyIterator()->append([
            'current_pass' => new Varchar([
                'name' => 'current_pass',
                'maxLength' => '100',
                'description' => t('Текущий пароль'),
                'runtime' => true,
                'Attr' => [['size' => '20', 'type' => 'password', 'autocomplete' => 'off']],
                'checker' => [function($user, $value) {
                    if ($user['changepass'] && $user->cryptPass($value) !== $user['pass']) {
                        return t('Неверно указан текущий пароль');
                    }
                    return true;
                }]
            ]),

            'openpass_confirm' => new Varchar([
                'name' => 'openpass_confirm',
                'maxLength' => '100',
                'description' => t('Повтор пароля'),
                'runtime' => true,
                'Attr' => [['size' => '20', 'type' => 'password', 'autocomplete' => 'off']],
                'checker' => [function($user, $value) {
                    if ($user['changepass'] && strcmp($user['openpass'], $user['openpass_confirm']) != 0) {
                        return t('Пароли не совпадают');
                    }
                    return true;
                }]
            ]),
        ]);

        //Отмечаем номер телефона, который указан у пользователя - как подтвержденный
        $user['__phone']->setVerifiedPhone($user['phone']);
        $user['__phone']->setVerificationAction(new TwoStepProfile());

        return $user;
    }
}
