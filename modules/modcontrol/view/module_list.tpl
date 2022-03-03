<div class="side-modules">
    {foreach $modules as $module}
        <div class="m-b-20">
            <a href="{adminUrl do="edit" mod=$module.class mod_controller="modcontrol-control"}" class="f-18">{$module.name}</a>
            <p>{$module.description}</p>
        </div>
    {/foreach}
</div>