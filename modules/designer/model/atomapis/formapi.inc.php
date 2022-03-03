<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\AtomApis;

use RS\Module\AbstractModel\BaseModel;

/**
 * Класс API для компонента форма
 */
class FormApi extends BaseModel
{
    /**
     * Возвращает данные формы
     *
     * @param integer $form_id - id формы
     *
     * @return array|false
     * @throws \RS\Orm\Exception
     */
    function getFormData($form_id)
    {
        $form = new \Feedback\Model\Orm\FormItem($form_id);
        if (!$form['id']){
            $this->addError(t('Форма %0 не найдена', [$form_id]));
            return false;
        }else{
            $form_data = $form->getValues();
            $fields = $form->getFields();

            $form_data['list'] = [];

            if (!empty($fields)){
                foreach($fields as $field){
                    /**
                     * @var \Feedback\Model\Orm\FormFieldItem $field
                     */
                    $form_data['list'][] = $field->getValues();
                }
            }
            return $form_data;
        }
    }
}