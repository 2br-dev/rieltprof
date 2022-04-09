{* Шаблон страницы пополнения баланса в личном кабинете *}
{extends file="%THEME%/helper/wrapper/my-cabinet.tpl"}
{block name="content"}
    <div class="col">
        <h1 class="mb-lg-6 mb-sm-5 mb-4">{t}Баланс:{/t} {$balance_string}</h1>
        <div class="tab-pills__wrap mb-5">
            <ul class="nav nav-pills tab-pills">
                <li class="nav-item">
                    <a href="{$router->getUrl('shop-front-mybalance')}" class="nav-link ">{t}История операций{/t}</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link active">{t}Пополнить баланс{/t}</a>
                </li>
            </ul>
        </div>
        <div>
            <div class="mb-4 col-xl-7 col-md-10">
                {t}Вы можете пополнить свой баланс наиболее удобным способом. Укажите сумму пополнения и выберите способ оплаты{/t}
            </div>
            <h2 class="mb-4">{t}Сумма пополнения{/t}</h2>
            <form method="POST">
                <div class="col-xl-4 col-md-6 col-sm-8 mb-6">
                    <label for="input-topic" class="form-label">{t currency=$base_currency.stitle}Сумма пополнения (%currency){/t}</label>
                    <div class="row">
                        <div class="col">
                            <input id="input-topic" class="rs-cost-field form-control {if $api->hasError()}is-invalid{/if}" type="text" name="cost" value="{$smarty.post.cost}" placeholder="{t}0.00{/t}" required>
                            {if $api->hasError()}<div class="invalid-feedback">{$api->getErrors()|join:", "}</div>{/if}
                        </div>
                        {if $current_currency.stitle != $base_currency.stitle}
                            {addjs file="%shop%/rscomponent/addfunds.js"}
                            <div class="col">
                                <input type="text" disabled class="form-control rs-converted-cost" data-ratio="{$current_currency.ratio}" data-liter="{$current_currency.stitle}">
                            </div>
                        {/if}
                    </div>
                </div>

                <div class="col-xxl-8 col-xl-9">
                    <div class="row w-100 g-3 row-cols-md-2 ">
                        {foreach $pay_list as $item}
                            <div>
                                <button name="payment" type="submit" class="lk-balance-up" value="{$item.id}">
                                    <span class="h3">{$item.title}</span>
                                    <span>{$item.description}</span>
                                </button>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </form>
        </div>
    </div>
{/block}