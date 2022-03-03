<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model\PaymentType;

use Partnership\Model\Api;
use phpDocumentor\Reflection\Types\Integer;
use RS\Config\Loader as ConfigLoader;
use RS\Exception as RSException;
use RS\Http\Request as HttpRequest;
use RS\Module\Manager as ModuleManager;
use RS\Orm\FormObject;
use RS\Orm\Type;
use RS\Site\Manager as SiteManager;
use Shop\Model\ChangeTransaction;
use Shop\Model\Orm\Address;
use Shop\Model\Orm\Company;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\Tax;
use Shop\Model\Orm\Transaction;
use Shop\Model\TaxApi;
use Shop\Model\TransactionAction;

/**
 * Абстрактный класс типа оплаты.
 */
abstract class AbstractType
{
    private $opt = [];

    /** @var Payment */
    protected $payment;
    protected $post_params = []; //Параметры для POST запроса
    /** @var Order */
    protected $order;
    /** @var Transaction */
    protected $transaction;

    /**
     * Возвращает название расчетного модуля (типа оплаты)
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Возвращает описание типа оплаты. Возможен HTML
     *
     * @return string
     */
    abstract public function getDescription();

    /**
     * Возвращает идентификатор данного типа оплаты. (только англ. буквы)
     *
     * @return string
     */
    abstract public function getShortName();

    /**
     * Возвращает true, если данный тип поддерживает проведение платежа через интернет
     *
     * @return bool
     */
    abstract public function canOnlinePay();

    /**
     * Возвращает true, если данный тип подразумевает наложенный платеж при оплате заказа
     *
     * @return bool
     */
    public function cashOnDelivery()
    {
        return !$this->canOnlinePay();
    }

    /**
     * Возвращает true, если можно обращаться к ResultUrl для данного метода оплаты.
     * Обычно необходимо для способов оплаты, которые применяются только на мобильных приложениях.
     * По умолчанию возвращает то же, что и canOnlinePay.
     *
     * @return bool
     */
    public function isAllowResultUrl()
    {
        return $this->canOnlinePay();
    }

    /**
     * Проверяет статус платежа
     * Актуально только для типов поддерживающих online оплату
     *
     * @param Transaction $transaction
     */
    public function checkPaymentStatus(Transaction $transaction)
    {
        $transaction->onResult(HttpRequest::commonInstance());
    }

    /**
     * Устанавливает настройки, которые были заданы в способе оплаты.
     * В случае, если расчетный класс вызывается у готового заказа,
     * то дополнительно устанавливаются order и transaction
     *
     * @param mixed $opt Настройки расчетного класса
     * @param Order $order Заказ
     * @param Transaction $transaction Транзакция
     */
    public function loadOptions(array $opt = null, Order $order = null, Transaction $transaction = null)
    {
        $this->opt = $opt;
        $this->order = $order;
        $this->transaction = $transaction;
    }

    /**
     * Получает значение опции способа оплаты
     *
     * @param string $key - ключ опции
     * @param mixed $default - значение по умолчанию
     * @return mixed
     */
    public function getOption($key = null, $default = null)
    {
        if ($key == null) return $this->opt;
        return isset($this->opt[$key]) ? $this->opt[$key] : $default;
    }

    /**
     * Возвращает true, если необходимо использовать
     * POST запрос для открытия страницы платежного сервиса
     *
     * @return bool
     */
    public function isPostQuery()
    {
        return false;
    }

    /**
     * Добавляет один параметр поста в определённый ключ
     *
     * @param string $key - ключ
     * @param string|array $value - значение
     */
    public function addPostParam($key, $value)
    {
        $this->post_params[$key] = $value;
    }

    /**
     * Добавляет параметры для Пост запроса
     *
     * @param array $post_params - массив параметров
     */
    public function addPostParams(array $post_params)
    {
        $this->post_params += $post_params;
    }

    /**
     * Возвращает параметры - ключ значение для выполнения поста
     * @return array
     */
    public function getPostParams()
    {
        return $this->post_params;
    }

    public function setOption($key_or_array = null, $value = null)
    {
        if (is_array($key_or_array)) {
            $this->opt = $key_or_array + $this->opt;
        } else {
            $this->opt[$key_or_array] = $value;
        }
    }

    /**
     * Функция срабатывает после создания заказа
     *
     * @param Order $order - объект заказа
     * @param Address $address - Объект адреса
     * @return mixed
     */
    public function onOrderCreate(Order $order, Address $address = null)
    {}

