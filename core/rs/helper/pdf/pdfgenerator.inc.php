<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Helper\Pdf;
use Dompdf\Dompdf;
use RS\View\Engine;

require_once(__DIR__ . '/dompdf/autoload.inc.php');

/**
 * Обертка над DOMPDF для ReadyScript
 */
class PDFGenerator extends Dompdf
{
    /**
     * Рендерит SMarty Шаблон и возвращает содержимое PDF документа
     *
     * @param string $template - путь к шаблону который надо воспроизвести
     * @param array $vars - массив переменнных
     * @return string
     */
    function renderTemplate($template, $vars)
    {
        $view = new Engine();
        $html = $view->assign($vars)->fetch($template);

        $this->loadHtml($html);
        $this->render();
        $pdf_content = $this->output();

        return $pdf_content;
    }
}