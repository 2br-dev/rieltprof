{* Шаблон для генерации форм для пользовательского режима *}
{if !$view_params || $view_params.form}
    {$prop->formView(['form' => true], $object)}
{/if}
{if !$view_params || $view_params.error}
    {$error=$object->getErrorsByForm($prop->name, ', ')}
    {if !empty($error)}<span class="formFieldError" data-field="{$prop->name}">{$error}</span>{/if}
{/if}