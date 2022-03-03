<div class="form-inline">
    <select name="{$fitem->getName()}" {$fitem->getAttrString()}>
    {html_options options=$fitem->getList() selected=$fitem->getValue()}
    </select>
</div>