    /**
     * Возвращает ORM объект для генерации формы в административной панели или null
     *
     * @return FormObject|void
     */
    public function getFormObject()
    {}

    /**
     * Возвращает идентификатор, уникализирующий продавца в рамках типа оплаты
     *
     * @return string
     */
    public function getTypeUnique(): string
    {
        return '';
    }

    /**
     * Возвращает общие настройки, зависящие от интерфейсов способа оплаты
     *
     * @return Type\AbstractType[]
     */
    final protected function getFormCommonProperties()
    {
        $properties = [];

        if (isset(class_uses($this)[TraitInterfaceRecurringPayments::class])) {
            /** @var TraitInterfaceRecurringPayments $this */
            $properties += $this->getFormRecurringPaymentsProperties();
        }

        return $properties;
    }

    /**
     * Возвращает дополнительный HTML для админ части в заказе
     *
     * @param Order $order - объект заказа
     * @return string
     */
    public function getAdminPaymentHtml(Order $order)
    {
        $html = '';

        if ($this instanceof InterfaceRecurringPayments && $this->isRecurringPaymentsActive()) {
            $html .= $this->getAdminRecurringPaymentsHtml($order);
        }

        $html .= $this->getAdminHTML($order);

        return $html;
    }

    /**
     * Возвращает дополнительный персональный HTML для админ части в заказе
     *
     * @param Order $order - объект заказа
     * @return string
     */
    public function getAdminHTML(Order $order)
    {
        return "";
    }

    /**
     * Действие с запросами к заказу для исполнения определённой операции
     *
     * @param Order $order - объект заказа
     */
    public function actionOrderQuery(Order $order)
    {}

    /**
     * Возвращает HTML форму данного типа оплаты, для ввода дополнительных параметров
     *
     * @return string
     * @throws \SmartyException
     */
    public function getFormHtml()
    {
        if ($params = $this->getFormObject()) {
            $params->getPropertyIterator()->arrayWrap('data');
            $params->getFromArray((array)$this->opt);
            $params->setFormTemplate(strtolower(str_replace('\\', '_', get_class($this))));
            $module = \RS\Module\Item::nameByObject($this);
            $tpl_folder = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.$module.\Setup::$MODULE_TPL_FOLDER;
            
            return $params->getForm(['payment_type' => $this], null, false, null, '%system%/coreobject/tr_form.tpl', $tpl_folder);
        }
        return '';
    }

    /**
     * Возвращает список названий документов и ссылки, по которым можно открыть данные документы,
     * генерируемых данным типом оплаты
     *
     * @return array
     */
    public function getDocsName()
    {}

    /**
     * Возвращает URL к печтной форме документа
     *
     * @param string $doc_key - ключ документа
     * @param bool $absolute - если true, то вернуть абсолютный URL
     * @return string
     * @throws \Exception
     */
    public function getDocUrl($doc_key = null, $absolute = false)
    {
        // Если это транзакия для оплаты заказа
        if($this->order){
            return \RS\Router\Manager::obj()->getUrl('shop-front-documents', ['doc_key' => $doc_key, 'order' => $this->order['hash']], $absolute);
        }
        
        // Если это транзакция для пополнения лицевого счета
        if($this->transaction){
            return \RS\Router\Manager::obj()->getUrl('shop-front-documents', ['doc_key' => $doc_key, 'transaction' => $this->transaction['sign']], $absolute);
        }
        
        throw new \Exception(t('Невозможно сформировать URL. Не передан ни объект заказа, ни объект транзакции'));
    }

    /**
     * Возвращает html документа для печати пользователем
     *
     * @param mixed $dockey
     */
    public function getDocHtml($dockey = null)
    {}

    /**
     * Возвращает объект компании, которая предоставляет услуги
     *
     * @return Company
     */
    public function getCompany()
    {
        $site_id = $this->order ? $this->order['site_id'] : null;

        $company = new Company();
        $company->getFromArray( ConfigLoader::getSiteConfig($site_id)->getValues() );
        return $company;
    }

    /**
     * Возвращает URL для перехода на сайт сервиса оплаты для совершения платежа
     * Используется только для Online-платежей
     *
     * @param Transaction $transaction
     * @return string
     */
    public function getPayUrl(Transaction $transaction)
    {}

    /**
     * Возвращает ID заказа исходя из REQUEST-параметров соотвествующего типа оплаты
     * Используется только для Online-платежей
     *
     * @param HttpRequest $request - входящий запрос
     * @return mixed
     */
    public function getTransactionIdFromRequest(HttpRequest $request)
    {
        return false;
    }

