<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Feedback\Model;

use Feedback\Model\Orm\FormFieldItem;
use Feedback\Model\Orm\FormItem;
use Feedback\Model\Orm\ResultItem;
use RS\Application\Auth as AppAuth;
use RS\Captcha\Manager as CaptchaManager;
use RS\Event\Exception as EventException;
use RS\Event\Manager as EventManager;
use RS\File\Tools as FileTools;
use RS\Helper\Mailer;
use RS\Helper\PhpMailer\phpmailerException;
use RS\Http\Request as HttpRequest;
use RS\Module\AbstractModel\EntityList;
use RS\Orm\Exception as OrmException;
use RS\Site\Manager as SiteManager;

class FormApi extends EntityList
{
    public $uniq;

    function __construct()
    {
        parent::__construct(new FormItem(), [
            'nameField' => 'title',
            'multisite' => true,
            'defaultOrder' => 'title',
        ]);
    }

    /**
     * Сохраняет результат формы в объект
     *
     * @param FormItem $form - ORM объект
     * @param mixed $form_fields - массив значений
     * @return ResultItem
     * @throws EventException
     */
    function saveResult(FormItem $form, array $form_fields)
    {
        $form_result = new ResultItem();
        $form_result['site_id'] = SiteManager::getSiteId();
        $form_result['dateof'] = date('Y-m-d H:i:s');
        $form_result['form_id'] = $form['id'];
        $form_result['ip'] = $_SERVER['REMOTE_ADDR'];
        $form_result['sending_url'] = HttpRequest::commonInstance()->getSelfUrl();
        $form_result['stext'] = serialize($form_fields);
        $form_result->insert();

        return $form_result;
    }

    /**
     * Отправляет ответ пользователю на его обращение в форму обратной связи
     *
     * @param string $to_email - на чей E-mail отправляется
     * @param string $answer - тело ответа
     * @throws phpmailerException
     */
    function sendAnswer($to_email, $answer)
    {
        $site = SiteManager::getSite();

        $mailer = new Mailer();
        $mailer->Subject = $site['title'] . t('. Ответ на форму обратной связи');
        $mailer->addAddress($to_email);
        $mailer->isHTML(false);
        $mailer->Body = $answer;
        $mailer->send();
    }

