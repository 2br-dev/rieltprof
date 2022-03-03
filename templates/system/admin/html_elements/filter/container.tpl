{* Контейнер фильтра *}

<div class="formfilter">
    {foreach $fcontainer->getLines() as $line}
        {$line->getView()}
    {/foreach}                

    <div class="more">
        {foreach $fcontainer->getSecContainers() as $sec_cont}
            {$sec_cont->getView()}
        {/foreach}
    </div>

    <button type="submit" class="btn btn-primary find">{t}применить фильтр{/t}</button>
</div>