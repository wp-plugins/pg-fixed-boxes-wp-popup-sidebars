<?php
defined( 'ABSPATH' ) OR exit;
/*
Plugin Name: PG Fixed Boxes WP Plugin (POPUP  Sidebars Plugin)
Plugin URI: http://fixedboxes.esy.es/wp/
Description: Center positioned pop up box which accepts any wiget of your choice ! with ability to make unlimited number of fully customizable boxes !
Version: 1.5
Author: PG Team
Author URI: http://parsigroup.net/
License: .
*/

/**************************
activation and deactivation hooks
**************************/

register_activation_hook(__FILE__,'pg_fixedbox_install'); 
register_deactivation_hook( __FILE__, 'pg_fixedbox_remove' );

function pg_fixedbox_install(){

}

function pg_fixedbox_remove() {
	//delete_option("fixed_boxes_data");
}

/**************************
including needed files.
**************************/

require_once ( plugin_dir_path( __FILE__ ).'inc/class_box.php' );

/**************************
including needed files.
**************************/

add_action('init', 'pg_fx_translation');
function pg_fx_translation() {
  load_plugin_textdomain( 'fixedboxes', false, dirname( plugin_basename( __FILE__ ) ).'/lang/' );
}

/**************************
adding admin pages
**************************/

add_action('admin_menu', 'pg_admin_add_page');
function pg_admin_add_page() {
	add_options_page("Fixed Boxes Settings", "Fixed Boxes", 'manage_options', "pg-main", "fixed_box_admin_page");
	add_options_page("Add New Boxe", "Add New Box", 'manage_options', "pg-add", "fixed_box_add_page");
}

function fixed_box_admin_page() {
	require_once ( plugin_dir_path( __FILE__ ).'inc/main.php' );	
}

function fixed_box_add_page() {
	require_once ( plugin_dir_path( __FILE__ ).'inc/add.php' );		
}


/**************************

loading style and js codes

we load plugin css and js files in function " appendthebox " which contains main js codes.
it is for prevent the loading scripts in non needed pages of your site !
**************************/

//add_action( 'wp_enqueue_scripts', 'pg_fixedbox_scripts' );
add_action( 'admin_enqueue_scripts', 'pg_fixedbox_admin_scripts' );

function pg_fixedbox_scripts(){
	wp_enqueue_script('jquery');
	wp_enqueue_style('pg-fixedbox-css',plugins_url().'/pg-fixed-box/css/styles.css');
	wp_enqueue_script('pg-fixedbox-jrumble',plugins_url().'/pg-fixed-box/js/effects.js');
}

function pg_fixedbox_admin_scripts($hook){
	wp_enqueue_style('pg-fixedbox-admin-css',plugins_url().'/pg-fixed-box/css/admin-style.css');
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script('pg-admin-js',plugins_url().'/pg-fixed-box/js/colorpicker.js', array( 'wp-color-picker' ), false, true );
}

function pg_custome_style($csstoadd){
	$src = $csstoadd;
	str_replace('<style>','',$src);
	str_replace('</style>','',$src);
	echo '<style>'.$csstoadd.'</style>';
}

/**************************
register a query var 
**************************/
add_filter( 'query_vars', 'pg_add_fx_query', 10, 1 );
function pg_add_fx_query($vars){   
    $vars[] = 'pg_sdbi';    
	$vars[] = 'pg_fxb';
    return $vars;
}

/**************************
connecting template to url
**************************/
add_action( 'template_redirect', 'pg_template_redirect' );
function pg_template_redirect(){
	global $wp_query;
	if( $wp_query->query_vars['pg_fxb'] == 'fxb_bar' ){
        include plugin_dir_path( __FILE__ ).'inc/pg-template.php';
        die();
   }
}

/**************************
Registring Widget Area's
**************************/
add_action( 'wp_loaded', 'widget_area_init' );
function widget_area_init() {
	$currentboxes = new pgboxdata();
	$boxes;
	$boxes = $currentboxes->get_boxes();
	if (is_array($boxes) && count($boxes)>0 ){
		foreach ($boxes as $key){
			if (function_exists('register_sidebar')) {
				register_sidebar(array(
					'name' => __($key['title']),
					'id'   => $key['theid'],
					'description'   => __('Insert a widget into this area, and it will be shown in the plugin pop up box.'),
					'before_widget' => '<div class="pgwidget ' . $key['theid'] . '">',
					'after_widget'  => '</div>',
					'before_title'  => '<h2 class="fixedbox-widget-title">',
					'after_title'   => '</h2>'
				));
			}
		}
	}
}

