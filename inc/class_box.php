<?php
class pgboxdata{
	protected $boxesdata;
	
	public function pgboxdata(){
		if(get_option('fixed_boxes_data')){
			$this->boxesdata = maybe_unserialize(get_option('fixed_boxes_data'));
		}
		else {
			$default = array(
						'title' => 'PG Fixed Box',
						'width' => '500',
						'height' => '300',
						'boxbackcolor' => '#ffffff',
						'boxeffect' => 'none',
						'boxcloseeffect' => 'none',
						'autoshow' => 'yes',
						'autoshowdelay' => 2000,
						'page' => 'everywhere',
						'wantbtn' => 'yes',
						'btnbackcolor' => '#E04343',
						'btntxtcolor' => '#000',
						'btntxt' => 'Show',
						'btnfontsize' => 16,
						'btnpos' => 'pg-btn-tl',
						'btnwidth' => 60,
						'btnheight' => 45,
						'btneffect' => 'none',
						'wantedskin' => 'dark',
						'box-status' => 'active',
						'custome-css' => '',
						'mobile_compatible' => 'yes'
						);
			$this->addbox($default);
		}
			
	}
	
	public function addbox($boxinf){
		if(is_array($boxinf) ){
			$data = $this->boxesdata;
			$uniqueid;
			$uniqueid = $this->generate_id(); 
			$data []= array(
						'theid' => $uniqueid,
						'title' => $boxinf['title'],
						'width' => $boxinf['width'],
						'height' => $boxinf['height'],
						'boxbackcolor' => $boxinf['boxbackcolor'],
						'boxeffect' => $boxinf['boxeffect'],
						'boxcloseeffect' => $boxinf['boxcloseeffect'],
						'autoshow' => $boxinf['autoshow'],
						'autoshowdelay' => $boxinf['autoshowdelay'],
						'page' => $boxinf['page'],
						'wantbtn' => $boxinf['wantbtn'],
						'btnbackcolor' => $boxinf['btnbackcolor'],
						'btntxtcolor' => $boxinf['btntxtcolor'],
						'btntxt' => $boxinf['btntxt'],
						'btnfontsize' => $boxinf['btnfontsize'],
						'btnpos' => $boxinf['btnpos'],
						'btnwidth' => $boxinf['btnwidth'],
						'btnheight' => $boxinf['btnheight'],
						'btneffect' => $boxinf['btneffect'],
						'wantedskin' => $boxinf['wantedskin'],
						'box-status' => $boxinf['box-status'],
						'custome-css' => $boxinf['custome-css'],
						'mobile_compatible' => $boxinf['mobile_compatible']
						);
			$this->updateit($data);
		}
	}
	
	function generate_id(){
		if(is_array($this->boxesdata)){
			$count = count($this->boxesdata);
			$releasedid = "fixedboxarea" . ($count);
			return $releasedid;
		}
		else
			return "fixedboxarea0";
	}
	
	function rewriteit($boxforrewrite){
		if(!is_array($boxforrewrite) || !$this->deletebox($boxforrewrite)){
			return false;
		}
		$data = $this->boxesdata;
		$data [] = $boxforrewrite;
		$this->updateit($data);
		return true;
	}
	
	function deletebox($boxfordelete){
		$whichform = $boxfordelete['theid'];
		$counter = 0;
		$removeit;
		$deleted = false;
		$availboxes = $this->boxesdata;
		foreach($availboxes as $key){
			if($key['theid'] == $whichform){
				$removeit[$counter] = true;
			}
			else
				$removeit[$counter] = false;
			$counter++;
		}
		$counter = 0;
		foreach($removeit as $key=>$val){
			if ($val == true){
				unset($availboxes[$counter]);
				$availboxes = array_values($availboxes);
				$this->updateit($availboxes);
				$deleted = true;
				
			}
			$counter++;
		}
		return $deleted;
		
	}
	
	function updateit($data){
		if(is_array($data)){
			$this->boxesdata = $data;
			update_option('fixed_boxes_data' , maybe_serialize($data));
			return true;
		}
	}

	function get_boxes(){
		if(is_array($this->boxesdata)){
			return $this->boxesdata;
		}
	}
	
	function get_box($id){
		$counter = 0;
		foreach($this->boxesdata as $key){
			if($key['theid'] == $id)
				return $this->boxesdata[$counter];
			$counter++;
		}
	}
}
?>