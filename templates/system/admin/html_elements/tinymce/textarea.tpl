<textarea class="tinymce" id="tinymce-{$param.id}" name="{$param.name}" {if isset($param.rows)}rows="{$param.rows}"{/if} {if isset($param.cols)}cols="{$param.cols}"{/if} {if isset($param.style)}style="{$param.style}"{/if}>{$data|escape:"html"}</textarea>
<script>
    $LAB
        .script({json_encode($param.jquery_tinymce_path)})
        .wait(function(){
            var txtEditor = $('#tinymce-{$param.id}');

            var initEditor = function() {
                var params = {json_encode($param.tiny_options)|default:'{}'};

                $('#tinymce-{$param.id}:visible')
                    .tinymce($.extend(params, {
                        setup: function (editor) {
                            editor.on('init', function (args) {
                                $(editor.getElement()).parent().removeClass('tiny-loading');
                            });
                        }
                    }))
                    .parent().addClass('tiny-loading');
            };

            $(function() {
                txtEditor.closest('.tab-pane').bind('on-tab-open', function() {
                    initEditor();
                });
                txtEditor.bind('became-visible', function() {
                    initEditor();
                });
                txtEditor.closest('form').on('beforeAjaxSubmit', function() {
                        $('#tinymce-{$param.id}:tinymce').each(function() {
                            $(this).tinymce().save();
                        });
                });
                txtEditor.closest('.dialog-window').on('dialogBeforeDestroy', function() {
                    var tiny_instance = txtEditor.tinymce();
                    if (tiny_instance) {
                        tiny_instance.remove();
                    }
                });

                setTimeout(function() {
                    initEditor();
                }, 10);
            });
        });
</script>