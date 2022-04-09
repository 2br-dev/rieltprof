{$cell->getValue()}

{$row = $cell->getRow()}
{if $row['license_text']}
    <div class="f-11 m-t-5 text-{$row['license_text_level']}">
        <i class="zmdi zmdi-info-outline"></i>
        {$row['license_text']}
    </div>
{/if}