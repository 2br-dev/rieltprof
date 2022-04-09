{* Шаблон заглушки блока. Отображается, когда блок не настроен *}
{$can_edit = $current_user->isAdmin() && !$this_controller->getDebugGroup()}
<div class="skeleton-block{if $can_edit} hoverable{/if}">
    {if $skeleton_html}
        {$skeleton_html}
    {else}
        <img src="{$THEME_IMG}/skeleton/{$skeleton}" alt="{$name}">
    {/if}
    {if $can_edit}
        <div class="skeleton-block__buttons">
            <div>
                <a class="skeleton-block__change" data-bs-offset="0, 0" data-bs-toggle="dropdown" data-bs-reference="parent" href="#">
                    <span class="me-2">{$name}</span>
                </a>
                <div class="dropdown-menu skeleton-block__dropdown dropdown-menu-end">
                    <div class="fs-6 text-gray mb-3">{t}Чтобы настроить блок, нужно:{/t}</div>
                    <ol>
                        {foreach $do as $action}
                            <li>
                                {if $action.href}
                                    <a href="{$action.href}" {if $action.class}class="{$action.class}"{/if}>{$action.title}</a>
                                {else}
                                    {$action.title}
                                {/if}
                            </li>
                        {/foreach}
                    </ol>
                </div>
            </div>
            {if $param.generate_by_grid}
                <div>
                    <a class="skeleton-block__delete" data-bs-toggle="dropdown" data-bs-reference="parent" href="#">
                        <svg width="16" height="18" viewBox="0 0 16 18" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4 0H4.5H11.5H12V0.5V2H16V3H14V17.5V18H13.5H2.5H2V17.5V3H0V2H4V0.5V0ZM5 2H11V1H5V2ZM3 3V17H13V3H3ZM6 6H7V15H6V6ZM10 6H9V15H10V6Z" />
                        </svg>
                    </a>
                    <div class="dropdown-menu skeleton-block__dropdown dropdown-menu-end text-center">
                        <div class="fs-4 fw-bold text-dark mb-3">{t}Удалить блок?{/t}</div>
                        <div>
                            <a href="{adminUrl do="delModule" mod_controller="templates-blockctrl" id="{$param._block_id}"}" class="crud-get btn btn-sm btn-light-danger px-4">{t}Да{/t}</a>
                            <a class="btn btn-sm btn-light-success px-4" data-bs-dismiss>{t}Нет{/t}</a>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    {/if}
</div>