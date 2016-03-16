

var mediaCatBtn = '<div style="display:none !important;"> <form id="btn-ear-medias"><div id="lyra-mbc"> <p><select name="cs-link" id="cs-link"><option value="--">Pick a media taxonomy</option><option value="general">General Search Box</option></select> </p><p><input type="submit" id="closeCustomBtn-2" value="Insert Shortcode button" class="button button-primary button-large"></p></div></form></div>';

jQuery(document).ready(function(jQuery) {
	var $ = jQuery;
	if(typeof tinymce != 'undefined'){


	jQuery.post(
		ajaxurl,
		{
			'action': 'retrieve_media_tax',
			'is_ajax': true
		},
		function(response){
			if(jQuery('#btn-ear-medias').length === 0 ){
				jQuery('body').append(mediaCatBtn);
			}
			//console.log(mediaCatBtn);
			$(response).appendTo('#cs-link');
		}
	);
	tinymce.create('tinymce.plugins.mediabycategory_plugin', {
		init : function(ed, url) {
			// Register command for when button is clicked
			ed.addCommand('mediabycategory_insert_shortcode', function() {
				selected = tinyMCE.activeEditor.selection.getContent();


				tb_show("Lyra Network - insert Medias by category" , "#TB_inline?height=200&amp;width=420&amp;inlineId=btn-ear-medias");
				jQuery('#lyra-btn input').each(function(){
					jQuery(this).keyup(function(){
						jQuery(this).attr('value',jQuery(this).val());
					});
				});

				jQuery('#closeCustomBtn-2').one("click",function(e){
					e.preventDefault();

					self.parent.tb_remove();

					var link = jQuery('#cs-link option:selected').val();
					if(link === "general"){
						var content = '[mediabycategory filter="true" ]';
						tinymce.execCommand('mceInsertContent', false, content);
					}else {
						var content =  '[mediabycategory tax="'+ link +'"]';
						if(link !== ''){
							tinymce.execCommand('mceInsertContent', false, content);
							return false;
						} else {
							alert('link is missing...');
							return false;
						}
					}


					return false;
				});

			});

			// Register buttons - trigger above command when clicked
			ed.addButton('mediabycategory_button', {title : 'Insert Medias by category', cmd : 'mediabycategory_insert_shortcode', image: url + '/medias.png' });
			return false;
		}

	});

	// Register our TinyMCE plugin
	// first parameter is the button ID1
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('mediabycategory_button', tinymce.plugins.mediabycategory_plugin);
	return false;
	} else {
		//console.warn('tinymce missing');
	}
});