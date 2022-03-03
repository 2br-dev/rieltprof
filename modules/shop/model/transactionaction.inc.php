<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Model;

use RS\Router\Manager as RouterManager;
use Shop\Model\Orm\Transaction;

class TransactionAction
{
    protected $transaction;
    protected $action;
    protected $title;
    protected $confirm_text;
    protected $css_class;

    public function __construct(Transaction $transaction, string $action, string $title)
    {
        $this->transaction = $transaction;
        $this->action = $action;
        $this->setTitle($title);
    }

    public function getHref()
    {
        return RouterManager::obj()->getAdminUrl('transactionAction', [
            'transaction_id' => $this->transaction['id'],
            'action' => $this->action
        ], 'shop-orderctrl');
    }

    /**
     * Возвращает имя действия
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Устанавливает имя действия
     *
     * @param string $title - имя действия
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Возвращает текст подтверждения
     *
     * @return string
     */
    public function getConfirmText(): string
    {
        return ($this->confirm_text) ?: t('Вы действительно хотите совершить данную операцию?');
    }

    /**
     * Устанавливает текст подтверждения
     *
     * @param string $confirm_text - текст подтверждения
     * @return self
     */
    public function setConfirmText(string $confirm_text): self
    {
        $this->confirm_text = $confirm_text;
        return $this;
    }

    /**
     * Возвращает css класс
     *
     * @return string
     */
    public function getCssClass(): string
    {
        return $this->css_class;
    }

    /**
     * Устанавливает css класс
     *
     * @param string $class - css класс
     * @return self
     */
    public function setCssClass(string $class): self
    {
        $this->css_class = $class;
        return $this;
    }
}
