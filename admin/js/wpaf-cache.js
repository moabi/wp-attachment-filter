/**
 * Created by dfieffe on 20/06/2016.
 */


/**
 * resfreshMediaFilter
 *
 */
function preloadCache(){

    //console.log(mediaTax);
    var jqxhr = jQuery.post( ajaxurl,{
        'action': 'preload_cache'
    })
        .done(function(data) {

            console.log(data);


        })
        .fail(function() {
            console.warn( "error" );
        })
        .always(function() {
            jQuery('.wpaf-js-loader').fadeOut();
            jQuery('.wpaf-js-loader-success').fadeIn();
        });

}


jQuery(function () {

    var $ = jQuery;
    //console.log(ajaxurl);
    /**
     * PRELOAD THE CACHE PART
     */
    $('#wpaf-js-cache-preloader').click(function(e){
        e.preventDefault();
        $('.wpaf-js-loader').fadeIn();
        $ajaxurl = $(this).attr('root');
        preloadCache();

    });

});