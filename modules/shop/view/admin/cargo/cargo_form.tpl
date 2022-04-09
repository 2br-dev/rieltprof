<div class="cargo-form__item" data-cargo-id="{$cargo.id}" {if $cargo.id != $current_cargo_id}hidden{/if}>
    <div>
        <label>{t}Название упаковки{/t}</label><br>
        <input type="text" name="cargo[{$cargo.id}][title]" value="{$cargo.title}">
    </div>
    <div>
        <label>{t}Ширина x Высота x Глубина (мм){/t}</label><br>
        <div class="cargo-form__whd">
            <input type="text" size="7" name="cargo[{$cargo.id}][width]" value="{$cargo.width}">
            <span class="cargo-form__whd-x">x</span><input type="text" size="7" name="cargo[{$cargo.id}][height]" value="{$cargo.height}">
            <span class="cargo-form__whd-x">x</span><input type="text" size="7" name="cargo[{$cargo.id}][dept]" value="{$cargo.dept}">
        </div>
    </div>
    <div>
        <label>{t}Вес, грамм{/t}</label><br>
        <input type="text" name="cargo[{$cargo.id}][weight]" value="{$cargo.weight}">
    </div>
</div>