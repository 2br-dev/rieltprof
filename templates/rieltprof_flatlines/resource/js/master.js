var objects;
var my_objects;

var object_photos_swiper;

$(() => {
	// ================= Initialize event listeners ==================
	$('body').on('change', '.input-field input, .input-field textarea', updateInputFieldLabel);
	$('body').on('change', 'input[type="file"]', updateInputFileField);
	$('body').on('click', '.object-link', toggleRowExpanded);
	$('body').on('click', '.burger', toggleSidebar);
	// $('body').on('click', 'a.phone', openPhonePopup);
	$('body').on('click', clickOutside);
	$('body').on('click', hideAbsolutes);
	$('body').on('click', '.object-link', gotoObject);
	$('body').on('click', '.expand-btn', switchSidebarWidth);
	$('body').on('click', '.tab-link', selectTab);
	$('body').on('click', '.fab-menu-trigger', toggleFab);
	$('body').on('click', '.fab-menu-closer', closeFab);
	$('body').on('click', '.toggle-view', toggleView);
	$('body').on('click', '.filters-trigger', toggleFilters);
	$('body').on('click', '.close-filters-trigger', closeFilters);
	$('body').on('click', '[data-target-modal]', openModal);
	$('body').on('click', '.modal .close', closeModal);
	// $('body').on('click', '.fab-wrapper.phone', loadFabPhone);

	// ================= Call primary functions ======================
	initSideBar();
	initSlider();

	if($('textarea').length){
		$('textarea').autoResize();
	}

	$('.response-rating-wrapper').each((index, selector) => {
		var initial = $(selector).data('rate');
		$(selector).find('.response-rating').rate({
			initial_value: initial
		})
	});

    if($('.rating').length){
        $('.rating').rate({
            update_input_field_name: $("#rating"),
            step_size: 1,
            initial_value: 1
        });
    }

    $('.response-rating-wrapper').each((index, selector) => {
        var initial = $(selector).data('rate');

        $(selector).find('.response-rating').rate({
            initial_value: initial,
            readonly: true
        })
    });
});

function closeFab(e){
    if(e){
        e.preventDefault();
        e.stopPropagation();
    }

	$(this).next().attr('href', '').text('').parents('.fab-wrapper').removeClass('expanded');
}

function closeModal(e){
    if(e) {
        e.preventDefault();
    }
	$(this).parents('.modal').removeClass('open');
    $(this).parents('.modal').find('form')[0].reset();
    $('.nempty').removeClass('nempty');
}

function openModal(e){
    if(e) {
        e.preventDefault();
    }
	var modalId = $(this).data('target-modal');
	$('#' + modalId).addClass('open');
}

function initSlider(){
	if($('.photos').length){
		object_photos_swiper = new Swiper('.photos', {
			pagination: {
				el: '.swiper-pagination',
				clickable: true
			}
		});
	}
}

function closeFilters(e){
    if(e) {
        e.preventDefault();
    }

    var filter_block = $('.filters-block');
    filter_block.removeClass('active');
	if(filter_block.hasClass('active-mobile')){
        filter_block.addClass('active');
        filter_block.removeClass('active-mobile');
    }else {
        $('.global-wrapper').removeClass('sidebar-shown');
    }
}

function toggleFilters(e){
    if(e) {
        e.preventDefault();
    }
	$('.global-wrapper').toggleClass('sidebar-shown');
    var filter_block = $('.filters-block');
    filter_block.toggleClass('active');
	if(!filter_block.hasClass('active')){
	    filter_block.addClass('active-mobile');
    }else{
	    filter_block.removeClass('active-mobile');
    }
}

function toggleView(e){
    if(e) {
        e.preventDefault();
    }
	var target = $(this).data('target');
	var mode = $(target).data('mode');
	var newMode = mode == 'list' ? 'cards' : 'list';
	
	$(target).attr('data-mode', newMode);
	$(this).attr('data-mode', newMode);

	$(target).data('mode', newMode);
	$(this).data('mode', newMode);
}

