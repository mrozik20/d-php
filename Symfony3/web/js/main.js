function showLoader(){
    //if($('body > .loader-modal').length==0){
    //    $('<div class="loader-modal"><div class="loader-circle"></div></div>').appendTo("body");
    //}
}
function hideLoader(){
    //$('body > .loader-modal').remove();
}

//TC-119 - set show class on load to expand the hidden active child menu item
$('.side-navbar > ul.list-unstyled > li.active').find('ul').addClass('show');

var cache = {};
function addAccordionTag(link, input, user, id, val, type) {
    $.ajax({
        async: true,
        cache: false,
        type: 'POST',
        url: link,
        data: {'id': id, 'val': val},
        success: function (html) {
            if(html!=''){
                $("." + user).append(html);
            }
            var tagsCount = input.closest('a').find('.tags-count');
            tagsCount.html($("." + user).find('.btn-outline-primary').length);

            if(type == 'company') {
                successNotification(translations.messages.add_company_success_message);
            } else {
                successNotification(translations.messages.add_tag_success_message);
            }
        },
        error: function (xhr, status, text) {
        	console.log(xhr);
			errorNotification(translations.messages.error_ajax_message)
      	},
    });
    return false;
}

$(document).on("click", ".del-accordion-tag", function() {
    var type = $(this).data("type");
    var id = $(this).data("id");
    var item = $(this).parent();
    var container = item.closest('.consumer-avatar-box-tags');
    if(type == 'company'){
        var container = item.closest('.consumer-avatar-box-company');
    }
    var tagsCount = container.prev().find('.tags-count');
    var link = container.data("url");
    item.remove();
    $(".table-scroll").getNiceScroll().resize();
    $.ajax({
        async: true,
        cache: false,
        type: 'POST',
        url: link,
        data: {'id': id},
        success: function (html) {
            tagsCount.html(Number(tagsCount.text()) - 1);
            if (Number(tagsCount.text()) == 0) {
            	var accordionHeader = tagsCount.closest('a');
    	    	var accodrionBody = accordionHeader.next();
    	    	accodrionBody.removeClass('show').removeClass('collapsing');
    	    	accordionHeader.addClass('collapsed');
            }

            if(type == 'company'){
                successNotification(translations.messages.del_company_success_message);
            } else {
                successNotification(translations.messages.del_tag_success_message);
            }
        },
        error: function (xhr, status, text) {
        	console.log(xhr);
			errorNotification(translations.error_message);
      	},
    });
    return false;
});

$(document).on("click", ".activity-user-details .add-accordion-tag-block", function(e){
	e.preventDefault();
	e.stopPropagation();
    if($(this).data("show") != "1") {
        $(this).data("show", "1");
        var input = $(this).find("input");
        input.removeClass("hidden").focus();
        var l = input.data('ajaxurl');
        var la = input.data('addurl');

        if(l in cache){} else{ cache[l] = {}}
        input.autocomplete({
            source: function( request, response ) {
                var term = request.term;
                if ( term in cache[l] ) {
                    response( cache[l][ term ] );
                    return;
                }

                $.getJSON( l, request, function( data, status, xhr ) {
                    cache[l][ term ] = data;
                    response( data );
                });
            },
            minLength: 1,
            select: function( event, ui ) {
            	addAccordionTag(la, input, input.data('user'), ui.item.id, '', input.data('type'));
            	input.val('').addClass("hidden").autocomplete('destroy');
            	input.parent().data("show", "0");
            }
        })
    }
    return false;
});

$(document).on("keyup", ".activity-user-details .add-accordion-tag-block input", function(e) {
    e.stopPropagation();
    var type = $(this).data("type");
    if(e.keyCode == 13) {
    	if ($(this).val().trim() != '') {
	    	var accordionHeader = $(this).closest('a');
	    	var accodrionBody = accordionHeader.next();
	    	accodrionBody.addClass('show').removeClass('collapsing');
	    	accordionHeader.removeClass('collapsed');

	    	addAccordionTag($(this).data('addurl'), $(this), $(this).data('user'), 0, $(this).val(), type);
	        $(this).val('').addClass("hidden").autocomplete('destroy');
	        $(this).parent().data("show", "0");
    	} else {
    		errorNotification(translations.messages.empty_tag_value);
			return false;
    	}
    }
    else if(e.keyCode == 27) {
        $(this).val('').addClass("hidden").autocomplete('destroy');
        $(this).parent().data("show", "0");
    }
});

