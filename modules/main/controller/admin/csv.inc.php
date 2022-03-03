<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Controller\Admin;

use RS\Controller\Admin\Front as AdminFront;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\ExceptionPageNotFound;
use RS\Controller\Result\Standard as ResultStandard;
use RS\Csv\AbstractSchema;
use RS\Html\Toolbar;
use Main\Model\Orm\CsvMap;
use RS\Html\Toolbar\Button as ToolbarButton;

class Csv extends AdminFront
{
    /**
     * Выполняет экспорт данных в CSV формате
     *
     * @return ResultStandard
     * @throws ExceptionPageNotFound
     * @throws \SmartyException
     */
    function actionExportCsv()
    {
        $referer = $this->url->request('referer', TYPE_STRING);
        $helper = new CrudCollection($this, null, null, [
            'topTitle' => t('Выберите колонки для экспорта'),
            'bottomToolbar' => new Toolbar\Element([
                'Items' => [
                    new ToolbarButton\SaveForm(null, t('Экспорт')),
                    new ToolbarButton\Cancel($referer),
                ],
            ]),
            'viewAs' => 'form',
        ]);

        $schema = $this->url->request('schema', TYPE_STRING);
        if ($csv_schema = AbstractSchema::getByShortName($schema)) {
            $csv_schema->setAction('export');
            $params = $this->url->request('params', TYPE_ARRAY); //Устанавливаем доп параметры если есть.
            $csv_schema->setParams($params);

            if ($this->url->isPost()) {
                $columns = $this->url->request('columns', TYPE_ARRAY);
                if (empty($columns)) {
                    return $this->result
                        ->setSuccess(false)
                        ->setErrors([
                            'destination' => [
                                'class' => 'field',
                                'fieldname' => t('Колонки на экспорт'),
                                'errors' => [t('Не задана ни одна колонка для экспорта')],
                            ],
                        ]);
                } else {
                    if (isset($_SESSION['export_data']['params'])) {
                        $params = $_SESSION['export_data']['params']; //Параметры установленные до
                    }
                    $_SESSION['export_data'] = [
                        'schema' => $schema,
                        'work_fields' => $columns,
                        'params' => $params,
                    ];
                    return $this->result->setSuccess(true)->setAjaxWindowRedirect($this->router->getAdminUrl('doexport'));
                }
            } else {
                unset($_SESSION['export_data']);
                //Установка дополнительных параметров схемы, при открытии окна импорта
                $_SESSION['export_data']['params'] = $csv_schema->getParams();
            }

            $this->view->assign([
                'schema' => $schema,
                'columns' => $csv_schema->getColumns(),
                'maps' => CsvMap::loadList($schema, CsvMap::TYPE_EXPORT),
            ]);
            $helper['form'] = $this->view->fetch('csv/export.tpl');
            return $this->result->setTemplate($helper['template']);
        } else {
            $this->e404(t('Схема обмена CSV не найдена'));
        }
    }

    /**
     * Возвращает CSV файл
     *
     * @return string
     */
    function actionDoExport()
    {
        $this->wrapOutput(false);
        if (isset($_SESSION['export_data'])) {
            $csv_schema = AbstractSchema::getByShortName($_SESSION['export_data']['schema']);
            $csv_schema->setWorkFields($_SESSION['export_data']['work_fields']);
            $csv_schema->setParams($_SESSION['export_data']['params']);
            $csv_schema->setAction('export');
            $csv_schema->export();
        } else {
            return t('Файл уже скачан. Сформируйте условия для CSV файла повторно');
        }
    }

    /**
     * Выполняет импорт данных в формате CSV
     *
     * @return ResultStandard
     * @throws ExceptionPageNotFound
     * @throws \SmartyException
     */
    function actionImportCsv()
    {
        $referer = $this->url->request('referer', TYPE_STRING);

        $helper = new CrudCollection($this, null, null, [
            'topTitle' => t('Импорт CSV файла'),
            'bottomToolbar' => new Toolbar\Element([
                    'Items' => [
                        'save' => new ToolbarButton\SaveForm(null, t('Загрузить файл')),
                        new ToolbarButton\Cancel($referer)
                    ]]
            ),
            'viewAs' => 'form'
        ]);

        $schema = $this->url->request('schema', TYPE_STRING);
        if ($csv_schema = AbstractSchema::getByShortName($schema)) {
            $csv_schema->setAction('import');
            $this->view->assign([
                'schema' => $schema,
                'maps' => CsvMap::loadList($schema, CsvMap::TYPE_IMPORT),
                'referer' => $referer
            ]);
            if ($this->url->isPost()) {
                if (isset($_SESSION['export_data']['params'])) {
                    $csv_schema->setParams($_SESSION['export_data']['params']);
                }
                if ($csv_schema->getUploader()->uploadFile($this->url->files('csvfile'))) {
                    //Формируем редирект на второй шаг импорта
                    $this->result->setSuccess(true);
                    return $this->actionProcessImport($schema, $csv_schema->getUploader()->getFilename(), $referer);
                } else {
                    $this->result->setErrors($csv_schema->getUploader()->getDisplayErrors());
                }
            } else {
                unset($_SESSION['export_data']);
                $params = $this->url->request('params', TYPE_ARRAY); //Устанавливаем доп параметры если есть.
                $csv_schema->setParams($params);
                $_SESSION['export_data']['params'] = $params; //Параметры установленные до
            }
            $helper['form'] = $this->view->fetch('csv/import.tpl');
            return $this->result->setTemplate($helper['template']);
        } else {
            $this->e404(t('Схема обмена CSV не найдена'));
        }
    }

