{* Шаблон для генерации форм для пользовательского режима, перегружает %system%/coreobject/prop_form.tpl *}
{$error = $object->getErrorsByForm($prop->name, ', ')}
{if !$view_params || $view_params.form}
    {$prop->addClass('form-control')|devnull}
    {if $error}
        {$prop->addClass('is-invalid')|devnull}
    {/if}
    {$prop->formView(['form' => true], $object)}
{/if}
{if !$view_params || $view_params.error}
    {if !empty($error)}<div class="invalid-feedback d-block" data-field="{$prop->name}">{$error}</div>{/if}
{/if}