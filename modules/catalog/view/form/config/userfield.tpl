<table class="otable">                          
    <tbody>
    <tr>
        <td class="otitle">{$elem.__buyinoneclick->getTitle()}</td>
        <td>{include file=$elem.__buyinoneclick->getRenderTemplate() field=$elem.__buyinoneclick}</td>
    </tr>
    <tr>
        <td class="otitle">{$elem.__dont_buy_when_null->getTitle()}</td>
        <td>{include file=$elem.__dont_buy_when_null->getRenderTemplate() field=$elem.__dont_buy_when_null}</td>
    </tr>
    <tr>
        <td class="otitle">{$elem.__oneclick_name_required->getTitle()}</td>
        <td>{include file=$elem.__oneclick_name_required->getRenderTemplate() field=$elem.__oneclick_name_required}</td>
    </tr>
    </tbody>
</table>

{$elem->getClickFieldsManager()->getAdminForm(t('Будут запрошены при оформлении заказа'))}