/**************************
inserting the box and fetching widget content .
**************************/
add_action( 'wp_head', 'pg_code_insertion' );
function pg_code_insertion(){
	$currentboxes = new pgboxdata();
	$boxes;
	$data;
	$loadedscripts = false;
	$boxes = $currentboxes->get_boxes();
	if (is_array($boxes)&& count($boxes)>0){
		foreach ($boxes as $key){
			if(($key['page'] == 'everywhere' || (is_home() && $key['page'] == 'home')
			|| (is_page() && $key['page'] == 'page') || (is_single() && $key['page'] == 'single')
			|| (is_archive() && $key['page'] == 'archive') || (is_category && $key['page'] == 'category'))
			&& $key['box-status'] == 'active'){
				
				$data [] = $key;	
				if(!$loadedscripts){
					if(trim($key['custome-css']) != false)
						pg_custome_style($key['custome-css']);
					pg_fixedbox_scripts();
					$loadedscripts = true;
				}
			}
		}
		if(count($data)>0)
			appendthebox($data);
	}
}

/**************************
codes to be appended
**************************/
function appendthebox($boxdata){

?>
<script type="text/javascript">
	var jj=jQuery.noConflict();
	jj(document).ready(function(){
	var fxcontents="";
<?php	

	foreach ($boxdata as $key){
?>
        
		jj.ajax({
			type: "GET",
			url: "<?php echo get_bloginfo('url'); ?>/index.php",
			data: { pg_fxb: "fxb_bar", pg_sdbi: "<?php echo $key['theid']; ?>" },
			success: function(data) { 
						var fxcontents = data; console.log(fxcontents); //alert(data);
						thescript = '\
						<scr'+'ipt>\
						var jj = jQuery.noConflict(); \
						jj(\'.pg-btn.btn<?php echo $key['theid'];?>\').click(function(){\
								jj(\'<?php echo '.pg-fixedbox' . $key['theid'];?>\').css({\
										\'visibility\':\'visible\',\
										\'display\':\'block\'\
								});\
							<?php if($key['boxeffect'] != 'none'){ ?>\
								runboxeffect(\'thebox<?php echo $key['theid'];?>\',\'<?php echo $key['boxeffect'];?>\');\
							<?php } ?>\
						});\
				<?php if($key['autoshow'] == 'yes'){ ?>\
						setTimeout( function() {\
							jj(\'<?php echo '.pg-fixedbox.' . $key['theid'];?>\').css({\
									\'visibility\':\'visible\',\
									\'display\':\'block\'\
							});\
						<?php if($key['boxeffect'] != 'none'){ ?>\
							runboxeffect(\'thebox<?php echo $key['theid'];?>\' , \'<?php echo $key['boxeffect'];?>\');\
						<?php } ?>\
						},<?php echo $key['autoshowdelay'];?>);\
				<?php } ?>\
				<?php if($key['btneffect'] != 'none'){ ?>\
						console.log(runbtneffect(\'btn<?php echo $key['theid'];?>\' , \'<?php echo $key['btneffect'];?>\'));\
				<?php } ?>\
						jj(\'.closebtn\').click(function(){\
								jj(this).parent().parent().parent().fadeOut();\
						}); \
						</scr'+'ipt>';
						
						
						jj('body').append('\
								<div class=\"pg-fixedbox <?php echo $key['theid']; ?> <?php echo 'pg-fixedbox' . $key['theid'];?>\">\
									<div class="pg-centered" style="<?php echo 'width:' . $key['width'].'px;height:' . $key['height'] . 'px;'; ?>\">\
										<div class=\"thebox<?php echo $key['theid'];?>\" style="<?php echo 'width:' . $key['width'].'px;height:' . $key['height'] . 'px;'; ?>\">\
											<span class=\"closebtn\"></span>\
											<div class=\"boxcontent\" style="<?php echo 'background-color:' . $key['boxbackcolor'] . ';"';?>>\
											'+ fxcontents +'\
											</div>\
										</div>\
									</div>\
								</div>\
							<?php if($key['wantbtn'] == 'yes'){ ?>\
								<div class=\"btncontainer <?php echo $key['btnpos']; ?>\"   style="width:<?php echo $key['btnwidth']; ?>px;height:<?php echo $key['btnheight']; ?>px;">\
									<div class=\"pg-btn btn<?php echo $key['theid']; ?>\"  style="background-color:<?php echo $key['btnbackcolor']; ?>;width:<?php echo $key['btnwidth']; ?>px;height:<?php echo $key['btnheight']; ?>px;">\
										<span class=\"btntext\" ><?php echo $key['btntxt']; ?></span>\
									</div>\
								</div>\
							<?php }?>\
							'+thescript); 
					}
		});
		
<?php 
	}
?>
	});

</script>

<?php 
}

?>