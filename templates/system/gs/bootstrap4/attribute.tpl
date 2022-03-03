{if $level.section["{$field}_xs"]} {$name}{$level.section->transformBootstrap4Width("{$field}_xs")}{/if}{*
*}{if $level.section["{$field}_sm"]} {$name}-sm{$level.section->transformBootstrap4Width("{$field}_sm")}{/if}{*
*}{if $level.section["{$field}"]} {$name}-md{$level.section->transformBootstrap4Width($field)}{/if}{*
*}{if $level.section["{$field}_lg"]} {$name}-lg{$level.section->transformBootstrap4Width("{$field}_lg")}{/if}{*
*}{if $level.section["{$field}_xl"]} {$name}-xl{$level.section->transformBootstrap4Width("{$field}_xl")}{/if}