<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType\Facebook;

use Export\Model\ExportType\Facebook\OfferType\Standard;
use Export\Model\Orm\ExportProfile;
use RS\Orm\Type;

class Facebook extends \Export\Model\ExportType\AbstractType
{
    function _init()
    {
        return parent::_init()
            ->append([
                t('Основные'),
                    'full_description' => new Type\Integer([
                        'description' => t('Всегда выгружать полное описание?'),
                        'hint' => 'Полное описание не может содержать более 5000 знаков<br>По умолчанию выгружается короткое описание',
                        'checkboxView' => [1,0],
                    ]),
                    'barcode_offer_uniq' => new Type\Integer([
                        'description' => t('Добавлять к арткулу комплектаций id комплектации'),
                        'checkboxView' => [1,0],
                        'default' => 0,
                    ]),
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
            ]);
    }

    public function getTitle()
    {
        return t('Facebook');
    }

    public function getShortName()
    {
        return 'facebook';
    }

    public function getDescription()
    {
        return t('Экспорт товаров в Facebook');
    }

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
        $writer->writeElement('title', t("Facebook"));
        $writer->writeElement('link', \RS\Http\Request::commonInstance()->getDomain(true));
        $writer->writeElement('description', t("Экспорт товаров в Facebook из %0", [$site['full_title']]));

        $this->exportOffers($profile, $writer);
        $this->fireAfterAllOffersEvent('afteroffersexport',$profile,$writer);
        $writer->endElement();
        $writer->endElement();
        $writer->endDocument();
        $writer->flush();
        return file_get_contents($profile->getTypeObject()->getCacheFilePath());
    }

    public function getOfferTypesClasses()
    {
        return [
            new Standard(),
        ];
    }

    protected function getRootTag()
    {
        return "channel";
    }
}