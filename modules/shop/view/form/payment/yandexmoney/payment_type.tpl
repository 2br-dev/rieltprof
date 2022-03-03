<style type="text/css">
    .categoryCodeTitle{
        padding: 10px 0;
        font-size: 12px;
        font-weight: bold;
    }
</style>
<div id="paymentTypeYandex">
    {include file=$field->getOriginalTemplate()}
</div>
<div id="categoryCodeYandex" style="display: none;">
    <div class="categoryCodeTitle">
        {t alias="Я.Касса, характеристика отвечающая за категорию товаров" filelink="https://money.yandex.ru/i/forms/types_of_products.xls"}
        Ниже характеристика отвечающая за категорию товаров по версии Яндекс для банков<br/>
        Смотрите <a href='%filelink' target='_blank'>%filelink</a>
            <br/>(Значением характеристики должно быть числовое из списка выше){/t}
    </div>
    {$elem.__category_code->formView()}
</div>
<script type="text/javascript">
   $(document).ready(function(){
       /**
       * Смена списка Типа метода оплаты
       */
       $("#paymentTypeYandex select").on('change', function(){
           $("#categoryCodeYandex").toggle(($(this).val()=='KV'));
       }); 
       $("#categoryCodeYandex").toggle(($("#paymentTypeYandex select").val()=='KV'));  
   });
</script>