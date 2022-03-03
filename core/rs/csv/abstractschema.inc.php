<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Csv;

use RS\Application\Application;
use RS\Config\Loader as ConfigLoader;
use RS\Csv\Preset\AbstractPreset;
use RS\Csv\Preset\Base as BasePreset;
use RS\Event\Manager as EventManager;
use RS\File\Uploader as FileUploader;
use RS\Helper\Tools as HelperTools;
use RS\Http\Request;
use RS\AccessControl\Rights;
use RS\AccessControl\DefaultModuleRights;
use RS\Module\AbstractModel\BaseModel;
use RS\Site\Manager as SiteManager;
use Site\Model\Orm\Site;

/**
 * Абстрактный класс схемы экспорта/импорта в формате CSV.
 * Схема описывает наборы колонок(preset), которые могут присутствовать в CSV файле.
 * Схема обрабатывает операции мпорта и экспорта данных в формате CSV.
 */
abstract class AbstractSchema extends BaseModel
{
    //Константы видимости полей при импорте и экспорте
    const FIELDSCOPE_IMPORT = 'import';
    const FIELDSCOPE_EXPORT = 'export';

    public $ids = [];
    public $rows = [];

    protected $csv_import_skip_first = true;
    protected $csv_delimiter = ';';
    protected $csv_enclosure = '"';
    protected $csv_charset = 'windows-1251';
    protected $fieldscope_fields = null; //Поля ограниченные областью видимости
    protected $limit = 100;
    protected $before_line_import;
    protected $after_line_import;
    protected $after_import;
    protected $query;
    protected $action; //Текущее действие
    protected $uploader;
    protected $work_fields;
    protected $base_id_field = 'id';
    /** @var BasePreset */
    protected $base_preset;
    protected $params = [];
    protected $presets;
    protected $import_upload_right = DefaultModuleRights::RIGHT_CREATE;

    public function __construct($base_preset, array $other_presets = [], $options = [])
    {
        $this->base_preset = $base_preset;
        $this->addPreset($base_preset);
        foreach ($other_presets as $preset) {
            $this->addPreset($preset);
        }

        $config = ConfigLoader::byModule('main');
        $this->csv_charset = $config['csv_charset'];
        $this->csv_delimiter = $config['csv_delimiter'];

        foreach ($options as $option => $value) {
            $method_name = 'set' . $option;
            if (method_exists($this, $method_name)) {
                $this->$method_name($value);
            }
        }

        EventManager::fire('csv.scheme.afterconstruct.' . $this->getShortName(), ['scheme' => $this]);
    }

    /**
     * Устанавливает доп. параметры схемы
     *
     * @param array $params - параметры
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * Возвращает доп. параметры схемы по ключу
     *
     * @param string $key - ключ в массиве параметров
     * @return array
     */
    public function getParamByKey($key)
    {
        return $this->params[$key];
    }

    /**
     * Возвращает все доп. параметры схемы
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Возвращает текущее действие (Импорт или Экспорт)
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Устанавливает текущее действие
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Устанавливает кодировку, в которой будет происходить импорт/экспорт
     *
     * @param mixed $charset
     * @return AbstractSchema
     */
    public function setCharset($charset)
    {
        $this->csv_charset = $charset;
        return $this;
    }

    /**
     * Устанавливает область видимости для указанных полей
     *
     * @param array $fields - массив полей
     */
    public function setFieldScope($fields)
    {
        $this->fieldscope_fields = $fields;
    }

    /**
     * Возвращает экземпляр класса схемы по короткому имени схемы
     *
     * @param string $short_name
     * @return AbstractSchema|bool
     */
    public static function getByShortName($short_name)
    {
        if (preg_match('/^([^\-]+?)\-(.*)$/', $short_name, $match)) {
            $class_name = str_replace('-', '\\', "-{$match[1]}-model-csvschema-{$match[2]}");
            if (class_exists($class_name)) {
                return new $class_name();
            }
        }
        return false;
    }

    /**
     * Возвращает сокращенное имя схемы
     *
     * @return string
     */
    public function getShortName()
    {
        return str_replace(['\\', '-model-csvschema'], ['-', ''], strtolower(get_class($this)));
    }

    /**
     * Добавляет набор колонок к экспортному файлу
     *
     * @param Preset\AbstractPreset $preset
     * @return AbstractSchema
     */
    public function addPreset(Preset\AbstractPreset $preset)
    {
        $id = (is_null($this->presets))? 0 : count($this->presets);
        $this->presets[$id] = $preset->setId($id)->setSchema($this);
        return $this;
    }

