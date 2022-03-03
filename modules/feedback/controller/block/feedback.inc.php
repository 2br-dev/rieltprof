<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Feedback\Controller\Block;

use Feedback\Model\FormApi;
use Feedback\Model\Orm\FormItem;
use RS\Controller\StandartBlock;
use RS\Orm\ControllerParamObject;
use \RS\Orm\Type;

/**
 * Блок-контроллер Статья
 */
class Feedback extends StandartBlock
{
    protected static $controller_title = 'Форма отправки на E-mail';
    protected static $controller_description = 'Отображает форму связи для пользователя с уведомлением, на E-mail';

    protected $default_params = [
        'indexTemplate' => 'blocks/feedback/feedback.tpl' //Шаблон отображения произвольной формы
    ];

    /**
     * Возвращает ORM объект, содержащий настриваемые параметры или false в случае,
     * если контроллер не поддерживает настраиваемые параметры
     * @return ControllerParamObject|false
     */
    function getParamObject()
    {
        return parent::getParamObject()->appendProperty([
            'form_id' => new Type\Varchar([
                'description' => t('Выберите форму из списка'),
                'list' => [['\Feedback\Model\FormApi', 'staticSelectList']]
            ]),
            'hvalues' => new Type\ArrayList([
                'description' => t('Массив для передачи скрытых полей key=>value'),
                'visible' => false
            ]),
            'values' => new Type\ArrayList([
                'description' => t('Массив для передачи в уже существующие поля key=>value'),
                'visible' => false
            ])
        ]);
    }

    /**
     * Показ формы отправки на e-mail и её обработка
     *
     */
    function actionIndex()
    {
        $api = new FormApi();
        /** @var FormItem $form */
        $form = $api->getOneItem($this->getParam('form_id'));
        $errors = [];
        $request = null;


        if ($form['id'] > 0) { //Установим готовые для подстановки значения полей, если задан массив в шаблоне
            $form->setValues($this->getParam('values'));
        }

        if ($this->isMyPost() && ($form['id'] > 0 && $form['public'])) {//Если результат пришёл и форма такая существует

            $form->setHiddenValues($this->getParam('hvalues'));//Получим массив скрытых полей
            if ($api->send($form, $this->url)) { //OK
                $this->view->assign('success', true);
            } else { //Если есть ошибки
                $errors = $api->getErrors();
                $request = $this->url;
            }
        }

        $this->view->assign([
            'form' => $form,
            'error_fields' => $errors,
            'request' => $request
        ]);
        return $this->result->setTemplate($this->getParam('indexTemplate'));
    }
}
