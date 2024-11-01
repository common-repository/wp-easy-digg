function edg_digg_me(post_id, url, digging_text, digged_text)
{	
	var count  = jQuery('#edg_' + post_id + ' .edg_count');	
	var button = jQuery('#edg_' + post_id + ' .edg_button');	
	
	button.html(digging_text);
	
	jQuery.get
	(
		url,
		{edg_post_id: post_id},
		function(data)
		{
            var count  = jQuery('#edg_' + post_id + ' .edg_count');	
            var button = jQuery('#edg_' + post_id + ' .edg_button');

            count.html(data);
            button.html(digged_text);
		},
		'text'
	);
}

function edg_page(url, page)
{
	var list_param;
    eval('list_param = ' + jQuery('#edg_list .edg_list_param').text());
	    
    jQuery('#edg_list .edg_page_nav').html('Loading...');
    
    list_param.edg_page = page;
    
	jQuery.get
	(
		url,
		list_param,
		function(data)
		{
			jQuery('#edg_list').html(data);
		},
		'text'
	); 
}