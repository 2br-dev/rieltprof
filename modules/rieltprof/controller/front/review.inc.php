<?php

namespace rieltprof\Controller\Front;

use RS\Controller\Front;
use RS\Orm\Type\Date;

/**
 * Фронт контроллер - отзыв о владельце объявления
 */
class Review extends Front
{
    function actionIndex()
    {
        $from = $this->request('from', TYPE_INTEGER, 0);
        $to = $this->request('to', TYPE_INTEGER, 0);
        $text = $this->request('text', TYPE_STRING, '');
        $rating = $this->request('rating', TYPE_FLOAT, 0);
        $error = '';
        $success = false;
        $user_from = new \Users\Model\Orm\User($from);
        if(!empty($_POST)){
            if(empty($text) || trim($text) == ''){
                $error = 'text';
                $success = false;
            }
            if($error == ''){
                $review = new \Rieltprof\Model\Orm\Review();
                $review['user_from'] = $user_from->getFio();
                $review['user_to'] = $to;
                $review['text'] = $text;
                $review['rating'] = $rating;
                $review['date'] = date('d.m.Y');
                if($review->insert()){
                    $user = new \Users\Model\Orm\User($to);
                    $new_balls = $user['balls'] + $rating;
                    $new_review_num = $user['review_num'] + 1;
                    $user['balls'] = $new_balls;
                    $user['review_num'] = $new_review_num;
                    $user['rating'] = $new_balls / $new_review_num;
                    $user->update();
                    $success = true;
                }else{
                    $success = false;
                }

            }
        }
        $this->result->addSection('success', $success);
        $this->result->addSection('error', $error);

        return $this->result;
    }
}
