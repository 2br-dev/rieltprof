<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Helper\QrCode;

use RS\Router\Manager;
use RS\Router\Route;

/**
 * Класс генерирует ссылку на изображение QR-код или Штрих-код
 */
class QrCodeGenerator
{
    /**
     * @param string $data произвольные данные
     * @param array $options Опции форирования QR кода или штрихкода

        f - Format. One of:

        png
        gif
        jpeg
        svg

        s - Symbology (type of barcode). One of:

        upc-a          code-39         qr     dmtx
        upc-e          code-39-ascii   qr-l   dmtx-s
        ean-8          code-93         qr-m   dmtx-r
        ean-13         code-93-ascii   qr-q   gs1-dmtx
        ean-13-pad     code-128        qr-h   gs1-dmtx-s
        ean-13-nopad   codabar                gs1-dmtx-r
        ean-128        itf

        d - Data. For UPC or EAN, use * for missing digit. For Codabar, use ABCD or ENT* for start and stop characters. For QR, encode in Shift-JIS for kanji mode.
        w - Width of image. Overrides sf or sx.
        h - Height of image. Overrides sf or sy.
        sf - Scale factor. Default is 1 for linear barcodes or 4 for matrix barcodes.
        sx - Horizontal scale factor. Overrides sf.
        sy - Vertical scale factor. Overrides sf.
        p - Padding. Default is 10 for linear barcodes or 0 for matrix barcodes.
        pv - Top and bottom padding. Default is value of p.
        ph - Left and right padding. Default is value of p.
        pt - Top padding. Default is value of pv.
        pl - Left padding. Default is value of ph.
        pr - Right padding. Default is value of ph.
        pb - Bottom padding. Default is value of pv.
        bc - Background color in #RRGGBB format.
        cs - Color of spaces in #RRGGBB format.
        cm - Color of modules in #RRGGBB format.
        tc - Text color in #RRGGBB format. Applies to linear barcodes only.
        tf - Text font for SVG output. Default is monospace. Applies to linear barcodes only.
        ts - Text size. For SVG output, this is in points and the default is 10. For PNG, GIF, or JPEG output, this is the GD library built-in font number from 1 to 5 and the default is 1. Applies to linear barcodes only.
        th - Distance from text baseline to bottom of modules. Default is 10. Applies to linear barcodes only.
        ms - Module shape. One of: s for square, r for round, or x for X-shaped. Default is s. Applies to matrix barcodes only.
        md - Module density. A number between 0 and 1. Default is 1. Applies to matrix barcodes only.
        wq - Width of quiet area units. Default is 1. Use 0 to suppress quiet area.
        wm - Width of narrow modules and spaces. Default is 1.
        ww - Width of wide modules and spaces. Applies to Code 39, Codabar, and ITF only. Default is 3.
        wn - Width of narrow space between characters. Applies to Code 39 and Codabar only. Default is 1.
     *
     * @param string (raw|base64) $data_format в какой формат кодировать при передаче по ссылке
     * @param bool $absolute Если true, то будет создана абсолютная ссылка
     * @return string
     */
    public static function buildUrl($data,
                                    $options = [],
                                    $data_format = null, //raw
                                    $absolute = false)
    {
        $params = [
            'data' => ($data_format == 'base64' ? base64_encode($data): $data),
        ];

        array_walk($options, function(&$value) {
            $value = (string)$value;
        });

        if ($data_format !== null) $params['data_format'] = $data_format;
        if ($options) $params['option'] = $options;

        $sign_key = $data.json_encode($options);
        $params['sign'] = self::getSign($sign_key);

        $router = Manager::obj();
        return $router->getUrl('main-front-qrcode', $params, $absolute);
    }

    /**
     * Возвращает подпись для запроса на генерацию QR кода
     *
     * @param string $sign_key строка для подписи
     * @return string
     */
    public static function getSign($sign_key)
    {
        return sha1($sign_key.md5(\Setup::$SECRET_KEY).'-- QR CODE --');
    }
}