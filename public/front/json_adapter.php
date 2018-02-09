<?php 

Header("Content-type: text/html; charset=utf-8");

$Dir="../";
include_once($Dir."lib/cache_main.php");
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

//-----------------------------------
//	이미지 업로드 처리
//-----------------------------------

if(empty($_FILES)){
		
	if($_REQUEST[sp_type]==""){
		exit;
	}
}else{
	
	define('_UPLOAD_NOTICE', $_SERVER['DOCUMENT_ROOT']."");
	
	$retArr = "";
	foreach ($_FILES as $key => $value) {
		//$keylower = strtoupper($key);
		$keylower = ($key);
		$values = $value;
		
	
		$imageKind = array ('image/pjpeg', 'image/jpeg', 'image/JPG', 'image/X-PNG', 'image/PNG', 'image/png', 'image/x-png', 'image/gif', 'image/GIF', 'image/jpegArray');
		//echo $_FILES[$key]['type'];
		//$retArr .= $_FILES[$key]['type'] . "^";
		
		if (in_array($_FILES[$key]['type'], $imageKind)) {
			
			
			$name = $_FILES[$key]['name'];
			
			$file = preg_replace('/^.+\.([^\.]{1,})$/','\\1',$name); //file 확장자 추출
			$img_ext = $file; //mb_strtolower($file);
	
	
	        $new_file = date('YmdHis')."_".mt_rand(100000, 999999).".".$img_ext;
	
			if($_REQUEST[save_folder]==""){
				$folder = "/data/shopimages/board/photo/";	
			}else{
				$folder = "/data/shopimages/$_REQUEST[save_folder]/";
			}
			
			
	
	        if (move_uploaded_file ($_FILES[$key]['tmp_name'], _UPLOAD_NOTICE.$folder.$new_file)) {
				// echo "1|".$new_file."|".$name."|".$folder;
				$retArr .= $key . "^". $new_file . "|";
				
				 
	        }
			
	 	} else {
        	//echo "0|업로드 파일이 아닙니다(".$_FILES['user_img']['type'].")";
		}
	}
	
	
	echo $retArr;
	
	exit;
	
}


//-----------------------------------
//	parameter
//-----------------------------------
$code 		= true;
$error_msg	= "";
$error_code	= "";
$error_chck = true;


$_arrayAes = array('');
$json_arr = new stdClass();


//-----------------------------------
//	parameter및변수선언
//-----------------------------------
$code 		= true;
$error_msg	= "";
$error_code	= "";
$error_chck = true;
$success = true;

$sp_type = trim($_REQUEST[sp_type]);
$sp_name = trim($_REQUEST[sp_name]);
$sp_param = trim($_REQUEST[sp_param]);
$sp_paging = trim($_REQUEST[sp_paging]);
$sp_dynamic = trim($_REQUEST[sp_dynamic]);
$sp_decode = trim($_REQUEST[sp_decode]);

$auto_type = trim($_REQUEST[auto_type]);

$adapter_conn =  get_db_conn();

$db = new Database($adapter_conn);


//쿼리
$xml =simplexml_load_file("../js/json_adapter/json_adapter_query.xml");

foreach($xml->children() as $node) {

    $arr = $node->attributes();
    $node_id = $arr["id"];

	if($node_id==$sp_name){
		$multi_query = $node;

	}

}


//데이터치환
$sp_paramArr = explode("|",$sp_param);
//weblog('pArr:'.$sp_paramArr, 4);

//echo $multi_query;
for($ii=0;$ii<count($sp_paramArr);$ii++){

	if(strpos($sp_paramArr[$ii], ":")){
		
		$sp_paramArrsub = explode(":", $sp_paramArr[$ii]);
		
		$sp_paramArrsub[1] = str_replace(";", "*-*" , $sp_paramArrsub[1]);
		$patterns = "/\#{".$sp_paramArrsub[0]."}/";
		$multi_query = preg_replace($patterns, $sp_paramArrsub[1], $multi_query,1);
		
	}else{
		$multi_query = str_replace("?", "{}" , $multi_query);
	
		//세미콜론처리
		$sp_paramArr[$ii] = str_replace(";", "*-*" , $sp_paramArr[$ii]);
	
		$patterns = "/\{}/";
		$multi_query = preg_replace($patterns, $sp_paramArr[$ii], $multi_query,1);
		
	}	



}	


