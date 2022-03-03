{extends file="%THEME%/wrapper_blacklist.tpl"}
{block name="content"}
    <div class="toolbar">
        <form class="toolbar-body" method="POST">
            {csrf}
            {$this_controller->myBlockIdInput()}
            <div class="input-field">
                <div class="prefix magnify"></div>
                <input type="text" name="phone" class="phone_mask"><label for="">Введите телефон для проверки</label>
            </div>
            <div class="btn-wrapper">
                <button type="submit" class="btn">Проверить</button>
            </div>
            <!-- <div class="separator"></div> -->
        </form>
    </div>
    <div class="content">
        <div class="crumbs-wrapper">
            <div class="crumbs-rest-wrapper">
                <div class="crumbs-rest">
                    <a href="/" class="crumb ">Главная</a>
                    <div class="separator">›</div>
                    <a class="crumb">Черный список</a>
                </div>
            </div>
        </div>
        <div class="responses-list">
            {if !empty($reviews)}
                <div class="title">
                    <span>{$phone}</span>
                </div>
                {foreach $reviews as $review}
                    <div class="responses">
                        <div class="response">
                            <div class="body">
                                {$review['comment']}
                            </div>
                            <div class="footer">
                                <div class="author">{$review['author']}</div>
                                <div class="date">{$review['date']}</div>
                            </div>
                        </div>
                    </div>
                {/foreach}
            {else}
                {if $phone != ''}
                    <div class="title">
                        <span>о номере <strong>{$phone}</strong> ничего не найдено</span>
                    </div>
                {/if}
            {/if}
        </div>
    </div>
{/block}