// ===================== Defined core functions ======================
function hideAbsolutes(e){
	var path = e.originalEvent.path;
	var filtered = path.filter(el => {
		return $(el).hasClass('fab-wrapper');
	});
	if(!filtered.length){
		$('.fab-wrapper').removeClass('opened');
	}
	if($('.navigation-holder').length){
		$('.navigation-holder').removeClass('active');
	}

}

function toggleFab(e){
    if(e) {
        e.preventDefault();
    }
	if($(this).parents('.fab-wrapper').find('.fab-menu-item').length){
		$(this).parents('.fab-wrapper').toggleClass('opened');
	}
}

function selectTab(e){
    if(e) {
        e.preventDefault();
    }
	var target = $(this).data('target');
	var parent = $(this).parents('.tabs-wrapper');

	parent.find('.tab').removeClass('active');
	parent.find('#'+target).addClass('active');
	parent.find('.tab-link').removeClass('active');
	$(this).addClass('active');
}

function switchSidebarWidth(e){
    if(e) {
        e.preventDefault();
    }
	$(this).parents('.categories-sidebar').toggleClass('collapsed');
	$(this).parents('.global-wrapper').toggleClass('expanded');
	var savedClass = $('.categories-sidebar').hasClass('collapsed') ? 'collapsed' : null;
	localStorage.setItem("sidebarClass", savedClass);
}

function gotoObject(){
	var objectUrl = $(this).data('url') ? $(this).data('url') : "/object.html";
	if($(window).width() <= 800)
		location.href = objectUrl;
}

function toggleSidebar(e){
    if(e) {
        e.stopPropagation();
    }
	var target = $(this).data('target');
	$(this).toggleClass('active');
	$('#'+target).toggleClass('active');
}

function initSideBar(){

	var sc = localStorage.getItem("sidebarClass");
	if (sc == 'expanded') {
		$('.categories-sidebar').addClass('no-transition');
		setTimeout(() => {
			$('.categories-sidebar').addClass(sc);
			$('.global-wrapper').removeClass('expanded');
		}, 100);
	}
}

function clickOutside(e){

	$('.fab').removeClass('expanded').text('');

	$('.phone').removeClass('opened').find('.bubble').text('');
	var path = e.originalEvent.path;

	var targetSidebar = $(path).filter((index, selector) => {
		return $(selector).hasClass('sidebar');
	});

	var targetFab = $(path).filter((index, selector) => {
		return $(selector).hasClass('fab-wrapper');
	})

	if(!targetFab.length){
		$('.fab-wrapper').removeClass('expanded').find('.fab-menu-trigger').attr('href', '').text('');
	}

	if(!targetSidebar.length){
		$('.sidebar').removeClass('active');
		$('.burger').removeClass('active');
	}
}

function openPhonePopup(e){

	var expandedClass = $(this).hasClass("opened") ? "" : "opened";
	
	if(expandedClass != ""){

		// $('.phone').removeClass('opened').find('.bubble').text('');
        if(e){
            e.preventDefault();
            e.stopImmediatePropagation();
        }
		$.ajax({
			url: '/data/phone_num.txt',
			success: result => {
				$(this).attr('href', 'tel:'+result).addClass(expandedClass).find('.bubble').text(result);
			}
		});
	}
}

function toggleRowExpanded(e){

	var this_expanded = $(this).hasClass('expanded') ? "expanded" : "";
	var objectCard = $(this).next().find('.object-card');
	
	var path = e.originalEvent.path;

	var links = path.filter((selector) => {
		return selector.tagName == 'A';
	})
	
	if(links.length)
		return;

	$('.object-link').removeClass('expanded');
	$('.object-card').removeClass('expanded');
	
	$(this).addClass(this_expanded);
	objectCard.addClass(this_expanded);

	$(this).toggleClass('expanded');
	objectCard.toggleClass('expanded');
}

function updateInputFieldLabel(){

	if($(this).val() == ''){
		$(this).removeClass('nempty');
	}else{
		$(this).addClass('nempty');
	}
}

function updateInputFileField(){
	var fileName = this.files[0].name;
	$(this).parents('.col').find('span').text(fileName);
}

function fillImages(){
	$('.lazy-image').each((index, el) => {
		var src = $(el).data('src');
		if(src && src != ''){
			$(el).css({
				backgroundImage: 'url('+src+')'
			});
		}
	})
}
