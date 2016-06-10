/**
 * This structure should be in the head of your theme...
 <div id="general-loader" class="loader">
 <div id="seconds" class="seconds">
 <div id="loading-bar"></div>
 </div>
 </div>
 */

/**
 * showLoadingProgress
 * add a progress bar
 */
function showLoadingProgress(){
	$('body').addClass('loading');
	$('#loading-bar').addClass('bar animating');
}
/**
 * hideLoadingProgress
 * hide progress bar
 */
function hideLoadingProgress(){
	$('body').removeClass('loading');
	$('#loading-bar').removeClass('bar animating');
}
var pdfDoc = null,
	pageNum = 1,
	pageRendering = false,
	pageNumPending = null,
	scale = 1.2,
	canvas = document.getElementById('pdf-canvas'),
	ctx = (canvas) ? canvas.getContext('2d') : '',
	pdfViewer = $('.pdf-viewer');
if(canvas !== null){
	document.getElementById('next').addEventListener('click', onNextPage);
	document.getElementById('prev').addEventListener('click', onPrevPage);
}


/**
 * Get page info from document, resize canvas accordingly, and render page.
 * @param num Page number.
 */
function renderPage(num) {
	pageRendering = true;
	// Using promise to fetch the page
	pdfDoc.getPage(num).then(function(page) {
		var viewport = page.getViewport(scale);
		canvas.height = viewport.height;
		canvas.width = viewport.width;
		// Render PDF page into canvas context
		var renderContext = {
			canvasContext: ctx,
			viewport: viewport
		};
		var renderTask = page.render(renderContext);
		// Wait for rendering to finish
		renderTask.promise.then(function () {
			pageRendering = false;
			if (pageNumPending !== null) {
				// New page rendering is pending
				renderPage(pageNumPending);
				pageNumPending = null;
			}
		});
	});
	// Update page counters
	document.getElementById('page_num').textContent = pageNum;
}
/**
 * If another page rendering in progress, waits until the rendering is
 * finised. Otherwise, executes rendering immediately.
 */
function queueRenderPage(num) {
	if (pageRendering) {
		pageNumPending = num;
	} else {
		renderPage(num);
	}
}
/**
 * Displays previous page.
 */
function onPrevPage() {
	if (pageNum <= 1) {
		return;
	}
	pageNum--;
	queueRenderPage(pageNum);
}

/**
 * Displays next page.
 */
function onNextPage() {
	if (pageNum >= pdfDoc.numPages) {
		return;
	}
	pageNum++;
	queueRenderPage(pageNum);
}

function pdfCloseViewer(){
	pdfViewer.fadeOut();
}

/**
 * wpafReadPdf
 * will fetch pdf uri, pdf.js script
 * load it in the pdf viewer
 * @param $id
 * @param $ajax_url
 */
function wpafReadPdf($id,$ajax_url){
	showLoadingProgress();
	var data = {
		'action': 'get_pdf',
		'pdfID': $id      // We pass php values differently!
	};

	var jqxhr = $.post( $ajax_url,data)
	.done(function(msg) {
		var uris = jQuery.parseJSON(msg);
		$.when(
			$.getScript( uris[1]+'compability.js' ),
			$.getScript( uris[1]+'pdf.js' ),
			$.getScript( uris[1]+'pdf.worker.js' ),
			$.Deferred(function( deferred ){
				$( deferred.resolve );
			})
		).done(function(){
			console.log(uris[0]);
			PDFJS.workerSrc =uris[1]+'pdf.worker.js';
			/**
			 * Asynchronously downloads PDF.
			 */
			PDFJS.getDocument(uris[0]).then(function (pdfDoc_) {
				pdfDoc = pdfDoc_;
				document.getElementById('page_count').textContent = pdfDoc.numPages;
				// Initial/first page rendering
				hideLoadingProgress();
				pdfViewer.fadeIn();
				renderPage(pageNum);
			});
		});
	})
	.fail(function(jqXHR, textStatus) {
		console.log( "Request failed: " + textStatus );
	});
}

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
			hideLoadingProgress();
		});

}



/**
 * wpafOffsetQuery
 * trigger an ajx query with offset data
 *
 * @param offsetPage integer number of pages to offset
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
			hideLoadingProgress();
		});

}

/**
 * closeSearchResults
 */
function closeSearchResults(target){
	targetId = $(target).parents('.filtering-results-eml');
	targetId.removeClass('active').empty().fadeOut().fadeIn();
}

jQuery(function(){
	'use strict';

	var body = $('body'),
		root = ( body.data('root').length ) ? body.data('root') + '/' : '',
		base = '/' + root,
		$ajaxurl = base+'wp-admin/admin-ajax.php';


	//get pdf
	$('.js-pdfreader').click(function (e) {
		e.preventDefault();
		var $id = $(this).attr('data-id');
		wpafReadPdf($id,$ajaxurl);
	});
	$('.js-pdf-close').click(function(e){
		e.preventDefault();
		pdfCloseViewer();
	});
	//filtering
	$('input[name="eml-submit"]').click(function(e){
		e.preventDefault();
		showLoadingProgress();
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
		showLoadingProgress();
		var wrapper = $(this).parents('.eml-filter-block');
		wrapper.find('input[type="submit"]').prop('disabled', true);
		var selectedMediaTax = $(this).find(":selected").val();
		$('.js-spin-it').fadeIn();
		resfreshMediaFilter(wrapper,selectedMediaTax,$ajaxurl);
	});

});
