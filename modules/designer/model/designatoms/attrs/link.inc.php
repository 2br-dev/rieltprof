<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\DesignAtoms\Attrs;

/**
 * Class Link - аттрибут типа ссылка
 */
class Link extends AbstractAttr {

    /**
     * AbstractCssProperty constructor.
     *
     * @param string $attribute - название аттрибута
     * @param string $title - имя аттрибута
     * @param mixed $value - значение аттрибута
     */
    function __construct($attribute, $title, $value = "")
    {
        if (empty($value)){
            $value = [
                'href' => '',
                'protocol' => 'http://',
                'blank' => false,
                'nofollow' => false
            ];
        }
        parent::__construct($attribute, $title, $value);

        $this->setAdditionalDataByKey('protocols', [
            'http://'  => 'http://',
            'https://' => 'https://',
            'tel:'     => 'tel:',
            'mailto:'  => 'mailto:',
            ''         => t('свой'),
        ]);
    }
}