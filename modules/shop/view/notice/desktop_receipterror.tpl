{assign var=receipt value=$data->receipt}
{assign var=transaction value=$data->transaction}

{t}Уважаемый, администратор!{/t} 
<p>На сайте {$url->getDomainStr()} {t}произошла ошибка при выписке чека по транзакции{/t} №{$transaction.id}.</p>