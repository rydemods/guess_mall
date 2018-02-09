<?php
//0929 원재 개발일정 단 하루 줌 ㅋㅋㅋ 
class PREMIUMBRAND
{
	public $Dir ="../";
	public $pb_list_num ='116'; //배너관리 프리미엄 배너 연동
	public $cube_number = 9; //큐브 9개 사용합니다
	public $pb_list = null;
	public $cube_list = null;
	public $section_list = null;
	public $section_slide_list = null;

	public function __construct($mode)
	{

		if($mode=='pb_list'){
			$this->pb_list();
		}

		if($mode=='write_cube'){
			$this->write_cube();
		}

		if($mode=='write_section'){
			$this->write_section();
		}

		if($mode=='pb_info'){
			$this->pb_info();
		}
	}

	function pb_info()
	{
		$brand_no = $_REQUEST['brand_no'];
		$sql = " select * from tblmainbannerimg where no = '{$brand_no}' ";
		$this->pb_info = pmysql_fetch_object( pmysql_query($sql) );
	}

	function pb_list()
	{
		$list = array();
		$sql = " select * from tblmainbannerimg where banner_no ={$this->pb_list_num} ";
		$result = pmysql_query($sql);
		while( $row = pmysql_fetch_object($result) ){
			$list[] = $row;
		}
		$this->pb_list = $list;
	}

	function write_cube()
	{
		$Dir ="../";
		include_once($Dir."lib/file.class.php");
		$imagepath = $Dir.DataDir."shopimages/premiumbrand/";
		$imagefile = new FILE($imagepath);
		$up_imagefile = $imagefile->upFiles();
			
		$cube_number = 9; //큐브 9개 사용합니다 ㅠㅠ
		
		//post data
		$brand_no = $_REQUEST['brand_no'];
		$sel_type = $_POST['sel_type'];
		$v_logo_file = $_POST['v_logo_file']; //이미 등록된 큐브 로고
		$v_bg_file = $_POST['v_bg_file'];//이미 등록된 배경 이미지

		$v_cube_file = $_POST['v_cube_file']; //이미 등록된 큐브이미지
		$v_cube_file2 = $_POST['v_cube_file2'];//이미 등록된 마우스오버 큐브이미지
		$v_thumb_file = $_POST['v_thumb_file'];//이미 등록된 동영상 썸네일 이미지
		$link_type = $_POST['link_type']; //링크 타입 앵커or프리
		$link = $_POST['link']; //큐브 이미지 링크
		$movie_link = $_POST['movie_link'];
		$use_cube = $_POST['use_cube'];

		##기존 데이터 있는지 체크##
		$chk_cube = array();
		$chk_sql = " select sort_no from tblpremiumbrand where brand_no = {$brand_no} AND type='C' ";
		$chk_result = pmysql_query($chk_sql);
		while( $chk_row = pmysql_fetch_object($chk_result) ){
			$chk_cube[$chk_row->sort_no] ='ok';
		}
		
		##넘어온 큐브 데이터 처리
		for($i =0; $i < $this->cube_number; $i++){
			$index = $i+1;
				
			if($chk_cube[$i+1]=='ok'){//이미 데이터가 있으므로 수정 쿼리
				$sql = " update tblpremiumbrand set ";
				if($sel_type[$i] == 'i'){
					if($v_cube_file[$i] == $up_imagefile['cube_file'][$i]['v_file'] || $up_imagefile['cube_file'][$i]['v_file'] == ""){
						$sql .= "img = '{$v_cube_file[$i]}', ";
					}else{
						$sql .= "img = '{$up_imagefile['cube_file'][$i]['v_file']}', ";
					}
					if($v_cube_file2[$i] == $up_imagefile['cube_file2'][$i]['v_file'] || $up_imagefile['cube_file2'][$i]['v_file'] == ""){
						$sql .= "img2 = '{$v_cube_file2[$i]}', ";
					}else{
						$sql .= "img2 = '{$up_imagefile['cube_file2'][$i]['v_file']}', ";
					}
						
				}else{
					if($v_thumb_file[$i] == $up_imagefile['thumb_file'][$i]['v_file'] || $up_imagefile['thumb_file'][$i]['v_file'] == ""){
						$sql .= "img = '{$v_thumb_file[$i]}', ";
					}else{
						$sql .= "img = '{$up_imagefile['thumb_file'][$i]['v_file']}', ";
					}
					
				}
				$sql .= " type2 = '{$sel_type[$i]}', ";
				$sql .= " link_type = '{$link_type[$i]}', ";
				$sql .= " link = '{$link[$i]}', ";
				$sql .= " movie_link = '{$movie_link[$i]}',";
				$sql .= " modifytime = now() ";
				$sql .= " where sort_no ={$index} AND brand_no ={$brand_no} AND type='C' ";
				pmysql_query($sql);
			}else{//데이터 없을시 insert
				if($sel_type[$i]){//선택한 타입이 있을대만 insert
					$insert_file ="";
					$insert_file2 ="";
					if($sel_type[$i] == 'i'){
						$insert_file = $up_imagefile['cube_file'][$i]['v_file'];
						$insert_file2 = $up_imagefile['cube_file2'][$i]['v_file'];
					}else{
						$insert_file = $up_imagefile['thumb_file'][$i]['v_file'];
					}
					$sql = "
						insert into tblpremiumbrand ( 
							sort_no,
							brand_no,
							type,
							type2,
							img,
							img2,
							link_type,
							link,
							movie_link,
							writetime
						) values (
							{$index},
							{$brand_no},
							'C',
							'{$sel_type[$i]}',
							'{$insert_file}',
							'{$insert_file2}',
							'{$link_type[$i]}',
							'{$link[$i]}',
							'{$movie_link[$i]}',
							now()
						)
					";
					pmysql_query($sql);
				}
			}
		}

		if($use_cube =='Y'){
			$u_sql = " update tblmainbannerimg set use_cube ='Y' where no ={$brand_no} ";
		}else{
			$u_sql = " update tblmainbannerimg set use_cube ='N' where no ={$brand_no} ";
		}
		pmysql_query($u_sql);

		if($v_logo_file == $up_imagefile['logo_file'][0]['v_file'] || $up_imagefile['logo_file'][0]['v_file'] == ""){
			$logo_sql = " update tblmainbannerimg set brand_logo = '{$v_logo_file}' where no ={$brand_no} ";
			pmysql_query($logo_sql);
		}else{
			$logo_sql = " update tblmainbannerimg set brand_logo = '{$up_imagefile['logo_file'][0]['v_file']}' where no ={$brand_no} ";
			pmysql_query($logo_sql);
		}

		if($v_bg_file == $up_imagefile['bg_file'][0]['v_file'] || $up_imagefile['bg_file'][0]['v_file'] == ""){
			$bg_sql = " update tblmainbannerimg set brand_bg = '{$v_bg_file}' where no ={$brand_no} ";
			pmysql_query($bg_sql);
		}else{
			$bg_sql = " update tblmainbannerimg set brand_bg = '{$up_imagefile['bg_file'][0]['v_file']}' where no ={$brand_no} ";
			pmysql_query($bg_sql);
		}
		
		echo "<script>alert('적용 되었습니다');window.close();</script>";

	}//end write_cube


