{assign var=row value=$cell->getRow()}
{if !empty($row['sites'])}{t}Сайтов{/t}: <strong>{$row['sites']}</strong><br>{/if}
{if !empty($row['product'])}{t}Продукт{/t}: <strong>{$row['product']}</strong><br>{/if}
{if !empty($row['expire_month'])}{t}Срок действия, месяцев{/t}: <strong>{$row['expire_month']}</strong><br>{/if}
{if !empty($row['upgrade_to_product'])}{t}Обновление до продукта{/t}: <strong>{$row['upgrade_to_product']}</strong><br>{/if}
{if isset($row['expire']) && $row['expire']>0}{t}Действительна до{/t}: <strong>{$row['expire']|date_format:"d.m.Y H:i"}</strong><br>{/if}
{if $row['update_expire']>0}{t}Обновление до{/t}: <strong>{$row['update_expire']|date_format:"d.m.Y H:i"}</strong>{/if}