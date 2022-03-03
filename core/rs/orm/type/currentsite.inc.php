<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class CurrentSite extends Integer
{
    protected
        $vis_form = false,
        $listen_post = false,
        $always_modify = true;
        
    function __construct(array $options = null)
    {
        $this->setDescription(t('ID сайта'));
        parent::__construct($options);
    }
    
    function get()
    {
        return (is_null($this->value) || $this->value === '') ? \RS\Site\Manager::getSiteId() : $this->value;
    }

}  