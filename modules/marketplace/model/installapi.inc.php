<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Marketplace\Model;

use RS\Config\Loader as ConfigLoader;
use RS\Event\Exception as EventException;
use RS\Exception as RSException;
use RS\File\Tools as FileTools;
use RS\Http\Request as HttpRequest;
use RS\Module\AbstractModel\BaseModel;
use RS\Module\Item;
use RS\Router\Manager;

/**
 * Класс содержит методы, необходимые для установки дополнений
 */
class InstallApi extends BaseModel
{
    private $addonArchiveApi;

    public function __construct()
    {
        $this->addonArchiveApi = new AddonArchiveApi();
    }

    /**
     * @param $archive_file_path
     * @return bool
     * @throws EventException
     * @throws RSException
     */
    public function installAddon($archive_file_path)
    {
        $addon_type = $this->addonArchiveApi->getAddonType($archive_file_path);
        if (!$addon_type) {
            foreach ($this->addonArchiveApi->getErrors() as $error) $this->addError($error);
            return false;
        }

        if ($addon_type === 'module')
            return $this->installModule($archive_file_path);
        else if ($addon_type === 'template')
            return $this->installTemplate($archive_file_path);
        else if ($addon_type === 'solution')
            return $this->installSolution($archive_file_path);

        return false;
    }

    /**
     * @param $archive_file_path
     * @return bool
     * @throws EventException
     * @throws RSException
     */
    public function installModule($archive_file_path)
    {
        $zip = new \ZipArchive();
        $zip->open($archive_file_path);
        $first_folder_stat = $zip->statIndex(0);
        $mod_name = basename($first_folder_stat['name']);

        $is_module_exists_before = \RS\Module\Manager::staticModuleExists($mod_name);
        if ($is_module_exists_before) {
            if (ConfigLoader::byModule($mod_name)->isLicenseUpdateExpired()) {
                return $this->addError(t('Модуль уже установлен, вы можете обновить модуль через "Центр обновления"'));
            }
        }

        $ok = $zip->extractTo(\Setup::$PATH . \Setup::$MODULE_FOLDER);
        if (!$ok) {
            return $this->addError(t('Не удалось распаковать архив в папку модулей'));
        }

        $mod_item = new Item($mod_name);
        $ok_or_errors = $mod_item->install();
        if ($ok_or_errors !== true) {
            foreach ($ok_or_errors as $error) $this->addError($error);

            if ($mod_name && !$is_module_exists_before) {
                //Если модуля до этого не было в системе, то удаляем его,
                //так как он не смог корректно установиться
                FileTools::deleteFolder(\Setup::$PATH . \Setup::$MODULE_FOLDER . '/' . $mod_name);
            }

            return false;
        }

        // Проверяем на на отсутсвие фатальных ошибок после установки модуля
        if (!$this->checkSiteForFatalErrors()) {
            // Отключаем модуль
            $config = ConfigLoader::byModule($mod_name);
            $config['enabled'] = 0;
            $config->update();

            return $this->addError(t('После установки модуля на сайте возникла фатальная ошибка. Модуль отключен'));
        }

        return true;
    }

    /**
     * Установка темы оформления
     *
     * @param $archive_file_path
     * @return bool
     */
    public function installTemplate($archive_file_path)
    {
        if ($this->addonArchiveApi->hasError()) {
            $errors = $this->addonArchiveApi->getErrors();
            foreach ($errors as $error) $this->addError($error);
            return false;
        }

        // Распаковка
        $zip = new \ZipArchive();
        $zip->open($archive_file_path);
        $ok = $zip->extractTo(\Setup::$SM_TEMPLATE_PATH);
        if (!$ok) {
            return $this->addError(t('Не удалось распаковать архив в папку тем оформления'));
        }

        return true;
    }

    /**
     * @param $archive_file_path
     * @return bool
     * @throws EventException
     * @throws RSException
     */
    public function installSolution($archive_file_path)
    {
        return $this->installModule($archive_file_path);
    }

    /**
     * Проверка сайта на базовую работоспособность - на предмет отсуствия фатальных ошибок
     * Делает запрос на локальный URL, ожидая определенного ответа.
     * Если ответ не соотвествует ожиданиям, предполагается что произошла ошибка
     * @return bool
     */
    private function checkSiteForFatalErrors()
    {
        $host = HttpRequest::commonInstance()->getSelfAbsoluteHost();
        $url = $host . Manager::obj()->getUrl('marketplace-front-checkforfatal', []);

        $context = [
            'ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        $json = file_get_contents($url, false, stream_context_create($context));
        $res = json_decode($json);
        return isset($res->success) && $res->success;
    }
}
