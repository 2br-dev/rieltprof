<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS;

/**
 * ReadyScript Exception
 */
class Exception extends \Exception
{
    protected $extra_info = '';
    protected $extra_data;

    function __construct($message = '', $code = 0, Exception $previous = null, $extra_info = '', array $extra_data = [])
    {
        $this->extra_info = $extra_info;
        $this->extra_data = $extra_data;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Возвращает дополнительную информацию об ошибке
     * @return string
     */
    public function getExtraInfo()
    {
        return $this->extra_info;
    }

    /**
     * Возвращает дополнительные данные ошибки
     *
     * @param string $key - ключ данных
     * @param null $default - значение по умолчанию
     * @return mixed|null
     */
    public function getExtraData(string $key, $default = null)
    {
        return $this->extra_data[$key] ?? $default;
    }
}
