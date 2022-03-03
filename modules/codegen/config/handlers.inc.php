<?php
namespace CodeGen\Config;
use RS\Html\Toolbar\Button\Add;
use RS\Html\Toolbar\Button\Button;
use RS\Router\Manager;

/**
* Класс содержит обработчики событий, на которые подписан модуль
*/
class Handlers extends \RS\Event\HandlerAbstract
{
    /**
    * Добавляет подписку на события
    * 
    * @return void
    */
    function init()
    {
        // Добавление кнопки на страницу "Настройка модулей"
        $this->bind('controller.exec.modcontrol-admin-control.index');
    }

    public static function controllerExecModcontrolAdminControlIndex(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        // Добавление кнопки на страницу "Настройка модулей"

        $url = Manager::obj()->getAdminUrl('generateModule', [], 'codegen-control');
        
        if ($helper['topToolbar']) {
            $helper['topToolbar']->addItem(
                new Button($url, t('сгенерировать новый модуль'), ['attr' => ['class' => 'crud-add']]),
                'generate_module'
            );
        }
    }
}