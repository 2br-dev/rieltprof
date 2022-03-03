/**
 * Плагин инилицализирует работу содания шаблонов автозадач
 *
 * @author ReadyScript lab.
 */
(function( $ ){
    $.fn.taskRule = function( method ) {
        var defaults = {
                taskRuleBlock: '.task-rule-block',
                taskContainer: '.task-container',
                taskContainerBody: '.task-container > tbody',
                taskItem: '.task-item',
                taskRemove: '.remove',
                taskEdit: '.edit',
                taskValue: '.task-value',
                taskNumber: '.task-number'

            },
            args = arguments;

        return this.each(function() {
            var $this = $(this).closest('form'),
                data = $this.data('taskRule');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('taskRule', data);
                    data.opt = $.extend({}, defaults, initoptions);

                    $this.on('click', '.add-autotask', addAutoTask);
                    $this.find(data.opt.taskContainer)
                        .tableDnD({
                            dragHandle: ".drag-handle",
                            onDragClass: "in-drag",
                            onDrop: updateNumbers
                        })
                         .on('click', data.opt.taskRemove, removeAutoTask)
                         .on('click', data.opt.taskEdit, editAutoTask)
                         .on('reset', reset);
                }
            };

            //private
            var addAutoTask = function(event, values, updateItem) {
                var ifrule = $('select[name="rule_if_class"]').val();

                $.rs.openDialog({
                    url: $(data.opt.taskRuleBlock, $this).data('urls').add,
                    ajaxOptions: {
                        data: {
                            rule_if_id: ifrule,
                            task_template_values: values
                        }
                    },
                    dialogOptions: {
                        width:'75%',
                        height:0.75 * $(window).height()
                    },
                    afterOpen: function(dialog) {
                        dialog.on('crudSaveSuccess', function(event, response) {
                            if (updateItem) {
                                updateItem.replaceWith(response.task_template_block);
                            } else {
                                $(data.opt.taskContainerBody, $this).append(response.task_template_block);
                            }
                            updateNumbers();
                            checkVisible();
                            $this.find(data.opt.taskContainer).tableDnDUpdate();
                        });
                    }
                });
            },

            removeAutoTask = function() {
                if (confirm(lang.t('Вы действительно желаете удалить задачу?'))) {
                    $(this).closest('tr').remove();
                    updateNumbers();
                    checkVisible();
                    $this.find(data.opt.taskContainer).tableDnDUpdate();
                }
            },

            editAutoTask = function(event) {
                var updateItem = $(this).closest(data.opt.taskItem);
                var values = updateItem.find(data.opt.taskValue).val();

                return addAutoTask(event, values, updateItem);
            },

            updateNumbers = function() {
                $(data.opt.taskItem, $this).each(function(i) {
                    $(this).find(data.opt.taskNumber).text(i+1);
                });
            },

            reset = function() {
                $(data.opt.taskContainerBody, $this).empty();
                checkVisible();
            },

            checkVisible = function() {
                var hasElements = $(data.opt.taskContainerBody, $this).children().length > 0;
                console.log(hasElements);

                $(data.opt.taskContainer).parent().toggle(hasElements);
            };


            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };

})( jQuery );