	function write_section()
	{
		$Dir ="../";
		include_once($Dir."lib/file.class.php");
		$imagepath = $Dir.DataDir."shopimages/premiumbrand/";
		$imagefile = new FILE($imagepath);
		$up_imagefile = $imagefile->upFiles();

		$brand_no = $_REQUEST['brand_no'];
		$v_scetion_file = $_POST['v_section_file'];
		$v_scetion_file_m = $_POST['v_section_file_m'];
		$v_slide_file = $_POST['v_slide_file'];
		$section_link = $_POST['section_link'];
		$slide_link = $_POST['slide_link'];
		$display1 = $_POST['display1'];
		$display2 = $_POST['display2'];
		$display_m = $_POST['display_m'];
		##기존 섹션 데이터 있는지 체크##
		$chk_section= array();
		$chk_sql = " select sort_no from tblpremiumbrand where brand_no = {$brand_no} AND type='S' ";
		$chk_result = pmysql_query($chk_sql);
		while( $chk_row = pmysql_fetch_object($chk_result) ){
			$chk_section[$chk_row->sort_no] ='ok';
		}
		
		##넘어온 섹션 데이터 처리
		for($i =0; $i<9; $i++){
			$index = $i+1;
			if($chk_section[$i+1]=='ok'){//이미 데이터가 있으므로 수정 쿼리

				$sql = " update tblpremiumbrand set ";

				if($v_scetion_file[$i] == $up_imagefile['section_file'][$i]['v_file'] || $up_imagefile['section_file'][$i]['v_file'] == ""){
					$sql .= "img = '{$v_scetion_file[$i]}', ";
				}else{
					$sql .= "img = '{$up_imagefile['section_file'][$i]['v_file']}', ";
				}
				if($v_scetion_file_m[$i] == $up_imagefile['section_file_m'][$i]['v_file'] || $up_imagefile['section_file_m'][$i]['v_file'] == ""){
					$sql .= "img_m = '{$v_scetion_file_m[$i]}', ";
				}else{
					$sql .= "img_m = '{$up_imagefile['section_file_m'][$i]['v_file']}', ";
				}
				
				$sql .= " link = '{$section_link[$i]}', ";
				$sql .= " display = '{$display1[$i]}', ";
				$sql .= " display_m = '{$display_m[$i]}', ";
				$sql .= " movie_link = '{$movie_link[$i]}',";
				$sql .= " modifytime = now() ";
				$sql .= " where sort_no ={$index} AND brand_no ={$brand_no} AND type='S' ";
				pmysql_query($sql);
			}else{//없으면 insert
				$insert_file ="";
				$insert_file_m ="";
				$insert_file = $up_imagefile['section_file'][$i]['v_file'];
				$insert_file_m = $up_imagefile['section_file_m'][$i]['v_file'];

				if($insert_file){//업로드한 파일이 있을때에만
					$sql = " 
						insert into tblpremiumbrand (
							brand_no,
							sort_no,
							type,
							img,
							img_m,
							link,
							display,
							display_m,
							writetime
						) values (
							{$brand_no},
							{$index},
							'S',
							'{$insert_file}',
							'{$insert_file_m}',
							'{$section_link[$i]}',
							'{$display1[$i]}',
							'{$display_m[$i]}',
							now()
						)
					";
					pmysql_query($sql);
				}

			}
		}

		##기존 섹션 슬라이드 데이터 있는지 체크##
		$chk_section_slide= array();
		$chk_sql = " select sort_no from tblpremium_slide where brand_no = {$brand_no} ";
		$chk_result = pmysql_query($chk_sql);
		while( $chk_row = pmysql_fetch_object($chk_result) ){
			$chk_section_slide[$chk_row->sort_no] ='ok';
		}

		for($i =0; $i<12; $i++){ //넘어온 슬라이드 데이터 처리
			$index = $i+1;
			if($chk_section_slide[$i+1]=='ok'){//이미 데이터가 있으므로 수정 쿼리
				$sql = " update tblpremium_slide set ";
				
				if($v_slide_file[$i] == $up_imagefile['slide_file'][$i]['v_file'] || $up_imagefile['slide_file'][$i]['v_file'] == ""){
					$sql .= "img = '{$v_slide_file[$i]}', ";
				}else{
					$sql .= "img = '{$up_imagefile['slide_file'][$i]['v_file']}', ";
				}

				$sql .= " link = '{$slide_link[$i]}' ";
				$sql .= " where sort_no ={$index} AND brand_no ={$brand_no} ";
				debug($sql);
				pmysql_query($sql);
			}else{
				$insert_slide_file ="";
				$insert_slide_file = $up_imagefile['slide_file'][$i]['v_file'];
				if($insert_slide_file){//업로드한 파일이 있을때에만 insert
					$sql = " 
						insert into tblpremium_slide (
							brand_no,
							sort_no,
							img,
							link
							
						) values (
							{$brand_no},
							{$index},
							'{$insert_slide_file}',
							'{$slide_link[$i]}'

						)
					";
					pmysql_query($sql);
				}
			}
		}
		##슬라이드 노출 조건 
		if($display2){
			$sql = "update tblpremium_slide set display = '{$display2}' where brand_no = {$brand_no} ";
			pmysql_query($sql);
		}
		echo "<script>alert('적용 되었습니다');window.close();</script>";
	}

