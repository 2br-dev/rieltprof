<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Controller\Admin;

use Main\Config\ModuleRights;
use Main\Model\LangApi;
use RS\AccessControl\Rights;
use RS\Controller\Admin\Front;
use RS\Controller\Admin\Helper\CrudCollection;
use RS\Controller\Result\Standard;
use RS\Html\Category\Element;
use RS\Html\Table\Element as TableElement;
use RS\Html\Table\Type as TableType;
use RS\Html\Filter;
use RS\Html\Toolbar\Button;
use RS\Html\Toolbar\Element as ToolbarElement;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;

/**
 * Контроллер отвечает за управление языковыми файлами
 */
class LangCtrl extends Front
{
    protected $api;
    protected $possible_langs;
    protected $lang = null;

    function __construct()
    {
        parent::__construct();
        $this->api = new LangApi();

        $this->possible_langs = array_keys($this->api->getPossibleLang());
        if ($this->possible_langs) {
            $this->lang = $this->url->convert($this->url->request('lang', TYPE_STRING, $this->possible_langs[0]), $this->possible_langs);
            $this->api->setLangFilter($this->lang);
        }
    }

    function actionIndex()
    {
        $helper = new CrudCollection($this, $this->api, $this->url, [
            'paginator'
        ]);
        $helper->viewAsTableCategory();
        $helper->setAppendModuleOptionsButton(false);
        $helper->setTopTitle(t('Управление переводами'));
        $helper->setTopHelp(t('В данном разделе вы можете управлять переводами фраз всех модулей и тем оформления, размещенных в специальных файлах messages.js.php, messages.lng.php. Подробнее об устройстве подсистемы интернационализации в ReadyScript можно узнать в <a href="%doc_href" target="_blank">документации</a>.', [
            'doc_href' => \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$RS_SERVER_DOMAIN.'/dev-manual/dev_lang.html'
        ]));

        $helper->setTopToolbar(new ToolbarElement([
            'items' => [
                new Button\Dropdown([
                        [
                            'title' => t('создать/обновить фразы'),
                            'attr' => [
                                'href' => $this->router->getAdminUrl('addLang', ['lang' => $this->lang]),
                                'class' => 'btn-success crud-add crud-sm-dialog'
                            ]
                        ],
                ]),
                new Button\Button($this->router->getAdminUrl('DownloadLangFileArchive'), t('скачать архив с переводами'), ['attr' => ['class' => 'crud-add crud-sm-dialog']]),
            ]
        ]));
        $helper->addCsvButton('main-lang');

        $helper->setCategory(new Element([
            //'noCheckbox' => true,
            'activeField' => 'id',
            'activeValue' => $this->lang,
            'sortable' => false,
            'mainColumn' => new TableType\Text('title', t('Язык'), ['href' => $this->router->getAdminPattern(false, [':lang' => '@id'])]),
            'headButtons' => [
                [
                    'text' => t('Языки'),
                    'tag' => 'span',
                    'attr' => [
                        'class' => 'lefttext'
                    ]
                ],
                [
                    'attr' => [
                        'title' => t('Добавить язык'),
                        'href' => $this->router->getAdminUrl('addLang'),
                        'class' => 'add crud-add crud-sm-dialog'
                    ]
                ],
            ],
        ]));

        $helper->setCategoryListFunction('getPossibleLangsData');
        $helper->setCategoryBottomToolbar(new ToolbarElement([
            'items' => [
                new Button\Delete(null, null, ['attr' =>
                    ['data-url' => $this->router->getAdminUrl('removeLang'),
                    'data-confirm-text' => t('Вы действительно желаете удалить все файлы переводов для выбранных языков (%count) во всех модулях и темах оформления?')]
                ]),
            ]
        ]));

        $helper->setFilter(new Filter\Control([
            'Container' => new Filter\Container([
                'Lines' => [
                    new Filter\Line(['Items' => [
                        new Filter\Type\Text('source', t('Исходная фраза')),
                        new Filter\Type\Text('translate', t('Перевод')),
                        new Filter\Type\Select('module', t('Модуль'), ['' => t('Все модули + текущая тема')] + $this->api->getTranslateModuleList()),
                        new Filter\Type\Select('type', t('Тип'), [
                            '' => t('Все'),
                            LangApi::LANG_FILE_TYPE_PHP => 'PHP фразы',
                            LangApi::LANG_FILE_TYPE_JS => 'JS фразы',
                        ]),
                        new Filter\Type\Select('show', t('Показывать'), [
                            '' => t('Все фразы'),
                            LangApi::FILTER_SHOW_WITH_TRANSLATE => t('Только с переводом'),
                            LangApi::FILTER_SHOW_NO_TRANSLATE => t('Не переведенные')
                        ]),
                    ]
                    ])
                ]]),
            //'ToAllItems' => ['FieldPrefix' => $this->api->defAlias()],
            'AddParam' => ['hiddenfields' => ['lang' => $this->lang]],
            'Caption' => t('Поиск по переводам')
        ]));

        $helper->setTable(new TableElement([
            'Columns' => [
                    new TableType\Checkbox('id'),
                    new TableType\Usertpl('source', t('Исходная фраза'), '%main%/admin/lang/phrase_form.tpl'),
                    new TableType\Usertpl('translate', t('Перевод'), '%main%/admin/lang/phrase_form.tpl'),
            ]
        ]));

        $helper->setBottomToolbar(new ToolbarElement([
            'items' => [
                new Button\Save($this->router->getAdminUrl('saveTranslatePage', ['lang' => $this->lang]), t('сохранить')),
                new Button\Delete($this->router->getAdminUrl('removeTranslate', ['lang' => $this->lang]), t('удалить'))
            ]
        ]));

        $helper->active();
        $table = $helper->getTableControl()->getTable();

        foreach($this->api->getTableGroupRows() as $any_row_data) {
            $table
                ->insertAnyRow([
                    new TableType\Text(null, null, ['Value' => $any_row_data['title'], 'TdAttr' => ['colspan' => 3]])
                ], $any_row_data['index'])
                ->setAnyRowAttr($any_row_data['index'], [
                    'class' => 'table-group-row no-hover'
                ]);
        }

        $this->app->addCss('%main%/lang.css');
        $this->app->addJs('%main%/langctrl.js');

        return $this->result->setTemplate($helper->getTemplate());
    }

