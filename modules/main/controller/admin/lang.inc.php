<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;

use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;

class Lang extends \RS\Controller\Admin\Front
{
    /**
    * Диалог создания фалов локализации. Спрашивает идентификатор языка (ru, en, es, de ...)
    * 
    */
    function actionCreateLangFilesDialog()
    {
        if($this->url->isPost()){
            $lang = $this->url->post('lang', TYPE_STRING);
            $module = $this->url->post('module', TYPE_STRING);

            if(!empty($lang)){
                // Создаем файлы локализации
                \Main\Model\LangApi::createLangFiles($lang, $module);
                $this->result->addMessage(t('Языковые файлы созданы'));
            }
            else{
                $this->result->addEMessage(t('Укажите двухсимвольный идентификатор языка'));
            }
        }
        
        $this->result->setSuccess(true);
        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper->setTopTitle(t('Генерация языковых файлов'));
        $helper->setBottomToolbar(new \RS\Html\Toolbar\Element([
            'items' => [
                new \RS\Html\Toolbar\Button\SaveForm(null, t('Создать'))
            ]
        ]));

        $this->view->assign([
            'modules' => \Main\Model\LangApi::getTranslateModuleList()
        ]);

        $helper['form'] = $this->view->fetch('%main%/lang_create_dialog.tpl');
        $helper->viewAsForm();

        return $this->result->setTemplate( $helper['template'] );
    }

    /**
     * Создает и отправляет на скачивание zip архив с фразами для перевода
     */
    function actionDownloadLangFileArchive()
    {
        $allow_langs = \Main\Model\LangApi::getPossibleLang();

        $form_object = new FormObject(new PropertyIterator([
            'lang' => new Type\Varchar([
                'description' => t('Язык'),
                'hint' => t('Выберите язык, файлы для которого поместить в архив.'),
                'listFromArray' => [$allow_langs]
            ])
        ]));

        if ($this->url->isPost()) {
            if ($form_object->checkData()) {
                if (in_array($form_object['lang'], $allow_langs)) {

                    $tmp_file = \Main\Model\LangApi::makeLangArchive($form_object['lang']);
                    if ($tmp_file) {
                        return $this->result->setSuccess(true)->setAjaxWindowRedirect($tmp_file);
                    } else {
                        return $this->result->setSuccess(true)->addEMessage(t('Файл не создан. Не найдено ни одного файла для перевода для выбранного языка.'));
                    }

                } else {
                    $form_object->addError(t('Некорректно выбран идентификатор языка'), 'lang');
                    return $this->result
                        ->setSuccess(false)
                        ->setErrors($form_object->getDisplayErrors());
                }
            } else {
                return $this->result
                    ->setSuccess(false)
                    ->setErrors($form_object->getDisplayErrors());
            }
        }

        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper->setTopTitle(t('Скачать архив с файлами для перевода'));

        $helper->setBottomToolbar(new \RS\Html\Toolbar\Element([
            'items' => [
                new \RS\Html\Toolbar\Button\SaveForm(null, t('Скачать'))
            ]
        ]));

        $helper->setFormObject($form_object);
        $helper->viewAsForm();

        return $this->result->setTemplate( $helper['template'] );
    }

}

