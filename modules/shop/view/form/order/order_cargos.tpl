{if $elem.id > 0}
    <div>
        <h3>{t}Грузовые места{/t}</h3>
        <p>{t}Опишите сформированные грузовые места для данного заказа. <br>Если в заказе имеются товары с маркировками, то предварительно обязательно необходимо выполнить отгрузку.{/t}</p>
        <div class="orderview-cargo-list">
            {$cargos = $elem->getCargos()}
            {if $cargos}
                <span class="m-r-10">
                    {foreach $cargos as $cargo}
                        <a class="btn btn-default crud-edit m-b-5 rs-order-check-сhanges" data-url="{adminUrl do=false order_id=$elem.id cargo_id=$cargo.id mod_controller="shop-cargoctrl"}">{$cargo->getTitle()}</a>
                    {/foreach}
                </span>
            {/if}
            <a class="btn btn-alt btn-primary crud-edit m-b-5 rs-order-check-сhanges" data-url="{adminUrl do=false order_id=$elem.id mod_controller="shop-cargoctrl"}">{t}Распределить по грузовым местам{/t}</a>
        </div>
    </div>
    <br>
    <br>
{/if}