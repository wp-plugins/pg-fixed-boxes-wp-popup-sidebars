<?php
/* Template Name: pg-sidebar-generate */

if($variable = get_query_var( 'pg_sdbi' )){
	$qustring = $wp_query->query_vars['pg_sdbi'];
	if (strlen($qustring) < 16 && strlen($qustring) > 12 &&  strpos($qustring,'ixedboxarea') !== false){
?>
		<div class="fxbsidebar">
		<?php
		if (function_exists('dynamic_sidebar') && dynamic_sidebar($wp_query->query_vars['pg_sdbi'])){

		}
		?>
		</div>
<?php
	}
}
?>


