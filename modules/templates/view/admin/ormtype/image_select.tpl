{addcss file="common/owlcarousel/owl.carousel.min.css" basepath="common"}
{addcss file="common/owlcarousel/owl.theme.default.min.css" basepath="common"}
{addjs file="owlcarousel/owl.carousel.min.js"}
{addcss file="%templates%/imageselect.css"}
{addjs file="%templates%/imageselect.js"}
<div class="image-select owl-theme">
    <div class="items owl-carousel" style="width:228px">
        {foreach $field->getFrames() as $frame}
            <div class="item" data-key="{$frame.value}" {if $frame.value == {$field->get()}}data-active{/if}>
                <div>
                    <img src="{$frame.image}" alt="">
                </div>
                <div class="m-t-10 text-center">
                    {$frame.title}
                </div>
            </div>
        {/foreach}
    </div>
    <input type="hidden" name="{$field->getFormName()}" value="{$field->get()}">
</div>