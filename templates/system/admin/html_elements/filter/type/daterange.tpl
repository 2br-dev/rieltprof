<div class="daterange">
    {$values=$fitem->getValue()}

    <div class="form-inline">
        <div class="input-group">
            <input type="text" name="{$fitem->getName()}[from]" value="{if isset($values.from)}{$values.from}{/if}" {$fitem->getAttrString()} date placeholder="{t}с{/t}" size="12">
            <span class="input-group-addon"><i class="zmdi zmdi-calendar-alt"></i></span>
        </div>
    </div>

    <div class="form-inline">
        <div class="input-group">
            <input type="text" name="{$fitem->getName()}[to]" value="{if isset($values.to)}{$values.to}{/if}" {$fitem->getAttrString()} date placeholder="{t}по{/t}" size="12">
            <span class="input-group-addon"><i class="zmdi zmdi-calendar-alt"></i></span>
        </div>
    </div>
</div>