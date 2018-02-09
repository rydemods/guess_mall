<?php
/********************************************************************* 
// 파 일 명		: ajax_good_feeling.php
// 설     명		: 호감 비호감 ajax
// 상세설명	:  호감/비호감 proc
// 작 성 자		: 2016-09-12 - daeyeob(김대엽)
// 
*********************************************************************/ 
?>
<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include_once($Dir."conf/config.ap_point.php");

$num = $_POST["num"];
$section = $_POST["section"];
$feeling_type = $_POST["feeling_type"];
$date = date("YmdHis");
$feeling_up_point = $pointSet['feelingUp']['point'];
$feeling_down_point = $pointSet['feelingDown']['point'];
$arr_feeling = array();

$select_sql = "SELECT no, feeling_type FROM tblgood_feeling 
			WHERE member_id = '{$_ShopInfo->getMemid()}' AND code = '{$num}' ";
$result = pmysql_query($select_sql,get_db_conn());
$row = pmysql_fetch_object( $result );
$count = pmysql_num_rows( $result );
if($count > 0){
	//이미 선택된 타입과 선택하려는 타입이 다를 경우는 선택 불가
	if($row->feeling_type != $feeling_type){
		$arr_feeling[] = array(
						'no'      => 0
				);
	}else{
		$delete_sql = "DELETE FROM tblgood_feeling WHERE member_id = '{$_ShopInfo->getMemid()}' AND code = '{$num}' ";
		pmysql_query($delete_sql, get_db_conn());
		if($feeling_type == "good"){
			//insert_point_act($_ShopInfo->getMemid(), $feeling_up_point * -1, "호감 포인트 차감", "feeling_up_point", $date, 0);
            insert_point_act($_ShopInfo->getMemid(), $feeling_up_point * -1, "호감 포인트 차감", "feeling_up_point_".$section, $date, $num);
		}else{
			//insert_point_act($_ShopInfo->getMemid(), $feeling_down_point * -1, "비호감 포인트 차감", "feeling_down_point", $date, 0);
            insert_point_act($_ShopInfo->getMemid(), $feeling_down_point * -1, "비호감 포인트 차감", "feeling_down_point_".$section, $date, $num);
		}
		$count = totalFeeling($num, $section, $feeling_type);
		$arr_feeling[] = array(
				'no'                  => $num,
				'feeling_cnt'      => $count,
				'feeling_type'    => $feeling_type,
				'point_type'      => 'minus'
		);
	}
}else{
	$inset_sql = "INSERT INTO tblgood_feeling (
	member_id,
	section,
	code,
	regdt,
	feeling_type
	)values(
	'{$_ShopInfo->getMemid()}',
	'{$section}',
	'{$num}',
	'{$date}',
	'{$feeling_type}'
	)";
	pmysql_query($inset_sql,get_db_conn());
	$count = totalFeeling($num, $section, $feeling_type);
	
	if($feeling_type == "good"){
		//insert_point_act($_ShopInfo->getMemid(), $feeling_up_point, "호감 포인트", "feeling_up_point", $date, 0);
        insert_point_act($_ShopInfo->getMemid(), $feeling_up_point, "호감 포인트", "feeling_up_point_".$section, $date, $num);
	}else{
		//insert_point_act($_ShopInfo->getMemid(), $feeling_down_point, "비호감 포인트", "feeling_down_point", $date, 0);
        insert_point_act($_ShopInfo->getMemid(), $feeling_down_point, "비호감 포인트", "feeling_down_point_".$section, $date, $num);
	}
	
	$arr_feeling[] = array(
			'no'                  => $num,
			'feeling_cnt'      => $count,
			'feeling_type'    => $feeling_type,
			'point_type'      => 'plus'
	);
	
}

echo json_encode( $arr_feeling );

?>