function addTag(link, parent, id, val){
    $.ajax({
        async: true,
        cache: false,
        type: 'POST',
        url: link,
        data: {'id': id, 'val': val},
        success: function (html) {
            if(html!='') {
            	parent.before(html);
            	successNotification(translations.messages.add_tag_success_message);
				$(".table-scroll").getNiceScroll().resize();
            }
        },
        error: function (xhr, status, text) {
        	console.log(xhr);
			errorNotification(translations.messages.error_ajax_message);
      	}
    });
}

function successNotification(text) {
	new Noty({
		type: 'success',
		layout: 'topCenter',
		text: text,
		timeout: 2500,
		progressBar: true,
		closeWith: ['click', 'button'],
		animation: {
			open: 'animated fadeIn', // Animate.css class names
			close: 'animated fadeOut' // Animate.css class names
		}
	}).show();
}

function errorNotification(text) {
	new Noty({
		type: 'error',
		layout: 'topCenter',
		text: text,
		timeout: 2500,
		progressBar: true,
		closeWith: ['click', 'button'],
		animation: {
			open: 'animated fadeIn', // Animate.css class names
			close: 'animated fadeOut' // Animate.css class names
		}
	}).show();
}

$(document).on("click", ".activity-user-details .add-accordion-notes-block", function(e){
	e.preventDefault();
	e.stopPropagation();

    //zatrzymanie ajaxów na dashboard
    if ( typeof listAjaxtimer !== 'undefined') {
        clearTimeout(listAjaxtimer);
    }
    if( typeof listAjax !== 'undefined' && listAjax && listAjax.readyState != 4) {
        listAjax.abort();
    }

	addUserNotes($(this), $(this).data('user'), $(this).closest('a'));
});

function addUserNotes(container, userId, item) {
	var modal = $('#add-modal-notes').modal('show');
	modal.find('#notes-list-confirm').off().on('click', function() {
		var textarea = modal.find('textarea');
		if (textarea.val().trim() != '') {
	    	$.ajax({
	            async: true,
	            cache: false,
	            type: 'POST',
	            url: container.data('url').replace("__id__", userId),
	            data: { 'text': textarea.val() },
	            success: function (json) {
	                if(json != '') {
	            		var notes = item.next().find('.notes');
	            		notes.html(notes.html() + '<div class="col-md-12 note-item"><span>' + json.content + '</span></div>');
	            		textarea.val('');

	            		modal.modal('hide');
	            		var notesCount = item.find('.notes-count');
	            		notesCount.html(Number(notesCount.text()) + 1);

				        if (typeof setTimerList != 'undefined' && $.isFunction(setTimerList)) {
				            setTimerList();
				        }

	                	item.next().addClass('show').removeClass('collapsing');
	                	item.removeClass('collapsed');

	                	successNotification(translations.messages.add_note_success_message);
	                }
	            },
	            error: function (xhr, status, text) {
	            	console.log(xhr);
					if (typeof setTimerList != 'undefined' && $.isFunction(setTimerList)) {
						setTimerList();
					}

					if(status != 'abort') {
	                	errorNotification(translations.messages.error_ajax_message);
	                }
	          	},
	        });
		} else {
			errorNotification(translations.messages.empty_note_value);
			return false;
		}
	});
}

function resizeReactionWidgetScroll() {
	if ($('.reaction-container').length > 0) {
		var height = $('.reaction-container .widget').outerHeight() - $('.reaction-container .widget-header').outerHeight() - 1;
		var scrollElement = $(".reaction-container .widget-scroll");
		scrollElement.css({ 'max-height': height });
		scrollElement.getNiceScroll().resize();
	}
}