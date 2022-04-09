<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace Main\Model\RelCanonical;

use Catalog\Controller\Front\ListProducts;
use RS\Application\Application;
use RS\Http\Request as HttpRequest;
use RS\Module\Manager as ModuleManager;
use RS\Router\Manager as RouterManager;
use SeoControl\Model\Api as SeoControlApi;

/**
 * Класс канонических ссылок по умолчанию
 */
class RelCanonicalRS extends AbstractRelCanonical
{
    /**
     * Возвращает название класса канонических ссылок
     *
     * @return string
     */
    public function getTitle(): string
    {
        return t('По умолчанию');
    }

    /**
     * Возвращает идентификатор класса канонических ссылок
     *
     * @return string
     */
    public function getId(): string
    {
        return 'rs';
    }

    /**
     * Возвращает описание класса канонических ссылок
     *
     * @return string
     */
    public function getDescription(): string
    {
        return t('Всем URL с GET параметрами добавляются канонические ссылки на URL без GET параметров.');
    }

    /**
     * Действия перед рендерингом HTML
     * В этом методе добавляются канонические ссылки
     *
     * @return void
     */
    public function onControllerBeforeWrap(): void
    {
        $request = HttpRequest::commonInstance();
        $relative_url = strtok($request->server('REQUEST_URI'), '?');

        if (ModuleManager::staticModuleExists('seocontrol') && ModuleManager::staticModuleEnabled('seocontrol')) {
            $api = new SeoControlApi();
            $rule = $api->getRuleForUri($request->server('REQUEST_URI'));

            if ($rule && $rule['disable_rel_canonical']) {
                return;
            }
        }

        $router = RouterManager::obj();
        $route = $router->getCurrentRoute();
        if ($route && $route->getId() == 'catalog-front-listproducts') {
            $category = $route->getExtra(ListProducts::ROUTE_EXTRA_CATEGORY);
            $relative_url = $router->getUrl('catalog-front-listproducts', ['category' => $category['_alias']]);
        }

        $url = mb_strtolower(trim($router->getRootUrl(true), '/') . $relative_url);
        Application::getInstance()->addCss($url, 'relcanonical', BP_ROOT, true, [
            'header' => true,
            'rel' => 'canonical',
            'type' => false,
            'media' => false
        ]);
    }
}
