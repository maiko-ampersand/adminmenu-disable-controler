<?php 
global $CA;
global $wp_roles;
global $wp_post_types;
global $menu;
global $submenu;
$plugins_url = plugins_url();
?>

<h1><?php $CA->_($CA::$plugin_name); ?></h1>
<link rel="stylesheet" type="text/css" href="<?php echo ($plugins_url.'/'.$CA::$plugin_fix.'/css/'.$CA::$plugin_fix.'.css'); ?>">

<table class="wp-list-table widefat fixed adminmenu-disable-controler-table">
	<thead>
		<tr>
			<th style="width:220px;">&nbsp;</th>
			<?php foreach ($wp_roles->roles as $rolekey => $rolevalue): ?>
			<?php if ($rolekey === 'administrator'){continue;} ?>
			<th>
				<?php  $CA->_($rolekey); ?>
				<p>
					<?php $CA->_('all'); ?>:
					<a href="#<?php $CA->_d($rolekey,array('.')); ?>" class="adminmenu-disable-controler-open-all-check"><?php $CA->_('check'); ?></a> / 
					<a href="#<?php $CA->_d($rolekey,array('.')); ?>" class="adminmenu-disable-controler-open-all-uncheck"><?php $CA->_('uncheck'); ?></a>
				</p>
			</th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php /* menus */ ?>
		<?php foreach ($menu as $menukey => $menuvalue): ?>
		<?php  
			$_menuname = explode(' ',strip_tags($menuvalue[0]));
			$_menuname = $_menuname[0];
			if (empty($_menuname)) {
				continue; // meybe separator.
			}
			$auth_menukey = $menukey;
			$menukey = explode('-', $menuvalue[5]);
			$menukey = $menukey[count($menukey)-1];
		?>
		<tr>
			<th>
				<em><?php $CA->_($_menuname); ?></em>
				<div class="adminmenu-disable-controler-opener-block">
					<?php if (is_array($submenu[$menuvalue[2]])
						|| isset($wp_post_types[$menukey]->cap)): ?>
						<?php $CA->_('表示'); ?>：
					<?php endif; ?>
					<?php if (is_array($submenu[$menuvalue[2]])): ?>
					<a href="#adminmenu-disable-controler-submenu-row-<?php $CA->_d($auth_menukey,array('.')); ?>" class="adminmenu-disable-controler-open">
						<?php $CA->_('サブメニュー'); ?>
					</a>
					<?php endif; ?>
					<?php if (isset($wp_post_types[$menukey]->cap)): ?>
					<a href="#adminmenu-disable-controler-cap-row-<?php $CA->_d($menukey,array('.')); ?>" class="adminmenu-disable-controler-open">
						<?php $CA->_('権限'); ?>
					</a>
					<?php endif; ?>
				</div>
			</th>
			<?php /* roles */ ?>
			<?php foreach ($wp_roles->roles as $rolekey => $rolevalue): ?>
			<?php if ($rolekey === 'administrator'){continue;} ?>
			<td>
				<input name="setting_check" value="<?php $CA->_($rolekey.','.$auth_menukey); ?>" id="<?php $CA->_($rolekey.'-'.$auth_menukey); ?>-" type="checkbox" class="adminmenu_disable_controler_check <?php $CA->_d($rolekey,array('.')); ?>" >
			</td>
			<?php endforeach; ?>
			<?php /* roles */ ?>
		</tr>
		<?php if (is_array($submenu[$menuvalue[2]])): ?>
		<?php /* submenus */ ?>
		<?php foreach ($submenu[$menuvalue[2]] as $subkey => $subvalue): ?>
		<tr class="adminmenu-disable-controler-submenu-row adminmenu-disable-controler-submenu-row-<?php $CA->_d($auth_menukey,array('.')); ?>">
			<th>
				<span class="adminmenu-disable-controler-submenu">
				<?php  
					$_menuname = strip_tags($subvalue[0]);
					$CA->_($_menuname);
				?>
				</span>
			</th>
			<?php /* roles */ ?>
			<?php foreach ($wp_roles->roles as $rolekey => $rolevalue): ?>
			<?php if ($rolekey === 'administrator'){continue;} ?>
			<td>
				<input name="setting_check" value="<?php $CA->_($rolekey.','.$auth_menukey.','.$subkey); ?>" id="<?php $CA->_($rolekey.'-'.$auth_menukey.'-'.$subkey); ?>" type="checkbox" class="adminmenu_disable_controler_check <?php $CA->_d($rolekey,array('.')); ?>">
			</td>
			<?php endforeach; ?>
			<?php /* roles */ ?>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		<?php if (isset($wp_post_types[$menukey]->cap)): ?>
		<?php foreach ($wp_post_types[$menukey]->cap as $capkey => $capvalue): ?>
		<tr class="adminmenu-disable-controler-cap-row adminmenu-disable-controler-cap-row-<?php $CA->_d($menukey,array('.')); ?>">
			<th>
				<span class="adminmenu-disable-controler-submenu">
				<?php  
					$CA->_($capvalue);
				?>
				</span>
			</th>
			<?php /* roles */ ?>
			<?php foreach ($wp_roles->roles as $rolekey => $rolevalue): ?>
			<?php if ($rolekey === 'administrator'){continue;} ?>
			<td>
				<input name="setting_check" value="<?php $CA->_($rolekey.','.$menukey.',,'.$capkey); ?>" id="<?php $CA->_($rolekey.'-'.$menukey.'--'.$capkey); ?>" type="checkbox" class="adminmenu_disable_controler_check <?php $CA->_d($rolekey,array('.')); ?>">
			</td>
			<?php endforeach; ?>
			<?php /* roles */ ?>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		<?php /* submenus */ ?>
		<?php endforeach; ?>
		<?php /* menus */ ?>
	</tbody>
