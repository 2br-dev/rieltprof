<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Site\Model\Orm;

use RS\Cache\Cleaner as CacheCleaner;
use RS\Http\Request as HttpRequest;
use RS\Orm\OrmObject;
use RS\Orm\Request as OrmRequest;
use RS\Orm\Type;
use RS\Theme\Item as ThemeItem;
use Site\Model\Api as SiteApi;
use Site\Model\RobotsTxtApi;

/**
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $title Краткое название сайта
 * @property string $full_title Полное название сайта
 * @property string $domains Доменные имена (через запятую)
 * @property string $folder Папка сайта
 * @property string $language Язык
 * @property integer $default По умолчанию
 * @property integer $update_robots_txt Обновить robots.txt
 * @property integer $redirect_to_main_domain Перенаправлять на основной домен
 * @property integer $redirect_to_https Перенаправлять на https
 * @property integer $sortn Сортировка
 * @property string $theme Тема
 * @property integer $is_closed Закрыть доступ к сайту
 * @property string $close_message Причина закрытия сайта
 * @property float $rating Средний балл(рейтинг)
 * @property integer $comments Кол-во комментариев к сайту
 * --\--
 */
class Site extends OrmObject
{
    protected static $table = 'sites';

    public function _init()
    {
        parent::_init();

        $this->getPropertyIterator()->append([
            'title' => new Type\Varchar([
                'checker' => [
                    'chkEmpty',
                    t('Укажите название')
                ],
                'description' => t('Краткое название сайта')
            ]),
            'full_title' => new Type\Varchar([
                'description' => t('Полное название сайта'),
                'hint' => t('Будет использовано в подписи почтовых уведомлений. Например: "Интернет-магазин модной женской одежды"')
            ]),
            'domains' => new Type\Text([
                'description' => t('Доменные имена (через запятую)'),
                'hint' => t('Первый домен в списке - является основным. Именно он используется для построения абсолютных ссылок')
            ]),

            'folder' => new Type\Varchar([
                'description' => t('Папка сайта'),
                'hint' => t('Например: en или version/english'),
                'attr' => [[
                    'size' => 30
                ]]
            ]),
            'language' => new Type\Varchar([
                'description' => t('Язык'),
                'hint' => t('2 английские буквы. Например: en или ru'),
                'attr' => [[
                    'size' => 2
                ]]
            ]),
            'default' => new Type\Integer([
                'description' => t('По умолчанию'),
                'hint' => t('Этот сайт будет открыт, даже если домен не соответствует заявленным'),
                'checkboxview' => [1, 0]
            ]),
            'update_robots_txt' => new Type\Integer([
                'description' => t('Обновить robots.txt'),
                'hint' => t('При установке данного флага, файл robots.txt будет перезаписан, а в .htaccess будут внесены необходимые изменения (при использовании мультисайтовости)'),
                'runtime' => true,
                'checkboxView' => [1, 0]
            ]),
            'redirect_to_main_domain' => new Type\Integer([
                'description' => t('Перенаправлять на основной домен'),
                'allowEmpty' => false,
                'checkboxView' => [1, 0],
                'hint' => t('Если включено, то при обращении к НЕ основному домену будт происходить 301 редирект на основной домен')
            ]),
            'redirect_to_https' => new Type\Integer([
                'description' => t('Перенаправлять на https'),
                'hint' => t('При изменении данной опции, необходимо очистить кэш браузера'),
                'allowEmpty' => false,
                'checkboxView' => [1, 0]
            ]),
            'sortn' => new Type\Integer([
                'description' => t('Сортировка'),
                'visible' => false
            ]),
            'theme' => new Type\Varchar([
                'attr' => [[
                    'readonly' => 'readonly'
                ]],
                'description' => t('Тема'),
                'runtime' => true,
                'template' => '%site%/form/site/theme.tpl'
            ]),
            'is_closed' => new Type\Integer([
                'description' => t('Закрыть доступ к сайту'),
                'hint' => t('Используйте данный флаг, чтобы закрыть доступ к сайту на время его разработки. Администраторы будут иметь доступ как на сайт, так и в административную панель.'),
                'template' => '%site%/form/site/is_closed.tpl',
                'checkboxView' => [1, 0]
            ]),
            'close_message' => new Type\Varchar([
                'description' => t('Причина закрытия сайта'),
                'hint' => t('Будет отображена пользователям'),
                'attr' => [[
                    'placeholder' => t('Причина закрытия сайта')
                ]],
                'visible' => false
            ]),
            'rating' => new Type\Decimal([
                'maxLength' => '3',
                'decimal' => '1',
                'default' => 0,
                'visible' => false,
                'description' => t('Средний балл(рейтинг)'),
                'hint' => t('Расчитывается автоматически, исходя из поставленных оценок')
            ]),
            'comments' => new Type\Integer([
                'maxLength' => '11',
                'description' => t('Кол-во комментариев к сайту'),
                'default' => 0,
                'visible' => false,
            ])
        ]);
    }