    /**
     * Отправяет форму на E-mail пользователя по определённому шаблону
     *
     * @param FormItem $form - форма для отправки
     * @param HttpRequest $request - то что пришло из POST для отправки
     * @return boolean
     * @throws EventException
     */
    function send(FormItem $form, HttpRequest $request)
    {
        $fields = $form->getFields(); //Получим поля существующие формы
        $captchaUsed = false;

        if ($form['use_csrf_protection']) {
            $request->checkCsrf();
        }

        $mail = [];
        foreach ($fields as $k => $form_field) {
            /** @var FormFieldItem $form_field */
            switch ($form_field['show_type']) {
                case FormFieldItem::SHOW_TYPE_EMAIL: //Если E-mail
                    $value = trim($request->post($form_field['alias'], TYPE_STRING));
                    //Проверим условие

                    if (!filter_var($value, FILTER_VALIDATE_EMAIL) && ($form_field['required'] == 1)) {  //Если поле пустое и обязательное
                        $this->addError(t('Поле %0 заполнено не верно', [$form_field['title']]), $form_field['alias']);
                    }

                    break;
                case FormFieldItem::SHOW_TYPE_FILE: //Если файл
                    $value = $request->files($form_field['alias']);
                    $text_error = "";
                    // Если файл не загружен, и это не обязательное поле, то проскакиваем
                    if (!$value['name'] && !$form_field['required']) {
                        unset($fields[$k]);
                        break;
                    }
                    if (((empty($value) && $form_field['required']) || $text_error = FileTools::checkUploadError($value['error']))) { //Проверим есть ли ошибки при загрузке
                        $this->addError(t('Загрузка файла не удалась. ') . $text_error, $form_field['alias']);
                    } else {
                        $fileinfo = pathinfo($value['name']); //Получим информацию о файле
                        //Проверим допустимые форматы
                        if (!$form_field->checkFilekExtension($fileinfo['extension'])) {
                            $this->addError(t('Загрузка файла не удалась. Расширение файла должно быть ' . $form_field['file_ext']), $form_field['alias']);
                        }
                        //Проверим размер максимальный для загрузки
                        if (!$form_field['file_size'] > $value['size']) {
                            $this->addError(t('Загрузка файла не удалась. Размер файла слишком большой для поля ' . $form_field['title']), $form_field['alias']);
                        }
                        if (!$this->hasError()) { //Если нет ошибок, то сохраним файл
                            $saved_file = $this->saveAttachedFile($value);
                            $value['real_file_name'] = $saved_file;
                            $value['real_file_name_absolute'] = SiteManager::getSite()->getAbsoluteUrl($saved_file);
                        }
                    }

                    break;
                case FormFieldItem::SHOW_TYPE_CAPTCHA:
                    $captchaUsed = true;
                    $value = trim($request->post($form_field['alias'], TYPE_STRING));
                    $captcha = CaptchaManager::currentCaptcha();
                    $captcha_context = 'form_' . $form_field['form_id'];
                    if (!$captcha->check($value, $captcha_context)) {
                        $this->addError($captcha->errorText(), $form_field['alias']);
                    }
                    break;
                case FormFieldItem::SHOW_TYPE_LIST:
                    $value = $request->post($form_field['alias'], TYPE_MIXED);
                    if (empty($value) && ($form_field['required'] == 1)) {  //Если поле пустое и обязательное
                        $this->addError(t('Поле "%0" не заполнено', [$form_field['title']]), $form_field['alias']);
                    }
                    break;
                default:  //По умолчанию если не файл и не Email
                    $value = trim($request->post($form_field['alias'], TYPE_STRING));

                    if (empty($value) && ($form_field['required'] == 1)) {  //Если поле пустое и обязательное
                        $this->addError(t('Поле "%0" не заполнено', [$form_field['title']]), $form_field['alias']);
                    }
                    //Если нужна валидация регулярным выражением
                    if (!empty($form_field['use_mask'])) {
                        if (!preg_match('%' . $form_field['mask'] . '%', $value)) { //Если не прошло валидацию
                            if ($form_field['error_text']) {
                                $this->addError($form_field['error_text'], $form_field['alias']);
                            } else {
                                $this->addError(t('Поле "%0" не прошло проверку', [$form_field['title']]), $form_field['alias']);
                            }

                        }
                    }
                    break;
            }

            $mail[] = [
                'field' => $form_field,
                'value' => $value
            ];

            $form_field->setValue($value);
        }

        //Если кто-то пытается слать без каптчи, а она требуется
        if (!$captchaUsed && ($form['use_captcha'] == "1") && !AppAuth::isAuthorize()) {
            foreach ($fields as $form_field) {
                if ($form_field['show_type'] == FormFieldItem::SHOW_TYPE_CAPTCHA) {
                    $this->addError(t('Требуется проверка защитного кода'), $form_field['alias']);
                }
            }
        }

        if ($this->hasError()) return false;

        $event_result = EventManager::fire('feedback.setvalues', [
            'values' => $mail
        ]);

        list($mail) = $event_result->extract();

        //Сохраним сведения о том, что мы отправляем письмо админку
        $result_item = $this->saveResult($form, $mail);

        //Отправим наше письмо
        $mail = new MailItem($form, $mail, $result_item['id']);
        if (!$mail->send()) {
            return $this->addError(t('Не удается отправить письмо'));
        }
        return true;
    }

    /**
     * Сохраняет файл на жёсткий диск, который был загружен
     *
     * @param array $file_info - массив загруженного файла
     * @return string
     */
    function saveAttachedFile($file_info)
    {
        $dir_to_save = \Setup::$PATH . \Setup::$STORAGE_DIR . "/files";
        if (!file_exists($dir_to_save)) { //Если папка е существут, создадим её
            FileTools::makePath($dir_to_save);
        }

        $fileinfo = pathinfo($file_info['name']); //Получим информацию о файле
        $file_name = md5($file_info['name'] . time()) . "." . $fileinfo['extension'];
        $file = $dir_to_save . "/" . $file_name;

        //Пересадим файл в нужную папку
        @copy($file_info['tmp_name'], $file);
        return \Setup::$STORAGE_DIR . "/files/" . $file_name;
    }
}
