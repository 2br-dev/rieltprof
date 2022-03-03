<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace AtolOnline\Model\CashRegisterType\Version;
use RS\Config\Loader;
use RS\Orm\Type;

/**
* Класс интерграции с АТОЛ Онлайн по протоколу версии 4
*/
class AtolOnlineV4 extends AtolOnlineV3
{
    const 
        API_URL = "https://online.atol.ru/possystem/v4/";
        //API_URL = "https://testonline.atol.ru/possystem/v4/";

    /**
     * Добавляет ошибку по коду. Возвращает false, если ошибки нет.
     * Возвращает true, если ошибка была
     *
     * @param integer $code
     * @return bool
     */
    protected function checkAuthError($response)
    {
        if ($response && $response['error']['code']) {
            $this->addError($response['error']['text'].' ID:'.$response['error']['error_id'].' Code:'.$response['error']['code']);
            return true;
        }
        return false;
    }

    /**
     * Возвращает url для нужной операции
     *
     * @param string $operation - нужная операция
     *
     * @return string
     * @throws \RS\Exception
     */
    protected function getOperationUrl($operation)
    {
        return $this->getApiUrl().$this->getOption('group_code')."/".$operation."?token=".urlencode($this->tokenid);
    }

    /**
     * Возвращает url для получения информации по чеку
     *
     * @param string $uuid - уникальный идентификатор чека от провайдера
     *
     * @return string
     * @throws \RS\Exception
     */
    protected function getReportUrl($uuid)
    {
        return $this->getApiUrl().$this->getOption('group_code')."/report/".$uuid."?token=".urlencode($this->tokenid);
    }

    /**
     * Добавляет в чек секцию со служебной информацией
     *
     * @param array $receipt - чек для отправки
     * @param string $operation_type - тип операции
     * @param string $sign - подпись
     *
     * @return array
     */
    protected function getServicePart($receipt, $operation_type, $sign)
    {
        $site_config = Loader::getSiteConfig();
        if (!$site_config->firm_email) {
            $this->addError(t('Укажите официальный Email компании в разделе Веб-сайт -> Настройка сайта'));
        }

        $my_config = $this->getCashRegisterTypeConfig();

        $section = $operation_type == self::OPERATION_SELL_CORRECTION ? 'correction' : 'receipt';

        $receipt[$section]['company']['inn']             = $this->getOption('inn'); //ИНН
        $receipt[$section]['company']['payment_address'] = $my_config->domain ? $my_config->domain : $this->getCurrentDomainUrl();
        $receipt[$section]['company']['email']           = $site_config->firm_email;

        $sno = $this->getOption('sno', 0);
        if ($sno){
            $receipt[$section]['company']['sno'] = $sno;
        }

        $receipt['service']['callback_url']    = $this->getCallbackUrl($operation_type, $sign);

        return $receipt;
    }

    /**
     * Добавляет в $receipt иформацию о покупателе
     *
     * @param $receipt
     * @param $user
     * @return mixed
     *
     * @return array
     */
    protected function getClientInfo($receipt, $user)
    {
        if ($user['e_mail'] != ""){
            $receipt['receipt']['client']['email'] = $user['e_mail'];
        }
        if ($user['phone']) {
            $receipt['receipt']['client']['phone'] = $this->preparePhone($user['phone']);
        }

        return $receipt;
    }



    /**
     * Добавляет ошибку при создании чека по её ответному коду
     *
     * @param string $response - ответ сервера АТОЛ на опрерацию регистрации документа продажи, возврата
     * @return bool
     */
    protected function checkCreateReceiptError($response)
    {
        if (isset($response['error'])) {
            $this->addError($response['error']['text'].' Error ID:'.$response['error']['error_id'].' Error Code:'.$response['error']['code']);
            return true;
        }

        return false;
    }

    /**
     * Возвращает секцию данных о налогах
     *
     * @param string $tax_id - идентификатор налога
     * @return array
     */
    protected function getItemTaxData(string $tax_id)
    {
        return ['vat' => ['type' => $tax_id]];
    }

    /**
     * Возвращает объект формы чека коррекции
     *
     * @return \RS\Orm\FormObject | false Если false, то это означает, что кассовый модуль не поддерживает чеки коррекции
     */
    public function getCorrectionReceiptFormObject()
    {
        $form_object = parent::getCorrectionReceiptFormObject()->appendProperty([
            'tax_sum' => new Type\Varchar([
                'description' => t('Сумма налога'),
                'hint' => t('Если без НДС, то нужно указать 0')
            ]),
            'type' => new Type\Varchar([
                'description' => t('Тип коррекции'),
                'hint' => t('Используется в API версии 4'),
                'listFromArray' => [[
                    'self' => t('Самостоятельно'),
                    'instruction' => t('По предписанию')
                ]]
            ]),
            'base_date' => new Type\Date([
                'description' => t('Дата документа основания для коррекции в формате'),
                'checker' => ['ChkEmpty', t('Укажите дату документа основания для возврата')]
            ]),
            'base_number' => new Type\Varchar([
                'description' => t('Номер документа основания для коррекции'),
                'checker' => ['ChkEmpty', t('Укажите номер документа основания для возврата')]
            ]),
            'base_name' => new Type\Varchar([
                'description' => t('Описание коррекции'),
                'checker' => ['ChkEmpty', t('Укажите описание документа основания для возврата')]
            ])
        ]);

        return $form_object;
    }

    /**
     * Обрабатывает дополнительную информацию о налогах и вписывает её в чек коррекции
     *
     * @param array $receipt
     * @param \RS\Orm\FormObject|array $data - объект с данными для чека коррекции
     * @return array
     */
    protected function getCorrectionTax($receipt, $data)
    {
        $receipt['correction']['correction_info'] = [
            'type' => $data['type'],
            'base_date' => date('d.m.Y', strtotime($data['base_date'])),
            'base_number' => $data['base_number'],
            'base_name' => $data['base_name']
        ];

        $receipt['correction']['vats'] = [
            [
                'type' => $data['tax'],
                'sum' => (float)$data['tax_sum']
            ]
        ];

        return $receipt;
    }

    /**
     * Позволяет модифицировать данные по умолчанию для позиции в чеке
     *
     * @param array $item_data - данные позиции в чеке
     * @return void
     */
    protected function modifyReceiptItemData(array &$item_data)
    {
        $item_data['name'] = mb_substr($item_data['name'], 0, 128);
    }
}
