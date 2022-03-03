{$files={moduleinsert var="files_data" name="\Files\Controller\Admin\Block\Files" link_type="files-shoporder" link_id="{$elem.id}"}}     
<div class="collapse-block{if $files_data.files} open{/if}">
   <div class="collapse-title">
      <i class="zmdi zmdi-chevron-right"></i><!--
            --><h3>{t}Прикрепленные файлы{/t}</h3><!--
            --><span class="help-text">{t}(будут видны покупателю на странице просмотра заказа){/t}</span>
   </div>
   <div class="collapse-content">
       {$files}
   </div>
</div>