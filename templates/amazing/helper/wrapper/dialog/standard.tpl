{* Шаблон обертки диалогового окна *}
<div class="modal-dialog modal-dialog-centered col {block "class"}{/block}" {block name="attributes"}{/block}>
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title h2">{block "title"}{/block}</div>
            <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close">
                <img src="{$THEME_IMG}/icons/close.svg" width="24" height="24" alt="">
            </button>
        </div>
        <div class="modal-body">{block "body"}{/block}</div>
    </div>
</div>