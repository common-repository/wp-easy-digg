<?php
/*
Plugin Name: WP-EasyDigg
Plugin URI:  http://blog.gekimoe.org/chating/wp-easy-digg
Plugin Description: Gives a smart button to digg the posts
Version: 1.1.3
Author: JAY
Author URI: http://www.gekimoe.org
*/

load_plugin_textdomain('edg', "/wp-content/plugins/wp-easy-digg/languages/");

// Class defination
class CEasyDigg
{
	public $plugin_name    = 'WP Easy Digg';
	public $meta_key       = 'edg_digg_count';
	public $option_key     = 'edg_options';	
	public $cache_key      = 'edg_cache';
    public $cookie_name    = 'edg_cookie';
    public $landomain      = 'edg';
	public $plugin_directory;
	public $plugin_path;
	public $plugin_options_path;
	public $jquery_path;
	public $ajax_path; 
	public $option;
	public $default_option;
    
	function __construct()
	{
		$this->plugin_directory 	= get_bloginfo('wpurl').'/'.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__));
		$this->plugin_path 			= get_bloginfo('wpurl').'/'.PLUGINDIR.'/'.plugin_basename(__FILE__);
		$this->plugin_options_path 	= dirname(plugin_basename(__FILE__)) . '/options.php';
		$this->jquery_path 			= get_bloginfo('wpurl').'/wp-includes/js/jquery/jquery.js';
		$this->ajax_path			= preg_replace('/([^:]:\/\/)[^\/]+(\/.*)?/i', "$1".$_SERVER['HTTP_HOST']."$2", get_bloginfo('wpurl'));
		$this->get_option();        
	}
		
	/**********************************
	 * Save, merge and update options
	 **********************************/	
	function merge_option()
	{
		$this->default_option = 
	    	array(
	    		'digg_text' 	=> 'Digg me',
	    		'digging_text' 	=> 'Digging',
	    		'digged_text' 	=> 'Digged',
	    		'position' 		=> array('horizontal' => 'left', 'vertical' => 'top'),
	            'auto_insert'	=> true,
	    		'on_excerpt'    => true,
	            'enable_paging' => true,
	    		'enable_jquery' => true,                    
	    		'widget'		=>
	        		array(
	        			'title'		   => 'Most digged',
                        'orderby'      => 'COUNT',
	        			'order'		   => 'DESC',
	        			'offset'	   => 0,
	        			'limit'		   => 10,
	        		    'period'       => 'ALL',
                        'days'         => 30,
	        		),
	        	'post_filter'   =>
	        		 array(
						'post'			=> true,
						'page'			=> false,
						'attachment'	=> false
					),
	    	);
	    $this->do_merge_option($this->option, $this->default_option);
	}
	
	function do_merge_option(&$src_arr, &$dst_arr)
	{
		if (!is_array($src_arr))
		{
			$src_arr = array();
		}
		$keys = array_keys($dst_arr);
		
		foreach($keys as $key)
	    {
	    	if (!isset($src_arr[$key]))
	    	{
	    		$src_arr[$key] = $dst_arr[$key];
	    	}
	    	else
	    	{
	    		if (is_array($src_arr[$key]))
	    		{
	    			$this->do_merge_option($src_arr[$key], $dst_arr[$key]);
	    		}
	    	}
	    }
	}
	
	function get_option()
	{
		$this->option = get_option($this->option_key);	
		$has_been_added = is_array($this->option);
		$this->merge_option();
		
		if (!$has_been_added)
		{			
			$this->update_option();
		}
	}
	
	function update_option()
	{
		if (!add_option($this->option_key, $this->option))
		{
			update_option($this->option_key, $this->option);
		}
	}
	
	function save_option_changes()
	{
		$this->option['digg_text'] 					= $_POST['digg_text'];
		$this->option['digging_text'] 				= $_POST['digging_text'];
		$this->option['digged_text'] 				= $_POST['digged_text'];
		$this->option['position']['horizontal'] 	= $_POST['position_hor'];
		$this->option['position']['vertical'] 		= $_POST['position_ver'];
		$this->option['auto_insert']				= isset($_POST['auto_insert']);
        $this->option['on_excerpt']                 = isset($_POST['on_excerpt']);
		$this->option['enable_paging'] 				= isset($_POST['enable_paging']);
        $this->option['enable_jquery'] 				= isset($_POST['enable_jquery']);
		$this->option['post_filter']['post']		= isset($_POST['post_filter_post']);
		$this->option['post_filter']['page']		= isset($_POST['post_filter_page']);
		$this->option['post_filter']['attachment']	= isset($_POST['post_filter_attachment']);
		$this->update_option();
	}
	
	function save_widget_changes()
	{
		$this->option['widget']['title']   = $_POST['widget_title'];
		$this->option['widget']['order']   = $_POST['widget_order'];
		$this->option['widget']['limit']   = intval($_POST['widget_limit']);
		$this->option['widget']['period']  = $_POST['widget_period'];
        $this->option['widget']['days']    = intval($_POST['widget_days']);
		$this->update_option();	
	}

	/**********************************
	 * Digg button
	 **********************************/
	
	function get_button(&$the_post = null, $echo = true)
	{
		global $post;
		if (!isset($the_post))
		{
			$the_post = &$post;
		}
        $code  = "<!-- EDG-BTN-START -->";
		$code .= "<div class=\"edg_wrapper edg_wrapper_{$this->option['position']['horizontal']}\">";
		$code .= "<div id=\"edg_{$the_post->ID}\" class=\"edg_container\">";
		$code .= "<div class=\"edg_count\">{$this->get_counts($the_post)}</div>";
		$code .= "<div class=\"edg_button\">";
        
        $cookie = $this->uncompress_array(stripslashes($_COOKIE[$this->cookie_name]));
		if (in_array($the_post->ID, $cookie))
		{
			$code .= $this->option['digged_text'];
		}
		else
		{
			$code .= "<span onclick=\"edg_digg_me({$the_post->ID}, '{$this->ajax_path}', '{$this->option['digging_text']}', '{$this->option['digged_text']}');\">";
			$code .= $this->option['digg_text'];
			$code .= "</span>";
		}
		$code .= "</div>";
		$code .= "</div>";
		$code .= "</div>";
		$code .= "<!-- EDG-BTN-END -->";
        
		if ($echo) echo $code;
		return $code;
	}
	
	function get_counts(&$the_post)
	{
		$str_value = get_post_meta($the_post->ID, $this->meta_key, true);
		if (strlen($str_value) == 0)
		{
			$this->hook_save_post($the_post);
		}
		return intval($str_value);
	}
	
	function increase_counts(&$the_post)
	{		
		update_post_meta($the_post->ID, $this->meta_key, $this->get_counts($the_post) + 1);
		wp_cache_delete($this->cache_key);
	}	
    
    function get_digg_post_pages(&$args = null)
    {
    	global $wpdb;
        if (!isset($args))
        {
         	$args = &$this->option['widget'];
        }
        $sql_statements = $this->get_sql_statements($args);
        extract($sql_statements);
            
        $sqlFinal  = $sql_count.$sql_where.$sql_filter.$sql_period;
        $total_posts = $wpdb->get_var($sqlFinal);
        return intval(($total_posts + $args['limit'] - 1) / $args['limit']);     
    }
	
	/**********************************
	 * Widget invoking and options
	 **********************************/	
	function get_list($query_string = '', $echo = true, $prefix = '<div id="edg_list">', $suffix = '</div>')
	{
		global $wpdb;
		
		$default = array('order' => 'DESC', 'orderby' => 'post_date', 'period' => 'ALL', 'days' => 30, 'limit' => 10, 'offset' => 0);
				
		$args = wp_parse_args($query_string, $default);
        
		$sql_statements = $this->get_sql_statements($args);
		extract($sql_statements);

		$sqlFinal  = $sql_select.$sql_where.$sql_filter.$sql_period.$sql_order.$sql_limit;
        
		$records   = $wpdb->get_results($sqlFinal);

		// List
		$code = "{$prefix}<ul>\n";
		foreach($records as $record)
		{
			$code .= "<li class=\"edg_list_item\">";
            
			$code .= "<span class=\"edg_list_count\">";
            if ($record->meta_value == 1)
            {
                $code .= sprintf(__('%d Digg', $this->landomain), $record->meta_value);
            }
            else
            {
                $code .= sprintf(__('%d Diggs', $this->landomain), $record->meta_value);
            }            
            $code .= "</span>";
			$code .= "<a href=\"".get_permalink($record->ID)."\">{$record->post_title}</a>";
			$code .= "</li>\n";
            
            $count++;
		}
        if ($count == 0)
        {
            $code .= "<li class=\"edg_list_item\">". __('Nothing digged', $this->landomain) . "</li>";
        }
		$code .= "</ul>\n";

		// Paging
        if ($this->option['enable_paging'])
        {    
        	$page = intval(($args['offset'] + $args['limit'] - 1) / $args['limit']);
        	
            $code .= "<div class=\"edg_page_nav\">";            
            if ($page > 1) $code .= "<a class=\"edg_page_prev\" onclick=\"edg_page('{$this->ajax_path}', 0)\">" . __('&laquo; First', $this->landomain) . "</a>";
			if ($page > 0) $code .= "<a class=\"edg_page_prev\" onclick=\"edg_page('{$this->ajax_path}', " . ($page - 1) . ")\">" . __('&laquo; Prev', $this->landomain) . "</a>";

            $total_pages = $this->get_digg_post_pages($args);
            
            if ($page < $total_pages - 2) $code .= "<a class=\"edg_page_next\" onclick=\"edg_page('{$this->ajax_path}', " . ($total_pages - 1) . ")\">" . __('Last &raquo;', $this->landomain) . "</a>";
            if ($page < $total_pages - 1) $code .= "<a class=\"edg_page_next\" onclick=\"edg_page('{$this->ajax_path}', " . ($page + 1) . ")\">" . __('Next &raquo;', $this->landomain) . "</a>";
            
            $list_param = array(
            'order' => $args['order'],
            'orderby' => $args['orderby'],
            'limit' => $args['limit'],
            'period' => $args['period'],
            'days' => $args['days']
            );
            
            $code .= "<span class=\"edg_list_param\">" . json_encode($list_param) . "</span>";
            $code .= "</div>{$suffix}";
		}
		if ($echo) echo $code;
		return $code;
	}
	
	function get_widget($args)
	{
		$code = wp_cache_get($this->cache_key);				
		if (strlen($code) == 0)
		{						
			$list_args  = $this->args_to_query_string($this->option['widget']);
			extract($args);
			
			$code  = "{$before_widget}\n";
			$code .= "{$before_title}{$this->option['widget']['title']}{$after_title}";
			$code .= $this->get_list($list_args, false);
			$code .= "{$after_widget}\n";
				
			wp_cache_add($this->cache_key, $code);
		}
		echo $code;
	}
	
	function get_widget_option()
	{
		if(isset($_POST['action']))
		{
			$action = $_POST['action'];
			if ($action == 'update')
			{
				$this->save_widget_changes();
			}
		}
		?>
    <style type="text/css">
    #input_list{list-style:none; padding:0; margin:0}    
    #input_list li{padding:0; margin:2px 0}
    #input_list label{display:block}
    #input_list .input_ctrl{border:1px solid #CCC}
    </style>
    <script type="text/javascript">
    function onPeriodChange()
    {
        document.getElementById('widget_days').style.display = 
            document.getElementById('widget_period').value != 'MANUAL' ? 'none' : 'inline';
    }
    </script>    
    <p>
    <label for="widget_title"><?php _e('Widget title: ', $this->landomain); ?></label>
	<input class="input_ctrl" id="widget_title" name="widget_title" type="text" value="<?php echo $this->option['widget']['title']?>" /><br />
    </p>
	<p>
    <label for="widget_order"><?php _e('Order: ', $this->landomain); ?></label>
	<select class="input_ctrl" id="widget_order" name="widget_order"/>
		<option value="ASC" <?php echo $this->option['widget']['order'] == 'ASC' ? 'selected="selected"' : ''?>><?php _e('ASC', $this->landomain); ?></option>
		<option value="DESC" <?php echo $this->option['widget']['order'] == 'DESC' ? 'selected="selected"' : ''?>><?php _e('DESC', $this->landomain); ?></option>
		<option value="RAND" <?php echo $this->option['widget']['order'] == 'RAND' ? 'selected="selected"' : ''?>><?php _e('RAND', $this->landomain); ?></option>
	</select>
    </p>
    <p>
	<label for="widget_period"><?php _e('Period: ', $this->landomain); ?></label>    
	<select class="input_ctrl" id="widget_period" name="widget_period" onchange="onPeriodChange();"/>
		<option value="MANUAL" <?php echo $this->option['widget']['period'] == 'MANUAL' ? 'selected="selected"' : ''?>><?php _e('Manual input', $this->landomain); ?></option>
        <option value="ALL" <?php echo $this->option['widget']['period'] == 'ALL' ? 'selected="selected"' : ''?>><?php _e('All', $this->landomain); ?></option>
		<option value="YEAR" <?php echo $this->option['widget']['period'] == 'YEAR' ? 'selected="selected"' : ''?>><?php _e('This year', $this->landomain); ?></option>
		<option value="MONTH" <?php echo $this->option['widget']['period'] == 'MONTH' ? 'selected="selected"' : ''?>><?php _e('This month', $this->landomain); ?></option>
		<option value="WEEK" <?php echo $this->option['widget']['period'] == 'WEEK' ? 'selected="selected"' : ''?>><?php _e('This week', $this->landomain); ?></option>
		<option value="DAY" <?php echo $this->option['widget']['period'] == 'DAY' ? 'selected="selected"' : ''?>><?php _e('This day', $this->landomain); ?></option>        
	</select>
    <span id="widget_days" <?php echo $this->option['widget']['period'] == 'MANUAL' ? '' : 'style="display:none;"'?> >
    <?php _e('in ', $this->landomain); ?>
    <input class="input_ctrl" name="widget_days" type="text" value="<?php echo $this->option['widget']['days'] ?>" size="4"/>
    <?php _e(' days', $this->landomain); ?>
    </span>
    </p>
    <p>
	<label for="widget_limit"><?php _e('Limit: ', $this->landomain); ?></label>
	<input class="input_ctrl" id="widget_limit" name="widget_limit" type="text" value="<?php echo $this->option['widget']['limit']?>" /><br />
    </p>
	<input type="hidden" name="action" value="update"/>
		<?php
	}
	
	/**********************************
	 * Utilities
	 **********************************/	
    function args_to_query_string(&$args)
    {
    	$query_string = '';
    	$keys = array_keys($args);
    	foreach($keys as $key)
    	{
    		$query_string .= "{$key}={$args[$key]}&";
    	}
    	return $query_string;
    }
    
	function get_sql_statements(&$args)
	{
		global $wpdb;
		
		$keys = array_keys($this->option['post_filter']);
		$init = false;		
		$sql_filter  = '';		
		foreach($keys as $key)
		{
			if ($this->option['post_filter'][$key])
			{
				if ($init) $sql_filter .= ', ';
				else $init = true;				
				$sql_filter .= "'{$key}'";
			}
		}
		
        $sql_period = '';
        if ($args['period'] == 'MANUAL')
        {
            $sql_period .= "AND DATEDIFF(Now(), P.post_date) <= {$args['days']} ";
        }
        else
        {
            if ($args['period'] != 'ALL')        
            {
                $sql_period .= "AND YEAR(P.post_date) = YEAR(Now()) ";
                if ($args['period'] != 'YEAR')
                {
                    $sql_period .= "AND MONTH(P.post_date) = MONTH(Now()) ";
                    if ($args['period'] != 'MONTH')
                    {
                        $sql_period .= "AND WEEK(P.post_date) = WEEK(Now()) ";
                        if ($args['period'] != 'WEEK')
                        {
                            $sql_period .= "AND DAY(P.post_date) = DAY(Now()) ";
                        }
                    }
                }
            }
        }
        
        $sql_orderby = '';
        
        if ($args['orderby'] == 'COUNT')
        {
            $sql_orderby = 'CAST(M.meta_value AS UNSIGNED)';
        }
        else
        {
            $sql_orderby = $args['orderby'];
        }
        
        if (isset($args['list_page']))
        {
            $args['offset'] = $args['list_page'] * $args['limit'];
        }
        
		$sql_statements = array(
			'sql_select' => "SELECT P.*, M.meta_value FROM {$wpdb->posts} P JOIN {$wpdb->postmeta} M ON P.ID = M.post_id ",
			'sql_count'  => "SELECT COUNT(*) FROM {$wpdb->posts} P JOIN {$wpdb->postmeta} M ON P.ID = M.post_id ",
			'sql_where'  => "WHERE M.meta_key = '{$this->meta_key}' AND CAST(M.meta_value AS UNSIGNED) > 0 ",
			'sql_filter' => "AND P.post_type IN ({$sql_filter}) ",
			'sql_period' => $sql_period,				
			'sql_order'  => ($args['order'] == 'RAND') ? "ORDER BY RAND() " : "ORDER BY {$sql_orderby} {$args['order']} ",
			'sql_limit'  => "LIMIT {$args['offset']}, {$args['limit']}",
		);
		return $sql_statements;
	}
	
    function compress_array($array)
    {
        foreach($array as $key => $value)
        {
            $result .= dechex($value) . 'S';
        }
        
        return $result;
    }
    
    function uncompress_array($string)
    {
        $array = preg_split('/S/', $string);
        
        foreach($array as $key => $value)
        {
            $array[$key] = hexdec($value);
        }
        
        return $array;
    }
    
	/**********************************
	 * Hooks and ajax processing
	 **********************************/
	function hook_save_post(&$the_post)
	{	
		add_post_meta($the_post->ID, $this->meta_key, 0, true);
	}
	
	function hook_head_include()
	{
		echo "<!-- Easy Digg Begin -->\n";
		if ($this->option['enable_jquery'])
		{
			echo "<script type=\"text/javascript\" src=\"{$this->jquery_path}\"></script>\n";
		}
		echo "<script type=\"text/javascript\" src=\"{$this->plugin_directory}/edg.js\"></script>\n";
		echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"{$this->plugin_directory}/edg.css\"/>\n";
		echo "<!-- Easy Digg End -->\n";
	}
	
	function hook_options_page()
	{
		add_options_page($this->plugin_name, $this->plugin_name, 10, $this->plugin_options_path);
	}
	
	function hook_widget()
	{
		wp_register_sidebar_widget($this->plugin_name, $this->plugin_name, array($this, 'get_widget'));
		wp_register_widget_control($this->plugin_name, $this->plugin_name, array($this, 'get_widget_option'));
	}
	
	function filter_the_content($content)
	{
		global $post;
		
		if ($this->option['post_filter'][$post->post_type])
		{
            if (is_feed())
            {
                $pattern  = "/<!-- EDG-BTN-START -->.*<!-- EDG-BTN-END -->/is";
                preg_replace($pattern, '', $content);
            }
            else
            {        
                $digg_code = $this->get_button($post, false);
				$content = $this->option['position']['vertical'] == 'top' ? $digg_code.$content : $content.$digg_code;
            }
		}
		return $content;
	}
	
	function filter_the_excerpt($content)
	{
		global $post;
		
		$content = get_the_content();
		$content = strip_tags($content);
		$content = $this->filter_the_content($content);
		
		return $content;
	}
	
	function ajax_process()
	{	
		if (isset($_GET['edg_post_id']))
		{		
			$edg_post_id = intval($_GET['edg_post_id']);
            $the_post = get_post($edg_post_id);
             
            if (isset($_COOKIE[$this->cookie_name]))
            {
                $cookie = $this->uncompress_array(stripslashes($_COOKIE[$this->cookie_name]));
            }
            else
            {
                $cookie = array();
            }
		                
			if (!in_array($edg_post_id, $cookie))	
			{
				$cookie[] = $edg_post_id;
		        $this->increase_counts($the_post);
                setcookie($this->cookie_name, $this->compress_array($cookie), time() + 3600 * 24 * 7);
			}
		    
		    echo $this->get_counts($the_post);	
			exit();
		}
		
		if (isset($_GET['edg_page']))
		{
		    $args  = $_GET;
		    $args['offset'] = $args['limit'] * $args['edg_page'];
		    
		    header('Content-type: text/html; charset=utf-8');
		    echo $this->get_list($this->args_to_query_string($args), false, '', '');		    
		    exit();
		}
	}
}

// Singelton instance
$edg_instance = new CEasyDigg();

// Hooks
add_action('admin_menu', 	array(&$edg_instance, 'hook_options_page'));
add_action('save_post', 	array(&$edg_instance, 'hook_save_post'));
add_action('wp_head', 		array(&$edg_instance, 'hook_head_include'));
add_action('widgets_init',  array(&$edg_instance, 'hook_widget'));

if ($edg_instance->option['auto_insert'])
{
	add_filter('the_content', 	array(&$edg_instance, 'filter_the_content'));
}

if ($edg_instance->option['on_excerpt'])
{
    add_filter('the_excerpt', 	array(&$edg_instance, 'filter_the_excerpt'), 8);
}


$wp_rewrite =& new WP_Rewrite();    // Why do I need this?
$edg_instance->ajax_process();
?>