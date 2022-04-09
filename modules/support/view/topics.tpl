{extends file="%THEME%/helper/wrapper/my-cabinet.tpl"}
{block name="content"}
    {addjs file="%support%/rscomponent/support.js"}
    <div class="col">
        <h1 class="mb-lg-6 mb-sm-5 mb-4">{t}Обращения в поддержку{/t}</h1>
        <div class="tab-pills__wrap mb-lg-6 mb-5">
            <ul class="nav nav-pills tab-pills" id="pills-tab">
                <li class="nav-item">
                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-home" type="button">{t}Создать обращение{/t}
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-profile" type="button">{t}История обращения{/t}
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" >
                <div class="col-sm-10 col-md-8 col-lg-10 col-xxl-8">
                    <form method="POST">
                        {if $supp->getNonFormerrors()}
                            <div class="alert alert-danger">{$supp->getNonFormerrors()|join:", "}</div>
                        {/if}
                        <div class="mb-3">
                            <div class="fs-5 mb-2">{t}Тема обращения{/t}</div>
                            <div>
                                {if count($list)>0}
                                    <select name="topic_id" class="form-select rs-support-topic-id">
                                        {foreach $list as $item}
                                            <option value="{$item.id}" {if $item.id == $supp.topic_id}selected{/if}>{$item.title}</option>
                                        {/foreach}
                                        <option value="0" {if $supp.topic_id == 0}selected{/if}>{t}Новая тема...{/t}</option>
                                    </select>
                                {/if}
                            </div>
                        </div>
                        <div class="mb-3 {if $supp.topic_id>0}d-none{/if} rs-support-topic-name">
                            <label for="input-topic" class="form-label">{t}Новая тема{/t}</label>
                            {$supp->getPropertyView('topic', ['id' => 'input-topic'])}
                        </div>
                        <div class="mb-4">
                            <label for="textarea1" class="form-label">{t}Опишите свой вопрос{/t}</label>
                            {$supp->getPropertyView('message', ['id' => 'textarea1'])}
                        </div>
                        <button type="submit" class="btn btn-primary col-12 col-sm-auto">{t}Отправить{/t}</button>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-profile">
                <h2 class="mb-lg-5 mb-4">{t}Ваши запросы{/t}</h2>
                {if $list}
                    <div class="last-child-margin-remove">
                        {foreach $list as $item}
                            <div class="lk-support-item mb-3" data-id="{$item.id}">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <a href="{$item->getUrl()}" class="link-dark text-decoration-none">{$item.title}</a>
                                    </div>
                                    <div>
                                        <a class="lk-support-item__delete rs-topic-delete"
                                           data-remove-url="{$router->getUrl('support-front-support', ["Act" => "delTopic", "id" => $item.id])}"
                                           title="{t}Удалить переписку по этой теме{/t}">
                                            <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.99935 6C9.81523 6 9.66602 6.14279 9.66602 6.31898V12.3477C9.66602 12.5237 9.81523 12.6667 9.99935 12.6667C10.1835 12.6667 10.3327 12.5237 10.3327 12.3477V6.31898C10.3327 6.14279 10.1835 6 9.99935 6Z" />
                                                <path d="M5.99935 6C5.81523 6 5.66602 6.14279 5.66602 6.31898V12.3477C5.66602 12.5237 5.81523 12.6667 5.99935 12.6667C6.18346 12.6667 6.33268 12.5237 6.33268 12.3477V6.31898C6.33268 6.14279 6.18346 6 5.99935 6Z" />
                                                <path d="M3.26116 5.30345V12.9968C3.26116 13.4515 3.43566 13.8785 3.7405 14.1849C4.04393 14.4922 4.46621 14.6666 4.90815 14.6673H11.0912C11.5333 14.6666 11.9555 14.4922 12.2589 14.1849C12.5637 13.8785 12.7382 13.4515 12.7382 12.9968V5.30345C13.3442 5.14976 13.7368 4.59038 13.6558 3.99624C13.5746 3.40223 13.0449 2.95787 12.4179 2.95775H10.7447V2.56743C10.7467 2.2392 10.6108 1.92402 10.3677 1.69214C10.1245 1.46039 9.79411 1.33134 9.45059 1.33403H6.54876C6.20524 1.33134 5.87487 1.46039 5.63169 1.69214C5.38851 1.92402 5.25269 2.2392 5.2546 2.56743V2.95775H3.58144C2.9544 2.95787 2.42477 3.40223 2.34358 3.99624C2.26252 4.59038 2.65518 5.14976 3.26116 5.30345ZM11.0912 14.0428H4.90815C4.34941 14.0428 3.91475 13.5842 3.91475 12.9968V5.33089H12.0846V12.9968C12.0846 13.5842 11.6499 14.0428 11.0912 14.0428ZM5.90819 2.56743C5.90602 2.40484 5.97291 2.24835 6.09367 2.13357C6.21431 2.01879 6.37847 1.95573 6.54876 1.95854H9.45059C9.62088 1.95573 9.78504 2.01879 9.90568 2.13357C10.0264 2.24823 10.0933 2.40484 10.0912 2.56743V2.95775H5.90819V2.56743ZM3.58144 3.58226H12.4179C12.7428 3.58226 13.0061 3.8339 13.0061 4.14432C13.0061 4.45475 12.7428 4.70638 12.4179 4.70638H3.58144C3.25656 4.70638 2.99321 4.45475 2.99321 4.14432C2.99321 3.8339 3.25656 3.58226 3.58144 3.58226Z" />
                                                <path d="M7.99935 6C7.81523 6 7.66602 6.14279 7.66602 6.31898V12.3477C7.66602 12.5237 7.81523 12.6667 7.99935 12.6667C8.18346 12.6667 8.33268 12.5237 8.33268 12.3477V6.31898C8.33268 6.14279 8.18346 6 7.99935 6Z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="fs-5 text-gray">{t time="{$item.updated|dateformat:"@date @time"}"}от %time{/t}
                                        {if $item.newcount>0} <span class="text-danger">({t}новых сообщений{/t}: {$item.newcount})</span>{/if}
                                    </div>
                                    <div><a href="{$item->getUrl()}">{t}Подробнее{/t}</a></div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                {else}
                    <div>{t}Еще нет ни одного обращения в поддержку{/t}</div>
                {/if}
            </div>
        </div>
    </div>
{/block}