    /**
     * Удаляет все файлы переводов для данного языка из системы
     */
    public function actionRemoveLang()
    {
        if ($access_error = Rights::CheckRightError($this, ModuleRights::RIGHT_TRANSLATE_DELETE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }

        $ids = $this->url->post('chk', TYPE_ARRAY);
        if ($this->api->removeLangs($ids)) {
            return $this->result->setSuccess(true);
        }

        return $this->result->addEMessage($this->api->getErrorsStr());
    }

    /**
     * Удаляет выбранные фразы из языковых файлов
     */
    public function actionRemoveTranslate()
    {
        if ($access_error = Rights::CheckRightError($this, ModuleRights::RIGHT_TRANSLATE_DELETE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }

        $ids = $this->url->post('chk', TYPE_ARRAY);
        if ($this->api->removePhrases($ids)) {
            return $this->result->setSuccess(true);
        }

        return $this->result->addEMessage($this->api->getErrorsStr());
    }

    /**
     * Сохраняет все фразы, представленные на странице
     *
     * @return Standard
     */
    public function actionSaveTranslatePage()
    {
        if ($access_error = Rights::CheckRightError($this, ModuleRights::RIGHT_TRANSLATE_UPDATE)) {
            return $this->result->setSuccess(false)->addEMessage($access_error);
        }

        $translates = $this->url->post('translate', TYPE_ARRAY, [], null);

        if ($this->api->saveTranslates($translates)) {
            return $this->result->addMessage(t('Изменения успешно сохранены'));
        }

        return $this->result->addEMessage($this->api->getErrorsStr());
    }

    /**
     * Создает и отправляет на скачивание zip архив с фразами для перевода
     */
    function actionDownloadLangFileArchive()
    {
        $form_object = new FormObject(new PropertyIterator([
            'lang' => new Type\Varchar([
                'description' => t('Язык'),
                'hint' => t('Выберите язык, файлы для которого поместить в архив.'),
                'listFromArray' => [array_combine($this->possible_langs, $this->possible_langs)]
            ])
        ]));

        if ($this->url->isPost()) {
            if ($form_object->checkData()) {
                if (in_array($form_object['lang'], $this->possible_langs)) {

                    $tmp_file = $this->api->makeLangArchive($form_object['lang']);
                    if ($tmp_file) {
                        return $this->result->setSuccess(true)->setAjaxWindowRedirect($tmp_file);
                    } else {
                        return $this->result->setSuccess(true)->addEMessage(t('Файл не создан. Не найдено ни одного файла для перевода для выбранного языка.'));
                    }

                } else {
                    $form_object->addError(t('Некорректно выбран идентификатор языка'), 'lang');
                    return $this->result
                        ->setSuccess(false)
                        ->setErrors($form_object->getDisplayErrors());
                }
            } else {
                return $this->result
                    ->setSuccess(false)
                    ->setErrors($form_object->getDisplayErrors());
            }
        }

        $helper = new \RS\Controller\Admin\Helper\CrudCollection($this);
        $helper->setTopTitle(t('Скачать архив с файлами для перевода'))
                ->setHeaderHtml($this->view->fetch('admin/lang/download_lang_help.tpl'))
                ->setBottomToolbar(new ToolbarElement([
                    'items' => [
                        new Button\SaveForm(null, t('Скачать'))
                    ]
                ]))
                ->setFormObject($form_object)
                ->viewAsForm();

        return $this->result->setTemplate( $helper['template'] );
    }

    /**
     * Диалог создания фалов локализации. Спрашивает идентификатор языка (ru, en, es, de ...)
     */
    function actionAddLang()
    {
        if ($access_error = Rights::CheckRightError($this, ModuleRights::RIGHT_TRANSLATE_GENERATION)) {
            return $this->result
                ->addSection('close_dialog', true)
                ->setSuccess(false)->addEMessage($access_error);
        }

        $form_object = new FormObject(new PropertyIterator([
            'lang' => new Type\Varchar([
                'description' => t('Язык'),
                'hint' => t('Двухсимвольный международный идентификатор языка'),
                'checker' => ['chkEmpty', t('Укажите двухсимвольный идентификатор языка')]
            ]),
            'module' => new Type\Varchar([
                'description' => t('Модуль'),
                'hint' => t('Будут сгенерированы файлы переводов только для выбранного модуля. Остальные останутся не тронуты'),
                'list' => [['\Main\Model\LangApi','getTranslateModuleList']]
            ]),
            'position' => new Type\Integer([
                'visible' => false,
            ])
        ]));

        $form_object['lang'] = $this->url->get('lang', TYPE_STRING);

        if ($this->url->isPost() && $form_object->checkData()) {

            // Создаем файлы локализации
            $status = $this->api->createLangFiles($form_object['lang'], $form_object['module'], $form_object['position']);
            if ($status === true) {
                return $this->result
                    ->setSuccess(true)
                    ->addMessage(t('Языковые файлы созданы'));
            } else {
                return $this->result
                    ->addSection('repeat', true)
                    ->addSection('queryParams', [
                            'url'=> $this->url->getSelfUrl(),
                            'data' => [
                                'lang' => $form_object['lang'],
                                'module' => $form_object['module'],
                                'position' => $status
                            ]]
                    );
            }
        }

        $helper = new CrudCollection($this);
        $helper->setTopTitle(t('Генерация файлов с переводами'))
                ->setHeaderHtml($this->view->fetch('admin/lang/create_lang_help.tpl'))
                ->setBottomToolbar(new ToolbarElement([
                    'items' => [
                        new Button\SaveForm(null, t('Создать'))
                    ]
                ]))
                ->setFormObject($form_object)
                ->viewAsForm();

        return $this->result->setTemplate( $helper['template'] );
    }

}