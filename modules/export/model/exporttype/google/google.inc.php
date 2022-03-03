<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Google;

use \Export\Model\Orm\ExportProfile as ExportProfile;
use \RS\Orm\Type;

class Google extends \Export\Model\ExportType\AbstractType 
{
    private 
        $shop_config;
    
    function _init()
    {
        return parent::_init()->append([
            t('Поля данных для комплектаций'),
                'size' => new Type\Varchar([
                    'description' => t('Наименование значения размера в поле параметров комплектаций(size)'),
                    'hint' => t('Нужно только при наличии комплектаций у товаров. Необязательное.')
                ]),
                'color' => new Type\Varchar([
                    'description' => t('Наименование значения цвета в поле параметров комплектаций(color)'),
                    'hint' => t('Нужно только при наличии комплектаций у товаров. Необязательное.')
                ]),
                'gender' => new Type\Varchar([
                    'description' => t('Наименование значения пола в поле параметров комплектаций(gender)'),
                    'hint' => t('Нужно только при наличии комплектаций у товаров. Необязательное.')
                ]),
                'age_group' => new Type\Varchar([
                    'description' => t('Наименование значения возвростной группы в поле параметров комплектаций(age_group)'),
                    'hint' => t('Нужно только при наличии комплектаций у товаров. Необязательное.')
                ]),
                'pattern' => new Type\Varchar([
                    'description' => t('Наименование значения узора в поле параметров комплектаций(pattern)'),
                    'hint' => t('Нужно только при наличии комплектаций у товаров. Необязательное.')
                ]),
                'size_type' => new Type\Varchar([
                    'description' => t('Наименование значения типа размера в поле параметров комплектаций(size_type)'),
                    'hint' => t('Нужно только при наличии комплектаций у товаров. Необязательное.')
                ]),
                'size_system' => new Type\Varchar([
                    'description' => t('Наименование значения система размеров в поле параметров комплектаций(size_system)'),
                    'hint' => t('Нужно только при наличии комплектаций у товаров. Необязательное.')
                ]),
                'not_use_shipping_weight_tag' => new Type\Integer([
                    'description' => t('Не использовать тег g:shipping_weight в фиде'),
                    'hint' => t('Если в Google Merchants указаны данные правил доставки, то в некоторых случаях данный тег нужно убирать. Необязательное.'),
                    'checkboxview' => [1, 0]
                ]),
        ]);
    }
    
    /**
    * Возвращает название типа экспорта
    * 
    * @return string
    */
    function getTitle()
    {
        return t('Google Merchants');
    }
    
    /**
    * Возвращает описание типа экспорта для администратора. Возможен HTML
    * 
    * @return string
    */
    function getDescription()
    {
        return t('Экспорт в формате Google.Merchants - RSS 2.0');
    }
    
    /**
    * Возвращает идентификатор данного типа экспорта. (только англ. буквы)
    * 
    * @return string
    */
    function getShortName()
    {
        return 'google';
    }
    
    /**
    * Возвращает корневой тэг документа
    * 
    * @return string
    */
    protected function getRootTag()
    {
        return "channel";
    }   
    
    /**
    * Возвращает список классов типов описания
    * 
    * @param string $export_type_name - идентификатор типа экспорта
    * @return \Export\Model\ExportType\AbstractOfferType[]
    */
    protected function getOfferTypesClasses()
    {
        return [
            new OfferType\Rss2(),
        ];
    }

    /**
     * Возвращает экспортированные данные (XML)
     *
     * @return string
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     */
    public function export()
    {
        $profile = $this->getExportProfile();
        $writer = new \Export\Model\MyXMLWriter();
        $writer->openURI($profile->getTypeObject()->getCacheFilePath());
        $writer->startDocument('1.0', self::CHARSET);
        $writer->setIndent(true);
        $writer->setIndentString("    ");
        $writer->startElement('rss');
            $writer->writeAttribute('version', '2.0');
            $writer->writeAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
            
            $writer->startElement($this->getRootTag());
                $site = \RS\Site\Manager::getSite();
                //Запись основных сведений
                $writer->writeElement('title', t("Экспорт товаров в Google Merchants"));
                $writer->writeElement('link', \RS\Http\Request::commonInstance()->getDomain(true));
                $writer->writeElement('description', t("Экспорт товаров в Google Merchants из сайта %0", [$site['full_title']]));
                
                $this->exportOffers($profile, $writer);
                $this->fireAfterAllOffersEvent('afteroffersexport',$profile,$writer);
            $writer->endElement();
        $writer->endElement();
        $writer->endDocument();
        $writer->flush();
        return file_get_contents($profile->getTypeObject()->getCacheFilePath());
    }
    
    /**
    * Переводит строку XML в форматированный XML
    * 
    * @param string $xml_string - строка XML
    */
    function toFormatedXML($xml_string)
    {
       $dom = new \DOMDocument('1.0');
       $dom->preserveWhiteSpace = false;
       $dom->formatOutput = true;
       $dom->loadXML($xml_string);
       return $dom->saveXML();
    }
}
