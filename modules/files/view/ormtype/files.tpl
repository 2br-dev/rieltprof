{* Добавление файлов в сделках *}
{if $field->getLinkType()}
    {moduleinsert name="\Files\Controller\Admin\Block\Files" link_type={$field->getLinkType()} link_id=$elem.id}
{else}
    {t}Не задан тип связи для отображения блока файлов{/t}
{/if}