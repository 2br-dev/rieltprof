<div class="formbox">
    <form id="userAddForm" method="POST" action="{urlmake}" data-order-block="#userBlockWrapper" enctype="multipart/form-data" class="crud-form" data-dialog-options='{ "width":600, "height":720 }'>

        <input name="is_reg_user" type="radio" value="0" id="link-user" checked>&nbsp;<label for="link-user">{t}Связать с зарегистрированным пользователем{/t}</label><br>
        <input name="is_reg_user" type="radio" value="1" id="reg-user">&nbsp;<label for="reg-user">{t}Зарегистрировать нового пользователя{/t}</label><br>
        <br>
        <div id="partner-link-user" class="reg-tab">
            {$field=$elem->__user_id}
            {include file=$field->getOriginalTemplate()}<br>
        </div>
        <div id="partner-reg-user" class="reg-tab" style="display:none">
            <table class="otable">
                <tr>
                    <td class="otitle">{t}Тип лица{/t}</td>
                    <td>
                        {$is_company=$user.is_company|default:0}
                        <input id="user_type_person" class="user-type" type="radio" name="is_company" value="0" {if !$is_company}checked="checked"{/if}/>
                        <label for="user_type_person">{t}Физическое лицо{/t}</label>&nbsp;
                        <input id="user_type_company" class="user-type" type="radio" name="is_company" value="1" {if $is_company}checked="checked"{/if}/>
                        <label for="user_type_company">{t}Юридическое лицо{/t}</label>
                    </td>
                </tr>
                <tr class="company {if !$user.is_company}hidden{/if}">
                    <td class="otitle">{$user.__company->getTitle()}</td>
                    <td>{$user.__company->formView()}</td>
                </tr>
                <tr class="company {if !$user.is_company}hidden{/if}">
                    <td class="otitle">{$user->__company_inn->getTitle()}</td>
                    <td>{$user.__company_inn->formView()}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user->__name->getTitle()}</td>
                    <td>{include file=$user->__name->getRenderTemplate() field=$user->__name}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user->__surname->getTitle()}</td>
                    <td>{include file=$user->__surname->getRenderTemplate() field=$user->__surname}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user->__midname->getTitle()}</td>
                    <td>{include file=$user->__midname->getRenderTemplate() field=$user->__midname}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user->__phone->getTitle()}</td>
                    <td>{include file=$user->__phone->getRenderTemplate() field=$user->__phone}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user->__login->getTitle()}</td>
                    <td>{include file=$user->__login->getRenderTemplate() field=$user->__login}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user->__e_mail->getTitle()}</td>
                    <td>{include file=$user->__e_mail->getRenderTemplate() field=$user->__e_mail}</td>
                </tr>
                <tr>
                    <td class="otitle">{$user.__pass->getTitle()}</td>
                    <td>
                        {include file=$user.__pass->getRenderTemplate() field=$user.__pass}
                        <input name="changepass" type="hidden" value="1">
                    </td>
                </tr>
                {if $conf_userfields->notEmpty()}
                    {foreach from=$conf_userfields->getStructure() item=fld}
                    <tr>
                        <td class="key">{$fld.title}</td>
                        <td class="value">
                            {$conf_userfields->getForm($fld.alias)}
                            {assign var=errname value=$conf_userfields->getErrorForm($fld.alias)}
                            {assign var=error value=$user->getErrorsByForm($errname, ', ')}
                            {if !empty($error)}
                                <span class="formFieldError">{$error}</span>
                            {/if}
                        </td>
                    </tr>
                    {/foreach}
                {/if}
            </table>

        </div>
    </form>
    <script type="text/javascript">
        /**
        * Получает массив объектов параметров из запроса
        */
        function getParamsArray(query){
            var params = [];
            var parse_params = query.split("&");
            
            for(var i=0;i<parse_params.length;i++){
                var param = parse_params[i].split("=");
                params.push({
                    name : param[0],
                    value : param[1],
                });
            }
            
            return params;
        }
    
        $(function() {
            /** Назначаем действия, если всё успешно вернулось */
            $('#userAddForm').on('crudSaveSuccess', function(event, response) {
                if (response.success && response.insertBlockHTML){ //Если всё удачно и вернулся HTML для вставки в блок
                    var insertBlock = $(this).data('order-block');

                    $(insertBlock).html(response.insertBlockHTML).trigger('new-content');
                    $('#orderForm').data('hasChanges', 1);

                    if (response.user_id){ //Если указан id пользователя
                       $('input[name="user_id"]').val(response.user_id); 
                    }
                    //Посмотрим, если есть кнопки с добавлением доставки заказу, то припишем к запросу ещё и пользователя
                    if ($(".editAddressButton").length && response.user_id){
                       $(".editAddressButton").each(function(){
                           var href = $(this).data('href') ? $(this).data('href') : $(this).attr('href');
                           //разберём запрос
                           var url_array = href.split('?');
                           var url       = url_array[0];
                           var params    = getParamsArray(url_array[1]);
                           //Допишем сведения о пользователе В запрос и обновим ссылку
                           let found_user_id = false;
                           $.each(params, (index, element) => {
                               if (element.name == 'user_id') {
                                   params[index]['value'] = response.user_id;
                                   found_user_id = true;
                               }
                           });
                           if (!found_user_id) {
                               params.push({
                                   name  : 'user_id',
                                   value : response.user_id
                               });
                           }

                           href = url + "?" + $.param(params);
                           $(this).attr('href', href);
                           $(this).data('href', href);
                       }); 
                    }
                }
            });

            /**
             * Смена типа пользователя
             */
            $(".user-type").on('change', function(){
                var val = $(this).val();
                if (val == '0'){
                    $(".company").addClass('hidden');
                }else{
                    $(".company").removeClass('hidden');
                }
            });
            
            //Смена типа регистрации пользователя 
            var regChange = function() {
                var value = $('input[name="is_reg_user"]:checked');
                $('.reg-tab').hide();
                $('#partner-'+value.attr('id')).show();
            };
            $('input[name="is_reg_user"]').change(regChange);
            regChange();
        });
    </script>

</div>