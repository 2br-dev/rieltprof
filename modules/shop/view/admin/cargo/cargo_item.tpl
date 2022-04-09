<li data-cargo-id="{$cargo.id}" {if $cargo.id == $current_cargo_id}class="act"{/if}>
    <a class="cargo-item-title">{$cargo.title|default:"Коробка"}</a>
    <a class="cargo-item-remove"><i title="{t}Удалить{/t}" class="zmdi zmdi-delete c-red"></i></a>
</li>