{$user=$elem->getUser()}
{$user->getFio()} (ID:{$user->id})
</td></tr>
<tr><td class="otitle">
{t}Текущий баланс{/t}:</td>
<td><b>{$user->getBalance()}</b>