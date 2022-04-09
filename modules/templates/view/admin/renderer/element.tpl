{$inside_template=$element_object.inside_template|default:$element_object.inset_template}

{if $element_object.outside_template}
    <div class="previewOutside">
        <div class="previewHelp">{t}Внешний шаблон{/t}</div>
        <div class="previewOutsideBody">{/if}

{if $element_object.wrap_element}
        <div class="previewWrapper">
            <div class="previewNode">&lt;{$element_object.wrap_element}{if $element_object.wrap_css_class} class="{$element_object.wrap_css_class}"{/if}&gt;</div>
            <div class="previewNodeBody">
{/if}

<div class="previewElement">
    {if !$element_object.invisible}
        {$css = $element_object->renderElementClass($grid_system)}
    <div class="previewNode">&lt;div{if $css} class="{$css}"{/if}&gt;</div>
    {/if}
    <div class="previewNodeBody">
        {if $inside_template}
            <div class="previewInside">
                <div class="previewHelp">{t}Внутренний шаблон{/t}</div>
                <div class="previewInsideBody">
        {/if}

        {if ($grid_system == 'gs960' || $grid_system == 'bootstrap') && $element_object.inset_align != 'wide'}
            <div>
                <div class="previewNode">&lt;div class="{$element_object->renderGridBlockClass($grid_system)}"></div>
                <div class="previewNodeBody">
        {/if}

        <div class="previewContent">{t}Здесь будет вложенный контент{/t}</div>

        {if ($grid_system == 'gs960' || $grid_system == 'bootstrap') && $element_object.inset_align != 'wide'}
                </div>
                <div class="previewNode">&lt;/div&gt;</div>
            </div>
        {/if}

        {if $inside_template}
                </div>
                <div class="previewHelp">{t}Внутренний шаблон{/t}</div>
            </div>
        {/if}
    </div>
    {if !$element_object.invisible}
    <div class="previewNode">&lt;/div&gt;</div>
    {/if}
</div>

{if $element_object.wrap_element}
            </div>
        <div class="previewNode">&lt;/{$element_object.wrap_element}&gt;</div>
    </div>
{/if}
{if $element_object.outside_template}
        </div>
        <div class="previewHelp">{t}Внешний шаблон{/t}</div>
    </div>
{/if}
{if $element_object.is_clearfix_after}
    &lt;div class="{$element_object->renderClearfixClass($grid_system)}"&gt;&lt;/div&gt;
{/if}