	function section_list($mode)
	{
		$brand_no = $_REQUEST['brand_no'];
		$list = array();
		$sql = " select * from tblpremiumbrand where brand_no = {$brand_no} AND type='S' ";
		if($mode=='web'){
			$sql .= " AND display ='Y' ";
		}
		if($mode=='mobile'){
			$sql .= " AND display_m ='Y' ";
		}
		$sql .= " order by sort_no asc ";
		$result = pmysql_query($sql);
		while( $row = pmysql_fetch_object($result) ){
			$list[$row->sort_no] = $row;
		}

		$this->section_list = $list;
	}

	function section_slide_list($mode)
	{
		
		$brand_no = $_REQUEST['brand_no'];
		$list = array();
		$sql = " select * from tblpremium_slide where brand_no = {$brand_no} ";
		if($mode){
			$sql .= " AND display ='Y' ";
		}
		$sql .= " order by sort_no asc ";
		$result = pmysql_query($sql);
		while( $row = pmysql_fetch_object($result) ){
			$list[$row->sort_no] = $row;
		}
		$this->section_slide_list = $list;
	}

	
	function cube_list()
	{
		$brand_no = $_REQUEST['brand_no'];
		//$brand_no = '796';
		$list = array();
		$sql = " select * from tblpremiumbrand where brand_no = {$brand_no} AND type='C' order  by sort_no asc ";
		$result = pmysql_query($sql);
		while( $row = pmysql_fetch_object($result) ){
			if($row->type2=='i'){
				$row->chk_sel_type['i']="checked";
				if($row->link_type=='a'){
					$row->chk_link_type['a']="checked";
				}else if($row->link_type=='f'){
					$row->chk_link_type['f']="checked";
				}
			}else if($row->type2=='m'){
				$row->chk_sel_type['m']="checked";
				$link = explode("/", str_replace(array('http://','https://'), '', $row->movie_link) );
				$row->link = $link[1];
			}
			$list[$row->sort_no] = $row;
		}
		$this->cube_list = $list;
	}


}//end class 