    /**
     * Вызывается при оплате сервером платежной системы.
     * Возвращает строку - ответ серверу платежной системы.
     * В случае неверной подписи бросает исключение
     * Используется только для Online-платежей
     *
     * @param Transaction $transaction - транзакция
     * @param HttpRequest $request - входящий запрос
     * @return ChangeTransaction|string
     */
    public function onResult(Transaction $transaction, HttpRequest $request)
    {}

    /**
     * Собирает результаты обработки нескольких платежей в один ответ
     * (используется только у оплат, которые в одном уведомлении отправляют информацию по нескольким платежам)
     *
     * @param string[] $result_array - результаты обработки платежей
     * @return string
     */
    public function wrapOnResultArray($result_array)
    {}

    /**
     * Вызывается при открытии страницы успеха, после совершения платежа
     * В случае неверной подписи бросает исключение
     * Используется только для Online-платежей
     *
     * @param Transaction $transaction
     * @param HttpRequest $request
     * @return void
     */
    public function onSuccess(Transaction $transaction, HttpRequest $request)
    {}

    /**
     * Вызывается при открытии страницы неуспешного проведения платежа
     * Используется только для Online-платежей
     *
     * @param Transaction $transaction
     * @param HttpRequest $request
     * @return void
     */
    public function onFail(Transaction $transaction, HttpRequest $request)
    {}

    /**
     * Вызывается при открытии страницы проверки статуса проведения платежа
     * Используется только для Online-платежей
     *
     * @param Transaction $transaction
     * @param HttpRequest $request
     * @return void
     */
    public function onStatus(Transaction $transaction, HttpRequest $request)
    {}

    /**
     * Возвращает список возможных действий с транзакцией
     *
     * @param Transaction $transaction - транзакция
     * @param Order $order - объект заказа для которого нужно вернуть действия
     * @return TransactionAction[]
     */
    public function getAvailableTransactionActions(Transaction $transaction, Order $order): array
    {
        return [];
    }

    /**
     * Исполняет действие с транзакцией
     * При успехе - возвращает текст сообщения для администратора, при неудаче - бросает исключение
     *
     * @param Transaction $transaction - транзакция
     * @param string $action - идентификатор исполняемого действия
     * @return string
     * @throws RSException
     */
    public function executeTransactionAction(Transaction $transaction, string $action): string
    {
        throw new RSException(t('Данный тип оплаты не поддерживает действий с транзакциями'));
    }

    /**
     * Возвращает абсолютную ссылку для текущего сайта или партнерского сайта.
     * Итоговая ссылка зависит от текущего партнерского сайта
     *
     * @param string $relative_url относительный URL
     * @return string
     */
    public function makeRightAbsoluteUrl($relative_url)
    {
        if (ModuleManager::staticModuleExists('partnership')) {
            $partner = Api::getCurrentPartner();
            if ($partner) {
                return $partner->getAbsoluteUrl($relative_url);
            }
        }

        $current_site = SiteManager::getSite();
        $uri = $current_site ? $current_site->getAbsoluteUrl($relative_url) : HttpRequest::commonInstance()->getSelfAbsoluteHost().$relative_url;

        return $uri;
    }

    /**
     * Возвращает установленный способ оплаты
     *
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * Устанавливает способ оплаты
     *
     * @param Payment $payment
     * @return self
     */
    public function setPayment(Payment $payment): self
    {
        $this->payment = $payment;
        return $this;
    }

    /**
     * Возвращает правильный код НДС
     *
     * @param Tax[] $taxes - список налогов
     * @param Address $address - объект адреса
     * @return string|null
     */
    protected function getNdsCode(array $taxes, Address $address)
    {
        $nds = TaxApi::getRightNds($taxes, $address);
        return static::handbookNds()[$nds] ?? null;
    }

    /**
     * Справочник кодов НДС
     * Ключи справочника должны соответствовать списку кодов НДС в TaxApi
     *
     * @return string[]
     */
    protected static function handbookNds()
    {
        static $nds = [
            TaxApi::TAX_NDS_NONE => TaxApi::TAX_NDS_NONE,
            TaxApi::TAX_NDS_0 => TaxApi::TAX_NDS_0,
            TaxApi::TAX_NDS_10 => TaxApi::TAX_NDS_10,
            TaxApi::TAX_NDS_20 => TaxApi::TAX_NDS_20,
            TaxApi::TAX_NDS_110 => TaxApi::TAX_NDS_110,
            TaxApi::TAX_NDS_120 => TaxApi::TAX_NDS_120,
        ];
        return $nds;
    }
}
