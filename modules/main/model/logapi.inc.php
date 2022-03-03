<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Model;

use RS\Db\Exception as DbException;
use RS\Log\AbstractLog;
use RS\Log\LogManager;
use RS\Html\Filter\Control as FilterControl;
use RS\Module\AbstractModel\BaseModel;
use RS\Site\Manager as SiteManager;

class LogApi extends BaseModel
{
    protected $table_group_rows = [];
    protected $filter = [];

    /**
     * Возвращает общее количество фраз для перевода
     *
     * @return int
     * @throws DbException
     */
    public function getListCount()
    {
        return count($this->getFullList());
    }

    public function addTableControl()
    {}

    /**
     * Возвращает список лог-файлов постранично
     *
     * @param int $page Номер страницы
     * @param int $page_size Количество элементов на странице
     * @return array
     * @throws DbException
     */
    public function getList($page = 1, $page_size = 100)
    {
        $offset = ($page - 1) * $page_size;
        $result = array_slice($this->getFullList(), $offset, $page_size);

        $this->table_group_rows = [];
        $last = '';
        foreach ($result as $key => $row) {
            if ($last != $row['group_title']) {
                $this->table_group_rows[] = [
                    'index' => $key,
                    'title' => $row['group_title'],
                ];
                $last = $row['group_title'];
            }
        }

        return $result;
    }

    /**
     * Возвращает полный список лог-файлов
     *
     * @return array
     * @throws DbException
     */
    protected function getFullList()
    {
        static $list;
        if ($list == null) {
            $list = [];

            $site_list = [];
            foreach (SiteManager::getSiteList() as $site) {
                $site_list[$site['id']] = $site;
            }

            foreach (LogManager::getInstance()->getLogList() as $log) {
                /** @var AbstractLog $log */
                foreach ($log->getFileLinks() as $site_id => $link) {
                    if (!empty($this->filter['group_title']) && $log->getIdentifier() != $this->filter['group_title']) {
                        continue;
                    }
                    $list[] = [
                        'log_class' => $log->getIdentifier(),
                        'site_id' => $site_id,
                        'site' => $site_list[$site_id]['title'] ?? t('Общий лог для всех сайтов'),
                        'last_change' => filemtime($link),
                        'group_title' => $log->getTitle(),
                    ];
                }
            }
        }
        return $list;
    }

    /**
     * Возвращает данные вставки групп в таблицу
     */
    public function getTableGroupRows()
    {
        return $this->table_group_rows;
    }

    /**
     * Устанавливает фильтр для отбора лог-файлов
     *
     * @param FilterControl $filter_control
     */
    function addFilterControl(FilterControl $filter_control)
    {
        $this->filter = $filter_control->getKeyVal();
    }
}
