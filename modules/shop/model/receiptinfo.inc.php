<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model;

use RS\Config\Loader;
use RS\Helper\QrCode\QrCodeGenerator;
use Shop\Model\Orm\Receipt;

/**
 * Класс определяет стандартизированную информацию по кассовому чеку
 */
class ReceiptInfo
{
    private $receipt;
    private $fiscal_receipt_number;
    private $shift_number;
    private $receipt_datetime;
    private $total;
    private $fn_number;
    private $ecr_registration_number;
    private $fiscal_document_number;
    private $fiscal_document_attribute;
    private $ofd_receipt_url;
    private $tax_mode;
    private $qr_code_data;


    function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * Возвращает объект чека
     *
     * @return Receipt
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * Возвращает Номер чека в смене
     *
     * @return mixed
     */
    public function getFiscalReceiptNumber()
    {
        return $this->fiscal_receipt_number;
    }

    /**
     * Устанавливает Номер чека в смене
     *
     * @param mixed $fiscal_receipt_number
     */
    public function setFiscalReceiptNumber($fiscal_receipt_number): void
    {
        $this->fiscal_receipt_number = $fiscal_receipt_number;
    }

    /**
     * Возвращает номер смены
     *
     * @return mixed
     */
    public function getShiftNumber()
    {
        return $this->shift_number;
    }

    /**
     * Устанавливает Номер смены
     *
     * @param mixed $shift_number
     */
    public function setShiftNumber($shift_number): void
    {
        $this->shift_number = $shift_number;
    }

    /**
     * Возвращает Дату и время чека
     *
     * @return string
     */
    public function getReceiptDatetime()
    {
        return $this->receipt_datetime;
    }

    /**
     * Устанавливает Дату и время чека
     *
     * @param string $receipt_datetime
     */
    public function setReceiptDatetime($receipt_datetime): void
    {
        $this->receipt_datetime = $receipt_datetime;
    }

    /**
     * Возвращает Сумму чека в рублях
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Устанавливает Сумму чека в рублях
     *
     * @param float $total
     */
    public function setTotal($total): void
    {
        $this->total = $total;
    }

    /**
     * Возвращает номер Фискального накопителя
     *
     * @return string
     */
    public function getFnNumber()
    {
        return $this->fn_number;
    }

    /**
     * Устанавливает номер Фискального накопителя
     *
     * @param string $fn_number
     */
    public function setFnNumber($fn_number): void
    {
        $this->fn_number = $fn_number;
    }

    /**
     * Возвращает регистрационный номер ККТ
     *
     * @return string
     */
    public function getEcrRegistrationNumber()
    {
        return $this->ecr_registration_number;
    }

    /**
     * Устанавливает регистрационный номер ККТ
     *
     * @param string $ecr_registration_number
     */
    public function setEcrRegistrationNumber($ecr_registration_number): void
    {
        $this->ecr_registration_number = $ecr_registration_number;
    }

    /**
     * Возвращает фискальный номер документа
     *
     * @return string
     */
    public function getFiscalDocumentNumber()
    {
        return $this->fiscal_document_number;
    }

    /**
     * Устаналивает фискальный номер документа
     *
     * @param mixed $fiscal_document_number
     */
    public function setFiscalDocumentNumber($fiscal_document_number): void
    {
        $this->fiscal_document_number = $fiscal_document_number;
    }

    /**
     * Возвращает фискальный признак документа
     *
     * @return string
     */
    public function getFiscalDocumentAttribute()
    {
        return $this->fiscal_document_attribute;
    }

    /**
     * Устанавливает фискальный признак документа
     *
     * @param string $fiscal_document_attribute
     */
    public function setFiscalDocumentAttribute($fiscal_document_attribute): void
    {
        $this->fiscal_document_attribute = $fiscal_document_attribute;
    }

    /**
     * Устанавливает ссылку на проверку чека в ОФД
     *
     * @param $ofd_receipt_url
     * @return void
     */
    public function setReceiptOftUrl($ofd_receipt_url): void
    {
        $this->ofd_receipt_url = $ofd_receipt_url;
    }

    /**
     * Возвращает ссылку на проверку чека в ОФД
     *
     * @return string
     */
    public function getReceiptOfdUrl()
    {
        if (!$this->ofd_receipt_url) {
            //Если явно не задан URL для проверки чека, возвращает общий URL на ОФД
            $shop_config = Loader::byModule('shop');
            return CashRegisterApi::getOFDReceiptUrlMask($shop_config['ofd']);
        }
        return $this->ofd_receipt_url;
    }

    /**
     * Устанавливает режим налогообложения для чека
     *
     * @param integer $tax_mode
     */
    public function setTaxMode($tax_mode)
    {
        $this->tax_mode = $tax_mode;
    }

    /**
     * Возвращает режим налогообложения
     *
     * @param bool $receipt_code Если true, то будет возвращена цифра для QR кода чека
     * @return mixed
     */
    public function getTaxMode($receipt_code = false)
    {
        if ($receipt_code) {
            switch($this->tax_mode) {
                case CashRegisterApi::TAX_MODE_OSN: return 0;
                case CashRegisterApi::TAX_MODE_USN_INCOME: return 1;
                case CashRegisterApi::TAX_MODE_USN_INCOME_OUTCOME: return 2;
                case CashRegisterApi::TAX_MODE_ENVD: return 3;
                case CashRegisterApi::TAX_MODE_ESN: return 4;
                case CashRegisterApi::TAX_MODE_PATENT: return 5;
                default: return null;
            }
        }

        return $this->tax_mode;
    }

    /**
     * Если сервис online касс возвращает данные для QR кода, то с помощью данного метода можно его установить
     * Это отключит необходимость генерировать QR код самостоятельно
     *
     * @param $data
     */
    public function setQrCodeData($data)
    {
        $this->qr_code_data = $data;
    }

    /**
     * Возвращает
     *
     * @return mixed
     */
    public function getQrCodeData()
    {
        return $this->qr_code_data;
    }

    /**
     * Возвращает тип операции по чеку для QR кода
     * @return integer|null
     */
    protected function getOperationType()
    {
        switch($this->receipt->type) {
            case Receipt::TYPE_SELL: return 1;
            case Receipt::TYPE_REFUND: return 2;
            case Receipt::TYPE_CORRECTION: return 7; //Коррекция прихода
        }

        return null;
    }

    /**
     * Возвращает ссылку на QR код чека
     *
     * @return string|null
     */
    public function getQrCodeImageUrl($width = 200, $height = 200, $absolute = false)
    {
        $data = $this->getQrCodeData();
        $operation_type = $this->getOperationType();
        $receipt_ts = strtotime($this->getReceiptDatetime());

        if (!$data) {
            $data = "t=".date('Ymd', $receipt_ts).'T'.date('Hi', $receipt_ts)
                .'&s='.$this->getTotal()
                .'&fn='.$this->getFnNumber()
                .'&i='.$this->getFiscalDocumentNumber()
                .'&fp='.$this->getFiscalDocumentAttribute()
                .'&n='.$operation_type;
        };

        if ($data) {
            return QrCodeGenerator::buildUrl($data, [
                'w' => $width,
                'h' => $height,
            ], null, $absolute);
        }

        return null;
    }
}