    public function beforeWrite($flag)
    {
        if ($flag == self::INSERT_FLAG) {
            $this['sortn'] = OrmRequest::make()
                    ->select('MAX(sortn) as max')
                    ->from($this)
                    ->exec()->getOneField('max', 0) + 1;
        }
        if ($this['default'] == 1) {
            //Сайт по-умолчанию может быть только один
            OrmRequest::make()
                ->update($this)
                ->set([
                    'default' => 0
                ])
                ->exec();
        }
    }

    public function afterWrite($flag)
    {
        if (\Setup::$INSTALLED && $flag == self::INSERT_FLAG) {
            $theme = new ThemeItem($this['theme']);
            $theme->setThisTheme(null, $this['id']);

            SiteApi::copyGroupRights($this);
        }

        if ($this['update_robots_txt']) {
            $robotsTxtApi = new RobotsTxtApi($this);
            $robotsTxtApi->AutoCreateSiteRobotsTxt();
        }

        //Очищаем кэш
        CacheCleaner::obj()->clean(CacheCleaner::CACHE_TYPE_COMMON);
    }

    /**
     * Возвращает список доменов, которые относятся к текущему сайту
     *
     * @return array
     */
    public function getDomainsList()
    {
        return preg_split('/[,\n\s]/', $this['domains'], null, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Возвращает главное доменное имя сайта
     *
     * @return string
     */
    public function getMainDomain()
    {
        $domains = $this->getDomainsList();
        return $domains ? trim($domains[0]) : '';
    }

    /**
     * Возвращает уникальный идентификатор сайта
     *
     * @return string
     */
    public function getSiteHash()
    {
        return sha1($this->getMainDomain() . $this['folder']);
    }

    /**
     * Возвращает ссылку на корень сайта
     *
     * @param bool $absolute - Если true, то будет возвращена абсолютная ссылка, иначе относительная
     * @param bool $add_root_folder - Если true, то приписывает в конце папку, в которой находится скрипт
     * @param bool $force_https - Если true, то всегда возвращается с https, иначе в зависимости от текущего протокола
     *
     * @return string
     */
    public function getRootUrl($absolute = false, $add_root_folder = true, $force_https = false)
    {
        $domain = $this->getMainDomain();

        $folder = !empty($this['folder']) ? '/' . trim($this['folder'], '/') . '/' : '/';
        if ($add_root_folder) {
            $folder = \Setup::$FOLDER . $folder;
        }

        if ($force_https) {
            $protocol = 'https';
        } else {
            $protocol = HttpRequest::commonInstance()->getProtocol();
        }

        return $absolute ? $protocol . '://' . $domain . $folder : $folder;
    }

    /**
     * Формирует абсолютный URL из относительного применительно к текущему сайту
     *
     * @param string $relative_uri
     * @return string
     */
    public function getAbsoluteUrl($relative_uri)
    {
        return $this->getRootUrl(true, false) . ltrim($relative_uri, '/');
    }

    public function delete()
    {
        $remain = OrmRequest::make()->from($this)->count();
        if ($remain == 1) {
            $this->addError(t('Необходимо наличие хотя бы одного сайта'));
            return false;
        }

        $result = parent::delete();
        if ($result) {
            $api = new SiteApi();
            //Удалим всю информацию касающуюся этого сайта 
            $api->deleteSiteNoNeedInfo($this['id']);

            $robots_txt_api = new RobotsTxtApi($this);
            $robots_txt_api->deleteRobotsTxt();
        }
        return true;
    }
}
