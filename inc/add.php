<?php
//Default options values
defined( 'ABSPATH' ) OR exit;
if ( is_admin() ) {

	$iscomplete = false;
	$updatedformtitle = '';
	$errors;
	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. 
	else {
		$check = true;
		if (!isset($_POST['box_name']) || trim($_POST['box_name']) == false)  {$check = false;	$errors [] = 'Please enter the name of box'; }
		if (!isset($_POST['box_width']) || !is_numeric($_POST['box_width'])) {$check = false;  $errors [] = 'Please correct the width field (It only accept Numbers)'; }
		if (!isset($_POST['box_height']) || !is_numeric($_POST['box_height'])) {$check = false;  $errors [] = 'Please correct the height field (It only accept Numbers)'; }
		if (!isset($_POST['boxbgcolor'])) {$check = false;	$errors [] = 'Please enter background-color of the box. Example : #FFFFFF'; }
		if(isset($_POST['autoshow'])){
			if (!isset($_POST['box_autoshow_delay']) || !is_numeric($_POST['box_autoshow_delay'])) {$check = false;  $errors [] = 'Please correct "Number of miliseconds" field of autoshow section (it is numeric and could not be empty)'; }
			elseif($_POST['box_autoshow_delay'] < 0 || $_POST['box_autoshow_delay'] > 50000) {$check = false;  $errors [] = 'Please correct the entered value in "Number of miliseconds" field of autoshow section (max: 50000, min:0)'; }
		}
		if(isset($_POST['iwantbtn'])){
			if (!isset($_POST['btnwidth']) || !is_numeric($_POST['btnwidth'])) {$check = false;  $errors [] = 'Please correct "Button width" field of i want button section (it is numeric and could not be empty)'; }
			elseif($_POST['btnwidth'] < 0 || $_POST['btnwidth'] > 5000) {$check = false;  $errors [] = 'Please correct the entered value in "Button width" field of i want button section (max: 5000, min:0)'; }
			if (!isset($_POST['btnheight']) || !is_numeric($_POST['btnheight'])) {$check = false;  $errors [] = 'Please correct "Button height" field of i want button section (it is numeric and could not be empty)'; }
			elseif($_POST['btnheight'] < 0 || $_POST['btnheight'] > 5000) {$check = false;  $errors [] = 'Please correct the entered value in "Button height" field of i want button section (max: 5000, min:0)'; }
		}
		if (!isset($_POST['btnbgcolor'])) {$check = false;	$errors [] = 'Please enter background-color of the button. Example : #FFFFFF'; }
			
		if($check){
			if(isset($_POST['iwantbtn']) && $_POST['iwantbtn'] == 'yes')
				$btnvisibility ='yes'; else $btnvisibility = 'no';
			if(isset($_POST['autoshow']) && $_POST['autoshow'] == 'yes'){
				$autoshow ='yes';
				$autoshowdelay = $_POST['box_autoshow_delay'];
			}else{ 
				$autoshow = 'no';
				$autoshowdelay = 2000;
			}
			$datasender = new pgboxdata();
			$data = array(
						'title' => $_POST['box_name'],
						'width' => $_POST['box_width'],
						'height' => $_POST['box_height'],
						'boxbackcolor' => $_POST['boxbgcolor'],
						'boxeffect' => $_POST['selectedboxeffect'],
						'autoshow' => $autoshow,
						'autoshowdelay' => $autoshowdelay,
						'page' => $_POST['selectedplace'],
						'wantbtn' => $btnvisibility,
						'btnbackcolor' => $_POST['btnbgcolor'],
						'btntxt' => $_POST['btntext'],
						'btnpos' => $_POST['btnplace'],
						'btnwidth' => $_POST['btnwidth'],
						'btnheight' => $_POST['btnheight'],
						'btneffect' => $_POST['selectedbtneffect'],
						'box-status' => $_POST['box-status'],
						'custome-css' => $_POST['custome-css']
						);
			$datasender->addbox($data);
			$updatedformtitle = $_POST['box_name'];
			$iscomplete = true;
?>			<script>// window.location = 'options-general.php?page=pg-main';</script>
<?php

		}

	}
?>

		<?php screen_icon(); echo "<h2 class='sidebar-name'>" .  __( 'ADD New Box', 'fixedboxes' ) . "</h2>";?>
		<hr />
		<?php if ( false !== $_REQUEST['updated'] && $iscomplete) { ?>
		<div class="updated fade"><p><strong><?php _e( 'Box Added' ); ?></strong></p></div>

		<?php }else if(false !== $_REQUEST['updated'] && !$iscomplete){ ?>
			<div class="error fade">
				<p><strong><?php _e( 'Errors detected :' ); ?></strong></p>
				<?php
				for($i = 0;$i<count($errors); $i++) echo '<p>'.$errors[$i].'</p>';
				?>
			</div>
		<?php }

		// If the form has just been submitted, this shows the notification ?>
			
	<div class="pg-content-wrap widgets-holder-wrap" style="background-color:white;padding:8px;margin:10px;">		
		<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">		
			<input type="hidden" name="updated" value="add" />

			<p><?php _e("Box Title :  ", 'fixedboxes' ); ?><input id="box_name" name="box_name" type="text" value="" size="20"><?php _e("  ex: My Box ", 'fixedboxes' ); ?></p>			
			<hr />	
			<p><?php _e("Box Width :  ", 'fixedboxes' ); ?><input id="box_width" name="box_width" type="text" value="" size="10">px<?php _e("  ex: 500", 'fixedboxes' ); ?></p>
			<p><?php _e("Box Height : ", 'fixedboxes' ); ?><input id="box_height" name="box_height" type="text" value="" size="10">px<?php _e("  ex: 300", 'fixedboxes' ); ?></p>
			<p><?php _e("Box Background Color : ", 'fixedboxes' ); ?><input class="boxbgcolor" name="boxbgcolor" type="text" value="#ffffff" class="pg-color-field" data-default-color="#ffffff" /><?php _e("  ex: #000" , 'fixedboxes' ); ?></p>
			
			<p><?php _e("Select one effect for box pop up : ", 'fixedboxes' ); ?></p>
			<p>
				<select name="selectedboxeffect">
					<option value="none"   <?php if ($key['selectedboxeffect'] == 'none') echo 'selected="selected"'; ?>><?php _e("None", 'fixedboxes' ); ?></option>
					<option value="rumble"   <?php if ($key['selectedboxeffect'] == 'rumble') echo 'selected="selected"'; ?>><?php _e("JRumble", 'fixedboxes' ); ?></option>
					<option value="shake"   <?php if ($key['selectedboxeffect'] == 'shake') echo 'selected="selected"'; ?>><?php _e("Shake", 'fixedboxes' ); ?></option>
				</select>
			</p>
			
			<hr />
			<p><input type="checkbox" name="autoshow" value="yes" checked><?php _e(" Show the box after specified miliseconds ", 'fixedboxes' ); ?></p>
			<p><?php _e("Number of miliseconds : ", 'fixedboxes' ); ?><input id="box_autoshow_delay" name="box_autoshow_delay" type="text" value="2000" size="20"><?php _e("  ex: 2000", 'fixedboxes' ); ?></p>
			<p><?php _e("In which page you want show it ? ", 'fixedboxes' ); ?>
				<select name="selectedplace">
					<option value="everywhere" selected="selected"><?php _e("Show in any page", 'fixedboxes') ?></option>
					<option value="home"><?php _e("Show only in home page", 'fixedboxes') ?></option>
					<option value="page"><?php _e("Show only in pages", 'fixedboxes') ?></option>
					<option value="single"><?php _e("Show only in single articles", 'fixedboxes') ?></option>
					<option value="archive"><?php _e("Show only in archive pages", 'fixedboxes') ?></option>
					<option value="category"><?php _e("Show only in category pages", 'fixedboxes') ?></option>
				</select>
			</p>
			<hr />
			<p><input type="checkbox" name="iwantbtn" value="yes" checked><?php _e(" Show a button for making box available or hide it ", 'fixedboxes' ); ?></p>
			<p><?php _e("Button Background Color : ", 'fixedboxes' ); ?><input class="btnbgcolor" name="btnbgcolor" type="text" value="#E04343" data-default-color="#E04343" /></p>			
			<p><?php _e("Button Text : ", 'fixedboxes' ); ?><input class="btntext" name="btntext" type="text" value=""  /></p>			
			<p><?php _e("Button width : ", 'fixedboxes' ); ?><input class="btnwidth" name="btnwidth" type="text" value=""  /></p>			
			<p><?php _e("Button height : ", 'fixedboxes' ); ?><input class="btnheight" name="btnheight" type="text" value=""  /></p>
			
			<p><?php _e("Select one effect for button hover : ", 'fixedboxes' ); ?></p>
			<p>
				<select name="selectedbtneffect">
					<option value="none" selected="selected"><?php _e("None", 'fixedboxes' ); ?></option>
					<option value="rumble" ><?php _e("JRumble", 'fixedboxes' ); ?></option>
					<option value="shake" ><?php _e("Shake", 'fixedboxes' ); ?></option>
				</select>
			</p>
			
			<p><?php _e("Choose position of the box button : ", 'fixedboxes' ); ?>
				<select name="btnplace">
					<option value="pg-btn-tl"><?php _e("top left", 'fixedboxes') ?></option>
					<option value="pg-btn-tr"><?php _e("top right", 'fixedboxes') ?></option>
					<option value="pg-btn-bl" selected="selected"><?php _e("bottom left", 'fixedboxes') ?></option>
					<option value="pg-btn-br"><?php _e("bottom right", 'fixedboxes') ?></option>
				</select>
			</p>
			<hr />
			<p><input type="textarea" name="custome-css" value="" cols="35" rows="5"></p>
			<hr />
			<p><input type="checkbox" name="box-status" value="active" checked><?php _e(" Show this box in my site. ", 'fixedboxes' ); ?></p>
			<p class="submit"><input type="submit" class="button-primary" value="Add Box" /></p>
		</form>	
		
	</div>
		
	<?php
}
?>