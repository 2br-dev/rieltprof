{addcss file="%catalog%/property.css?v=2"}
{addjs file="%catalog%/property.js?v=2"}

<div data-name="tab2" id="propertyblock" data-owner-type="group">
    {foreach $export_profiles as $profile}
        <p>Profile: {$profile['title']}</p>

        <dropList></dropList>

        <updateApiButton></updateApiButton>
    {/foreach}
</div>