    /**
    * Выполняет импорт CSV по шаблону
    */
    function actionProcessImport($schema_def = null, $filename_def = null, $referer_def = null)
    {
        $referer   = $this->url->request('referer', TYPE_STRING, $referer_def);
        $schema    = $this->url->request('schema', TYPE_STRING, $schema_def);
        $filename  = $this->url->request('filename', TYPE_STRING, $filename_def);
        $start_pos = $this->url->request('start_pos', TYPE_INTEGER, 0);
        $config    = \RS\Config\Loader::byModule('main');

        if ($csv_schema = \RS\Csv\AbstractSchema::getByShortName($schema)) {
            $csv_schema->setAction('import');
            if (isset($_SESSION['export_data']['params'])){  //Устанавливаем доп параметры
                $csv_schema->setParams($_SESSION['export_data']['params']);
            }

            $helper = new \RS\Controller\Admin\Helper\CrudCollection($this, null, null, [
                'topTitle' => t('Параметры импорта CSV файла'),
                'bottomToolbar' => new Toolbar\Element([
                    'Items' => [
                        'save' => new ToolbarButton\SaveForm(null, t('Начать импорт')),
                        new ToolbarButton\Cancel($referer)
                    ]]
                ),
                'viewAs' => 'form'
            ]);

            $csv_schema->getUploader()->setFilename($filename);
            $csv_file    = $csv_schema->getUploader()->getAbsolutePath();
            $csv_columns = $csv_schema->analizeColumns($csv_file);
            $columns     = $csv_columns['schema']; //Сопоставленные колонки

            if ($this->url->isPost() && $this->url->post('import_start', TYPE_INTEGER)) {
                //Проверяем шаблон импорта
                $columns = array_values($this->url->request('columns', TYPE_ARRAY));
                if ($csv_schema->validateImportWorkField($columns)) {
                    $csv_schema->setWorkFields($columns);
                    //Начинаем импортировать строки
                    $result = $csv_schema->import($csv_file, $config['csv_check_timeout'], $start_pos);

                    if ($result === true) {
                        //Импорт завершён
                        return $this->result
                                ->setSuccess(true)
                                ->addMessage(t('Импорт успешно завершен'));

                    }elseif (is_integer($result)){

                        //Импорт нужно продолжить
                        return $this->result
                                ->setSuccess(true)
                                ->addSection('repeat',true)
                                ->addSection('start_pos',$result)
                                ->addSection('queryParams', [
                                    'url' => $this->url->getSelfUrl(),
                                    'data'=> [
                                        'ajax' => 1,
                                        'referer' => $referer,
                                        'schema' => $schema,
                                        'start_pos' => $result,
                                        'import_start' => 1,
                                        'filename' => $filename,
                                        'columns' => $this->url->request('columns', TYPE_ARRAY, null),
                                    ]
                                ])
                                ->addSection('redirect',\RS\Router\Manager::obj()->getAdminUrl('processImport'));
                    }
                } else {
                    $this->result
                            ->setSuccess(false)
                            ->setErrors($csv_schema->getDisplayErrors());
                }
                //Импортируем данные
            }

            $this->view->assign([
                'schema' => $schema,
                'maps' => CsvMap::loadList($schema, CsvMap::TYPE_IMPORT),
                'csv_columns' => $csv_columns,
                'selected_columns' => $columns,
                'columns' => $csv_schema->getColumns(),
                'filename' => $filename
            ]);
            $helper['form'] = $this->view->fetch('csv/import_params.tpl');
            return $this->result->setTemplate( $helper['template'] );
        } else {
            $this->e404(t('Схема обмена CSV не найдена'));
        }
    }

    /**
     * Сохраняет предустановку
     */
    function actionSaveMap()
    {
        $title = $this->url->request('title', TYPE_STRING);
        $columns = $this->url->request('columns', TYPE_ARRAY);
        $schema = $this->url->request('schema', TYPE_STRING);
        $type = $this->url->convert($this->url->request('type', TYPE_STRING), ['export', 'import']);

        $map = new CsvMap();
        $map['schema'] = $schema;
        $map['title'] = $title;
        $map['type'] = $type;
        $map['columns'] = $columns;
        $map->insert();

        $this->view->assign([
            'map' => $map,
        ]);

        return $this->result->setSuccess(true)->setTemplate('csv/export_csv_option.tpl');
    }

    /**
     * Возвращает предустановку
     */
    function actionDeleteMap()
    {
        $id = $this->url->request('id', TYPE_INTEGER);
        $map = new CsvMap($id);
        if ($map['id']) {
            $map->delete();
        }
        return $this->result->setSuccess(true);
    }
}
