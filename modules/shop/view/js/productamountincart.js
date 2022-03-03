/**
 * Плагин, блок "количество товара в корзине"
 *
 * @author ReadyScript lab.
 */
(function ($) {
    $.fn.cartAmount = function (data) {
        if ($(this).data('cartAmount')) return false;
        $(this).data('cartAmount', {});

        let defaults = {
            input: '.rs-cartAmount_input',
            addButton: '.rs-to-cart',
            increaseButton: '.rs-cartAmount_inc',
            decreaseButton: '.rs-cartAmount_dec',
            classInCart: 'rs-inCart',
            productId: null,
            amountAddToCart: 1,
            amountStep: 1,
            minAmount: null,
            maxAmount: null,
            amountBreakPoint: null,
            forbidRemoveProducts: false,
            forbidChangeRequests: false,
            isCached: 0,
        };

        let $this = $(this);
        $this.options = $.extend({}, defaults, data);
        // $this.options.multiple = $this.attr('multiple');
        // $this.options.disallow_select_branches = $this.attr('disallowSelectBranches');

        if ($this.options.isCached) {
            let productId = $this.data('productId');
            let numList = global.cartProducts[productId];
            if (numList) {
                let total = 0;
                for (let num in numList) {
                    total = total + numList[num];
                }
                $this[0].querySelector('.rs-cartAmount_input').value = total;
                $this[0].classList.add('rs-inCart');
                console.log(productId, total);
            } else {
                $this[0].querySelector('.rs-cartAmount_input').value = 0;
                $this[0].classList.remove('rs-inCart');
            }
        }

        document.querySelector('body').addEventListener('cart.removeProduct', (event) => {
            if (event.detail.productId == $this.options.productId) {
                $($this.options.input, $this).val(0).trigger('keyup');
            }
        });
        
        $this
            // кнопка "В корзину"
            .on('click', $this.options.addButton, function () {
                $($this.options.input, $this).val($this.options.amountAddToCart);
                $this.addClass($this.options.classInCart);
                $this.trigger('add-product');
            })
            // нажатие на "плюсик"
            .on('click', $this.options.increaseButton, function () {
                let input = $($this.options.input, $this);
                let old_value = parseFloat(input.val());
                let new_value = Math.round((old_value + $this.options.amountStep) * 1000) / 1000;
                if (new_value < $this.options.minAmount) {
                    new_value = $this.options.minAmount;
                }
                if (old_value < $this.options.amountBreakPoint && new_value > $this.options.amountBreakPoint) {
                    new_value = $this.options.amountBreakPoint;
                }
                if ($this.options.maxAmount !== null && new_value > $this.options.maxAmount) {
                    new_value = $this.options.maxAmount;
                    $this.trigger('max-limit');
                } else {
                    $this.trigger('increase-amount');
                }
                input.val(new_value).trigger('keyup');

                return false;
            })
            // нажатие на "минусик"
            .on('click', $this.options.decreaseButton, function () {
                let input = $($this.options.input, $this);
                let old_value = parseFloat(input.val());
                let new_value = Math.round((old_value - $this.options.amountStep) * 1000) / 1000;
                if (new_value < $this.options.minAmount) {
                    new_value = 0;
                }
                if (old_value > $this.options.amountBreakPoint && new_value < $this.options.amountBreakPoint) {
                    new_value = $this.options.amountBreakPoint;
                }
                if (new_value != 0 || !$this.options.forbidRemoveProducts) {
                    $this.trigger('decrease-amount');
                    input.val(new_value).trigger('keyup');
                }

                return false;
            })
            // изменение количества
            .on('keyup', $this.options.input, function(event){
                let noChangesKeycodes = [16, 17, 18, 35, 36, 37, 39];
                if (noChangesKeycodes.includes(event.keyCode)) {
                    return false;
                }

                let amount = $(this).val();
                if ($this.options.maxAmount !== null && amount > $this.options.maxAmount) {
                    amount = $this.options.maxAmount;
                    $this.trigger('max-limit');
                }
                if (amount == 0) {
                    if ($this.options.forbidRemoveProducts) {
                        amount = $this.options.amountAddToCart;
                        $(this).val(amount);
                    } else {
                        $this.removeClass($this.options.classInCart);
                        $this.trigger('remove-product');
                    }
                }
                let url = $this.data('url');
                let data = {
                    id: $this.options.productId,
                    amount: amount
                };
                if (!$this.options.forbidChangeRequests) {
                    $.ajax({
                        url: url,
                        data: data,
                        type: 'POST',
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                $.cart('refresh');
                            }
                        }
                    });
                }
            });
    };

    $(document).ready(function () {
        $('body').on('new-content', () => {
            $('.rs-cartAmount').each(function () {
                $(this).cartAmount($(this).data('cartAmountOptions'));
            });
        });

        $('.rs-cartAmount').each(function () {
            $(this).cartAmount($(this).data('cartAmountOptions'));
        });
    });
})(jQuery);