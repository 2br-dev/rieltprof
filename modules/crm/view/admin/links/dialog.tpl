<div class="formbox">
        <div class="tabs link-manager" id="linkAddForm" data-dialog-options='{ "width":600, "height":720 }'>

            <ul class="tab-nav" role="tablist">
                {foreach $links_type_objects as $type}
                    <li class="{if $type->getId() == $link_type}active{/if}"><a data-target="#tab-link-{$type->getId()}" data-toggle="tab" role="tab">{$type->getTabName()}</a></li>
                {/foreach}
            </ul>

            <div class="tab-content">
                {foreach $links_type_objects as $type}
                    <div id="tab-link-{$type->getId()}" class="tab-pane {if $type->getId() == $link_type}active{/if}" role="tabpanel">
                        <form method="POST" action="{urlmake}" enctype="multipart/form-data" {if $type->getId() == $link_type}class="crud-form"{/if}>
                            <input type="hidden" name="link_type" value="{$type->getId()}">
                            {$type->getTabForm()->getForm()}
                        </form>
                    </div>
                {/foreach}
            </div>
        </div>
</div>