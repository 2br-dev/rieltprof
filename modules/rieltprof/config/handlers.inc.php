<?php

namespace rieltprof\Config;

use Alerts\Model\Manager;
use Catalog\Controller\Block\SearchLine as CatalogSearchLine;
use Rieltprof\Model\Notice\UserRemovedFromPublic;
use rieltprof\Model\Orm\District;
use RS\Config\Loader as ConfigLoader;
use RS\Event\HandlerAbstract;
use RS\Event\Manager as EventManager;
use RS\Orm\Type;
use Search\Model\SearchApi;

/**
 * Класс содержит обработчики событий, на которые подписан модуль
 */
class Handlers extends HandlerAbstract
{
    /**
     * Добавляет подписку на события
     *
     * @return void
     */
    function init()
    {
        $this
            ->bind('start')
            ->bind('getroute', null, null, 0)
            ->bind('initialize')
            ->bind('getmenus')
            ->bind('cron')

            ->bind('orm.init.users-user')
            ->bind('orm.afterwrite.users-user')
            ->bind('orm.delete.users-user')
            // Квартира
            ->bind('orm.init.rieltprof-flat')
            ->bind('controller.exec.rieltprof-admin-flatctrl.add')
            ->bind('controller.exec.rieltprof-admin-flatctrl.edit')
            // Дом
            ->bind('orm.init.rieltprof-house')
            ->bind('controller.exec.rieltprof-admin-housectrl.add')
            ->bind('controller.exec.rieltprof-admin-housectrl.edit')
            // Комната
            ->bind('orm.init.rieltprof-room')
            ->bind('controller.exec.rieltprof-admin-roomctrl.add')
            ->bind('controller.exec.rieltprof-admin-roomctrl.edit')
            // Таунхаус
            ->bind('orm.init.rieltprof-townhouse')
            ->bind('controller.exec.rieltprof-admin-townhousectrl.add')
            ->bind('controller.exec.rieltprof-admin-townhousectrl.edit')
            // Дуплекс
            ->bind('orm.init.rieltprof-duplex')
            ->bind('controller.exec.rieltprof-admin-duplexctrl.add')
            ->bind('controller.exec.rieltprof-admin-duplexctrl.edit')
            // Дача
            ->bind('orm.init.rieltprof-countryhouse')
            ->bind('controller.exec.rieltprof-admin-countryhousectrl.add')
            ->bind('controller.exec.rieltprof-admin-countryhousectrl.edit')
            // Участок
            ->bind('orm.init.rieltprof-plot')
            ->bind('controller.exec.rieltprof-admin-plotctrl.add')
            ->bind('controller.exec.rieltprof-admin-plotctrl.edit')
            // Гараж
            ->bind('orm.init.rieltprof-garage')
            ->bind('controller.exec.rieltprof-admin-garagectrl.add')
            ->bind('controller.exec.rieltprof-admin-garagectrl.edit')
            // Коммерция
            ->bind('orm.init.rieltprof-commercial')
            ->bind('controller.exec.rieltprof-admin-commercialctrl.add')
            ->bind('controller.exec.rieltprof-admin-commercialctrl.edit')
            // Новостройка
            ->bind('orm.init.rieltprof-newbuilding')
            ->bind('controller.exec.rieltprof-admin-newbuildingctrl.add')
            ->bind('controller.exec.rieltprof-admin-newbuildingctrl.edit')

//            ->bind('orm.beforewrite.catalog-product', null, null, 0)
            ->bind('orm.afterwrite.catalog-product', null, null, 0)
            ->bind('orm.afterload.catalog-product')

            ->bind('controller.exec.rieltprof-admin-garagectrl.add')

            ->bind('controller.exec.users-admin-ctrl.index')

            ->bind('orm.init.catalog-dir')
            ;
    }

