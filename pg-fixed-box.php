<?php
defined( 'ABSPATH' ) OR exit;
/*
Plugin Name: PG Fixed Boxes Plugin (WP POPUP  Sidebars)
Plugin URI: http://fixedboxes.esy.es/wp/
Description: Center positioned pop up box which accepts any wiget of your choice ! with ability to make unlimited number of fully customizable boxes !
Version: 2.6
Author: PG Team
Author URI: http://parsigroup.net/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
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
adding on activation setting link
**************************/
function pg_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=pg-main">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'pg_settings_link' );

/**************************

loading style and js codes

we load plugin css and js files in function " appendthebox " which contains main js codes.
it is for prevent the loading scripts in non needed pages of your site !
**************************/

//add_action( 'wp_enqueue_scripts', 'pg_fixedbox_scripts' );
add_action( 'admin_enqueue_scripts', 'pg_fixedbox_admin_scripts' );

function pg_fixedbox_scripts(){
	wp_enqueue_script('jquery');
	wp_enqueue_style('pg-fixedbox-css',plugin_dir_url(__FILE__).'css/styles.css');
	wp_enqueue_script('pg-fixedbox-effects',plugin_dir_url(__FILE__).'js/effects.js');
}

function pg_fixedbox_admin_scripts($hook){
	wp_enqueue_style('pg-fixedbox-admin-css',plugin_dir_url(__FILE__).'css/admin-style.css');
	if(is_rtl())
		wp_enqueue_style('pg-fixedbox-admin-css-rtl',plugin_dir_url(__FILE__).'css/admin-style-rtl.css');
	wp_enqueue_script('pg-admin-js',plugin_dir_url(__FILE__).'js/admin-effect.js');
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script('pg-admin-colorpicker-js',plugin_dir_url(__FILE__).'js/colorpicker.js', array( 'wp-color-picker' ), false, true );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script('jquery-ui-accordion');
}

function pg_custome_style($csstoadd){
	$src = $csstoadd;
	str_replace('<style>','',$src);
	str_replace('</style>','',$src);
	echo '<style>'.$csstoadd.'</style>';
}

function pg_fixedbox_transit(){
	wp_enqueue_script('pg-transit-js',plugin_dir_url(__FILE__).'js/transit.js');
}

function pg_fixedbox_skin($skinname){
	if($skinname == 'dark'){
		wp_enqueue_style('pg-skin-dark',plugin_dir_url(__FILE__).'skins/dark/style.css');
	}
	elseif($skinname == 'light'){
		wp_enqueue_style('pg-skin-light',plugin_dir_url(__FILE__).'skins/light/style.css');
	}
	elseif($skinname == 'flat'){
		wp_enqueue_style('pg-skin-flat',plugin_dir_url(__FILE__).'skins/flat/style.css');
	}
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
	if(get_query_var('pg_fxb') !== '')
		if( $wp_query->query_vars['pg_fxb'] == 'fxb_bar' ){
			include plugin_dir_path( __FILE__ ).'inc/pg-template.php';
			die();
		}
	return;
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
	$data = array();
	$loadedmainscripts = false;
	$is_loaded_transit = false;
	$mobilecomp;
	$boxes = $currentboxes->get_boxes();
	$transeffects = array('pgrotate2','pgrect','pgscale');
	//print_r($boxes);
	if (is_array($boxes) && count($boxes) > 0){
		foreach ($boxes as $key){
			if(($key['page'] == 'everywhere' || (is_home() && $key['page'] == 'home')
			|| (is_page() && $key['page'] == 'page') || (is_single() && $key['page'] == 'single')
			|| (is_archive() && $key['page'] == 'archive') || (is_category() && $key['page'] == 'category'))
			&& $key['box-status'] == 'active'){
				
				$data [] = $key;	
				if(!$loadedmainscripts){
					pg_fixedbox_scripts();
					pg_fixedbox_skin($key['wantedskin']);
					$loadedmainscripts = true;
				}
				
				if(trim($key['custome-css']) != false)
					pg_custome_style($key['custome-css']);
						
				if($key['mobile_compatible'] == 'yes')
					$mobilecomp [$key['theid']] = 1;
						
				if (!$is_loaded_transit && (in_array($key['boxeffect'] , $transeffects ) 
				|| in_array($key['btneffect'] , $transeffects ) || in_array($key['boxcloseeffect'] , $transeffects))){
					pg_fixedbox_transit();
					$is_loaded_transit = true;
				}
			}
		}
		if(isset($data) && count($data)>0){
			appendthebox($data,$mobilecomp);
		}
	}
}

