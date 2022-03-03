<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Controller\Admin;

use Designer\Model\PresetApi;
use RS\Controller\Admin\Front;
use Designer\Model\BlocksApi;

/**
 * Контроллер, позволяющий управлять блоками дизайнера
 */
class BlocksCtrl extends Front
{
    /**
     * @var BlocksApi $blocks_api
     */
    protected $blocks_api;

    public function init()
    {
        $this->blocks_api = new BlocksApi();
        $this->wrapOutput(false);
    }

    /**
     * Возвращает массив данных для записи
     *
     * @return array
     */
    private function getDataFromUrl()
    {
        return @json_decode(htmlspecialchars_decode($this->request('data', TYPE_STRING, "", false)), true);
    }

    /**
     * Возвращает идентификатор блока из переданных данных
     *
     * @return string
     * @throws \RS\Exception
     */
    private function getBlockId()
    {
        $block_id = $this->request('block_id', TYPE_STRING, null);
        if (!$block_id && $block_id != 'settings') {
            throw new \RS\Exception(t('Не указан идентификатор блока'));
        }
        return $block_id;
    }

    /**
     * Сохраняет настройки блока дизайнера
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function actionSaveData()
    {
        $from_preset = $this->request('from_preset', TYPE_INTEGER, 0);
        $block_id = $this->getBlockId();
        $data     = $this->getDataFromUrl();

        if ($from_preset){ //Если сохраняем из пресета, то
            $new_data = $this->blocks_api->saveBlocksDataFromPreset($block_id, $data);
            $this->result->addSection('data', $new_data);
        }else{
            $this->blocks_api->saveBlocksData($block_id, $data);
        }
        $this->blocks_api->clearResourceCacheFolder();

        if ($this->blocks_api->hasError()) {
            return $this->result->setSuccess(false)->addEMessage($this->blocks_api->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Удаляет настройки блока дизайнера
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function actionDeleteData()
    {
        $block_id = $this->getBlockId();
        $this->blocks_api->deleteBlocksData($block_id);
        $this->blocks_api->clearResourceCacheFolder();

        if ($this->blocks_api->hasError()) {
            return $this->result->setSuccess(false)->addEMessage($this->blocks_api->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Сохраняет данные для атома
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function actionSaveAtomData()
    {
        $block_id = $this->getBlockId();
        $data     = $this->getDataFromUrl();

        if ($block_id != 'settings'){ //Если это не общие настройки
            $this->blocks_api->saveAtomData($block_id, $data);
        }else{
            $this->blocks_api->saveAtomDataForSettings($data);
        }
        $this->blocks_api->clearResourceCacheFolder();

        if ($this->blocks_api->hasError()) {
            return $this->result->setSuccess(false)->addEMessage($this->blocks_api->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Возвращает значения полей ORM объекта
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function actionGetOrmFieldsData()
    {
        $type = $this->request('type', TYPE_STRING, null);
        $attr_name = $this->request('attr_name', TYPE_STRING, null);
        $id   = $this->request('id', TYPE_INTEGER, null);

        if (empty($id)){
            return $this->result->setSuccess(false)->addEMessage(t('Не передан идентификатор ORM объекта'));
        }
        if (empty($attr_name)){
            return $this->result->setSuccess(false)->addEMessage(t('Не передан аттрибут атома для данных'));
        }
        if (empty($type)){
            return $this->result->setSuccess(false)->addEMessage(t('Не передан тип атома'));
        }

        $data = $this->blocks_api->loadOrmFieldsInfo($type, $attr_name, $id);

        if ($this->blocks_api->hasError()) {
            return $this->result->setSuccess(false)->addEMessage($this->blocks_api->getErrorsStr());
        }
        return $this->result->setSuccess(true)->addSection('data', $data);
    }

    /**
     * Сохраняет данные для строки
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function actionSaveRowData()
    {
        $block_id = $this->getBlockId();
        $data     = $this->getDataFromUrl();

        $this->blocks_api->saveRowData($block_id, $data);
        $this->blocks_api->clearResourceCacheFolder();

        if ($this->blocks_api->hasError()) {
            return $this->result->setSuccess(false)->addEMessage($this->blocks_api->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Возвращает данные по пресету
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Exception
     */
    function actionGetPreset()
    {
        $alias = $this->url->request('preset_id', TYPE_STRING, false);

        if (empty($alias)){
            return $this->result->setSuccess(false)->addEMessage(t('Не передан идентификатор пресета'));
        }

        $api  = new PresetApi();
        $data = $api->getPreset($alias);

        if ($api->hasError()){
            //Уникализируем ошибочки
            $errors = $api->getErrors();
            $errors = array_unique($errors);
            $this->blocks_api->clearResourceCacheFolder();
            return $this->result->setSuccess(false)->addEMessage(implode(", ", $errors));
        }
        if (empty($data)) {
            $this->blocks_api->clearResourceCacheFolder();
            return $this->result->setSuccess(false)->addEMessage(t('Не удалось получить данные по пресету'));
        }
        $this->blocks_api->clearResourceCacheFolder();
        return $this->result->setSuccess(true)->addSection('data', $data);
    }


