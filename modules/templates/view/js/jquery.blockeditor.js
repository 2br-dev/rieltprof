/**
* Редактор блоков
*/

(function( $ ){
  $.fn.blockEditor = function(options) {  
      
      options = $.extend({
          sortSectionUrl: '',
          sortBlockUrl: '',
          gridSystem: ''
      }, options);
      
      return this.each(function() {
          var context = this;
          
          
          //Разворачиваем инструменты у широких блоков, секций, ...
          var expandTools = function() {
              $('.gs-manager .smart-dropdown').each(function() {
                  var common_tools = $(this).parent();
                  var width = 50;
                  $(this).find('.dropdown-menu li').each(function() {
                      width = width + 28;
                  });

                  var need_width = ($(this).offset().left - common_tools.offset().left) + width;
                  var is_need_wide = common_tools.width() > need_width;
                  $(this).closest('.block, .area, .row, .gs-manager').toggleClass('wide', is_need_wide);

                  if (is_need_wide) {
                      //Включаем tooltip
                      $('.dropdown-menu a', this).each(function() {
                          $(this).tooltip('enable');
                      });
                  } else {
                      //Выключаем tooltip
                      $('.dropdown-menu a', this).each(function() {
                          $(this).tooltip('disable');
                      });
                  }

              });
          };
          
          expandTools();
          
          var redrawColumns = function() {
              var current_device = $('.device-selector li.act').data('device');              
              var devices = ['lg', 'md', 'sm', 'xs', 'xl'];
              var sectionSizesReference = {
                  '-1': 'auto',
                  '-2': 'col'
              };

              $('.pageview .section-width').each(function() {
                  
                  var start = devices.indexOf(current_device);
                  var result = '';
                  for(var i=start; i < devices.length; i++) {
                      var width = $(this).data(devices[i] + '-width');
                      if (width) {
                          result = width; 
                          break;
                      }
                  }
                  if (sectionSizesReference[result]) {
                      result = sectionSizesReference[result];
                  }
                  $(this).text(result ? result : '-');
              });
          };
          
          redrawColumns();
          //Активируем переключатель устройств
          $('.device-selector li').off('.blockeditor').on('click.blockeditor', function() {
              var current_device = $(this).addClass('act').data('device');
                            
              $(this).siblings().removeClass('act');
              $('.pageview').removeClass('xs sm md lg xl').addClass(current_device);
              
              $.cookie('page-constructor-device-'+options.gridSystem , current_device);
              redrawColumns();
              expandTools();
          });
          
          //Активируем переключатель активности сетки
          $('.gs-manager .grid-switcher').off('.blockeditor').on('click.blockeditor', function() {
              var is_off = $(this).toggleClass('off').is('.off');
              var container = $(this).closest('.gs-manager').toggleClass('grid-disabled', is_off);
              $.cookie('page-constructor-disabled-' + container.data('container-id'), is_off ? 1 : null);
              expandTools();
          });

          //Активируем переключатель активности сетки
          $('.gs-manager .visible-switcher').off('.blockeditor').on('click.blockeditor', function() {
              var is_off = $(this).toggleClass('off').is('.off');
              var container = $(this).closest('.gs-manager').toggleClass('visible-disabled', is_off);
              $.cookie('page-visible-disabled-' + container.data('container-id'), is_off ? 1 : null);
          });
          
          $('.gs-manager .block .iswitch').off('.blockeditor').on('click.blockeditor', function() {
              var block = $(this).closest('.block');
              block.toggleClass('on');
              
              $.ajaxQuery({
                  url: options.toggleViewBlock,
                  data: {
                    id: block.data('blockId')
                  }
              });                  
          });

          //private
          var 
              sourceContainer,
              initSortBlocks = function() {
                  //Включаем сортировку блоков
                  $('.sort-blocks', context).sortable({
                      connectWith: '.workarea.sort-blocks',
                      placeholder: 'sortable-placeholder',
                      forcePlaceholderSize: true,
                      handle: '.drag-block-handler',
                      start: function(event, ui) {
                          ui.placeholder.addClass(ui.item.attr('class'));
                          ui.item.startParent = ui.item.closest('.area[data-section-id]');
                      },
                      change: function(event, ui) {
                          checkInsetAlign(ui.placeholder);
                      },
                      update: function(event, ui) {
                          if (!ui.item.data('blockId')){
                              return;
                          }
                          let parent = ui.item.closest('.area[data-section-id]');
                          ui.item.stopParent = parent;

                          let ajax_options = {
                              id: ui.item.data('blockId'),
                              parent_id : ui.item.closest('[data-section-id]').data('sectionId')
                          };

                          //Определим параметры перемещения
                          let next = ui.item.next();
                          let prev = ui.item.prev();
                          ajax_options['position'] = 0;
                          if (next.length || prev.length){
                              let position   = 'before';
                              let block_id;
                              if (!next.length){
                                  block_id = prev.data('blockId');
                                  position = "after";
                              }else{
                                  block_id = next.data('blockId');
                              }
                              ajax_options['position'] = position;
                              ajax_options['block_id'] = block_id;
                          }

                          $.ajaxQuery({
                              url  : options.sortBlockUrl,
                              data : ajax_options
                          });

                          checkInsetAlign(ui.item);

                          checkSectionSortType(ui.item.startParent);
                          checkSectionSortType(ui.item.stopParent);

                          initSortSections();
                          initSortBlocks();
                          expandTools();

                          ui.item.startParent = null;
                          ui.item.stopParent = null;
                      }
                  });
              },
              //Включаем сортировку секций
              initSortSections = function() {
                  //Включаем сортировку секций

                  $('.sort-sections', context).sortable({
                      forcePlaceholderSize: true,
                      tolerance: 'pointer',
                      //cancel: '.container > .workarea',
                      connectWith: '.workarea.sort-sections:not(.container-workarea)',
                      placeholder: 'sortable-placeholder',
                      handle: '> .commontools .drag-handler',
                      start: function(event, ui) {
                          ui.placeholder.addClass(ui.item.attr('class')).append('<div class="border"></div>');
                          ui.item.startParent = ui.item.parent().closest('[data-section-id]');
                      },
                      change: function(event, ui) {
                          checkAlphaOmega(ui.item.parent().closest('[data-section-id]'));
                      },
                      update: function(event, ui) {
                          if (!ui.item.startParent){
                              return;
                          }

                          ui.item.stopParent = ui.item.parent().closest('[data-section-id]');

                          checkAlphaOmega(ui.item.startParent);
                          checkAlphaOmega(ui.item.stopParent);

                          let ajax_options = {
                              id: ui.item.data('sectionId'),
                              parent_id : ui.item.stopParent.data('sectionId')
                          };

                          //Определим параметры перемещения
                          let next = ui.item.next();
                          let prev = ui.item.prev();
                          ajax_options['position'] = 0;
                          if (next.length || prev.length){
                              let position   = 'before';
                              let section_id;
                              if (!next.length){
                                  section_id = prev.data('sectionId');
                                  position = "after";
                              }else{
                                  section_id = next.data('sectionId');
                              }
                              ajax_options['position']   = position;
                              ajax_options['section_id'] = section_id;
                          }

                          $.ajaxQuery({
                              url  : options.sortSectionUrl,
                              data : ajax_options
                          });

                          checkSectionSortType(ui.item.startParent);
                          checkSectionSortType(ui.item.stopParent);

                          initSortSections();
                          expandTools();

                          ui.item.startParent = null;
                          ui.item.stopParent = null;
                      }
                  });
              },
              checkInsetAlign = function(block) {
                  var parent = block.closest('[data-inset-align]');

                  block.removeClass('alignright alignleft');
                  if (parent.data('insetAlign') == 'right') {
                      block.addClass('alignright');
                  }
                  else if (parent.data('insetAlign') == 'left') {
                      block.addClass('alignleft');
                  }
              },
              checkSectionSortType = function(section) {
                  if (!section || section.is('.row')){
                      return;
                  }

                  if (section.is('.area')) {
                      var has_sections = $('> .workarea [data-section-id]', section).length > 0;
                      var has_blocks = $('> .workarea [data-block-id]', section).length > 0;

                      if (has_sections) {
                          $('> .workarea', section).addClass('sort-sections').removeClass('sort-blocks');
                      }
                      if (has_blocks) {
                          $('> .workarea', section).removeClass('sort-sections').addClass('sort-blocks');
                      }
                      if (!has_sections && !has_blocks) {
                          $('> .workarea', section).addClass('sort-sections sort-blocks');
                      }
                  }
              },
              checkAlphaOmega = function(sectionParent) {
                  $('> .workarea > .area', sectionParent).removeClass('alpha omega');

                  if (!sectionParent.is('.gs-manager')) { //Если перемещение произошло в секции
                      $('> .workarea >.area:not(.ui-sortable-helper):first', sectionParent).addClass('alpha');
                      $('> .workarea >.area:not(.ui-sortable-helper):last', sectionParent).addClass('omega');
                  }
              },
              onStartContainerDrag = function(e) {
                  sourceContainer = $(this).closest('.gs-manager').get(0);
                  $(sourceContainer).addClass('sourceContainer');
                  $('.gs-manager:not(".sourceContainer")')
                    .bind('mouseenter.cDrag', onContainerEnter)
                    .bind('mouseleave.cDrag', onContainerLeave);
              },
              onContainerEnter = function(e) {
                  if (sourceContainer) {
                      $('.destinationContainer').removeClass('destinationContainer');
                      $(this).addClass('destinationContainer').append('<div class="dstOverlay" />');
                  }
              },
              onContainerLeave = function(e) {
                  if (sourceContainer) {
                      $(this).removeClass('destinationContainer');
                      $('.dstOverlay', this).remove();
                  }
              },
              onStopContainerDrag = function(e) {
                  if (sourceContainer) {
                      //Перемещаем контейнеры
                      var dst = $('.destinationContainer:first');
                      
                      if (dst.length) {
                          var dst_clone = dst.clone().insertAfter(dst);
                          dst.insertAfter(sourceContainer);
                          dst_clone.replaceWith(sourceContainer);
                          
                          var 
                            source_id = $(sourceContainer).data('containerId'),
                            destination_id = dst.data('containerId');
                          
                          $.ajaxQuery({
                              url: options.sortContainerUrl,
                              data: {
                                source_id: source_id,
                                destination_id: destination_id
                              }
                          });
                      }
                      
                      //Завершаем перемещение
                      $(sourceContainer).removeClass('sourceContainer');
                      sourceContainer = null;
                      $('.gs-manager').unbind('.cDrag');
                      $('.destinationContainer').removeClass('destinationContainer');
                      $('.dstOverlay').remove();
                  }
              };

          initSortSections();
          initSortBlocks();
          
          $('.gs-manager > .commontools > .drag-handler').on({
              mousedown: onStartContainerDrag,
          });
          
          $('body').mouseup(onStopContainerDrag);
          $(window).resize(function() {
              expandTools();
          });
      });
      
  };
})( jQuery );