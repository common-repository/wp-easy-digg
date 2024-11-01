<?php
	if (isset($_POST['action']))
	{
		$action = $_POST['action'];
		$edg_instance->save_option_changes();
?>
<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><strong>Settings saved.</strong></p></div>	
<?php
	}
?>

<div class="wrap">
	<h2><?php printf(__("%s Options", $edg_instance->landomain), $edg_instance->plugin_name); ?></h2>
	<form method="post" action="">
		<table width="100%" cellspacing="2" cellpadding="5" class="form-table">
			<tr>
				<th scope="row"><?php _e('Dispaly text', $edg_instance->landomain); ?></th>
				<td>
					<?php _e('Digg me', $edg_instance->landomain); ?>
					<input type="text" name="digg_text" value="<?php echo $edg_instance->option['digg_text']?>" size="8"/>
					<?php _e('Digging', $edg_instance->landomain); ?>
					<input type="text" name="digging_text" value="<?php echo $edg_instance->option['digging_text']?>" size="8"/>
					<?php _e('Digged', $edg_instance->landomain); ?>
					<input type="text" name="digged_text" value="<?php echo $edg_instance->option['digged_text']?>" size="8"/>
					<br/><?php _e('Set the text for the digg buttons.', $edg_instance->landomain); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Display position', $edg_instance->landomain); ?></th>
				<td>
					<table>
						<tr>
							<td><?php _e('Horizontal', $edg_instance->landomain); ?></td>
							<td>
								<input type="radio" id="position_hor_left" name="position_hor" value="left" <?php echo $edg_instance->option['position']['horizontal'] == 'left' ? "checked=\"checked\"" : ""?>>
								<label for="position_hor_left"><? _e('Left', $edg_instance->landomain); ?></label>
								<input type="radio" id="position_hor_right" name="position_hor" value="right" <?php echo $edg_instance->option['position']['horizontal'] == 'right' ? "checked=\"checked\"" : ""?>>
								<label for="position_hor_right"><? _e('Right', $edg_instance->landomain); ?></label>
							</td>
						</tr>
						<tr>
							<td><?php _e('Vertical', $edg_instance->landomain); ?></td>
							<td>
								<input type="radio" id="position_ver_top" name="position_ver" value="top" <?php echo $edg_instance->option['position']['vertical'] == 'top' ? "checked=\"checked\"" : ""?>>
								<label for="position_hor_left"><? _e('Top', $edg_instance->landomain); ?></label>
								<input type="radio" id="position_ver_bottom" name="position_ver" value="bottom" <?php echo $edg_instance->option['position']['vertical'] == 'bottom' ? "checked=\"checked\"" : ""?>>
								<label for="position_hor_right"><? _e('Bottom', $edg_instance->landomain); ?></label>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Display digg button', $edg_instance->landomain); ?></th>
				<td>
                    <input type="checkbox" name="post_filter_post" id="post_filter_post" value="true" <?php echo $edg_instance->option['post_filter']['post'] ? "checked=\"checked\"" : ""?>/>
					<label for="post_filter_post"><? _e('on post', $edg_instance->landomain); ?></label>
                    <input type="checkbox" name="post_filter_page" id="post_filter_page" value="true" <?php echo $edg_instance->option['post_filter']['page'] ? "checked=\"checked\"" : ""?>/>
					<label for="post_filter_page"><? _e('on page', $edg_instance->landomain); ?></label>
                    <input type="checkbox" name="post_filter_attachment" id="post_filter_attachment" value="true" <?php echo $edg_instance->option['post_filter']['attachment'] ? "checked=\"checked\"" : ""?>/>
					<label for="post_filter_attachment"><? _e('on attachment', $edg_instance->landomain); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php _e('Miscellaneous', $edg_instance->landomain); ?></th>
				<td>
					<input type="checkbox" name="auto_insert" id="auto_insert" value="true" <?php echo $edg_instance->option['auto_insert'] ? "checked=\"checked\"" : ""?>/>
					<label for="auto_insert"><? _e('Auto Insert into post', $edg_instance->landomain); ?></label>&nbsp;&nbsp;
					<input type="checkbox" name="on_excerpt" id="on_excerpt" value="true" <?php echo $edg_instance->option['on_excerpt'] ? "checked=\"checked\"" : ""?>/>
					<label for="on_excerpt"><? _e('Auto insert into excerpt', $edg_instance->landomain); ?></label>
					<br/><?php _e('If uncheck these options, please add tag: <strong>&lt;?php $GLOBALS[\'edg_instance\']-&gt;get_button(); ?&gt;</strong> to anywhere you want.', $edg_instance->landomain); ?><br/>
					<input type="checkbox" name="enable_paging" id="enable_paging" value="true" <?php echo $edg_instance->option['enable_paging'] ? "checked=\"checked\"" : ""?>/>
					<label for="enable_paging"><? _e('Enable list paging', $edg_instance->landomain); ?></label><br/>
                    <input type="checkbox" name="enable_jquery" id="enable_jquery" value="true" <?php echo $edg_instance->option['enable_jquery'] ? "checked=\"checked\"" : ""?>/>
					<label for="enable_jquery"><? _e('Enable including jQuery', $edg_instance->landomain); ?></label>
					<br/><?php _e('If some other plugins or modules have already include the jQuery library, please uncheck this option. Otherwise, please check this option.', $edg_instance->landomain); ?>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="hidden" name="action" value="update"/>
			<input type="submit" value="<?php _e('Save Changes', $edg_instance->landomain); ?>"/>
            <?php /* <input type="button" value="<?php _e('View Statistics', $edg_instance->landomain); ?>" onclick="location.href='?page=wp-easy-digg/stats.php'" /> */ ?>
		</p>
	</form>
</div>