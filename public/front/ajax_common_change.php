<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$idx = $_POST["idx"];
$table = $_POST["table"];

$delivery_addr = array(); // 배송지 정보

// q&q 문의 상세정보
if ($table == "tblboard"){

	$sql = "SELECT * FROM {$table} WHERE num = '".$idx."'";
	$result = pmysql_query( $sql, get_db_conn() );

	while( $row = pmysql_fetch_object( $result ) ){
		$delivery_addr[] = array(
				'num'      			=> $row->num,
				'id'      			=> $row->mem_id,
				'name'      		=> $row->name,
				'email'      		=> $row->email,
				'phone'      		=> $row->hp,
				'ip'      			=> $row->ip,
				'subject'      		=> $row->title,
				'content'      		=> $row->content,
				'is_secret'      		=> $row->is_secret
		);
	}
}

// as 문의 상세정보 
if ($table == "tblasinfo"){
	
	$sql = "SELECT * FROM {$table} WHERE idx = '".$idx."'";
	$result = pmysql_query( $sql, get_db_conn() );
	
	while( $row = pmysql_fetch_object( $result ) ){
		$delivery_addr[] = array( 
	            'idx'      			=> $row->idx, 
	            'id'      			=> $row->id, 
	            'name'      		=> $row->name, 
	            'email'      		=> $row->email, 
	            'phone'      		=> $row->phone, 
	            'ip'      			=> $row->ip, 
	            'subject'      		=> $row->subject, 
	            'content'      		=> $row->content, 
	            'date'      		=> $row->date, 
	            're_id'      		=> $row->re_id, 
	            're_name'      		=> $row->re_name, 
	            're_subject'      	=> $row->re_subject, 
	            're_content'      	=> $row->re_content, 
	            're_date'      		=> $row->re_date, 
	            'type_mode'      	=> $row->type_mode, 
	            'productcode'      	=> $row->productcode, 
	            'chk_mail'      	=> $row->chk_mail, 
	            'chk_sms'      		=> $row->chk_sms, 
	            'up_filename'      	=> $row->up_filename, 
	            'ori_filename'      => $row->ori_filename, 
	            'open_yn'      		=> $row->open_yn, 
	            'status'      		=> $row->status, 
	            'udp_dt'      		=> $row->udp_dt, 
	            'reg_dt'      		=> $row->reg_dt
		);
	}
}

// 1:1 문의 수정
if ($table == "tblpersonal"){
	
	$sql = "SELECT * FROM {$table} WHERE idx = '".$idx."'";
	$result = pmysql_query( $sql, get_db_conn() );
	
	while( $row = pmysql_fetch_object( $result ) ){
		$delivery_addr[] = array(
				'idx'      				=> $row->idx,
				'id'      				=> $row->id,
				'name'      			=> $row->name,
				'email'      			=> $row->email,
				'ip'      				=> $row->ip,
				'subject'      			=> $row->subject,
				'date'      			=> $row->date,
				'content'      			=> $row->content,
				're_date'      			=> $row->re_date,
				're_content'      		=> $row->re_content,
				'head_title'      		=> $row->head_title,
				're_id'      			=> $row->re_id,
				're_subject'      		=> $row->re_subject,
				'HP'      				=> $row->HP,
				'sno'      				=> $row->sno,
				'parent'      			=> $row->parent,
				'chk_mail'      		=> $row->chk_mail,
				'chk_sms'      			=> $row->chk_sms,
				'productcode'      		=> $row->productcode,
				'up_filename'      		=> $row->up_filename,
				'ori_filename'      	=> $row->ori_filename,
				're_writer'      		=> $row->re_writer,
				'open_yn'      			=> $row->open_yn
		);
	}
}

echo json_encode( $delivery_addr );

?>