    /**
     * Сохраняет данные для колонки
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function actionSaveColumnData()
    {
        $block_id = $this->getBlockId();
        $data     = $this->getDataFromUrl();

        $this->blocks_api->saveColumnData($block_id, $data);
        $this->blocks_api->clearResourceCacheFolder();

        if ($this->blocks_api->hasError()) {
            return $this->result->setSuccess(false)->addEMessage($this->blocks_api->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Добавление нового атома в колонку
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function actionAddNewAtomToColumn()
    {
        $block_id = $this->getBlockId();
        $col_id   = $this->request('col_id', TYPE_STRING, null);
        $index    = $this->request('index', TYPE_INTEGER, null);
        $data     = $this->getDataFromUrl();

        if (!$col_id) {
            return $this->result->setSuccess(false)->addEMessage(t('Не указан идентификатор колонки'));
        }
        if ($index === null) {
            return $this->result->setSuccess(false)->addEMessage(t('Не указана позиция вставки'));
        }

        $this->blocks_api->addAtomToColumn($block_id, $col_id, $index, $data);
        $this->blocks_api->clearResourceCacheFolder();

        if ($this->blocks_api->hasError()) {
            return $this->result->setSuccess(false)->addEMessage($this->blocks_api->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Перемещение атома в колонке атома в колонку
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function actionMoveAtomInColumn()
    {
        $block_id  = $this->getBlockId();
        $col_id    = $this->request('col_id', TYPE_STRING, null);
        $new_index = $this->request('new_index', TYPE_INTEGER, null);
        $old_index = $this->request('old_index', TYPE_INTEGER, null);

        if (!$col_id) {
            return $this->result->setSuccess(false)->addEMessage(t('Не указан идентификатор колонки'));
        }
        if ($new_index === null) {
            return $this->result->setSuccess(false)->addEMessage(t('Не указана старая позиция'));
        }
        if ($old_index === null) {
            return $this->result->setSuccess(false)->addEMessage(t('Не указана новая позиция'));
        }

        $this->blocks_api->moveAtomInColumn($block_id, $col_id, $new_index, $old_index);
        $this->blocks_api->clearResourceCacheFolder();

        if ($this->blocks_api->hasError()) {
            return $this->result->setSuccess(false)->addEMessage($this->blocks_api->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Удаляет атом из блока
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function actionDeleteAtom()
    {
        $block_id = $this->getBlockId();
        $id       = $this->request('id', TYPE_STRING, null);

        if (!$id) {
            return $this->result->setSuccess(false)->addEMessage(t('Не указан идентификатор атома'));
        }

        $this->blocks_api->deleteAtom($block_id, $id);
        $this->blocks_api->clearResourceCacheFolder();

        if ($this->blocks_api->hasError()) {
            return $this->result->setSuccess(false)->addEMessage($this->blocks_api->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }

    /**
     * Удаляет колонку из блока
     *
     * @return \RS\Controller\Result\Standard
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     */
    function actionDeleteColumn()
    {
        $block_id = $this->getBlockId();
        $id       = $this->request('id', TYPE_STRING, null);

        if ($id === null) {
            return $this->result->setSuccess(false)->addEMessage(t('Не указан номер колонки'));
        }

        $this->blocks_api->deleteColumn($block_id, $id);
        $this->blocks_api->clearResourceCacheFolder();

        if ($this->blocks_api->hasError()) {
            return $this->result->setSuccess(false)->addEMessage($this->blocks_api->getErrorsStr());
        }
        return $this->result->setSuccess(true);
    }
}