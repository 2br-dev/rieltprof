<?php

namespace rieltprof\Controller\Front;

use RS\Controller\Front;
use RS\Orm\Type\Date;

/**
 * Фронт контроллер - отзыв о владельце объявления
 */
class BlackList extends Front
{
    function actionIndex()
    {
//        $current_user = \RS\Application\Auth::getCurrentUser();
//        $this->view->assign([
//            'current_user' => $current_user
//        ]);
        $phone = str_replace(" ", "", $this->request('phone', TYPE_STRING, ''));
        if(!empty($_POST)){
            $reviews = \RS\Orm\Request::make()
                ->from(new \Rieltprof\Model\Orm\BlackList())
                ->where([
                    'phone' => $phone,
                    'public' => 1
                ])->objects();
            $this->view->assign('reviews', $reviews);
            $this->view->assign('phone', $phone);
        }

        return $this->result->setTemplate('%rieltprof%/blacklist.tpl');
    }

    public function actionAddContact()
    {
        /**
         * @var \Users\Model\Orm\User $current_user
         */
        $current_user = \RS\Application\Auth::getCurrentUser();
        $user_fio = $current_user->getFio();
        $phone = str_replace(" ", "", $this->request('phone', TYPE_STRING, ''));
        $comment = $this->request('comment', TYPE_STRING, '');
        $error = [];
        $success = false;
        if(!empty($_POST)){
            $denied_add = \RS\Orm\Request::make()
                ->from(new \Rieltprof\Model\Orm\BlackList())
                ->where([
                    'author' => $user_fio,
                    'phone' => $phone
                ])->count();
            if($denied_add){
                $error['denied'] = true;
            }
            if($phone == ''){
                $error['phone'] = true;
            }
            if(trim($comment) == ''){
                $error['comment'] = true;
            }
            if(empty($error)){
                $blackList = new \Rieltprof\Model\Orm\BlackList();
                $blackList['author'] = $user_fio;
                $blackList['phone'] = $phone;
                $blackList['comment'] = trim($comment);
                $blackList['public'] = 1;
                $blackList['date'] = date('d.m.Y');
                if($blackList->insert()){
                    $success = true;
                }
            }
        }
        $this->result->setSuccess(true)->addSection(['success' => $success, 'error' => $error]);
        return $this->result;
    }
}
