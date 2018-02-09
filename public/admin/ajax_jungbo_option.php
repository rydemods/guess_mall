<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/jungbo_code.php");

$code = $_POST['code'];
$prcode = $_POST['prcode'];
$jungbo_val = '';
#정보고시 코드를 받아서 테이블 모양을 리턴해준다
if( $code ){
	if( $prcode ){
		//상품코드가 있으면 해당 내용도 가져온다
		$sql = "SELECT sabangnet_prop_val FROM tblproduct WHERE productcode = '".$prcode."' ";
		$result = pmysql_query( $sql, get_db_conn() );
		$row = pmysql_fetch_object( $result );
		if( ord($row->sabangnet_prop_val) ) {
			$sabangnet_prop_val = explode("||",$row->sabangnet_prop_val);
			$prop_type = $sabangnet_prop_val[0];
			if( $prop_type == $code ) $jungbo_val = $sabangnet_prop_val;
		}
	}
	//정보고시 코드로 해당 내용을 가져온다
	$incode = $jungbo_code[$code];
	$optionKey = 1;
	if( $incode ){
?>
		<!-- <colgroup><col style="width:30%"/><col style="width:*"/></colgroup> -->
<?php
		foreach( $incode['option'] as $inKey=>$inVal ){
?>
			<tr>
				<th>
					<span><?=$inVal?></span>
					<input type='hidden' name='jungbo_prop_option' id='' value='<?=$inVal?>' >
				</th>
				<td>
					<input type='text' name='jungbo_prop_val' id='' value='<?=$jungbo_val[$optionKey]?>' style="width:450px;" />
					<input type='checkbox' name='option_chk' > 상세정보 별도표기
					<br><span class='font_blue' ><?=$incode['comment'][$inKey]?></span>
				</td>
			</tr>
<?php
			$optionKey++;
		}
	} else {
		exit;
	}
} else {
	exit;
}


?>