</table>
<p class="submit">
	<input type="submit" name="submit" id="adminmenu-disable-controler-submit-regist" class="button button-primary" value="<?php $CA->_('更新'); ?>">
	<input type="submit" name="submit" id="adminmenu-disable-controler-submit-reset" class="button button-delete" value="<?php $CA->_('設定リセット'); ?>">
</p>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo ($plugins_url.'/'.$CA::$plugin_fix.'/js/'.$CA::$plugin_fix.'.js'); ?>"></script>
<script type="text/javascript">
$(function(){
	// initial data read.
	$.ajax({
		type: "POST",
		url: ajaxurl,
		dataType: 'json',
		data: {
			action: 'adminmenu_disable_controler_read'
		}
	})
	.done(function(data){
		var d = document;
		var _obj = {};
		for (var i = data.length - 1; i >= 0; i--) {
			var chk = d.getElementById(
				data[i].role_name+'-'
				+data[i].disable_menu_key
				+(data[i].disable_submenu_key !== '' ? '-'+data[i].disable_submenu_key : '-' )
				+(data[i].remove_cap !== '' ? '-'+data[i].remove_cap : '' )
			)
			if(!chk){continue;}
			$(chk).attr('checked','checked');
			_obj['adminmenu-disable-controler-submenu-row-'+data[i].disable_menu_key] = true;
			_obj['adminmenu-disable-controler-cap-row-'+data[i].disable_menu_key] = true;
		};
		for (var key in _obj) {
			$('.'+key).show();
		};
		$('.adminmenu-disable-controler-progress').fadeOut('fast');
	});

	// register
	$('#adminmenu-disable-controler-submit-regist').on('click',function(){
		var checked = (function($chks){
			var s = [];
			$chks.each(function(){
				s.push($(this).val());
			});
			return s;
		})($('.adminmenu_disable_controler_check:checked'));
		$.ajax({
			type: "POST",
			url: ajaxurl,
			beforeSend : function(){
				$('.adminmenu-disable-controler-progress').fadeIn('slow');
			},
			data: {
				action: 'adminmenu_disable_controler_regist',
				values: checked
			}
		})
		.done(function( data ) {
			$('.adminmenu-disable-controler-progress').fadeOut('fast');
		});
	});

	// reset
	$('#adminmenu-disable-controler-submit-reset').on('click',function(){
		if (confirm('<?php $CA->_("設定をリセットしてもよろしいですか?"); ?>')) {
			$.ajax({
				type: "POST",
				url: ajaxurl,
				beforeSend : function(){
					$('.adminmenu-disable-controler-progress').fadeIn('slow');
				},
				data: {
					action: 'adminmenu_disable_controler_reset',
				}
			})
			.done(function( data ) {
				$('.adminmenu-disable-controler-progress').fadeOut('fast');

				var checked = (function($chks){
					$chks.each(function(){
						$(this)[0].checked = false;
					})
				})($('.adminmenu_disable_controler_check:checked'));
			});
		};
	});
});
</script>
<div class="adminmenu-disable-controler-progress adminmenu-disable-controler-progress-cover">
</div>
<div class="adminmenu-disable-controler-progress adminmenu-disable-controler-progress-message-wrap">
	<span class="adminmenu-disable-controler-progress-message">
	<?php $CA->_('just a moment please. ( ˘ω˘ )'); ?>
	</span>
</div>