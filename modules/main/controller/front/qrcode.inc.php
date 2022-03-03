<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Front;

use RS\Controller\Front;
use RS\Helper\QrCode\BarcodeGenerator;
use RS\Helper\QrCode\BarcodeLib;
use RS\Helper\QrCode\QrCodeGenerator;

/**
 * Класс генерирует QR код или штрихкод по входящим данным "на лету"
 * Для генерации ссылки на QR-код воспользуйтесь классом RS\Helper\QrCode\QrCodeGenerator
 */
class QRCode extends Front
{
    public function actionIndex()
    {
        $data_format = $this->url->convert($this->url->request('format', TYPE_STRING), ['raw', 'base64']);
        $data = $this->url->request('data', TYPE_STRING, '', null);
        $sign = $this->url->request('sign', TYPE_STRING);
        $options = $this->url->request('option', TYPE_ARRAY);

        $type = isset($options['f']) ? $options['f'] : 'png';
        $symbology = isset($options['s']) ? $options['s'] : 'qr';

        if ($data_format == 'base64') {
            $data = base64_decode($data);
            if (!$data) {
                throw new \RS\Exception(t('Не удалось распаковать данные'));
            }
        }

        $sign_key = $data.json_encode($options);

        if (QrCodeGenerator::getSign($sign_key) !== $sign) {
            throw new \RS\Exception(t('Неверная подпись'));
        }

        $qr_generator = new BarcodeLib();
        $qr_generator->output_image($type, $symbology, $data, $options);
    }
}