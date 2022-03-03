<?php

namespace CodeGen\Model;

use RS\Orm\FormObject;
use RS\Orm\Type;

class GenerateModuleForm extends FormObject{


    function __construct()
    {
        $properties = new \RS\Orm\PropertyIterator([
            'name' => new Type\Varchar([
                'maxLength' => 25,
                'description' => t('Имя модуля (только английские буквы)'),
                'checker' => ['chkEmpty', t('Укажите имя дополнения')],
                ' checker' => ['chkPattern', t('Неверные символы в имени дополнения'), '/^[a-z0-9]+$/i'],
                '  checker' => ['chkPattern', t('Не должно начинаться с цифры'), '/^[^0-9]+[a-z0-9]+$/i'],
                '   checker' => [function($form, $val){
                    if(is_dir(\Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.strtolower($val))){
                        return t('Папка модуля уже существует. Пожалуйста, удалите папку или придумайте другое имя');
                    }
                    return true;
                }],
                'attr'=> [['placeholder'=>'MyModule']],
            ]),
            'title' => new Type\Varchar([
                'maxLength' => 255,
                'description' => t('Отображаемое название модуля'),
                'checker' => ['chkEmpty', t('Поле должно быть заполнено')],
                'attr'=> [['placeholder' => t('Мой модуль')]],
            ]),
            'description' => new Type\Text([
                'description' => t('Краткое описание модуля'),
                'checker' => ['chkEmpty', t('Поле должно быть заполнено')],
                'attr'=> [['placeholder' => t('Описание моего модуля')]],
            ]),
            'author' => new Type\Varchar([
                'description' => t('Автор'),
                'checker' => ['chkEmpty', t('Поле должно быть заполнено')],
                'attr'=> [['placeholder' => 'My Company']],
            ]),
        ]);

        parent::__construct($properties);
    }
}