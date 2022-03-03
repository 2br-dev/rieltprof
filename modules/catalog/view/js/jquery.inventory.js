(function( $ ) {
    $.fn.inventory = function (method) {

        var options = {
            inventory : 'inventorization', // тип документа - инвентаризация
            added_items : '.added-items'
        };

        var methods = {
            init: function(){
                addProducts('.hidden_id_to_add');

                if($('input[name="type"]').val() == options.inventory){
                    $('select[name="warehouse"]').on('change', function () {
                        refresh(true);
                    });
                }
                initEvents();
                iniOffers($(".offers"));


                $('.barcode-scanner').on("keypress", function (event) {
                    if ( event.which == 13 ) {
                        event.preventDefault();
                        addProductFromReader(event.target.value, $(this).data('href'));
                        event.target.value = '';
                    }
                });

                $('.product-group-container').selectProduct(
                    {
                        showCostTypes: false,
                        startButton: '.select-button',
                        // По закрытию диалога
                        onResult: function () {
                            // Для каждого вставленного продукта
                            addProducts('.input-container input');
                        }
                    }
                );
                if(!$(".addproduct").attr('disabled')) {
                    $(this).on("click", function () {
                        $(".select-button").click();
                    });
                }

            }
        };

        var addProducts = function (selector){
            if($(selector).length) {
                $(selector).each(function () {
                    var prod_id = $(this).val();
                    var uniq = genUniq();
                    var $input = $('<input type="hidden">');
                    $(options.added_items).append($input.attr('name', 'items[' + uniq + '][product_id]').val(prod_id).clone());
                    $(options.added_items).append($input.attr('name', 'items[' + uniq + '][amount]').val(1).clone());
                    if ($('input[name="type"]').val() == options.inventory) {
                        $(options.added_items).append($input.attr('name', 'items[' + uniq + '][calc_amount]').val(0).clone());
                        $(options.added_items).append($input.attr('name', 'items[' + uniq + '][fact_amount]').val(0).clone());
                    }
                });
                bindAmounts();
                fillAmounts();
                refresh();
            }
        };

        var addProductFromReader = function (sku, href) {
            $.ajaxQuery(
                {
                    url: href,
                    type: 'GET',
                    data: {
                        sku: sku,
                    },
                    contentType: "application/json",
                    complete: function (response) {
                        let success = response.responseJSON.success;
                        if (success) {
                            let id = response.responseJSON.id,
                                offer_id = response.responseJSON.offer_id,
                                append_new = true;

                            $("tr.product-row").each(function() { //Перебрать все строки с товарами
                                let cells = $(this).children();
                                let offers = cells.children("select.offers");
                                if (offer_id == offers.val()) { // Если товар с такой комплектацией уже в документе
                                    if ($('input[name="type"]').val() == options.inventory) { // Если документ инвентаризации
                                        let amount = cells.children("input.fact-amount");
                                        amount.val(parseInt(amount.val()) + 1); // Увеличиваем количество
                                        bindAmountsFact();
                                    } else {
                                        let amount = cells.children("input.amount");
                                        amount.val(parseInt(amount.val()) + 1); // Увеличиваем количество
                                    }
                                    append_new = false; // Указываем, что не нужно создавать новый товар
                                    return true;
                                }
                            });

                            if (append_new) {// Если нужно добавить новый товар
                                var $input = $('<input type="hidden">');
                                var uniq = genUniq();
                                $(options.added_items).append($input.attr('name', 'items[' + uniq + '][product_id]').val(id).clone());
                                $(options.added_items).append($input.attr('name', 'items[' + uniq + '][amount]').val(1).clone());
                                $(options.added_items).append($input.attr('name', 'items[' + uniq + '][offer_id]').val(offer_id).clone());
                                if ($('input[name="type"]').val() == options.inventory) {
                                    $(options.added_items).append($input.attr('name', 'items[' + uniq + '][calc_amount]').val(0).clone());
                                    $(options.added_items).append($input.attr('name', 'items[' + uniq + '][fact_amount]').val(1).clone());
                                }

                            }
                            bindAmounts();
                            fillAmounts();
                            refresh();
                        }
                    }
                }
            );
        };

        //снимает галочки в форме выбора товаров
        var uncheck = function(){
            var inputs = $('.productblock').find(':input[type="checkbox"]');
            inputs.prop("checked", false);
            inputs.trigger('change');
        };

        var initOffers = function(){
            var selectors = $(".offers");
            selectors.each(function () {
                var uniq = $(this).data('uniq');
                var $input = $('<input type="hidden">');
                var offer = $(":input[name='items[" + uniq + "][offer_id]'][type='hidden']");
                if(!offer.length) {
                    $(options.added_items).append($input.attr('name', 'items[' + uniq + '][offer_id]').val($(this).val()).clone());
                }
            });
        };

        var initEvents = function(){
            bindAmounts();
            bindOffers();
            openLoadCsvDialog();
            bindOnSelectProperty();
            if(!$('.addproduct').attr('disabled')) {
                $('.remove').on('click', function () {
                    var uniq = $(this).data('uniq');
                    $('.product-row[data-uniq="' + uniq + '"]').remove();
                    $('input[name^="items[' + uniq + ']"][type="hidden"]').remove();
                });
            }
            if(!$('.multi_delete').attr('disabled')) {
                $('.multi_delete').on('click', function () {
                    $('.m_delete:checked').each(function () {
                        var uniq = $(this).data('uniq');
                        $('.remove[data-uniq="' + uniq + '"]').trigger('click');
                    })
                });
            }
            $('.chk_head').on('change', function () {
                if($(this).is( ":checked" )){
                    $('.m_delete').prop('checked', true);
                }else{
                    $('.m_delete').prop('checked', false);
                }
            });
            $('.m_delete').on('change', function () {
                if(!$(this).is( ":checked" )){
                    $('.chk_head').prop('checked', false);
                }
            });
            if($('input[name="type"]').val() == options.inventory){
                initInventory();
            };
        };

        var openLoadCsvDialog = function() {
            if(!$(".loadCsv").attr('disabled')){
                var load_button = $(".loadCsv");
                var init_data = load_button.data("init");
                if(!init_data) {
                    load_button.data("init", 'init');
                    load_button.on("click", function () {
                        $.rs.openDialog({
                            dialogOptions: {
                                width: $(this).data('crud-dialog-width'),
                                height: $(this).data('crud-dialog-height')
                            },
                            url: $(this).data('url'),
                            afterOpen: function (dialog) {
                                bindDialogEvents(dialog);
                            }
                        });
                    });
                }
            }
        };

        var bindDialogEvents = function(dialog) {
            var form = $(dialog);
            form
                .on('crudSaveSuccess', function(event, response) {
                    var table = response.table;
                    var inputs = response.inputs;
                    $(".ordersEdit").append(table);
                    $(".added-items").append(inputs);
                    initEvents();
                    refresh();
                });
        };

        var bindAmounts = function(){
            $(".amount[data-uniq]").each(function () {
                $(this).on('change', function(){
                    var uniq = $(this).data('uniq');
                    var amount = $(this).val();
                    $(":input[type='hidden'][name='items["+uniq+"][amount]']").val(amount);
                });
            });
        };

        var bindAmountsFact = function () {
            $(".fact-amount").each(function () {
                // $(this).on('change', function() {
                    var uniq = $(this).parent().parent().data('uniq');
                    var amount = $(this).val();
                    $(":input[type='hidden'][name='items["+uniq+"][fact_amount]']").val(amount);
                // });
            });
        };

        var bindOffers = function(){
            $(".offers").on("change", function () {
                var uniq = $(this).data('uniq');
                $(":input[name='items[" + uniq + "][offer_id]'][type='hidden']").val($(this).val());
            });
        };

        //заполняет количество выбранных товаров в скрытых полях
        var fillAmounts = function(){
            $(".amount[data-uniq]").each(function () {
                $(this).trigger('change');
            });
        };

        var genUniq = function() {
            return Math.floor((1 + Math.random()) * 0xffFFffFFff).toString(16).substring(1);
        };

        //обновления списка товаров
        var refresh =  function(warehouse_change) {
            var form = $('.crud-form'),
                data = form.serializeArray();

            data.push({name:"refresh", value: 1});
            data.push({name:"warehouse", value: $('select[name="warehouse"]').val()});
            if(warehouse_change){
                if($('input[name="recalculate"]').prop('checked')) {
                    data.push({name: "recalculate", value: true});
                }
            }

            $.ajaxQuery({
                url: form.attr('action'),
                data: data,
                type: 'POST',
                success: function(response) {
                    $('.ordersEdit').empty();
                    $('.ordersEdit').append(response.html);
                    uncheck();
                    initOffers();
                    initEvents();
                    if(warehouse_change){
                        if($('input[name="type"]').val() == options.inventory){
                            if($('input[name="recalculate"]').prop('checked')) {
                                $(".offers").trigger('change');
                            }
                        }else{
                            $(".offers").trigger('change');
                        }
                    }else{
                        var rows = $(".product-row");
                        rows.each(function () {
                            if(!$("input[name='items["+$(this).data("uniq")+"][calc_amount]']").val()){
                                $(this).find(".offers").trigger("change");
                            }
                        });
                    }
                }
            });
        };

        //привязка события изменения комплектации
        bindOnSelectProperty = function () {
            //при изменении значении характеристики
            $('.product-multioffer').on('change', function () {
                var offer_selector = $(this).parent().parent().find('.offers');
                var prop_selectors = $(this).parent().find('.product-multioffer');
                var current_info = [];
                prop_selectors.each(function () {
                    var arr = [];
                    arr[0] = $(this).data("prop-title");
                    arr[1] = $(this).val();
                    current_info.push(arr);
                });
                var options = offer_selector.find('option');
                var found = false;
                options.each(function () {
                    let match = 0;
                    current_info.forEach((current_value, current_index) => {
                        if ($(this).data("info")) {
                            $(this).data("info").forEach((value, index) => {
                                if (JSON.stringify(current_value) == JSON.stringify(value)) {
                                    match = match + 1;
                                }
                            });
                        }
                    });
                    if (match == current_info.length) {
                        found = true;
                        offer_selector.val($(this).val())
                        offer_selector.change();
                    }
                });
                if(!found){
                    offer_selector.val(-1);
                    offer_selector.change();
                }
            });
            //при изменении комплектации
            $('.offers').on('change', function () {
                if($('input[name="type"]').val() == options.inventory){
                    getOfferNum($(this).val(), $("select[name='warehouse']").val(), $(this).data("uniq"));
                }
                iniOffers($(this));
            });
        };

        var initInventory = function (){
            $('.fact-amount').on('change', function () {
                var parent_tr = $(this).parent().parent();
                var calc_amount = parent_tr.find('.calc-amount').val();
                var amount = $(this).val() - calc_amount;
                parent_tr.find('.final-amount').val(amount);
                var uniq = parent_tr.data('uniq');
                $(":input[type='hidden'][name='items["+uniq+"][amount]']").val(amount);
                $(":input[type='hidden'][name='items["+uniq+"][calc_amount]']").val(parent_tr.find('.calc-amount').val());
                $(":input[type='hidden'][name='items["+uniq+"][fact_amount]']").val(parent_tr.find('.fact-amount').val());
            });
            $('.fact-amount').change();
        };

        function iniOffers(_this){
            var props = _this.parent().find('select[class="product-multioffer"]');
            var info = _this.find("option:selected").data('info');
            props.each(function(){
                var prop = $(this).data("prop-title");
                var found = false;
                if(info) {
                    for (var i = 0; i < info.length; i++) {
                        if (prop.toLowerCase() == info[i][0].toLowerCase()) {
                            $(this).val(info[i][1]);
                            found = true;
                            break;
                        }
                    }
                }
                if(!found){
                    $(this).val(-1);
                }
            });
        }

        function getOfferNum(offer_id, wh, uniq){
            $.ajaxQuery({
                url: $(".pr-table").data('getnum'),
                data: {"offer_id" : offer_id, "warehouse" : wh},
                type: 'POST',
                success: function(response) {
                    $('tr[data-uniq="'+uniq+'"]').find(".calc-amount").val(response);
                    $('tr[data-uniq="'+uniq+'"]').find(".fact-amount").change();
                }
            });
        }

        function ArrayToObject(arr){
            var obj = {};
            if(arr) {
                for (var i = 0; i < arr.length; i++) {
                    obj[arr[i][0].toLowerCase()] = arr[i][1].toLowerCase();
                }
            }
            return obj
        }

        function compareObjects(obj1, obj2){
            return JSON.stringify(obj1) === JSON.stringify(obj2);
        }

        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        }
    };
})(jQuery);

