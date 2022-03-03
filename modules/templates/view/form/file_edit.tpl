{addjs file="ace-master/ace/ace.js" basepath="common" no_compress=true}
<div class="formbox">        
    <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form" id="template-edit-form" data-dialog-options='{ "dialogClass": "template-edit-win" }'>
        <input type="hidden" name="basepath" value="{$epath.type}:{$epath.type_value}/">
        <input type="hidden" name="ext" value="{$ext}">
        <div class="notabs">
            <table class="otable no-td-width" width="100%">
            <tr>
                <td class="otitle" style="width:150px">{t}Имя файла{/t}</td>
                <td><div class="file-container-text">{if $epath.type == 'theme'}
                        {t}Тема{/t}:{$root_sections.themes[$epath.type_value].title}
                    {else}
                        {t}Модуль{/t}:{$root_sections.modules[$epath.type_value].title}
                    {/if}</div>
                    <input style="width:500px" type="text" name="filename" value="{$data.filename|escape}"><span class="field-error" data-field="filename"></span><br>
                    </td>
            </tr>
            <tr>
                <td class="otitle"></td>
                <td><input type="checkbox" id="overwrite" name="overwrite" value="1" {if $data.overwrite}checked{/if}> 
                <label for="overwrite" class="fieldhelp">{t}Перезаписывать файл, если таковой уже существует{/t}</label></td>
            </tr>
            <tr>
                <td class="otitle">{t}Содержание файла{/t}</td>
                <td>
                    <div style="position:relative">
                        {assign var=editor_modes value=['css' => 'css', 'tpl' => 'html', 'js' => 'javascript']}
                        <textarea data-editor-mode="{$editor_modes[$ext]}" id="code_source_editor" name="content" style="width:100%; height:300px">{$data.content|escape}</textarea>
                        <div id="code_editor" style="display:none"></div>
                        <span class="field-error" data-field="overwrite"></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="checkbox" id="switchSyntaxHL"> <label for="switchSyntaxHL" class="fieldhelp">{t}Включить подсветку синтаксиса{/t}</label></td>
            </tr>
                                                                            </table>
        </div>
    </form>
</div>

<style type="text/css" media="screen">
    #code_editor { 
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
</style>

<script>
$.allReady(function() {
    var editor;
    var $editor_textarea = $('#code_source_editor');    
    
    var setSyntaxHL = function() {
        var $editor_div = $('#code_editor');
        if ($('#switchSyntaxHL').is(':checked')) {
            if ($editor_textarea.is(':visible')) {
                $editor_textarea = $('#code_source_editor').css('visibility', 'hidden');
                if (!editor) {
                    editor = ace.edit($editor_div.get(0));
                    editor.getSession().setUseWorker(false);
                    var mode = $editor_textarea.data('editorMode');
                    console.log('mode', mode);
                    editor.getSession().setMode("ace/mode/" + mode);
                }
                editor.getSession().setValue( $editor_textarea.val() );                
                editor.resize();
                $editor_div.show();
                $.cookie('tmanager-use-editor', 1);
            }
        } else {
            if ($editor_div.is(':visible')) {
                $editor_div.hide();
                $editor_textarea.val( editor.getSession().getValue() );
                $editor_textarea.css('visibility', 'visible');
                $.cookie('tmanager-use-editor', null);
            }
        }
    }
    
    $('#switchSyntaxHL').change(setSyntaxHL);
    
    if ($.cookie('tmanager-use-editor') == 1) {
        $('#switchSyntaxHL').get(0).checked = true;
        setTimeout(function() {
            $('#switchSyntaxHL').trigger('change');
        }, 250);
    }
    
    $('#template-edit-form').bind('beforeAjaxSubmit', function() {
        if ($('#switchSyntaxHL').is(':checked')) {
            $editor_textarea.val( editor.getSession().getValue() );
        }
    });
})
</script>