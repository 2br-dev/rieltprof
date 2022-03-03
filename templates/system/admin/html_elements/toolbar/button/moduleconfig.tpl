<a {if $button->getHref()!=''}href="{$button->getHref()}"{/if} {$button->getAttrLine()}>
    <img src="{$Setup.IMG_PATH}/adminstyle/modoptions.png">
    {if $button->getTitle()}<span class="visible-xs-inline">{$button->getTitle()}</span>{/if}</a>