    public static function cron($params)
    {
        $config = \RS\Config\Loader::byModule('rieltprof');
        $public_object = \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Product())
            ->where([
                'public' => 1
            ])->exec()->fetchAll();
            // Если будет несколько запусков тогда нужно будет проверять на какой минуте запущен cron
//        foreach ($params['minutes'] as $minute) {
//            if($minute % 1438 == 0){
                //каждый день в 23:59 проверяем активные обявления на актуальность. Актуальность - не более 30 дней
                foreach ($public_object as $key => $value){
                    // Проверяем объявление на атуальность
                    $is_actual = $config->isActualAd($value, 90);
                    // Если не актуальное то реквизит public = 0
                    if(!$is_actual){
                        $object = $config->getObjectByType($value['controller'], $value['id']);
                        $table = $object::_getTableArray();
                        \RS\Orm\Request::make()
                            ->update($table[1])
                            ->set([
                                'public' => 0
                            ])
                            ->where([
                                'id' => $value['id']
                            ])->exec();
                        //Отправить письмо владелцу объявления если обновление прошло успешно
                        //TODO - подумать над тем чтоб не отправлять сообщения прямо в 23:59
                        $user = new \Users\Model\Orm\User($object['owner']);
                        $notice = new UserRemovedFromPublic();
                        $notice->init($user, $object);
                        Manager::send($notice);
                    }
                }
//            }
//        }
    }

    /**
     * Вместе с пользователем удалим все его объявления
     * @param $params
     */
    public static function ormDeleteUsersUser($params): void
    {
        $orm = $params['orm'];
        $allAds = \RS\Orm\Request::make()
            ->from(new \Catalog\Model\Orm\Product())
            ->where([
                'owner' => $orm['id']
            ])->objects();
        if($allAds){
            foreach ($allAds as $ad){
                $ad->delete();
            }
        }
    }

    public static function ormInitCatalogDir(\Catalog\Model\Orm\Dir $orm)
    {
        $orm->getPropertyIterator()->append([
            t('Основные'),
                'display_name' => new Type\Varchar([
                    'description' => t('Как отображать на сайте')
                ]),
            t('Дополнительно'),
                'category_other_action' => new Type\Integer([
                    'description' => t('Категория с другим действием'),
                    'hint' => t('Если категория имеет родителя Продажа, то выбрать категорию с таким же названием и родителем Аренда'),
                    'maxLength' => 11,
                    'list' =>array(['\Catalog\Model\DirApi', 'selectList'], array(0 => t('не выбрано')))
                ])
        ]);
    }
    public static function ormInitRieltprofDuplex(\Rieltprof\Model\Orm\Duplex $orm)
    {
        $orm->getPropertyIterator()->append(array(
            t('Основные'),
            'controller' => new Type\Varchar([
                'description' => t('Контроллер'),
                'visible' => false
            ]),
            'actual_on_date' => new Type\Date([
                'description' => t('актуально на дату')
                // При добавлении = дата создания
            ]),
            'cost_product' => new Type\Integer([
                'description' => t('Цена'),
                'maxLength' => 15,
                'Checker' => ['chkEmpty', t('Укажите цену')],
                'rentVisible' => false
            ]),
            'cost_rent' => new Type\Integer([
                'description' => t('Цена за мес.'),
                'Checker' => ['chkEmpty', t('Укажине Цену в мес.')],
                'saleVisible' => false,
            ]),
            'cost_one' => new Type\Integer([
                'description' => t('Цена за 1 кв. метр'),
                'visible' => false
            ]),
            'note' => new Type\Text(array(
                'description' => t('Примечание'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'personal_note' => new Type\Text(array(
                'description' => t('Личные пометки (будут видны только Вам)'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'owner' => new Type\Integer([
                'description' => t('Владелец объявления'),
                'visible' => false
            ]),
            'object' => new Type\Varchar([
                'description' => t('Объект'),
                'visible' => false
            ]),
            t('Локация'),
            'country' => new Type\Varchar(array(
                'description' => t('Страна'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Россия')
            )),
            'region' => new Type\Varchar(array(
                'description' => t('Край, область'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Краснодарский край')
            )),
            'city' => new Type\Varchar(array(
                'description' => t('Населенный пункт'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Краснодар')
            )),
            'county' => new Type\ArrayList(array(
                'description' => t('Округ'),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getCountyList'], [0 => 'Не указано']),
                'runtime' => true
                //                    'Checker' => ['chkEmpty', t('Заполните поле Округ')]
            )),
            '_county' => new Type\Varchar([
                'description' => t('Округ (serialize)'),
                'visible' => false
            ]),
            'district' => new Type\ArrayList(array(
                'description' => t('Район'),
                'Attr' => array(array('size' => 5)),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getDistrictList'], [0 => 'Не выбрано']),
                'runtime' => true,
                'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле Район')],
            )),
            '_district' => new Type\Varchar([
                'description' => t('Район (serialize)'),
                'visible' => false
            ]),
            'street' => new Type\Varchar([
                'description' => t('Улица'),
                'maxLength' => 255
            ]),
            'house' => new Type\Integer([
                'description' => t('Дом'),
                'maxLength' => 3
            ]),
            'liter' => new Type\Varchar([
                'description' => t('Литер/Корпус'),
            ]),
            t('Параметры'),
            'rooms' => new Type\Integer([
                'description' => t('Количество комнат'),
                'maxLength' => 2,
                'Checker' => ['chkEmpty', t('Укажите количество комнат')]
            ]),
            'rooms_isolated' => new Type\Integer([
                'description' => t('Все комнаты изолированы'),
                'CheckBoxView' => array(1,0),
                'default' => 1
            ]),
            'split_wc' => new Type\Integer([
                'description' => t('Раздельный санузел'),
                'CheckBoxView' => array(1,0),
                'default' => 1
            ]),
            'material' => new Type\Integer([
                'description' => t('Материал стен'),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getMaterialList'], [0 => 'Не выбрано']),
//                'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Материал стен')],
                'runtime' => true
            ]),
            '_material' => new Type\Varchar([
                'description' => t('Материал стен (serialize)'),
                'visible' => false
            ]),
            'year' => new Type\Integer([
                'description' => t('Год постройки'),
                'maxLength' => 4,
//                'checker' => ['chkEmpty', t('Заполниет поле - Год постройки')]
            ]),
            'state' => new Type\Integer([
                'description' => t('Состояние'),
                'list' => array(array('\Rieltprof\Model\ParamsApi', 'getStateList'), [0 => 'Не выбрано']),
//                'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Состояние')]
            ]),
            '_state' => new Type\Varchar([
                'description' => t('Состояние (serialize)'),
                'visible' => false
            ]),
            'square' => new Type\Double([
                'description' => t('Площадь'),
                'maxLength' => 11,
                'decimal' => 3,
                'allowEmpty' => false,
                'Checker' => ['chkEmpty', t('Заполните поле Площадь')]
//                'checker' => [['\Rieltprof\Model\ParamsApi', 'checkSquare'], t('поле Площадь')]
            ]),
            'square_kitchen' => new Type\Double([
                'description' => t('Площадь кухни'),
                'maxLength' => 11,
                'decimal' => 3,
//                'Checker' => ['chkEmpty', t('Заполните поле Площадь кухни')]
            ]),
            'square_living' => new Type\Double([
                'description' => t('Жилая площадь'),
                'maxLength' => 11,
//                'Checker' => ['chkEmpty', t('Заполните поле Жилая площадь')]
            ]),
            'flat_house' => new Type\Integer([
                'description' => t('Этажность дома'),
                'maxLength' => 3,
//                'Checker' => ['chkEmpty', t('Укажите Этажность дома')]
            ]),
            t('Дополнительные'),
            'quickly' => new Type\Integer([
                'description' => t('Срочно'),
                'CheckBoxView' => array(1,0),
                'default' => 0,
            ]),
            'mark' => new Type\Integer([
                'description' => t('Закладку можно'),
                'CheckBoxView' => array(1,0),
                'default' => 0,
            ]),
            'only_cash' => new Type\Integer([
                'description' => t('Только наличные'),
                'template' => '%rieltprof%/form/catalog/only_cash.tpl',
                'default' => 0,
                'rentVisible' => false
            ]),
            'mortgage' => new Type\Integer([
                'description' => t('Ипотеку рассматриваем'),
                'CheckBoxView' => [1,0],
                'visible' => false,
                'default' => 0,
                'rentVisible' => false
            ]),
            'breakdown' => new Type\Integer([
                'description' => t('Нужна разбивка по сумме'),
                'CheckBoxView' => [1,0],
                'rentVisible' => false
            ]),
            'encumbrance' => new Type\Integer([
                'description' => t('Обременение банка'),
                'template' => '%rieltprof%/form/catalog/encumbrance.tpl',
                'default' => 0,
                'rentVisible' => false
            ]),
            'encumbrance_notice' => new Type\Varchar([
                'description' => t('Банк, Сумма'),
                'visible' => false,
                'maxLength' => 255
            ]),
            'child' => new Type\Integer([
                'description' => t('Несовершеннолетние дети/опека'),
                'CheckBoxView' => [1, 0],
                'maxLength' => 1,
                'rentVisible' => false
            ]),
            'exclusive' => new Type\Integer([
                'description' => t('Эксклюзив чистый'),
                'default' => 0,
                'template' => '%rieltprof%/form/catalog/exclusive.tpl'
            ]),
            'advertise' => new Type\Integer([
                'description' => t('От себя рекламирую в интернете'),
                'CheckBoxView' => [1, 0],
                'visible' => false
            ]),
            t('Земля'),
            'land_area' => new Type\Decimal([
                'description' => t('Площадь земельного участка (сот.)'),
                'Checker' => ['chkEmpty', t('Заполните поле - Площадь земельного участка')]
            ])
        ));
    }
    public static function ormInitRieltprofTownhouse(\Rieltprof\Model\Orm\TownHouse $orm)
    {
        $orm->getPropertyIterator()->append(array(
            t('Основные'),
            'controller' => new Type\Varchar([
                'description' => t('Контроллер'),
                'visible' => false
            ]),
            'actual_on_date' => new Type\Date([
                'description' => t('актуально на дату')
                // При добавлении = дата создания
            ]),
            'cost_product' => new Type\Integer([
                'description' => t('Цена'),
                'maxLength' => 15,
                'Checker' => ['chkEmpty', t('Укажите цену')],
                'rentVisible' => false
            ]),
            'cost_rent' => new Type\Integer([
                'description' => t('Цена за мес.'),
                'Checker' => ['chkEmpty', t('Укажине Цену в мес.')],
                'saleVisible' => false,
            ]),
            'cost_one' => new Type\Integer([
                'description' => t('Цена за 1 кв. метр'),
                'visible' => false
            ]),
            'note' => new Type\Text(array(
                'description' => t('Примечание'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'personal_note' => new Type\Text(array(
                'description' => t('Личные пометки (будут видны только Вам)'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'owner' => new Type\Integer([
                'description' => t('Владелец объявления'),
                'visible' => false
            ]),
            'object' => new Type\Varchar([
                'description' => t('Объект'),
                'visible' => false
            ]),
            t('Локация'),
            'country' => new Type\Varchar(array(
                'description' => t('Страна'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Россия')
            )),
            'region' => new Type\Varchar(array(
                'description' => t('Край, область'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Краснодарский край')
            )),
            'city' => new Type\Varchar(array(
                'description' => t('Населенный пункт'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Краснодар')
            )),
            'county' => new Type\ArrayList(array(
                'description' => t('Округ'),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getCountyList'], [0 => 'Не указано']),
                'runtime' => true
                //                    'Checker' => ['chkEmpty', t('Заполните поле Округ')]
            )),
            '_county' => new Type\Varchar([
                'description' => t('Округ (serialize)'),
                'visible' => false
            ]),
            'district' => new Type\ArrayList(array(
                'description' => t('Район'),
                'Attr' => array(array('size' => 5)),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getDistrictList'], [0 => 'Не выбрано']),
                'runtime' => true,
                'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле Район')],
            )),
            '_district' => new Type\Varchar([
                'description' => t('Район (serialize)'),
                'visible' => false
            ]),
            'street' => new Type\Varchar([
                'description' => t('Улица'),
                'maxLength' => 255
            ]),
            'house' => new Type\Integer([
                'description' => t('Дом'),
                'maxLength' => 3
            ]),
            'liter' => new Type\Varchar([
                'description' => t('Литер/Корпус'),
            ]),
            t('Параметры'),
            'rooms' => new Type\Integer([
                'description' => t('Количество комнат'),
                'maxLength' => 2,
                'Checker' => ['chkEmpty', t('Укажите количество комнат')]
            ]),
            'rooms_isolated' => new Type\Integer([
                'description' => t('Все комнаты изолированы'),
                'CheckBoxView' => array(1,0),
                'default' => 1
            ]),
            'split_wc' => new Type\Integer([
                'description' => t('Раздельный санузел'),
                'CheckBoxView' => array(1,0),
                'default' => 1
            ]),
            'material' => new Type\Integer([
                'description' => t('Материал стен'),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getMaterialList'], [0 => 'Не выбрано']),
//                'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Материал стен')],
                'runtime' => true
            ]),
            '_material' => new Type\Varchar([
                'description' => t('Материал стен (serialize)'),
                'visible' => false
            ]),
            'year' => new Type\Integer([
                'description' => t('Год постройки'),
                'maxLength' => 4,
//                'checker' => ['chkEmpty', t('Заполниет поле - Год постройки')]
            ]),
            'state' => new Type\Integer([
                'description' => t('Состояние'),
                'list' => array(array('\Rieltprof\Model\ParamsApi', 'getStateList'), [0 => 'Не выбрано']),
//                'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Состояние')]
            ]),
            '_state' => new Type\Varchar([
                'description' => t('Состояние (serialize)'),
                'visible' => false
            ]),
            'square' => new Type\Double([
                'description' => t('Площадь'),
                'maxLength' => 11,
                'decimal' => 3,
                'allowEmpty' => false,
                'Checker' => ['chkEmpty', t('Заполните поле Площадь')]
//                'checker' => [['\Rieltprof\Model\ParamsApi', 'checkSquare'], t('поле Площадь')]
            ]),
            'square_kitchen' => new Type\Double([
                'description' => t('Площадь кухни'),
                'maxLength' => 11,
                'decimal' => 3,
//                'Checker' => ['chkEmpty', t('Заполните поле Площадь кухни')]
            ]),
            'square_living' => new Type\Double([
                'description' => t('Жилая площадь'),
                'maxLength' => 11,
//                'Checker' => ['chkEmpty', t('Заполните поле Жилая площадь')]
            ]),
            'flat_house' => new Type\Integer([
                'description' => t('Этажность дома'),
                'maxLength' => 3,
//                'Checker' => ['chkEmpty', t('Укажите Этажность дома')]
            ]),
            t('Дополнительные'),
            'quickly' => new Type\Integer([
                'description' => t('Срочно'),
                'CheckBoxView' => array(1,0),
                'default' => 0,
            ]),
            'mark' => new Type\Integer([
                'description' => t('Закладку можно'),
                'CheckBoxView' => array(1,0),
                'default' => 0,
            ]),
            'only_cash' => new Type\Integer([
                'description' => t('Только наличные'),
                'template' => '%rieltprof%/form/catalog/only_cash.tpl',
                'default' => 0,
                'rentVisible' => false
            ]),
            'mortgage' => new Type\Integer([
                'description' => t('Ипотеку рассматриваем'),
                'CheckBoxView' => [1,0],
                'visible' => false,
                'default' => 0,
                'rentVisible' => false
            ]),
            'breakdown' => new Type\Integer([
                'description' => t('Нужна разбивка по сумме'),
                'CheckBoxView' => [1,0],
                'rentVisible' => false
            ]),
            'encumbrance' => new Type\Integer([
                'description' => t('Обременение банка'),
                'template' => '%rieltprof%/form/catalog/encumbrance.tpl',
                'default' => 0,
                'rentVisible' => false
            ]),
            'encumbrance_notice' => new Type\Varchar([
                'description' => t('Банк, Сумма'),
                'visible' => false,
                'maxLength' => 255
            ]),
            'child' => new Type\Integer([
                'description' => t('Несовершеннолетние дети/опека'),
                'CheckBoxView' => [1, 0],
                'maxLength' => 1,
                'rentVisible' => false
            ]),
            'exclusive' => new Type\Integer([
                'description' => t('Эксклюзив чистый'),
                'default' => 0,
                'template' => '%rieltprof%/form/catalog/exclusive.tpl'
            ]),
            'advertise' => new Type\Integer([
                'description' => t('От себя рекламирую в интернете'),
                'CheckBoxView' => [1, 0],
                'visible' => false
            ]),
            t('Земля'),
            'land_area' => new Type\Decimal([
                'description' => t('Площадь земельного участка (сот.)'),
                'Checker' => ['chkEmpty', t('Заполните поле - Площадь земельного участка')]
            ])
        ));
    }
    public static function ormInitRieltprofCountryhouse(\Rieltprof\Model\Orm\CountryHouse $orm)
    {
        $orm->getPropertyIterator()->append(array(
            t('Основные'),
            'controller' => new Type\Varchar([
                'description' => t('Контроллер'),
                'visible' => false
            ]),
            'actual_on_date' => new Type\Date([
                'description' => t('актуально на дату')
                // При добавлении = дата создания
            ]),
            'cost_product' => new Type\Integer([
                'description' => t('Цена'),
                'maxLength' => 15,
                'Checker' => ['chkEmpty', t('Укажите цену')],
                'rentVisible' => false
            ]),
            'cost_rent' => new Type\Integer([
                'description' => t('Цена за мес.'),
                'Checker' => ['chkEmpty', t('Укажине Цену в мес.')],
                'saleVisible' => false,
            ]),
            'cost_one' => new Type\Integer([
                'description' => t('Цена за 1 кв. метр'),
                'visible' => false
            ]),
            'note' => new Type\Text(array(
                'description' => t('Примечание'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'personal_note' => new Type\Text(array(
                'description' => t('Личные пометки (будут видны только Вам)'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'owner' => new Type\Integer([
                'description' => t('Владелец объявления'),
                'visible' => false
            ]),
            'object' => new Type\Varchar([
                'description' => t('Объект'),
                'visible' => false
            ]),
            t('Локация'),
            'country' => new Type\Varchar(array(
                'description' => t('Страна'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Россия')
            )),
            'region' => new Type\Varchar(array(
                'description' => t('Край, область'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Краснодарский край')
            )),
            'city' => new Type\Varchar(array(
                'description' => t('Населенный пункт'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Краснодар')
            )),
            'county' => new Type\ArrayList(array(
                'description' => t('Округ'),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getCountyList'], [0 => 'Не указано']),
                'runtime' => true
                //                    'Checker' => ['chkEmpty', t('Заполните поле Округ')]
            )),
            '_county' => new Type\Varchar([
                'description' => t('Округ (serialize)'),
                'visible' => false
            ]),
            'district' => new Type\ArrayList(array(
                'description' => t('Район'),
                'Attr' => array(array('size' => 5)),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getDistrictList'], [0 => 'Не выбрано']),
                'runtime' => true,
                'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле Район')],
            )),
            '_district' => new Type\Varchar([
                'description' => t('Район (serialize)'),
                'visible' => false
            ]),
            'street' => new Type\Varchar([
                'description' => t('Улица'),
                'maxLength' => 255
            ]),
            'house' => new Type\Integer([
                'description' => t('Дом'),
                'maxLength' => 3
            ]),
            'liter' => new Type\Varchar([
                'description' => t('Литер/Корпус'),
            ]),
            t('Параметры'),
            'rooms' => new Type\Integer([
                'description' => t('Количество комнат'),
                'maxLength' => 2,
//                'Checker' => ['chkEmpty', t('Укажите количество комнат')]
            ]),
            'rooms_isolated' => new Type\Integer([
                'description' => t('Все комнаты изолированы'),
                'CheckBoxView' => array(1,0),
                'default' => 1
            ]),
            'split_wc' => new Type\Integer([
                'description' => t('Раздельный санузел'),
                'CheckBoxView' => array(1,0),
                'default' => 1
            ]),
            'material' => new Type\Integer([
                'description' => t('Материал стен'),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getMaterialList'], [0 => 'Не выбрано']),
//                'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Материал стен')],
                'runtime' => true
            ]),
            '_material' => new Type\Varchar([
                'description' => t('Материал стен (serialize)'),
                'visible' => false
            ]),
            'year' => new Type\Integer([
                'description' => t('Год постройки'),
                'maxLength' => 4,
//                'checker' => ['chkEmpty', t('Заполниет поле - Год постройки')]
            ]),
            'state' => new Type\Integer([
                'description' => t('Состояние'),
                'list' => array(array('\Rieltprof\Model\ParamsApi', 'getStateList'), [0 => 'Не выбрано']),
//                'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Состояние')]
            ]),
            '_state' => new Type\Varchar([
                'description' => t('Состояние (serialize)'),
                'visible' => false
            ]),
            'square' => new Type\Double([
                'description' => t('Площадь'),
                'maxLength' => 11,
                'decimal' => 3,
                'allowEmpty' => false,
                'Checker' => ['chkEmpty', t('Заполните поле Площадь')]
//                'checker' => [['\Rieltprof\Model\ParamsApi', 'checkSquare'], t('поле Площадь')]
            ]),
            'square_kitchen' => new Type\Double([
                'description' => t('Площадь кухни'),
                'maxLength' => 11,
                'decimal' => 3,
//                'Checker' => ['chkEmpty', t('Заполните поле Площадь кухни')]
            ]),
            'square_living' => new Type\Double([
                'description' => t('Жилая площадь'),
                'maxLength' => 11,
//                'Checker' => ['chkEmpty', t('Заполните поле Жилая площадь')]
            ]),
            'flat_house' => new Type\Integer([
                'description' => t('Этажность дома'),
                'maxLength' => 3,
//                'Checker' => ['chkEmpty', t('Укажите Этажность дома')]
            ]),
            t('Дополнительные'),
            'quickly' => new Type\Integer([
                'description' => t('Срочно'),
                'CheckBoxView' => array(1,0),
                'default' => 0,
            ]),
            'mark' => new Type\Integer([
                'description' => t('Закладку можно'),
                'CheckBoxView' => array(1,0),
                'default' => 0,
            ]),
            'only_cash' => new Type\Integer([
                'description' => t('Только наличные'),
                'template' => '%rieltprof%/form/catalog/only_cash.tpl',
                'default' => 0,
                'rentVisible' => false
            ]),
            'mortgage' => new Type\Integer([
                'description' => t('Ипотеку рассматриваем'),
                'CheckBoxView' => [1,0],
                'visible' => false,
                'default' => 0,
                'rentVisible' => false
            ]),
            'breakdown' => new Type\Integer([
                'description' => t('Нужна разбивка по сумме'),
                'CheckBoxView' => [1,0],
                'rentVisible' => false
            ]),
            'encumbrance' => new Type\Integer([
                'description' => t('Обременение банка'),
                'template' => '%rieltprof%/form/catalog/encumbrance.tpl',
                'default' => 0,
                'rentVisible' => false
            ]),
            'encumbrance_notice' => new Type\Varchar([
                'description' => t('Банк, Сумма'),
                'visible' => false,
                'maxLength' => 255
            ]),
            'child' => new Type\Integer([
                'description' => t('Несовершеннолетние дети/опека'),
                'CheckBoxView' => [1, 0],
                'maxLength' => 1,
                'rentVisible' => false
            ]),
            'exclusive' => new Type\Integer([
                'description' => t('Эксклюзив чистый'),
                'default' => 0,
                'template' => '%rieltprof%/form/catalog/exclusive.tpl'
            ]),
            'advertise' => new Type\Integer([
                'description' => t('От себя рекламирую в интернете'),
                'CheckBoxView' => [1, 0],
                'visible' => false
            ]),
            t('Земля'),
            'land_area' => new Type\Decimal([
                'description' => t('Площадь земельного участка (сот.)'),
                'Checker' => ['chkEmpty', t('Заполните поле - Площадь земельного участка')]
            ])
        ));
    }
    public static function ormInitRieltprofRoom(\Rieltprof\Model\Orm\Room $orm)
    {
        $orm->getPropertyIterator()->append(array(
            t('Основные'),
                'controller' => new Type\Varchar([
                    'description' => t('Контроллер'),
                    'visible' => false
                ]),
                'actual_on_date' => new Type\Date([
                    'description' => t('актуально на дату')
                    // При добавлении = дата создания
                ]),
                'cost_product' => new Type\Integer([
                    'description' => t('Цена'),
                    'maxLength' => 15,
                    'Checker' => ['chkEmpty', t('Укажите цену')],
                    'rentVisible' => false
                ]),
                'cost_rent' => new Type\Integer([
                    'description' => t('Цена за мес.'),
                    'Checker' => ['chkEmpty', t('Укажине Цену в мес.')],
                    'saleVisible' => false,
                ]),
                'cost_one' => new Type\Integer([
                    'description' => t('Цена за 1 кв. метр'),
                    'visible' => false
                ]),
                'note' => new Type\Text(array(
                    'description' => t('Примечание'),
                    'Attr' => array(array('rows' => 3, 'cols' => 80)),
                )),
                'personal_note' => new Type\Text(array(
                    'description' => t('Личные пометки (будут видны только Вам)'),
                    'Attr' => array(array('rows' => 3, 'cols' => 80)),
                )),
                'owner' => new Type\Integer([
                    'description' => t('Владелец объявления'),
                    'visible' => false
                ]),
                'object' => new Type\Varchar([
                    'description' => t('Объект'),
                    'visible' => false
                ]),
            t('Локация'),
                'country' => new Type\Varchar(array(
                    'description' => t('Страна'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Россия')
                )),
                'region' => new Type\Varchar(array(
                    'description' => t('Край, область'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Краснодарский край')
                )),
                'city' => new Type\Varchar(array(
                    'description' => t('Населенный пункт'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Краснодар')
                )),
                'county' => new Type\ArrayList(array(
                    'description' => t('Округ'),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getCountyList'], [0 => 'Не указано']),
                    'runtime' => true
                )),
                '_county' => new Type\Varchar([
                    'description' => t('Округ (serialize)'),
                    'visible' => false
                ]),
                'district' => new Type\ArrayList(array(
                    'description' => t('Район'),
                    'Attr' => array(array('size' => 5)),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getDistrictList'], [0 => 'Не выбрано']),
                    'runtime' => true,
                    'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле Район')],
                )),
                '_district' => new Type\Varchar([
                    'description' => t('Район (serialize)'),
                    'visible' => false
                ]),
                'street' => new Type\Varchar([
                    'description' => t('Улица'),
                    'maxLength' => 255
                ]),
                'house' => new Type\Integer([
                    'description' => t('Дом'),
                    'maxLength' => 3
                ]),
                'liter' => new Type\Varchar([
                    'description' => t('Литер/Корпус'),
                ]),
            t('Параметры'),
                'rooms' => new Type\Integer([
                    'description' => t('Количество комнат'),
                    'maxLength' => 2,
                    'Checker' => ['chkEmpty', t('Укажите количество комнат')]
                ]),
                'material' => new Type\Integer([
                    'description' => t('Материал стен'),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getMaterialList'], [0 => 'Не выбрано']),
                ]),
                '_material' => new Type\Varchar([
                    'description' => t('Материал стен (serialize)'),
                    'visible' => false
                ]),
                'year' => new Type\Integer([
                    'description' => t('Год постройки'),
                    'maxLength' => 4
                ]),
                'state' => new Type\Integer([
                    'description' => t('Состояние'),
                    'list' => array(array('\Rieltprof\Model\ParamsApi', 'getStateList'), [0 => 'Не выбрано']),
//                    'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Состояние')]
                ]),
                '_state' =>new Type\Varchar([
                    'description' => t('Состояние (serialize)'),
                    'visible' => false
                ]),
                'square' => new Type\Double([
                    'description' => t('Площадь'),
                    'maxLength' => 11,
                    'decimal' => 3,
                    'allowEmpty' => false,
                    'Checker' => ['chkEmpty', t('Заполните поле Площадь')]
//                    'checker' => [['\Rieltprof\Model\ParamsApi', 'checkSquare'], t('поле Площадь')]
                ]),
                'square_kitchen' => new Type\Double([
                    'description' => t('Площадь кухни'),
                    'maxLength' => 11,
                    'decimal' => 3,
//                    'Checker' => ['chkEmpty', t('Заполните поле Площадь кухни')]
                ]),
                'square_living' => new Type\Double([
                    'description' => t('Жилая площадь'),
                    'maxLength' => 11,
//                    'Checker' => ['chkEmpty', t('Заполните поле Жилая площадь')]
                ]),
                'flat' => new Type\Integer([
                    'description' => t('Этаж'),
                    'maxLength' => 3,
//                    'Checker' => ['chkEmpty', t('Заполните поле Этаж')]
//                    'checker' => [['\Rieltprof\Model\ParamsApi', 'checkFlat'], t('поле Этаж')]
                ]),
                'flat_house' => new Type\Integer([
                    'description' => t('Этажность дома'),
                    'maxLength' => 3,
//                    'Checker' => ['chkEmpty', t('Укажите Этажность дома')]
//                    'checker' => [['\Rieltprof\Model\ParamsApi', 'checkFlatHouse'], t('поле Этажность дома')]
                ]),
                'is_first' => new Type\Integer([
                    'description' => t('Первый этаж'),
                    'CheckBoxView' => array(1,0),
                    'visible' => false,
                    'default' => 0
                ]),
                'is_last' => new Type\Integer([
                    'description' => t('Последний этаж'),
                    'CheckBoxView' => array(1,0),
                    'visible' => false,
                    'default' => 0
                ]),
            t('Дополнительные'),
                'quickly' => new Type\Integer([
                    'description' => t('Срочно'),
                    'CheckBoxView' => array(1,0),
                    'default' => 0,
                ]),
                'mark' => new Type\Integer([
                    'description' => t('Закладку можно'),
                    'CheckBoxView' => array(1,0),
                    'default' => 0,
                ]),
                'only_cash' => new Type\Integer([
                    'description' => t('Только наличные'),
                    'template' => '%rieltprof%/form/catalog/only_cash.tpl',
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'mortgage' => new Type\Integer([
                    'description' => t('Ипотеку рассматриваем'),
                    'CheckBoxView' => [1,0],
                    'visible' => false,
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'breakdown' => new Type\Integer([
                    'description' => t('Нужна разбивка по сумме'),
                    'CheckBoxView' => [1,0],
                    'rentVisible' => false
                ]),
                'encumbrance' => new Type\Integer([
                    'description' => t('Обременение банка'),
                    'template' => '%rieltprof%/form/catalog/encumbrance.tpl',
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'encumbrance_notice' => new Type\Varchar([
                    'description' => t('Банк, Сумма'),
                    'visible' => false,
                    'maxLength' => 255
                ]),
                'child' => new Type\Integer([
                    'description' => t('Несовершеннолетние дети/опека'),
                    'CheckBoxView' => [1, 0],
                    'maxLength' => 1,
                    'rentVisible' => false
                ]),
                'remodeling' => new Type\Integer([
                    'description' => t('Перепланировка'),
                    'CheckBoxView' => [1, 0],
                    'maxLength' => 1,
                    'rentVisible' => false,
                    'default' => 0,
                    'template' => '%rieltprof%/form/catalog/remodeling.tpl'
                ]),
                'remodeling_legalized' => new Type\Integer([
                    'description' => t('Перепланировка узаконена?'),
                    'CheckBoxView' => [1,0],
                    'visible' => false,
                    'rentVisible' => false
                ]),
                'exclusive' => new Type\Integer([
                    'description' => t('Эксклюзив чистый'),
                    'default' => 0,
                    'template' => '%rieltprof%/form/catalog/exclusive.tpl'
                ]),
                'advertise' => new Type\Integer([
                    'description' => t('От себя рекламирую в интернете'),
                    'CheckBoxView' => [1, 0],
                    'visible' => false
                ])
        ));
    }
    public static function ormInitRieltprofFlat(\Rieltprof\Model\Orm\Flat $orm)
    {
        $orm->getPropertyIterator()->append(array(
            t('Основные'),
            'controller' => new Type\Varchar([
                'description' => t('Контроллер'),
                'visible' => false
            ]),
            'actual_on_date' => new Type\Date([
                'description' => t('актуально на дату')
                // При добавлении = дата создания
            ]),
            'cost_product' => new Type\Integer([
                'description' => t('Цена'),
                'maxLength' => 15,
                'Checker' => ['chkEmpty', t('Укажите цену')],
                'rentVisible' => false
            ]),
            'cost_rent' => new Type\Integer([
                'description' => t('Цена за мес.'),
                'Checker' => ['chkEmpty', t('Укажине Цену в мес.')],
                'saleVisible' => false,
            ]),
            'cost_one' => new Type\Integer([
                'description' => t('Цена за 1 кв. метр'),
                'visible' => false
            ]),
            'note' => new Type\Text(array(
                'description' => t('Примечание'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'personal_note' => new Type\Text(array(
                'description' => t('Личные пометки (будут видны только Вам)'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'owner' => new Type\Integer([
                'description' => t('Владелец объявления'),
                'visible' => false
            ]),
            'object' => new Type\Varchar([
                'description' => t('Объект'),
                'visible' => false
            ]),
            t('Локация'),
            'country' => new Type\Varchar(array(
                'description' => t('Страна'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Россия')
            )),
            'region' => new Type\Varchar(array(
                'description' => t('Край, область'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Краснодарский край')
            )),
            'city' => new Type\Varchar(array(
                'description' => t('Населенный пункт'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Краснодар')
            )),
            'county' => new Type\ArrayList(array(
                'description' => t('Округ'),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getCountyList'], [0 => 'Не указано']),
                'runtime' => true
//                    'Checker' => ['chkEmpty', t('Заполните поле Округ')]
            )),
            '_county' => new Type\Varchar([
                'description' => t('Округ (serialize)'),
                'visible' => false
            ]),
            'district' => new Type\ArrayList(array(
                'description' => t('Район'),
                'Attr' => array(array('size' => 5)),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getDistrictList'], [0 => 'Не выбрано']),
                'runtime' => true,
                'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле Район')],
            )),
            '_district' => new Type\Varchar([
                'description' => t('Район (serialize)'),
                'visible' => false
            ]),
            'street' => new Type\Varchar([
                'description' => t('Улица'),
                'maxLength' => 255
            ]),
            'house' => new Type\Integer([
                'description' => t('Дом'),
                'maxLength' => 3
            ]),
            'liter' => new Type\Varchar([
                'description' => t('Литер/Корпус'),
            ]),
            t('Параметры'),
            'rooms_list' => new Type\ArrayList([
                'description' => t('Количество комнат'),
                'list' => [['Rieltprof\Model\ParamsApi', 'getRoomsList'], [0 => 'Не выбрано']],
                'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Количество комнат')]
            ]),
            '_rooms_list' => new Type\Varchar([
                'description' => t('Количество комнат (serialize)'),
                'visible' => false
            ]),
            'rooms_isolated' => new Type\Integer([
                'description' => t('Все комнаты изолированы'),
                'CheckBoxView' => array(1,0),
                'default' => 1
            ]),
            'split_wc' => new Type\Integer([
                'description' => t('Раздельный санузел'),
                'CheckBoxView' => array(1,0),
                'default' => 1
            ]),
            'material' => new Type\Integer([
                'description' => t('Материал стен'),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getMaterialList'], [0 => 'Не выбрано']),
//                'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Состояние')]
            ]),
            '_material' => new Type\Varchar([
                'description' => t('Материал стен (serialize)'),
                'visible' => false
            ]),
            'year' => new Type\Integer([
                'description' => t('Год постройки'),
                'maxLength' => 4
            ]),
            'state' => new Type\Integer([
                'description' => t('Состояние'),
                'list' => array(array('\Rieltprof\Model\ParamsApi', 'getStateList'), [0 => 'Не выбрано']),
//                'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Состояние')]
            ]),
            '_state' => new Type\Varchar([
                'description' => t('Состояние (serialize)'),
                'visible' => false
            ]),
            'square' => new Type\Double([
                'description' => t('Площадь'),
                'maxLength' => 11,
                'decimal' => 3,
                'allowEmpty' => false,
                'Checker' => ['chkEmpty', t('Заполните поле Площадь')]
//                'checker' => [['Rieltprof\Model\ParamsApi', 'checkSquare'], t('Поле Площадь')]
            ]),
            'square_kitchen' => new Type\Double([
                'description' => t('Площадь кухни'),
                'maxLength' => 11,
                'decimal' => 3,
//                'Checker' => ['chkEmpty', t('Заполните поле Площадь кухни')]
//                'checker' => [['Rieltprof\Model\ParamsApi', 'checkFlatSquareKitchen'], t('Заполните поле Площадь кухни')]
            ]),
            'square_living' => new Type\Double([
                'description' => t('Жилая площадь'),
                'maxLength' => 11,
//                'Checker' => ['chkEmpty', t('Заполните поле Жилая площадь')]
//                'checker' => [['Rieltprof\Model\ParamsApi', 'checkFlatSquareKitchen'], t('Заполните поле Жилая площадь')]
            ]),
            'flat' => new Type\Integer([
                'description' => t('Этаж'),
                'maxLength' => 3,
//                'Checker' => ['chkEmpty', t('Заполните поле Этаж')]
//                'checker' => [['Rieltprof\Model\ParamsApi', 'checkFlat'], t('Поле Этаж')]
            ]),
            'flat_house' => new Type\Integer([
                'description' => t('Этажность дома'),
                'maxLength' => 3,
//                'Checker' => ['chkEmpty', t('Укажите Этажность дома')]
//                'checker' => [['Rieltprof\Model\ParamsApi', 'checkFlatHouse'], t('Поле Этажность дома')]
            ]),
            'is_first' => new Type\Integer([
                'description' => t('Первый этаж'),
                'CheckBoxView' => array(1,0),
                'visible' => false,
                'default' => 0
            ]),
            'is_last' => new Type\Integer([
                'description' => t('Последний этаж'),
                'CheckBoxView' => array(1,0),
                'visible' => false,
                'default' => 0
            ]),
            t('Дополнительные'),
            'quickly' => new Type\Integer([
                'description' => t('Срочно'),
                'CheckBoxView' => array(1,0),
                'default' => 0,
            ]),
            'mark' => new Type\Integer([
                'description' => t('Закладку можно'),
                'CheckBoxView' => array(1,0),
                'default' => 0,
            ]),
            'only_cash' => new Type\Integer([
                'description' => t('Только наличные'),
                'template' => '%rieltprof%/form/catalog/only_cash.tpl',
                'default' => 0,
                'rentVisible' => false
            ]),
            'mortgage' => new Type\Integer([
                'description' => t('Ипотеку рассматриваем'),
                'CheckBoxView' => [1,0],
                'visible' => false,
                'default' => 0,
                'rentVisible' => false
            ]),
            'breakdown' => new Type\Integer([
                'description' => t('Нужна разбивка по сумме'),
                'CheckBoxView' => [1,0],
                'rentVisible' => false,
                'default' => 0
            ]),
            'encumbrance' => new Type\Integer([
                'description' => t('Обременение банка'),
                'template' => '%rieltprof%/form/catalog/encumbrance.tpl',
                'default' => 0,
                'rentVisible' => false
            ]),
            'encumbrance_notice' => new Type\Varchar([
                'description' => t('Банк, Сумма'),
                'visible' => false,
                'maxLength' => 255
            ]),
            'child' => new Type\Integer([
                'description' => t('Несовершеннолетние дети/опека'),
                'CheckBoxView' => [1, 0],
                'maxLength' => 1,
                'rentVisible' => false
            ]),
            'remodeling' => new Type\Integer([
                'description' => t('Перепланировка'),
//                'CheckBoxView' => [1, 0],
//                'maxLength' => 1,
                'rentVisible' => false,
                'default' => 0,
                'template' => '%rieltprof%/form/catalog/remodeling.tpl'
            ]),
            'remodeling_legalized' => new Type\Integer([
                'description' => t('Перепланировка узаконена?'),
                'CheckBoxView' => [1,0],
                'visible' => false,
                'rentVisible' => false
            ]),
            'exclusive' => new Type\Integer([
                'description' => t('Эксклюзив чистый'),
                'default' => 0,
                'template' => '%rieltprof%/form/catalog/exclusive.tpl'
            ]),
            'advertise' => new Type\Integer([
                'description' => t('От себя рекламирую в интернете'),
                'CheckBoxView' => [1, 0],
                'visible' => false
            ])
        ));
    }
    public static function ormInitRieltprofHouse(\Rieltprof\Model\Orm\House $orm)
    {
        $orm->getPropertyIterator()->append(array(
            t('Основные'),
                'controller' => new Type\Varchar([
                    'description' => t('Контроллер'),
                    'visible' => false
                ]),
                'actual_on_date' => new Type\Date([
                    'description' => t('актуально на дату')
                    // При добавлении = дата создания
                ]),
                'cost_product' => new Type\Integer([
                    'description' => t('Цена'),
                    'maxLength' => 15,
                    'Checker' => ['chkEmpty', t('Укажите цену')],
                    'rentVisible' => false
                ]),
                'cost_rent' => new Type\Integer([
                    'description' => t('Цена за мес.'),
                    'Checker' => ['chkEmpty', t('Укажине Цену в мес.')],
                    'saleVisible' => false,
                ]),
                'cost_one' => new Type\Integer([
                    'description' => t('Цена за 1 кв. метр'),
                    'visible' => false
                ]),
                'note' => new Type\Text(array(
                    'description' => t('Примечание'),
                    'Attr' => array(array('rows' => 3, 'cols' => 80)),
                )),
                'personal_note' => new Type\Text(array(
                    'description' => t('Личные пометки (будут видны только Вам)'),
                    'Attr' => array(array('rows' => 3, 'cols' => 80)),
                )),
                'owner' => new Type\Integer([
                    'description' => t('Владелец объявления'),
                    'visible' => false
                ]),
                'object' => new Type\Varchar([
                    'description' => t('Объект'),
                    'visible' => false
                ]),
            t('Локация'),
                'country' => new Type\Varchar(array(
                    'description' => t('Страна'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Россия')
                )),
                'region' => new Type\Varchar(array(
                    'description' => t('Край, область'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Краснодарский край')
                )),
                'city' => new Type\Varchar(array(
                    'description' => t('Населенный пункт'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Краснодар')
                )),
                'county' => new Type\ArrayList(array(
                    'description' => t('Округ'),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getCountyList'], [0 => 'Не указано']),
                    'runtime' => true
    //                    'Checker' => ['chkEmpty', t('Заполните поле Округ')]
                )),
                '_county' => new Type\Varchar([
                    'description' => t('Округ (serialize)'),
                    'visible' => false
                ]),
                'district' => new Type\ArrayList(array(
                    'description' => t('Район'),
                    'Attr' => array(array('size' => 5)),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getDistrictList'], [0 => 'Не выбрано']),
                    'runtime' => true,
                    'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле Район')],
                )),
                '_district' => new Type\Varchar([
                    'description' => t('Район (serialize)'),
                    'visible' => false
                ]),
                'street' => new Type\Varchar([
                    'description' => t('Улица'),
                    'maxLength' => 255
                ]),
                'house' => new Type\Integer([
                    'description' => t('Дом'),
                    'maxLength' => 3
                ]),
                'liter' => new Type\Varchar([
                    'description' => t('Литер/Корпус'),
                ]),
            t('Параметры'),
                'rooms' => new Type\Integer([
                    'description' => t('Количество комнат'),
                    'maxLength' => 2,
//                    'Checker' => ['chkEmpty', t('Укажите количество комнат')]
                ]),
                'rooms_isolated' => new Type\Integer([
                    'description' => t('Все комнаты изолированы'),
                    'CheckBoxView' => array(1,0),
                    'default' => 1
                ]),
                'split_wc' => new Type\Integer([
                    'description' => t('Раздельный санузел'),
                    'CheckBoxView' => array(1,0),
                    'default' => 1
                ]),
                'material' => new Type\Integer([
                    'description' => t('Материал стен'),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getMaterialList'], [0 => 'Не выбрано']),
//                    'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Материал стен')],
                    'runtime' => true
                ]),
                '_material' => new Type\Varchar([
                    'description' => t('Материал стен (serialize)'),
                    'visible' => false
                ]),
                'year' => new Type\Integer([
                    'description' => t('Год постройки'),
                    'maxLength' => 4,
//                    'checker' => ['chkEmpty', t('Заполниет поле - Год постройки')]
                ]),
                'state' => new Type\Integer([
                    'description' => t('Состояние'),
                    'list' => array(array('\Rieltprof\Model\ParamsApi', 'getStateList'), [0 => 'Не выбрано']),
//                    'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Состояние')]
                ]),
                '_state' => new Type\Varchar([
                    'description' => t('Состояние (serialize)'),
                    'visible' => false
                ]),
                'square' => new Type\Double([
                    'description' => t('Площадь'),
                    'maxLength' => 11,
                    'decimal' => 3,
                    'allowEmpty' => false,
                    'Checker' => ['chkEmpty', t('Заполните поле Площадь')]
//                    'checker' => [['\Rieltprof\Model\ParamsApi', 'checkSquare'], t('поле Площадь')]
                ]),
                'square_kitchen' => new Type\Double([
                    'description' => t('Площадь кухни'),
                    'maxLength' => 11,
                    'decimal' => 3,
//                    'Checker' => ['chkEmpty', t('Заполните поле Площадь кухни')]
                ]),
                'square_living' => new Type\Double([
                    'description' => t('Жилая площадь'),
                    'maxLength' => 11,
//                    'Checker' => ['chkEmpty', t('Заполните поле Жилая площадь')]
                ]),
                'flat_house' => new Type\Integer([
                    'description' => t('Этажность дома'),
                    'maxLength' => 3,
//                    'Checker' => ['chkEmpty', t('Укажите Этажность дома')]
                ]),
            t('Дополнительные'),
                'quickly' => new Type\Integer([
                    'description' => t('Срочно'),
                    'CheckBoxView' => array(1,0),
                    'default' => 0,
                ]),
                'mark' => new Type\Integer([
                    'description' => t('Закладку можно'),
                    'CheckBoxView' => array(1,0),
                    'default' => 0,
                ]),
                'only_cash' => new Type\Integer([
                    'description' => t('Только наличные'),
                    'template' => '%rieltprof%/form/catalog/only_cash.tpl',
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'mortgage' => new Type\Integer([
                    'description' => t('Ипотеку рассматриваем'),
                    'CheckBoxView' => [1,0],
                    'visible' => false,
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'breakdown' => new Type\Integer([
                    'description' => t('Нужна разбивка по сумме'),
                    'CheckBoxView' => [1,0],
                    'rentVisible' => false
                ]),
                'encumbrance' => new Type\Integer([
                    'description' => t('Обременение банка'),
                    'template' => '%rieltprof%/form/catalog/encumbrance.tpl',
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'encumbrance_notice' => new Type\Varchar([
                    'description' => t('Банк, Сумма'),
                    'visible' => false,
                    'maxLength' => 255
                ]),
                'child' => new Type\Integer([
                    'description' => t('Несовершеннолетние дети/опека'),
                    'CheckBoxView' => [1, 0],
                    'maxLength' => 1,
                    'rentVisible' => false
                ]),
                'exclusive' => new Type\Integer([
                    'description' => t('Эксклюзив чистый'),
                    'default' => 0,
                    'template' => '%rieltprof%/form/catalog/exclusive.tpl'
                ]),
                'advertise' => new Type\Integer([
                    'description' => t('От себя рекламирую в интернете'),
                    'CheckBoxView' => [1, 0],
                    'visible' => false
                ]),
            t('Земля'),
                'land_area' => new Type\Decimal([
                    'description' => t('Площадь земельного участка (сот.)'),
                    'Checker' => ['chkEmpty', t('Заполните поле - Площадь земельного участка')]
                ])
        ));
    }
    public static function ormInitRieltprofPlot(\Rieltprof\Model\Orm\Plot $orm)
    {
        $orm->getPropertyIterator()->append(array(
            t('Основные'),
            'controller' => new Type\Varchar([
                'description' => t('Контроллер'),
                'visible' => false
            ]),
            'actual_on_date' => new Type\Date([
                'description' => t('актуально на дату')
                // При добавлении = дата создания
            ]),
            'cost_product' => new Type\Integer([
                'description' => t('Цена'),
                'maxLength' => 15,
                'Checker' => ['chkEmpty', t('Укажите цену')],
                'rentVisible' => false
            ]),
            'cost_rent' => new Type\Integer([
                'description' => t('Цена за мес.'),
                'Checker' => ['chkEmpty', t('Укажине Цену в мес.')],
                'saleVisible' => false,
            ]),
            'cost_one' => new Type\Integer([
                'description' => t('Цена за 1 кв. метр'),
                'visible' => false
            ]),
            'note' => new Type\Text(array(
                'description' => t('Примечание'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'personal_note' => new Type\Text(array(
                'description' => t('Личные пометки (будут видны только Вам)'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'owner' => new Type\Integer([
                'description' => t('Владелец объявления'),
                'visible' => false
            ]),
            'object' => new Type\Varchar([
                'description' => t('Объект'),
                'visible' => false
            ]),
            t('Локация'),
                'country' => new Type\Varchar(array(
                    'description' => t('Страна'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Россия')
                )),
                'region' => new Type\Varchar(array(
                    'description' => t('Край, область'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Краснодарский край')
                )),
                'city' => new Type\Varchar(array(
                    'description' => t('Населенный пункт'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Краснодар')
                )),
                'county' => new Type\ArrayList(array(
                    'description' => t('Округ'),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getCountyList'], [0 => 'Не указано']),
                    'runtime' => true
                    //                    'Checker' => ['chkEmpty', t('Заполните поле Округ')]
                )),
                '_county' => new Type\Varchar([
                    'description' => t('Округ (serialize)'),
                    'visible' => false
                ]),
                'district' => new Type\ArrayList(array(
                    'description' => t('Район'),
                    'Attr' => array(array('size' => 5)),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getDistrictList'], [0 => 'Не выбрано']),
                    'runtime' => true,
                    'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле Район')],
                )),
                '_district' => new Type\Varchar([
                    'description' => t('Район (serialize)'),
                    'visible' => false
                ]),
                'street' => new Type\Varchar([
                    'description' => t('Улица'),
                    'maxLength' => 255
                ]),
                'house' => new Type\Integer([
                    'description' => t('Дом'),
                    'maxLength' => 3
                ]),
                'liter' => new Type\Varchar([
                    'description' => t('Литер/Корпус'),
                ]),
            t('Дополнительные'),
                'quickly' => new Type\Integer([
                    'description' => t('Срочно'),
                    'CheckBoxView' => array(1,0),
                    'default' => 0,
                ]),
                'mark' => new Type\Integer([
                    'description' => t('Закладку можно'),
                    'CheckBoxView' => array(1,0),
                    'default' => 0,
                ]),
                'exclusive' => new Type\Integer([
                    'description' => t('Эксклюзив чистый'),
                    'default' => 0,
                    'template' => '%rieltprof%/form/catalog/exclusive.tpl'
                ]),
                'advertise' => new Type\Integer([
                    'description' => t('От себя рекламирую в интернете'),
                    'CheckBoxView' => [1, 0],
                    'visible' => false
                ]),
            t('Земля'),
                'land_area' => new Type\Decimal([
                    'description' => t('Площадь земельного участка (сот.)'),
                    'Checker' => ['chkEmpty', t('Заполните поле - Площадь земельного участка')]
                ])
        ));
    }
    public static function ormInitRieltprofGarage(\Rieltprof\Model\Orm\Garage $orm)
    {
        $orm->getPropertyIterator()->append(array(
            t('Основные'),
                'controller' => new Type\Varchar([
                    'description' => t('Контроллер'),
                    'visible' => false
                ]),
                'actual_on_date' => new Type\Date([
                    'description' => t('актуально на дату')
                    // При добавлении = дата создания
                ]),
                'cost_product' => new Type\Integer([
                    'description' => t('Цена'),
                    'Checker' => ['chkEmpty', t('Укажите Цену')],
                    'rentVisible' => false
                ]),
                'cost_rent' => new Type\Integer([
                    'description' => t('Цена за мес.'),
                    'Checker' => ['chkEmpty', t('Укажине Цену в мес.')],
                    'saleVisible' => false,
                ]),
                'note' => new Type\Text(array(
                    'description' => t('Примечание'),
                    'Attr' => array(array('rows' => 3, 'cols' => 80)),
                )),
                'personal_note' => new Type\Text(array(
                    'description' => t('Личные пометки (будут видны только Вам)'),
                    'Attr' => array(array('rows' => 3, 'cols' => 80)),
                )),
                'owner' => new Type\Integer([
                    'description' => t('Владелец объявления'),
                    'visible' => false
                ]),
                'object' => new Type\Varchar([
                    'description' => t('Объект'),
                    'visible' => false
                ]),
            t('Локация'),
                'country' => new Type\Varchar(array(
                    'description' => t('Страна'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Россия')
                )),
                'region' => new Type\Varchar(array(
                    'description' => t('Край, область'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Краснодарский край')
                )),
                'city' => new Type\Varchar(array(
                    'description' => t('Населенный пункт'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Краснодар')
                )),
                'county' => new Type\ArrayList(array(
                    'description' => t('Округ'),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getCountyList'], [0 => 'Не указано']),
                    'runtime' => true
    //                    'Checker' => ['chkEmpty', t('Заполните поле Округ')]
                )),
                '_county' => new Type\Varchar([
                    'description' => t('Округ (serialize)'),
                    'visible' => false
                ]),
                'district' => new Type\ArrayList(array(
                    'description' => t('Район'),
                    'Attr' => array(array('size' => 5)),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getDistrictList'], [0 => 'Не выбрано']),
                    'runtime' => true,
                    'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле Район')],
                )),
                '_district' => new Type\Varchar([
                    'description' => t('Район (serialize)'),
                    'visible' => false
                ]),
                'street' => new Type\Varchar([
                    'description' => t('Улица'),
                    'maxLength' => 255
                ]),
                'house' => new Type\Integer([
                    'description' => t('Дом'),
                    'maxLength' => 3
                ]),
            t('Параметры'),
                'material' => new Type\Integer([
                    'description' => t('Материал стен'),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getMaterialList'], [0 => 'Не выбрано']),
//                    'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Материал стен')]
                ]),
                '_material' => new Type\Varchar([
                    'description' => t('Материал стен (serialize)'),
                    'visible' => false
                ]),
                'year' => new Type\Integer([
                    'description' => t('Год постройки'),
                    'maxLength' => 4
                ]),
                'state' => new Type\Integer([
                    'description' => t('Состояние'),
                    'list' => array(array('\Rieltprof\Model\ParamsApi', 'getStateList'), [0 => 'Не выбрано']),
//                    'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Состояние')]
                ]),
                '_state' =>new Type\Varchar([
                    'description' => t('Состояние (serialize)'),
                    'visible' => false
                ]),
                'square' => new Type\Double([
                    'description' => t('Площадь'),
                    'maxLength' => 11,
                    'decimal' => 3,
                    'allowEmpty' => false,
                    'Checker' => ['chkEmpty', t('Заполните поле Площадь')]
                ]),
                'flat_house' => new Type\Integer([
                    'description' => t('Этажность'),
                    'maxLength' => 3,
//                    'Checker' => ['chkEmpty', t('Укажите Этажность')]
                ]),
            t('Дополнительные'),
                'quickly' => new Type\Integer([
                    'description' => t('Срочно'),
                    'CheckBoxView' => array(1,0),
                    'default' => 0,
                ]),
                'mark' => new Type\Integer([
                    'description' => t('Закладку можно'),
                    'CheckBoxView' => array(1,0),
                    'default' => 0,
                ]),
                'only_cash' => new Type\Integer([
                    'description' => t('Только наличные'),
                    'template' => '%rieltprof%/form/catalog/only_cash.tpl',
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'mortgage' => new Type\Integer([
                    'description' => t('Ипотеку рассматриваем'),
                    'CheckBoxView' => [1,0],
                    'visible' => false,
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'breakdown' => new Type\Integer([
                    'description' => t('Нужна разбивка по сумме'),
                    'CheckBoxView' => [1,0],
                    'rentVisible' => false,
                    'default' => 0
                ]),
                'encumbrance' => new Type\Integer([
                    'description' => t('Обременение банка'),
                    'template' => '%rieltprof%/form/catalog/encumbrance.tpl',
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'encumbrance_notice' => new Type\Varchar([
                    'description' => t('Банк, Сумма'),
                    'visible' => false,
                    'maxLength' => 255,
                    'default' => ''
                ]),
                'child' => new Type\Integer([
                    'description' => t('Несовершеннолетние дети/опека'),
                    'CheckBoxView' => [1, 0],
                    'maxLength' => 1,
                    'rentVisible' => false,
                    'default' => 0
                ]),
                'exclusive' => new Type\Integer([
                    'description' => t('Эксклюзив чистый'),
                    'default' => 0,
                    'template' => '%rieltprof%/form/catalog/exclusive.tpl'
                ]),
                'advertise' => new Type\Integer([
                    'description' => t('От себя рекламирую в интернете'),
                    'CheckBoxView' => [1, 0],
                    'visible' => false,
                    'default' => 0
                ])
        ));
    }
    public static function ormInitRieltprofCommercial(\Rieltprof\Model\Orm\Commercial $orm)
    {
        $orm->getPropertyIterator()->append(array(
            t('Основные'),
                'controller' => new Type\Varchar([
                    'description' => t('Контроллер'),
                    'visible' => false
                ]),
                'actual_on_date' => new Type\Date([
                    'description' => t('актуально на дату')
                    // При добавлении = дата создания
                ]),
                'cost_product' => new Type\Integer([
                    'description' => t('Цена'),
                    'maxLength' => 15,
                    'Checker' => ['chkEmpty', t('Укажите цену')],
                    'rentVisible' => false
                ]),
                'cost_rent' => new Type\Integer([
                    'description' => t('Цена за мес.'),
                    'Checker' => ['chkEmpty', t('Укажине Цену в мес.')],
                    'saleVisible' => false,
                ]),
                'cost_one' => new Type\Integer([
                    'description' => t('Цена за 1 кв. метр'),
                    'visible' => false
                ]),
                'note' => new Type\Text(array(
                    'description' => t('Примечание'),
                    'Attr' => array(array('rows' => 3, 'cols' => 80)),
                )),
                'personal_note' => new Type\Text(array(
                    'description' => t('Личные пометки (будут видны только Вам)'),
                    'Attr' => array(array('rows' => 3, 'cols' => 80)),
                )),
                'owner' => new Type\Integer([
                    'description' => t('Владелец объявления'),
                    'visible' => false
                ]),
                'object' => new Type\Varchar([
                    'description' => t('Объект'),
                    'visible' => false
                ]),
            t('Локация'),
                'country' => new Type\Varchar(array(
                    'description' => t('Страна'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Россия')
                )),
                'region' => new Type\Varchar(array(
                    'description' => t('Край, область'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Краснодарский край')
                )),
                'city' => new Type\Varchar(array(
                    'description' => t('Населенный пункт'),
                    'maxLength' => 255,
                    'visible' => false,
                    'default' => t('Краснодар')
                )),
                'county' => new Type\ArrayList(array(
                    'description' => t('Округ'),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getCountyList'], [0 => 'Не указано']),
                    'runtime' => true
    //                    'Checker' => ['chkEmpty', t('Заполните поле Округ')]
                )),
                '_county' => new Type\Varchar([
                    'description' => t('Округ (serialize)'),
                    'visible' => false
                ]),
                'district' => new Type\ArrayList(array(
                    'description' => t('Район'),
                    'Attr' => array(array('size' => 5)),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getDistrictList'], [0 => 'Не выбрано']),
                    'runtime' => true,
                    'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле Район')],
                )),
                '_district' => new Type\Varchar([
                    'description' => t('Район (serialize)'),
                    'visible' => false
                ]),
                'street' => new Type\Varchar([
                    'description' => t('Улица'),
                    'maxLength' => 255
                ]),
                'house' => new Type\Integer([
                    'description' => t('Дом'),
                    'maxLength' => 3
                ]),
                'liter' => new Type\Varchar([
                    'description' => t('Литер/Корпус'),
                ]),
            t('Параметры'),
                'rooms' => new Type\Integer([
                    'description' => t('Количество комнат'),
                    'maxLength' => 2,
                ]),
                'rooms_isolated' => new Type\Integer([
                    'description' => t('Все комнаты изолированы'),
                    'CheckBoxView' => array(1,0),
                    'default' => 1
                ]),
                'split_wc' => new Type\Integer([
                    'description' => t('Раздельный санузел'),
                    'CheckBoxView' => array(1,0),
                    'default' => 1
                ]),
                'material' => new Type\Integer([
                    'description' => t('Материал стен'),
                    'list' => array(['\Rieltprof\Model\ParamsApi', 'getMaterialList'], [0 => 'Не выбрано']),
                ]),
                '_material' => new Type\Varchar([
                    'description' => t('Материал стен (serialize)'),
                    'visible' => false
                ]),
                'year' => new Type\Integer([
                    'description' => t('Год постройки'),
                    'maxLength' => 4
                ]),
                'state' => new Type\Integer([
                    'description' => t('Состояние'),
                    'list' => array(array('\Rieltprof\Model\ParamsApi', 'getStateList'), [0 => 'Не выбрано']),
//                    'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Состояние')]
                ]),
                '_state' => new Type\Varchar([
                    'description' => t('Состояние (serialize)'),
                    'visible' => false
                ]),
                'square' => new Type\Double([
                    'description' => t('Площадь'),
                    'maxLength' => 11,
                    'decimal' => 3,
                    'allowEmpty' => false,
                    'Checker' => ['chkEmpty', t('Заполните поле Площадь')]
//                    'checker' => [['\Rieltprof\Model\ParamsApi', 'checkSquare'], t('поле Площадь')]
                ]),
                'flat' => new Type\Integer([
                    'description' => t('Этаж'),
                    'maxLength' => 3,
//                    'Checker' => ['chkEmpty', t('Заполните поле Этаж')]
//                    'checker' => [['\Rieltprof\Model\ParamsApi', 'checkFlat'], t('поле Этаж')]
                ]),
                'flat_house' => new Type\Integer([
                    'description' => t('Этажность дома'),
                    'maxLength' => 3,
//                    'Checker' => ['chkEmpty', t('Укажите Этажность дома')]
//                    'checker' => [['\Rieltprof\Model\ParamsApi', 'checkFlatHouse'], t('поле Этажность дома')]
                ]),
                'is_first' => new Type\Integer([
                    'description' => t('Первый этаж'),
                    'CheckBoxView' => array(1,0),
                    'visible' => false,
                    'default' => 0
                ]),
                'is_last' => new Type\Integer([
                    'description' => t('Последний этаж'),
                    'CheckBoxView' => array(1,0),
                    'visible' => false,
                    'default' => 0
                ]),
            t('Дополнительные'),
                'quickly' => new Type\Integer([
                    'description' => t('Срочно'),
                    'CheckBoxView' => array(1,0),
                    'default' => 0,
                ]),
                'mark' => new Type\Integer([
                    'description' => t('Закладку можно'),
                    'CheckBoxView' => array(1,0),
                    'default' => 0,
                ]),
                'only_cash' => new Type\Integer([
                    'description' => t('Только наличные'),
                    'template' => '%rieltprof%/form/catalog/only_cash.tpl',
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'mortgage' => new Type\Integer([
                    'description' => t('Ипотеку рассматриваем'),
                    'CheckBoxView' => [1,0],
                    'visible' => false,
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'breakdown' => new Type\Integer([
                    'description' => t('Нужна разбивка по сумме'),
                    'CheckBoxView' => [1,0],
                    'rentVisible' => false
                ]),
                'encumbrance' => new Type\Integer([
                    'description' => t('Обременение банка'),
                    'template' => '%rieltprof%/form/catalog/encumbrance.tpl',
                    'default' => 0,
                    'rentVisible' => false
                ]),
                'encumbrance_notice' => new Type\Varchar([
                    'description' => t('Банк, Сумма'),
                    'visible' => false,
                    'maxLength' => 255
                ]),
                'remodeling' => new Type\Integer([
                    'description' => t('Перепланировка'),
                    'CheckBoxView' => [1, 0],
                    'maxLength' => 1,
                    'rentVisible' => false,
                    'default' => 0,
                    'template' => '%rieltprof%/form/catalog/remodeling.tpl'
                ]),
                'remodeling_legalized' => new Type\Integer([
                    'description' => t('Перепланировка узаконена?'),
                    'CheckBoxView' => [1,0],
                    'visible' => false,
                    'rentVisible' => false
                ]),
                'exclusive' => new Type\Integer([
                    'description' => t('Эксклюзив чистый'),
                    'default' => 0,
                    'template' => '%rieltprof%/form/catalog/exclusive.tpl'
                ]),
                'advertise' => new Type\Integer([
                    'description' => t('От себя рекламирую в интернете'),
                    'CheckBoxView' => [1, 0],
                    'visible' => false
                ])
        ));
    }
    public static function ormInitRieltprofNewbuilding(\Rieltprof\Model\Orm\NewBuilding $orm)
    {
        $orm->getPropertyIterator()->append(array(
            t('Основные'),
            'controller' => new Type\Varchar([
                'description' => t('Контроллер'),
                'visible' => false
            ]),
            'actual_on_date' => new Type\Date([
                'description' => t('актуально на дату')
                // При добавлении = дата создания
            ]),
            'cost_product' => new Type\Integer([
                'description' => t('Цена'),
                'maxLength' => 15,
                'Checker' => ['chkEmpty', t('Укажите цену')],
                'rentVisible' => false
            ]),
            'cost_rent' => new Type\Integer([
                'description' => t('Цена за мес.'),
                'Checker' => ['chkEmpty', t('Укажине Цену в мес.')],
                'saleVisible' => false,
            ]),
            'cost_one' => new Type\Integer([
                'description' => t('Цена за 1 кв. метр'),
                'visible' => false
            ]),
            'note' => new Type\Text(array(
                'description' => t('Примечание'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'personal_note' => new Type\Text(array(
                'description' => t('Личные пометки (будут видны только Вам)'),
                'Attr' => array(array('rows' => 3, 'cols' => 80)),
            )),
            'owner' => new Type\Integer([
                'description' => t('Владелец объявления'),
                'visible' => false
            ]),
            'object' => new Type\Varchar([
                'description' => t('Объект'),
                'visible' => false
            ]),
            t('Локация'),
            'country' => new Type\Varchar(array(
                'description' => t('Страна'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Россия')
            )),
            'region' => new Type\Varchar(array(
                'description' => t('Край, область'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Краснодарский край')
            )),
            'city' => new Type\Varchar(array(
                'description' => t('Населенный пункт'),
                'maxLength' => 255,
                'visible' => false,
                'default' => t('Краснодар')
            )),
            'county' => new Type\ArrayList(array(
                'description' => t('Округ'),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getCountyList'], [0 => 'Не указано']),
                'runtime' => true
//                    'Checker' => ['chkEmpty', t('Заполните поле Округ')]
            )),
            '_county' => new Type\Varchar([
                'description' => t('Округ (serialize)'),
                'visible' => false
            ]),
            'district' => new Type\ArrayList(array(
                'description' => t('Район'),
                'Attr' => array(array('size' => 5)),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getDistrictList'], [0 => 'Не выбрано']),
                'runtime' => true,
                'Checker' => [['\Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле Район')],
            )),
            '_district' => new Type\Varchar([
                'description' => t('Район (serialize)'),
                'visible' => false
            ]),
            'street' => new Type\Varchar([
                'description' => t('Улица'),
                'maxLength' => 255
            ]),
            'house' => new Type\Integer([
                'description' => t('Дом'),
                'maxLength' => 3
            ]),
            'liter' => new Type\Varchar([
                'description' => t('Литер/Корпус'),
            ]),
            t('Параметры'),
            'rooms_list' => new Type\ArrayList([
                'description' => t('Количество комнат'),
                'list' => [['Rieltprof\Model\ParamsApi', 'getRoomsList'], [0 => 'Не выбрано']],
                'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Количество комнат')]
            ]),
            '_rooms_list' => new Type\Varchar([
                'description' => t('Количество комнат (serialize)'),
                'visible' => false
            ]),
            'rooms_isolated' => new Type\Integer([
                'description' => t('Все комнаты изолированы'),
                'CheckBoxView' => array(1,0),
                'default' => 1
            ]),
            'split_wc' => new Type\Integer([
                'description' => t('Раздельный санузел'),
                'CheckBoxView' => array(1,0),
                'default' => 1
            ]),
            'material' => new Type\Integer([
                'description' => t('Материал стен'),
                'list' => array(['\Rieltprof\Model\ParamsApi', 'getMaterialList'], [0 => 'Не выбрано']),
//                'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Состояние')]
            ]),
            '_material' => new Type\Varchar([
                'description' => t('Материал стен (serialize)'),
                'visible' => false
            ]),
            'year' => new Type\Integer([
                'description' => t('Год постройки'),
                'maxLength' => 4
            ]),
            'state' => new Type\Integer([
                'description' => t('Состояние'),
                'list' => array(array('\Rieltprof\Model\ParamsApi', 'getStateList'), [0 => 'Не выбрано']),
//                'Checker' => [['Rieltprof\Model\ParamsApi', 'checkEmpty'], t('Заполните поле - Состояние')]
            ]),
            '_state' => new Type\Varchar([
                'description' => t('Состояние (serialize)'),
                'visible' => false
            ]),
            'square' => new Type\Double([
                'description' => t('Площадь'),
                'maxLength' => 11,
                'decimal' => 3,
                'allowEmpty' => false,
                'Checker' => ['chkEmpty', t('Заполните поле Площадь')]
//                'checker' => [['Rieltprof\Model\ParamsApi', 'checkSquare'], t('поле Площадь')]
            ]),
            'square_kitchen' => new Type\Double([
                'description' => t('Площадь кухни'),
                'maxLength' => 11,
                'decimal' => 3,
//                'checker' => [['Rieltprof\Model\ParamsApi', 'checkFlatSquareKitchen'], t('Заполните поле Площадь кухни')]
            ]),
            'square_living' => new Type\Double([
                'description' => t('Жилая площадь'),
                'maxLength' => 11,
//                'checker' => [['Rieltprof\Model\ParamsApi', 'checkFlatSquareKitchen'], t('Заполните поле Жилая площадь')]
            ]),
            'flat' => new Type\Integer([
                'description' => t('Этаж'),
                'maxLength' => 3,
//                'Checker' => ['chkEmpty', t('Заполните поле Этаж')]
//                'checker' => [['Rieltprof\Model\ParamsApi', 'checkFlat'], t('поле Этаж')]
            ]),
            'flat_house' => new Type\Integer([
                'description' => t('Этажность дома'),
                'maxLength' => 3,
//                'Checker' => ['chkEmpty', t('Укажите Этажность дома')]
//                'checker' => [['Rieltprof\Model\ParamsApi', 'checkFlatHouse'], t('поле Этажность дома')]
            ]),
            'is_first' => new Type\Integer([
                'description' => t('Первый этаж'),
                'CheckBoxView' => array(1,0),
                'visible' => false,
                'default' => 0
            ]),
            'is_last' => new Type\Integer([
                'description' => t('Последний этаж'),
                'CheckBoxView' => array(1,0),
                'visible' => false,
                'default' => 0
            ]),
            t('Дополнительные'),
            'quickly' => new Type\Integer([
                'description' => t('Срочно'),
                'CheckBoxView' => array(1,0),
                'default' => 0,
            ]),
            'mark' => new Type\Integer([
                'description' => t('Закладку можно'),
                'CheckBoxView' => array(1,0),
                'default' => 0,
            ]),
            'only_cash' => new Type\Integer([
                'description' => t('Только наличные'),
                'template' => '%rieltprof%/form/catalog/only_cash.tpl',
                'default' => 0,
                'rentVisible' => false
            ]),
            'mortgage' => new Type\Integer([
                'description' => t('Ипотеку рассматриваем'),
                'CheckBoxView' => [1,0],
                'visible' => false,
                'default' => 0,
                'rentVisible' => false
            ]),
            'breakdown' => new Type\Integer([
                'description' => t('Нужна разбивка по сумме'),
                'CheckBoxView' => [1,0],
                'rentVisible' => false
            ]),
            'encumbrance' => new Type\Integer([
                'description' => t('Обременение банка'),
                'template' => '%rieltprof%/form/catalog/encumbrance.tpl',
                'default' => 0,
                'rentVisible' => false
            ]),
            'encumbrance_notice' => new Type\Varchar([
                'description' => t('Банк, Сумма'),
                'visible' => false,
                'maxLength' => 255
            ]),
            'child' => new Type\Integer([
                'description' => t('Несовершеннолетние дети/опека'),
                'CheckBoxView' => [1, 0],
                'maxLength' => 1,
                'rentVisible' => false
            ]),
            'exclusive' => new Type\Integer([
                'description' => t('Эксклюзив чистый'),
                'default' => 0,
                'template' => '%rieltprof%/form/catalog/exclusive.tpl'
            ]),
            'advertise' => new Type\Integer([
                'description' => t('От себя рекламирую в интернете'),
                'CheckBoxView' => [1, 0],
                'visible' => false
            ])
        ));
    }

    public static function controllerExecRieltprofAdminRoomCtrlAdd(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Room();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminRoomCtrlEdit(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Room();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminFlatCtrlAdd(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Flat();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminFlatCtrlEdit(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Flat();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminHouseCtrlAdd(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\House();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminHouseCtrlEdit(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\House();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminTownhouseCtrlAdd(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\TownHouse();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminTownhouseCtrlEdit(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\TownHouse();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminCountryhouseCtrlAdd(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\CountryHouse();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminCountryhouseCtrlEdit(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\CountryHouse();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminDuplexCtrlAdd(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Duplex();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminDuplexCtrlEdit(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Duplex();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminGarageCtrlAdd(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Garage();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminGarageCtrlEdit(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Garage();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminPlotCtrlAdd(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Plot();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminPlotCtrlEdit(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Plot();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminCommercialCtrlAdd(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Commercial();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminCommercialCtrlEdit(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\Commercial();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminNewbuildingCtrlAdd(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\NewBuilding();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }
    public static function controllerExecRieltprofAdminNewbuildingCtrlEdit(\RS\Controller\Admin\Helper\CrudCollection $helper)
    {
        $url = \RS\Http\Request::commonInstance();
        $action = $url->request('action', TYPE_STRING);
        $orm = new \Rieltprof\Model\Orm\NewBuilding();
        if($action == 'sale'){
            $helper->setFormSwitch('sale');
            $orm['__cost_rent']->removeAllCheckers();
        }
        if($action == 'rent'){
            $helper->setFormSwitch('rent');
            $orm['__cost_product']->removeAllCheckers();
        }
    }

    public static function ormAfterWriteCatalogProduct($params)
    {
        $flag = $params['flag'];
        $orm = $params['orm'];
        if($flag == 'insert'){
            $id = $orm['id'];
            $orm['alias'] = 'object_'. $id;
            $orm->update();
        }
    }
    public static function ormAfterLoadCatalogProduct($params)
    {
        $orm = $params['orm'];
        $orm['district'] = @unserialize($orm['_district']);
    }

    public static function getMenus($items)
    {
        $items[] = [
            'title' => t('Партнеры'),
            'alias' => 'partners',
            'link' => '%ADMINPATH%/rieltprof-partnersctrl/',
            'sortn' => 101,
            'parent' => 0,
            'typelink' => 'link'
        ];
        $items[] = array(
            'title' => t('Черный список'),
            'alias' => 'blacklist',
            'link' => '%ADMINPATH%/rieltprof-blacklistctrl/',
            'typelink' => 'link',
            'parent' => 0,
            'sortn' => 102
        );
        return $items;
    }

    public static function ormAfterWriteUsersUser($params)
    {
        /**
         * @var \Users\Model\Orm\User $orm
         */
        $orm = $params['orm'];
        $flag = $params['flag'];
        if($flag == 'insert'){
            $orm->linkGroup(['rieltor']);
            $orm['access'] = 0;
            $orm->update();
        }
    }

    /**
     * Расширение объектов
     *
     */
    public static function initialize()
    {
        // Расширяет класс \Users\Model\Orm\User
        \Users\Model\Orm\User::attachClassBehavior(new \Rieltprof\Model\Behavior\UsersUser());
        \Catalog\Model\Orm\Product::attachClassBehavior(new \Rieltprof\Model\Behavior\CatalogProduct());
        \Catalog\Model\Orm\Dir::attachClassBehavior(new \Rieltprof\Model\Behavior\CatalogDir());

    }

    /**
     * Добавляем переключатель - Допущен к поиску на сайте - в список учетных записей
     */
    public static function controllerExecUsersAdminCtrlIndex(\RS\Controller\Admin\Helper\CrudCollection $controller)
    {
        /**
         * @var \RS\Html\Table\Element $table
         */
        $current_user = \RS\Application\Auth::getCurrentUser();
        if($current_user->inGroup('admins')){
            $table = $controller['table']->getTable();
            $columns = $table->getColumns();
            $last_column = array_pop($columns);
            $columns[] = new \RS\Html\Table\Type\Userfunc('access', t('Допущен к поиску'), function($value, $field){
                /**
                 * @var \Users\Model\Orm\User $user
                 */
                $user = $field->getRow();
                $user_id = $user['id'];
                if($user['access']){
                    $switch = 'on';
                }
                else{
                    $switch = '';
                }
                return '<div class="toggle-switch rs-switch crud-switch '.$switch.'" data-url="/admin/rieltprof-tools/?id='.$user_id.'&do=AjaxToggleUserAllowedToSite">
                            <label class="ts-helper"></label>
                        </div>';
            });
            $columns[] = new \RS\Html\Table\Type\Text('admin_comment', t('Коммент. Админа'));
            $columns[] = $last_column;
            $table->setColumns($columns);
        }
    }

    /**
     * Расширям объект User
     * @param \Users\Model\Orm\User $data
     */
    public static function ormInitUsersUser(\Users\Model\Orm\User $data)
    {
        $data->getPropertyIterator()->append(array(
            'access' => new Type\Integer(array(
                'description' => t('Доступ к поиску'),
                'maxLength' => 1,
                'CheckBoxView' => array(1, 0),
                'default' => 0
            )),
            t('Фото'),
                'photo' => new \RS\Orm\Type\Image(array(
                    'description' => t('Фото'),
                    'max_file_size'    => 10000000, //Максимальный размер - 10 Мб
                    'allow_file_types' => array('image/pjpeg', 'image/jpeg', 'image/png', 'image/gif'),//Допустимы форматы jpg, png, gif
                    'checker' => [['\Users\Model\Orm\User', 'checkUserPhotoField'], t('Загрузите ваше фото')],
                )),
            t('Рейтинг'),
                'rating' => new \RS\Orm\Type\Double([
                    'description' => t('Рейтинг риелтора'),
                    'default' => 0
                ]),
                'review_num' => new \RS\Orm\Type\Integer([
                    'description' => t('Количество отзывов')
                ]),
                'balls' => new Type\Integer([
                    'description' => t('Количство балов')
                ]),
            // вывести в список пользователей
            t('Комментарий админа'),
                'admin_comment' => new \RS\Orm\Type\Text([
                    'description' => t('Комментарий админа'),
                    'Attr' => array(array('rows' => 3, 'cols' => 80)),
                ])
        ));
    }

    public static function start()
    {
        $user = \RS\Application\Auth::getCurrentUser();
        $request_uri = strtok($_SERVER['REQUEST_URI'], '?');
        //Если пользователь не является супервизором - то не может войти в админку
        // if(!$user->inGroup('supervisor') && ($request_uri == '/admin/' || $request_uri == '/admin')){
        //     \RS\Application\Application::getInstance()->redirect('/');
        // }
        $can_access_page = ($request_uri == '/register/' || $request_uri == '/auth/' || \RS\Router\Manager::obj()->isAdminZone());
        if (!$can_access_page){
            if(!\RS\Application\Auth::isAuthorize() || !$user['access']) {
                \RS\Application\Application::getInstance()->redirect('/auth/');
            }
        }
        if($request_uri == '/auth/' && \RS\Application\Auth::isAuthorize() && $user['access']){
            \RS\Application\Application::getInstance()->redirect('/');
        }
    }

    /**
     * Возвращает маршруты данного модуля. Откликается на событие getRoute.
     * @param array $routes - массив с объектами маршрутов
     * @return array of \RS\Router\Route
     */
    public static function getRoute(array $routes)
    {
        $routes[] = new \RS\Router\Route('users-front-register', '/register/', array(
            'controller' => 'rieltprof-front-register'
        ), t('Регистрация пользователя'));
        $routes[] = new \RS\Router\Route('users-front-auth', '/auth/', array(
            'controller' => 'rieltprof-front-auth'
        ), t('Регистрация пользователя'));
        $routes[] = new \RS\Router\Route('rieltprof-front-ownerprofile', '/owner-profile/{id}/', null, t('Данные владельца объявления'));
        $routes[] = new \RS\Router\Route('rieltprof-front-myreview', '/my-review/', null, t('Мои отзывы'));
        $routes[] = new \RS\Router\Route('rieltprof-front-ownerreview', '/owner-review/{id}', null, t('Отзывы о владельце объявления'));
        $routes[] = new \RS\Router\Route('rieltprof-front-blacklist', '/blacklist/', null, t('Проверить контакт'));
        return $routes;
    }

}