    /**
     * Устанавливает какое поле в первичной выборке является уникальным идентификатором
     *
     * @param string $field
     * @return AbstractSchema
     */
    public function setBaseIdField($field)
    {
        $this->base_id_field = $field;
        return $this;
    }

    /**
     * Возвращает объект пресета по id
     *
     * @param mixed $id
     * @return Preset\AbstractPreset|bool
     */
    public function getPreset($id)
    {
        return isset($this->presets[$id]) ? $this->presets[$id] : false;
    }

    /**
     * Устанавливает количество элементов, которое должно быть загружено за один запрос
     *
     * @param integer $limit
     * @return AbstractSchema
     */
    public function setPageSize($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Устанавливает запрос для базовой выборки
     *
     * @param \RS\Orm\Request $q
     * @return AbstractSchema
     */
    public function setBaseQuery(\RS\Orm\Request $q)
    {
        $this->query = $q;
        return $this;
    }

    /**
     * Возвращает запрос для базовой выборки
     *
     * @return \RS\Orm\Request
     */
    public function getBaseQuery()
    {
        if (!$this->query) {
            $this->query = $this->base_preset->getSelectRequest();
        }
        return $this->query;
    }

    /**
     * Возвращает полный список колонок, которые могут быть представлены в CSV файле
     *
     * @return array
     */
    public function getColumns()
    {
        $result = [];
        foreach ($this->presets as $preset) {
            /** @var AbstractPreset $preset */
            $result += $preset->getColumns();
        }

        //Если у нас есть поля видимость которых надо ограничить
        if ($this->fieldscope_fields) {
            $action = $this->getAction(); //Текущая операция
            $fieldscope_keys = array_keys($this->fieldscope_fields); //Получим ключи
            foreach ($result as $key => $columnset) {
                $col_key = $columnset['key'];
                //Если мы нашли в массиве колонку которую надо исключить из текущей операции
                if (in_array($col_key, $fieldscope_keys) && ($this->fieldscope_fields[$col_key] != $action)) {
                    unset($result[$key]);
                }
            }
        }

        return $result;
    }

    /**
     * Устанавливает какие поля и в какой последовательности должны присутствовать в выгрузке,
     * а также какие поля в какой последовательности присутствуют в загрузке
     *
     * @param string[] $fields - список полей
     * @return void
     */
    public function setWorkFields(array $fields)
    {
        $this->work_fields = $fields;
    }

    /**
     * Устанавливает импортировать ли первую строку
     *
     * @param mixed $bool
     */
    public function setImportSkipFirst($bool)
    {
        $this->csv_import_skip_first = $bool;
    }

    /**
     * Устанавливает произвольный обработчик, который выполняется перед импортом
     * строки данных
     *
     * @param mixed $callback
     */
    public function setBeforeLineImport($callback)
    {
        $this->before_line_import = $callback;
    }

    /**
     * Устанавливает произвольный обработчик, который выполняется после импорта
     * строки данных
     *
     * @param mixed $callback
     */
    public function setAfterLineImport($callback)
    {
        $this->after_line_import = $callback;
    }

    /**
     * Устанавливает произвольный обработчик, который выполняется после завершения
     * шага импорта
     *
     * @param mixed $callback
     */
    public function setAfterImport($callback)
    {
        $this->after_import = $callback;
    }

    /**
     * Возвращает поля, участвующие в выгрузке CSV
     *
     * @return array
     */
    public function getWorkFields()
    {
        if (!$this->work_fields) {
            $this->work_fields = array_keys($this->getColumns());
        }
        return $this->work_fields;
    }

    /**
     * Формирует CSV файл
     *
     * @param string $destination - путь к файлу
     * @return int
     */
    public function exportToFile($destination)
    {
        $offset = 0;
        $this->rows = [];


        $fp = fopen($destination, 'w');

        $work_fields = $this->getWorkFields();
        //Возвращаем колонки
        $row_columns = $this->getColumns();
        $columns = [];
        foreach ($work_fields as $id) {
            if (isset($row_columns[$id])) {
                $columns[$id] = iconv('utf-8', $this->csv_charset, $row_columns[$id]['title']);
            }
        }
        fputcsv($fp, $columns, $this->csv_delimiter, $this->csv_enclosure);

        $class_short_name = $this->getShortName();

        while ($offset == 0 || count($this->rows)) {
            $this->rows = $this->loadRows($offset, $this->limit);
            $offset += $this->limit;

            if ($this->base_id_field) {
                $this->ids = [];
                foreach ($this->rows as $row) {
                    $this->ids[] = $row[$this->base_id_field];
                }
            }

            //Все пресеты загружают данные
            foreach ($this->presets as $preset) {
                /** @var AbstractPreset $preset */
                $preset->loadData();
            }

            foreach ($this->rows as $n => $row) {
                $row_data = [];
                foreach ($this->presets as $preset) {
                    //Запрашиваем у каждого набора колонок массив данных
                    $preset->beforeRowExport($n);
                    EventManager::fire('csv.beforelineexport.' . $class_short_name, [
                        'schema' => $this
                    ]);
                    $row_data += $preset->getColumnsData($n);
                }

                //Фильрация результата. Исключение ненужных колонок. Сортировка.
                $columns_data = [];
                foreach ($work_fields as $id) {
                    if (array_key_exists($id, $row_data)) {
                        $columns_data[$id] = iconv('utf-8', $this->csv_charset . "//IGNORE", $row_data[$id]);
                    } else {
                        $columns_data[$id] = '';
                    }
                }

                //Вывод
                fputcsv($fp, $columns_data, $this->csv_delimiter, $this->csv_enclosure);
            }
        }
        $result = ftell($fp);
        fclose($fp);
        return $result;
    }

    /**
     * Отправляет в output сформированный файл CSV
     *
     * return void
     */
    public function export()
    {
        $domain = Request::commonInstance()->getDomainStr();
        $filename = $domain . '-' . $this->getShortName() . '.csv';
        $mime = 'text/csv';
        $app = Application::getInstance();

        $app->cleanOutput();
        $app->headers->addHeaders([
            'Content-Type' => $mime,
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Connection' => 'close'
        ]);
        $app->headers->sendHeaders();
        $this->exportToFile('php://output');
    }

    /**
     * Импортирует данные из CSV файла по текущей схеме
     *
     * @param string $file - путь к файлу
     * @param boolean $check_timeout - использовать пошаговое прохождение CSV файла?
     * @param integer $start_pos - позиция файла с которой начинать, чтение
     * @param int $site_id - id сайта
     * @return bool Возвращает true, в случае успеха, иначе - false
     */
    public function import($file, $check_timeout = false, $start_pos = 0, $site_id = null)
    {
        if ($site_id) {
            //Устанавливаем в качестве текущего, указанный сайт
            $before_site = SiteManager::getSite();
            SiteManager::setCurrentSite(new Site($site_id));
        }

        if (!$this->work_fields) {
            //Если испорт будет происходить без ручного указания колонок, то определяем колонки автоматически
            $auto_columns = $this->analizeColumns($file);
            $this->work_fields = $auto_columns['schema'];
        }

        $work_fields = $this->getWorkFields();

        if ($this->csv_charset == 'windows-1251') {
            setlocale(LC_CTYPE, 'ru_RU.cp1251');
        }

        @ini_set('auto_detect_line_endings', true);
        $fp = fopen($file, 'r');
        $i = 0;

        fseek($fp, $start_pos); //Начинаем читать с нужного места

        $start_time = time(); //Время начала
        $max_exec_time = ConfigLoader::byModule('main')->csv_timeout;

        $class_short_name = $this->getShortName();
        if ($start_pos == 0) {
            EventManager::fire('csv.beforeimport.' . $class_short_name, [
                'schema' => $this
            ]);
        }

        while ($fp
            && (!$check_timeout || ((time() - $start_time) < ($max_exec_time - 2)))
            && ($row = fgetcsv($fp, null, $this->csv_delimiter, $this->csv_enclosure)) !== false) {

            if ($i++ == 0 && $this->csv_import_skip_first && $start_pos == 0) {
                continue;
            }

            $row = $this->convertRowCharset($row);

            $row_arr = implode($row);
            if (!mb_strlen($row_arr)) continue; //Проскакиваем совершенно пустые строки

            foreach ($this->presets as $preset) {
                $preset->row = []; //Очищаем перед импортом строки внутренние данные всех пресетов
            }
            //Заполняем массивы данных preset'ов.
            foreach ($work_fields as $n => $key) {
                foreach ($this->presets as $preset) {
                    /** @var BasePreset $preset */
                    foreach ($preset->getColumns() as $column_id => $column) {
                        if ($column_id == $key && isset($row[$n])) {
                            //Применяем переназначение свойств
                            $real_key = $preset->hasMap($column['key']) ? $preset->getMappedField($column['key']) : $column['key'];

                            $preset->row[$real_key] = trim($row[$n]);
                        }
                    }
                }
            }

            if ($this->before_line_import) {
                call_user_func($this->before_line_import, $this);
            }
            $event_result = EventManager::fire('csv.beforelineimport.' . $class_short_name, [
                'schema' => $this
            ]);
            if ($event_result->getEvent()->isStopped()) {
                continue;
            }

            //Импортируем в обратном порядке.(справа налево)
            foreach (array_reverse($this->presets) as $preset) {
                if ($preset->row) {
                    if ($preset->beforeRowImport() !== false) {
                        $preset->importColumnsData();
                        $preset->afterRowImport();
                    }
                }
            }

            if ($this->after_line_import) {
                call_user_func($this->after_line_import, $this);
            }
            EventManager::fire('csv.afterlineimport.' . $class_short_name, [
                'schema' => $this
            ]);

        }

        if ($this->after_import) {
            call_user_func($this->after_import, $this);
        }
        EventManager::fire('csv.afterimport.' . $class_short_name, [
            'schema' => $this
        ]);

        if ($site_id) {
            //Восстанавливаем текущий сайт
            SiteManager::setCurrentSite($before_site);
        }

        //Для пошаговой загрузки, вернём то место на котором прервались
        if ($check_timeout && ((time() - $start_time) >= ($max_exec_time - 2))) {
            $last_pos = ftell($fp);
            fclose($fp);
            return $last_pos;
        }

        //Если импорт завершен
        fclose($fp);
        return true;
    }

    /**
     * Возвращает объект загрузчика файла.
     * @return \RS\File\Uploader
     */
    public function getUploader()
    {
        if (!isset($this->uploader)) {
            $this->uploader = new FileUploader('csv');
            $this->uploader->setField(t('CSV файл'), 'csvfile');
            $self = $this;
            $this->uploader->setRightChecker(function ($uploader, $post_file_arr) use ($self) {
                //Проверяем права на запись модуля, к которому принадлежит схема.
                return Rights::CheckRightError($self, $this->import_upload_right);
            });
        }
        return $this->uploader;
    }

    /**
     * Конвертирует строку с данными в требуемую кодировку
     *
     * @param array $row
     * @return array
     */
    public function convertRowCharset($row)
    {
        if ($this->csv_charset != 'utf-8') {
            array_walk($row, function (&$value, $key, $in_charset) {
                $value = iconv($in_charset, 'utf-8', $value);
            }, $this->csv_charset);
        }
        return $row;
    }

    /**
     * Возвращает true, если шаблон импорта корректен, иначе текст ошибки
     *
     * @param string[] $work_fields - список полей
     * @return bool
     */
    public function validateImportWorkField($work_fields)
    {
        foreach ($work_fields as $k => $fld) {
            if ($fld == '') unset($work_fields[$k]);
        }
        if (count(array_flip($work_fields)) != count($work_fields)) {
            return $this->addError(t('Одна и та же колонка назначена дважды'));
        }
        return true;
    }

    /**
     * Анализирует CSV файл и возвращает имеющиеся колонки, а также возможное соответствие колонкам схемы
     *
     * @param mixed $file
     * @return array
     */
    public function analizeColumns($file)
    {
        if ($this->csv_charset == 'windows-1251') {
            setlocale(LC_CTYPE, 'ru_RU.cp1251');
        }

        $fp = fopen($file, 'r');
        @ini_set('auto_detect_line_endings', true);
        $row = fgetcsv($fp, null, $this->csv_delimiter, $this->csv_enclosure);
        fclose($fp);
        $row = $this->convertRowCharset($row);
        $schema_columns = $this->getColumns();

        $result = [];
        $used_id = [];
        foreach ($row as $n => $val) {
            $result['csv'][$n] = HelperTools::teaser($val, 200);
            //Пытаемся сопоставить подходящую колонку из схемы
            foreach ($schema_columns as $id => $column) {
                if (trim($val) == $column['title'] && !isset($used_id[$id])) {
                    $result['schema'][$n] = $id;
                    $used_id[$id] = 1;
                    break;
                } else {
                    $result['schema'][$n] = '';
                }
            }
        }
        return $result;
    }

    /**
     * Загружает и возвращает $limit строк с объектами выборки
     *
     * @param integer $offset Смещение, относительно начала
     * @param integer $limit Количество элементов
     * @return array
     */
    public function loadRows($offset, $limit)
    {
        $q = $this->getBaseQuery();
        $q->limit($offset, $this->limit);
        return $q->objects();
    }
}
