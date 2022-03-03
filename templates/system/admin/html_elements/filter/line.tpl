<fieldset>
    {foreach $fline->getItems() as $item}
        <div class="form-group">
            {$item->getView()}
        </div>
    {/foreach}
</fieldset>