<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Support\Model;
use Main\Model\NoticeSystem\HasMeterInterface;

class TopicApi extends \RS\Module\AbstractModel\EntityList
                    implements HasMeterInterface
{
    function __construct()
    {
        parent::__construct(new \Support\Model\Orm\Topic, [
            'multisite' => true
        ]);
    }

    /**
     * Возвращает API по работе со счетчиками
     *
     * @return \Main\Model\NoticeSystem\MeterApiInterface
     */
    function getMeterApi()
    {
        return new TopicMeterApi($this);
    }
}
?>
