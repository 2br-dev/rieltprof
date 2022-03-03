<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Feedback\Config;
 
/**
* Класс отвечает за установку и обновление модуля
*/
class Install extends \RS\Module\AbstractInstall
{
    function install()
    {
        if ($result = parent::install()) {
            $this->importCsv(new \Feedback\Model\CsvSchema\Forms, 'forms');
        }
        return $result;
    }
    
    /**
    * Выполняется, после того, как были установлены все модули. 
    * Здесь можно устанавливать настройки, которые связаны с другими модулями.
    * 
    * @param array $options параметры установки
    * @return bool
    */        
    function deferredAfterInstall($options)
    {
        if ($options['set_demo_data']) {
            $site_config = \RS\Config\Loader::getSiteConfig();
            if ($site_config->getThemeName() == 'default') {
                $form = \Feedback\Model\Orm\FormItem::loadByWhere([
                    'title' => t('Обратная связь')
                ]);

                //Настраиваем блок новости в дефолтном шаблоне
                \Templates\Model\PageApi::setupModule(null, 'feedback\controller\block\button', [
                    'form_id' => $form['id']
                ]);
            }
        }
        return true;        
    }    
    
}