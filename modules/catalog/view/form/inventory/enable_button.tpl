<a data-href="{$router->getAdminUrl('Disable', [], 'catalog-inventoryctrl')}" class="btn btn-danger switch-control enabled {if !$elem.inventory_control_enable}hidden{/if}">{t}Отключить{/t}</a>
<a data-href="{$router->getAdminUrl('EnableControl', [], 'catalog-inventoryctrl')}" class="btn btn-success switch-control {if $elem.inventory_control_enable}hidden{/if}">{t}Включить{/t}</a>

<script>
    $(document).ready(function () {
        $(".switch-control").on('click', function () {
            if(!$(this).hasClass('enabled')) {
                $.rs.openDialog({
                        dialogOptions: {
                            width: $(this).data('crud-dialog-width'),
                            height: $(this).data('crud-dialog-height')
                        },
                        url: $(this).data('href'),
                        afterOpen: function (dialog) {
                            var form = $(dialog);
                            form.on('crudSaveSuccess', function(event, response) {
                                $('.switch-control').toggleClass('hidden');
                            });
                        }
                    });
            }else{
                $.ajaxQuery({
                    url: $(this).data('href'),
                    type: 'get',
                    success: function(response) {
                        $('.switch-control').toggleClass('hidden');
                    }
                });
            }
        });
    });
</script>