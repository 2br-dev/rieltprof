<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Application\Compress;

class Js extends AbstractCompress
{
    const
        COMPRESS_JSMINIFIER = 2;
        
    function __construct(array $js_list)
    {
        parent::__construct($js_list, \Setup::$COMPRESS_JS_PATH, '.js');
    }

    /**
     * Минимизирует файлы
     *
     * @param string $source данные для сжатия
     * @param string $output_file имя файла, в который следует записать минимизированные данные
     * @param integer $compress_type тип сжатия. 0 - не сжимать, 1 - объединять, 2 - сжимать
     */
    function compress($js_source, $output_file, $compress_type)
    {
        $output = false;
        if ($compress_type == self::COMPRESS_JSMINIFIER) {
            try {
                $output = JsShrinkMinifer::minify($js_source, ['flaggedComments' => false]);
            } catch (Exception $e) {}
        }
        
        if ($output === false) $output = $js_source;
        
        return file_put_contents($output_file, $output);
    }
}



