
/**
 * mediaFilter
 * Main query for ajax medias request
 *
 * @param container string uniq ID
 * @param values
 * @param $ajaxurl string
 * @param offset integer
 */
function mediaFilter(container,values,$ajaxurl,offset){
	//console.log(values);
	$resultContainer = $('#res-'+ container );
	$resultContainerTax = $('#'+ container ).attr('data-default-term');
	offsetCount = (offset) ? offset : 0;
	var jqxhr = $.post( $ajaxurl,{
			'action': 'filter_eml_media_query',
			'values':   values,
			'offset': offsetCount,
			'tax': $resultContainerTax
		})
		.done(function(data) {
			//console.log(data);
			//Load html results
			$resultContainer.empty().append(data);
			//build active filters for ux
			activefilters = '<div class="active-filters">';
			for (i = 0; i < values.length; i++) {
				$valFilter = values[i].value;
				if($valFilter !== "" && $valFilter !== '0'){
					activefilters += '<span>'+ values[i].value + '</span>';
				} else if($valFilter === '0'){
					activefilters += '<span>All</span>';
				}

				//console.log(values[i].value);
			}
			activefilters += '</div>';
			//Load html active filters results
			$resultContainer.find('.em-filters-active').empty().append(activefilters);
			//init lightbox if exists... should be test
			if(initMpf()){
				initMpf();
			}

		})
		.fail(function(jqXHR, textStatus) {
			$resultContainer.empty().append("Request failed: " + textStatus);
		})
		.always(function() {
			$resultContainer.addClass('active');
			$('input[name="eml-submit"]').removeClass('processing');
		});

}


/**
 * wpafOffsetQuery
 * trigger an ajx query with offset data
 *
 * @param offsetPage
 * @param el
 */
function wpafOffsetQuery(offsetPage){

	target = $('input[name="eml-submit"]');
	target.attr('data-offset',offsetPage);
	target.trigger( "click" );
}

/**
 * resfreshMediaFilter
 *
 * @param wrapper
 * @param mediaTax string
 * @param $ajaxurl string
 */
function resfreshMediaFilter(wrapper,mediaTax,$ajaxurl){
	container = wrapper.attr('id');
	$resultContainer = $('#res-'+ container );
	$resultContainerTax = $('#'+ container ).attr('data-default-term');
	//console.log($resultContainerTax);
	var jqxhr = $.post( $ajaxurl,{
			'action': 'refresh_eml_filters',
			'value':   mediaTax
		})
		.done(function(data) {
			var obj = jQuery.parseJSON( data );

			$('.eml-mime-type').replaceWith(obj.mime);
			//iterate through custom fields
			acfObj = obj.acf;
			for (i = 0; i < acfObj.length; i++) {
				el = acfObj[i];
				elID = $(el).attr('id');
				$('#'+elID).replaceWith(el);
			}

		})
		.fail(function() {
			console.warn( "error" );
		})
		.always(function() {
			wrapper.find('input[type="submit"]').prop('disabled', false);
			$('.js-spin-it').fadeOut();
		});

}


jQuery(function(){
	'use strict';

	var body = $('body'),
		root = ( body.data('root').length ) ? body.data('root') + '/' : '',
		base = '/' + root,
		$ajaxurl = base+'wp-admin/admin-ajax.php';

	//filtering
	$('input[name="eml-submit"]').click(function(e){
		e.preventDefault();
		var offset = $(this).attr('data-offset');
		$(this).addClass('processing');
		var container = $(this).parents('.eml-filter-block').attr('id'),
			values = [];
		$('.eml-js-filter:checked,.cs-link,.eml-js-term').each(function(){
			var inputName = $(this).attr('name'),
				inputValue  = $(this).val();
			values.push(
				{
					value: inputValue,
					name  : inputName
				}
			);

		});
		//console.log(values);
		mediaFilter(container, values,$ajaxurl,offset);
	});

	//update acf && mime type on category update
	$('select[name="cs-link"]').change(function(){
		var wrapper = $(this).parents('.eml-filter-block');
		wrapper.find('input[type="submit"]').prop('disabled', true);
		var selectedMediaTax = $(this).find(":selected").val();
		$('.js-spin-it').fadeIn();
		resfreshMediaFilter(wrapper,selectedMediaTax,$ajaxurl);
	});

});