//페이징변수치환
	if($sp_paging!=""){
	
		$sp_pagingArr = explode("|",$sp_paging);
		$_currpage = $sp_pagingArr[0];
		$_roundpage= $sp_pagingArr[1];
		$start = ($_currpage-1)*$_roundpage;
		
	
		$multiArrD = explode("order by", $multi_query);
		$multiArrH = explode("select", $multiArrD[0]);
		$multiArrH2 = "";
		
		for($ii=0;$ii<count($multiArrH);$ii++){
			if($ii==0){
				
			}else if($ii==1){
				$multiArrH2 .= $multiArrH[$ii];
			}else{
				$multiArrH2 .= "select".$multiArrH[$ii];
			}
		}
		
		
		
		$query_list = " select row_number () over (order by $multiArrD[1]), ".$multiArrH2.
			
		" limit  $_roundpage offset ($_currpage - 1) * $_roundpage ";
	
		//$multi_query = $query_cnt.";".$query_list;
		$multi_query = $query_list;
	
		//$multi_query = str_replace("#start#", $start, $multi_query);
		//$multi_query = str_replace("#end#", $sp_pagingArr[1], $multi_query);
	
	}




list($code, $error_msg, $json_arr_sub, $total_count) = $db->$sp_type($sp_name, $sp_param, $multi_query, $sp_decode);


class Database {

    private $host_name;
    private $host_id;
    private $host_password;
    private $db_name;
	private $error_msg;

    function Database($conn) {

        //$this->conn = mysqli_connect($this->host_name, $this->host_id, $this->host_password, $this->db_name);
        $this->conn = $conn;

    }
	

    public function get_insert_id() {
        $id = mysqli_insert_id($this->conn);
        return $id;
    }

	

	public function getDBFunc($sp_name, $sp_param, $multi_query, $sp_decode){
		$code = true;
		$result=pmysql_query($multi_query,$adapter_conn);
		$ii=0;
		if($result){
			while ($row = pmysql_fetch_object($result)) {
				
				$json_arr_sub[$ii] = new stdClass();
				foreach ($row as $key => $value) {
					//$keylower = strtoupper($key);
					$keylower = ($key);
					$values = $value;
				
					$json_arr_sub[$ii]->$keylower	= trim(html_entity_decode($values));
						
				}
				 $ii+=1;
			}	
			
		}else{
			$this->error_msg = "쿼리오류";
			$code = false;
		}
		

		return array($code, $this->error_msg, $json_arr_sub, $total_count);
		
	}


	public function setDBFunc($sp_name, $sp_param, $multi_query){

		BeginTrans();
		$code = true;
		
		$multi_query = str_replace("<br>", "" , $multi_query);
		$queryArr = explode(";",$multi_query);
		
		for($ii=0;$ii<count($queryArr);$ii++){
			
			if(trim($queryArr[$ii])!=""){
				//echo $multi_query;exit;
				$result = pmysql_query( $multi_query, $adapter_conn );

				if (!$result) {
					$code = false;
	            }

			}
		}
				
		
		if( pmysql_errno() ){
			$this->this_code = 'E04';
			$this->success = false;
		} else {
			$this->this_code = 'S02';
			$this->return_idx = $basketidx;
		}


		if($code){
			CommitTrans();
		} else {
			RollbackTrans();
			$code = false;
		}

		return array($code, $this->error_msg, "");

	}
	
	
	
}


//-----------------------------------
//  JSON생성
//-----------------------------------

	$DEV_MODE = true;

	$json_arr->code = $code;
	$json_arr->error_code = $error_code;
    $json_arr->error_msg = urlencode($error_msg);
	if($DEV_MODE){
		$json_arr->query = $multi_query;	
	}
	
	$json_arr->data = $json_arr_sub;


	//apilog( "[Adapter] output : ".urldecode(json_encode($json_arr))  );

	echo urldecode(json_encode($json_arr))."\n";


	

?>
