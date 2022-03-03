{assign var=receipt value=$data->receipt}
{assign var=transaction value=$data->transaction}
{t}Произошла ошибка при выписке чека по транзакции{/t} №{$transaction.id}
