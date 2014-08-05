<?php
//Default options values
defined( 'ABSPATH' ) OR exit;
if ( is_admin() ) {
	
	$isupdated = false;
	$isdeleted = false;
	$boxes = new pgboxdata();
	$deletedformtitle = '';
	$updatedformtitle = '';
	$errors;
	$availboxes = $boxes->get_boxes();
	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. 
	else {
		if(isset($_POST['action']) && $_POST['action'] == 'Delete'){
			$whichform = $_POST['fordelete'];
			foreach($availboxes as $key){
				if($key['theid'] == $whichform){
					$deletedformtitle = $key['title'];
					$isdeleted = $boxes->deletebox($key);
				}
			}
		}elseif(isset($_POST['action']) && $_POST['action'] == 'Save'){
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
					$autoshow ='no';
					$autoshowdelay = 2000;
				}
				$boxforrewrite = array(
						'theid' => $_POST['updated'],
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
				$boxes->rewriteit($boxforrewrite);
				$updatedformtitle = $_POST['box_name'];
				$isupdated = true;
				
			}
		}

	}

	?>
		<?php screen_icon(); echo "<h2 class='sidebar-name'>" .  __( 'Edit or remove available boxes', 'fixedboxes' ) . "</h2>";?>
		<hr />
		<?php if ( false !== $_REQUEST['updated'] && $isupdated) { ?>
		<div class="updated fade"><p><strong><?php echo __('Box ') .$updatedformtitle. __( "'s Detail Updated" ); ?></strong></p></div>
		<?php }else if(false !== $_REQUEST['updated'] && !$isupdated && !$isdeleted){ ?>
			<div class="error fade">
				<p><strong><?php _e( 'Errors detected :' ); ?></strong>
				<?php
				for($i = 0; $i < count($errors); $i++) echo '<p>'.$errors[$i].'</p>';
				?>
				</p>
			</div>
		<?php }else if(false !== $_REQUEST['updated'] && !$isupdated && $deleted){ ?>
			<div class="updated fade"><p><strong><?php echo __('Box ').$deletedformtitle.__( 'Is Deleted' ); ?></strong></p></div>
		<?php }
	$availboxes = $boxes->get_boxes();
	foreach($availboxes as $key){	
	
	?>		
		<div class="pg-content-wrap <?php echo	$key['theid']; ?> widgets-holder-wrap" style="background-color:white;padding:8px;margin:10px;">
			<div class="sidebar-name">
				<h2><?php echo $key['title']; ?></h2>
			</div>
			<form	name="boxes"  method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">		
				<input type="hidden" name="updated" value="<?php echo $key['theid']; ?>" />
				<input type="hidden" name="fordelete" value="<?php echo $key['theid']; ?>" />	

				<p><?php _e("Box title : ", 'fixedboxes' ); ?><input id="box_name" name="box_name" type="text" value="<?php echo $key['title']; ?>" size="20"><?php _e("  ex: My Box ", 'fixedboxes' ); ?></p>			
				<hr />	
				<p><?php _e("Box Width : ", 'fixedboxes' ); ?><input id="box_width" name="box_width" type="text" value="<?php echo $key['width']; ?>" size="4">px<?php _e("  ex: 500", 'fixedboxes' ); ?></p>
				<p><?php _e("Box Height : ", 'fixedboxes' ); ?><input id="box_height" name="box_height" type="text" value="<?php echo $key['height']; ?>" size="4">px<?php _e("  ex: 300", 'fixedboxes' ); ?></p>
				<p><?php _e("Box background Color : ", 'fixedboxes' ); ?><input class="boxbgcolor" name="boxbgcolor" type="text" value="<?php echo $key['boxbackcolor']; ?>" data-default-color="<?php echo $key['boxbackcolor']; ?>" /></p>
				
				<p><?php _e("Select one effect for box pop up : ", 'fixedboxes' ); ?></p>
				<p>
					<select name="selectedboxeffect">
						<option value="none"   <?php if ($key['boxeffect'] == 'none') echo 'selected="selected"'; ?>><?php _e("None", 'fixedboxes' ); ?></option>
						<option value="rumble"   <?php if ($key['boxeffect'] == 'rumble') echo 'selected="selected"'; ?>><?php _e("JRumble", 'fixedboxes' ); ?></option>
						<option value="shake"   <?php if ($key['boxeffect'] == 'shake') echo 'selected="selected"'; ?>><?php _e("Shake", 'fixedboxes' ); ?></option>
					</select>
				</p>
				
				<hr />
				<p><input type="checkbox" name="autoshow" value="yes" <?php if($key['autoshow'] == 'yes') echo 'checked'; ?>><?php _e(" Show the box after specified miliseconds ", 'fixedboxes' ); ?></p>
				<p><?php _e("Number of miliseconds : ", 'fixedboxes' ); ?><input id="box_autoshow_delay" name="box_autoshow_delay" type="text" value="<?php echo $key['autoshowdelay']; ?>" size="4"><?php _e("  ex: 2000", 'fixedboxes' ); ?></p>
				<p><?php _e("In which page you want show it ? ", 'fixedboxes' ); ?></p>
				<p>
					<select name="selectedplace">
						<option value="everywhere"  <?php if ($key['page'] == 'everywhere') echo 'selected="selected"'; ?>><?php _e("Show in any page", 'fixedboxes' ); ?></option>
						<option value="home"   <?php if ($key['page'] == 'home') echo 'selected="selected"'; ?>><?php _e("Show only in home page", 'fixedboxes' ); ?></option>
						<option value="page"   <?php if ($key['page'] == 'page') echo 'selected="selected"'; ?>><?php _e("Show only in pages", 'fixedboxes' ); ?></option>
						<option value="single"   <?php if ($key['page'] == 'single') echo 'selected="selected"'; ?>><?php _e("Show only in single articles", 'fixedboxes' ); ?></option>
						<option value="archive"   <?php if ($key['page'] == 'archive') echo 'selected="selected"'; ?>><?php _e("Show only in archive pages", 'fixedboxes' ); ?></option>
						<option value="category"   <?php if ($key['page'] == 'category') echo 'selected="selected"'; ?>><?php _e("Show only in category pages", 'fixedboxes' ); ?></option>
					</select>
				</p>
				<hr />
				<p><input type="checkbox" name="iwantbtn" value="yes" <?php if($key['wantbtn'] == 'yes') echo 'checked'; ?>><?php _e(" Show a button for making box available or hide it ", 'fixedboxes' ); ?></p>
				<p><?php _e("Button Background Color : ", 'fixedboxes' ); ?><input class="btnbgcolor" name="btnbgcolor" type="text" value="<?php echo $key['btnbackcolor']; ?>" data-default-color="<?php echo $key['btnbackcolor']; ?>" /></p>			
				<p><?php _e("Button Text : ", 'fixedboxes' ); ?><input class="btntext" name="btntext" type="text" value="<?php echo $key['btntxt']; ?>"  /></p>			
				<p><?php _e("Button width : ", 'fixedboxes' ); ?><input class="btnwidth" name="btnwidth" type="text" value="<?php echo $key['btnwidth']; ?>"  /></p>			
				<p><?php _e("Button height : ", 'fixedboxes' ); ?><input class="btnheight" name="btnheight" type="text" value="<?php echo $key['btnheight']; ?>"  /></p>

				<p><?php _e("Select one effect for button hover : ", 'fixedboxes' ); ?></p>
				<p>
					<select name="selectedbtneffect">
						<option value="none"   <?php if ($key['btneffect'] == 'none') echo 'selected="selected"'; ?>><?php _e("None", 'fixedboxes' ); ?></option>
						<option value="rumble"   <?php if ($key['btneffect'] == 'rumble') echo 'selected="selected"'; ?>><?php _e("JRumble", 'fixedboxes' ); ?></option>
						<option value="shake"   <?php if ($key['btneffect'] == 'shake') echo 'selected="selected"'; ?>><?php _e("Shake", 'fixedboxes' ); ?></option>
					</select>
				</p>
				
				<p><?php _e("Choose position of the box button : ", 'fixedboxes' ); ?>
					<select name="btnplace">
						<option value="pg-btn-tl"  <?php if ($key['btnpos'] == 'pg-btn-tl') echo 'selected="selected"'; ?>><?php _e("top left", 'fixedboxes') ?></option>
						<option value="pg-btn-tr"  <?php if ($key['btnpos'] == 'pg-btn-tr') echo 'selected="selected"'; ?>><?php _e("top right", 'fixedboxes') ?></option>
						<option value="pg-btn-bl"  <?php if ($key['btnpos'] == 'pg-btn-bl') echo 'selected="selected"'; ?>><?php _e("bottom left", 'fixedboxes') ?></option>
						<option value="pg-btn-br"  <?php if ($key['btnpos'] == 'pg-btn-br') echo 'selected="selected"'; ?>><?php _e("bottom right", 'fixedboxes') ?></option>
					</select>
				</p>
				<hr />
				<?php _e(" Insert Your Custome Css ", 'fixedboxes' ); ?>
				<p><textarea type="textarea" name="custome-css" value="" cols="35" rows="5"><?php echo $key['custome-css']; ?></textarea></p>
				<hr />
				<p><input type="checkbox" name="box-status" value="active" <?php if($key['box-status'] == 'active') echo 'checked'; ?>><?php _e(" Show this box in my site ", 'fixedboxes' ); ?></p>

				<p class="submit">
					<input class="button-primary" name="action" type="submit"  value="Save" />
					<input class="button-primary" name="action" type="submit"  value="Delete" />
				</p>
			</form>	
			
		</div>
			
	<?php
	}

}

?>