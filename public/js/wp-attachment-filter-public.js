var body = $('body'),
	root = (body.data('root').length) ? body.data('root') + '/' : '',
	base = '/' + root,
	$ajaxurl = base+'wp-admin/admin-ajax.php';

/**
 * mediaFilter
 *
 * @param container
 * @param values array
 * @return string
 */
function mediaFilter(container,values){
	$resultContainer = $('#res-'+ container );
	$resultContainerTax = $('#'+ container ).attr('data-default-term');

	var jqxhr = $.post( $ajaxurl,{
			'action': 'filter_eml_media_query',
			'values':   values,
			'tax': $resultContainerTax
		})
		.done(function(data) {
			$resultContainer.empty().append(data);
			activefilters = '<div class="active-filters">';
			for (i = 0; i < values.length; i++) {
				if(values[i].value !== ""){
					activefilters += '<span>'+ values[i].value + "</span>";
				}

				//console.log(values[i].value);
			}
			activefilters += '</div>';
			$resultContainer.find('.em-filters-active').empty().append(activefilters);
			initMpf();
		})
		.fail(function(jqXHR, textStatus) {
			$resultContainer.empty().append("Request failed: " + textStatus);
		})
		.always(function() {
			$resultContainer.addClass('active');
		});

}


/**
 * resfreshMediaFilter
 *
 * @param wrapper
 * @param mediaTax
 */
function resfreshMediaFilter(wrapper,mediaTax){
	container = wrapper.attr('id');
	$resultContainer = $('#res-'+ container );
	$resultContainerTax = $('#'+ container ).attr('data-default-term');
	console.log($resultContainerTax);
	var jqxhr = $.post( $ajaxurl,{
			'action': 'refresh_eml_filters',
			'value':   mediaTax
		})
		.done(function(data) {
			var obj = jQuery.parseJSON( data );
			console.log(obj);
			$('.eml-mime-type').replaceWith(obj.mime);
			$('.eml-acf-field').replaceWith(obj.acf);
		})
		.fail(function() {
			console.warn( "error" );
		})
		.always(function() {
			wrapper.find('input[type="submit"]').prop('disabled', false);
			$('.js-spin-it').fadeOut();
		});

}


(function( $ ) {
	'use strict';

	//filtering
	$('input[name="eml-submit"]').click(function(e){
		e.preventDefault();
		var container = $(this).parents('.eml-filter-block').attr('id'),
			values = [];
		$('.eml-js-filter:checked,.eml-js-term').each(function(){
			var inputName = $(this).attr('name'),
				inputValue  = $(this).val();
			values.push(
				{
					value: inputValue,
					name  : inputName
				}
			);

		});
		console.log(values);
		mediaFilter(container, values);
	});

	//update acf && mime type on category update
	$('select[name="eml-media-tax"]').change(function(){
		var wrapper = $(this).parents('.eml-filter-block');
		wrapper.find('input[type="submit"]').prop('disabled', true);
		var selectedMediaTax = $(this).find(":selected").val();
		$('.js-spin-it').fadeIn();
		resfreshMediaFilter(wrapper,selectedMediaTax);
	});

})( jQuery );