/**************************
codes to be appended
**************************/
function appendthebox($boxdata,$mobilecomp){

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
						var fxcontents = data;
						thescript = '\
						<scr'+'ipt>\
						var jj = jQuery.noConflict(); \
						var tandl = getfxpos( jj(\'.pg-centered<?php echo $key['theid'];?>\').width(), jj(\'.pg-centered<?php echo $key['theid'];?>\').height()  );\
						\
						\
						jj(\'.pg-centered<?php echo $key['theid'];?>\').css({\'top\' : tandl[0] , \'left\' : tandl[1]});\
						jj(window).resize(function(){\
							tandl = getfxpos( jj(\'.pg-centered<?php echo $key['theid'];?>\').width(), jj(\'.pg-centered<?php echo $key['theid'];?>\').height()  );\
							jj(\'.pg-centered<?php echo $key['theid'];?>\').css({\'top\' : tandl[0] , \'left\' : tandl[1]});\
						});\
						\
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
						runbtneffect(\'btn<?php echo $key['theid'];?>\' , \'<?php echo $key['btneffect'];?>\');\
				<?php } ?>\
						jj(\'.closebtn\').click(function(){\
								jj(this).parent().parent().parent().fadeOut();\
								runboxeffect(\'thebox<?php echo $key['theid'];?>\' , \'<?php echo $key['boxcloseeffect'];?>\');\
						}); \
			<?php if($key['mobile_compatible'] == 'yes'){ ?>\
					<?php if($mobilecomp[$key['theid']] == 1){ ?>\
						if( jj(window).width() < <?php echo $key['width']; ?>){\
							jj(\'.pg-centered<?php echo $key['theid'];?>\').addClass(\'mobile\').width( jj(window).width() - 30).height(jj(window).height()-100);\
							jj(\'.thebox<?php echo $key['theid'];?>\').addClass(\'mobile\').width( jj(window).width() - 42).height(jj(window).height()-140);\						}\
						jj(window).resize(function() {\
							if( jj(window).width() < <?php echo $key['width']; ?>+30){\
								jj(\'.pg-centered<?php echo $key['theid'];?>\').addClass(\'mobile\').width( jj(window).width() - 30).height(jj(window).height()-100);\
								jj(\'.thebox<?php echo $key['theid'];?>\').addClass(\'mobile\').width( jj(window).width() - 42).height(jj(window).height()-140);\
							}\
							else if( jj(window).width() > <?php echo $key['width']; ?>+30){\
								jj(\'.pg-centered<?php echo $key['theid'];?>\').removeClass(\'mobile\').width( <?php echo $key['width']+6; ?> ).height(<?php echo $key['height']+6; ?>);\
								jj(\'.thebox<?php echo $key['theid'];?>\').removeClass(\'mobile\').width( <?php echo $key['width']; ?> ).height(<?php echo $key['height']; ?>);\
							}\
						});\
					<?php } ?>\
			<?php } ?>\
						</scr'+'ipt>';
						
						
						jj('body').append('\
							<?php if($key['wantbtn'] == 'yes'){ ?>\
								<div class=\"btncontainer <?php echo $key['btnpos']; ?>\"   style="width:<?php echo $key['btnwidth']; ?>px;height:<?php echo $key['btnheight']; ?>px;">\
									<div class=\"pg-btn btn<?php echo $key['theid']; ?> bt-<?php echo $key['wantedskin']; ?>\"  style="background-color:<?php echo $key['btnbackcolor']; ?>;width:<?php echo $key['btnwidth']; ?>px;height:<?php echo $key['btnheight']; ?>px;">\
										<span class=\"btntext\" style="color:<?php echo $key['btntxtcolor']; ?>;font-size:<?php echo $key['btnfontsize']; ?>px;"><?php echo $key['btntxt']; ?></span>\
									</div>\
								</div>\
							<?php }?>\
								<div class=\"pg-fixedbox <?php echo $key['theid']; ?> <?php echo 'pg-fixedbox' . $key['theid'];?> fxb-<?php echo $key['wantedskin']; ?>\">\
									<div class=\"pg-centered  pg-centered<?php echo $key['theid'];?> \" style="width:<?php echo $key['width']+12; ?>px;height:<?php echo $key['height']+40; ?>px;\">\
										<div class=\"thebox thebox<?php echo $key['theid'];?> th-<?php echo $key['wantedskin']; ?>\" style="<?php echo 'width:' . $key['width'].'px;height:' . $key['height'] . 'px;'; ?>\">\
											<span class=\"closebtn cl-<?php echo $key['wantedskin']; ?>\"></span>\
											<div class=\"boxcontent\" style="<?php echo 'background-color:' . $key['boxbackcolor'] . ';"';?>>\
											'+ fxcontents +'\
											</div>\
										</div>\
									</div>\
								</div>\
							'+thescript); 
					}
		});
		//jj(this).parent().parent().parent().fadeOut();\
<?php 
	}
?>
	});

</script>

<?php 
}

?>