{addcss file="%banners%/schedule.css"}
{include file=$field->getOriginalTemplate()}
<div id="scheduleForm" class="scheduleForm {if !$field->get()}hidden{/if}">
    <div class="row">
        {$elem.__date_start->getTitle()}<br/>
        {$elem.__date_start->formView()}
    </div>
    <div class="row">
        {$elem.__date_end->getTitle()}<br/>
        {$elem.__date_end->formView()}
    </div>
</div>
<script type="text/javascript">
    /**
     * Изменение флажка расписания
     */
    $("[name='use_schedule']").on('change', function(){
        $("#scheduleForm").toggleClass('hidden', !$(this).prop('checked'));
    });
</script>