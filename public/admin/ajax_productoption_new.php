<?php

$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$prcode = $_POST['it_id']; // 상품코드
$opt_subject = $_POST['opt_subject']; // 옵셥명 array();
$opt_content = $_POST['opt_content']; // 옵셥항목 array();

$po_run = false;
$arrSubject = array();
$arrContent = array();
$arrCount = array();
$arrDepth = 0;

if( !empty($_POST) ){ // prcode if

	foreach( $opt_subject as $subjectKey=>$subjectVal ){
		//$arrSubject[$subjectKey] = explode( '@#', preg_replace( '/[\'\"]/', '', trim( stripslashes( $subjectVal ) ) ) );
		$arrSubject[$subjectKey] = preg_replace( '/[\'\"]/', '', trim( stripslashes( $subjectVal ) ) );
	} // option_subject foreach
	foreach( $opt_content as $contentKey=>$contentVal ){
		$arrContent[$contentKey] = explode( '@#', preg_replace( '/[\'\"]/', '', trim( stripslashes( $contentVal ) ) ) );
		$arrCount[$contentKey] = count( $arrContent[$contentKey] );
	}  // option_content foreach

	if( !$arrSubject[0] || !$arrContent[0] ){
		echo '옵션1과 옵션1 항목을 입력해 주십시오.';
        exit;
	}

	$arrDepth = count( $arrCount );
	$po_run = true;

} // empty(POST) else if

if( $po_run ){

	$opt_s	= array();
	$opt_e	= array();
	$codeArr = array();

	for ( $i = ( $arrDepth - 1 ); $i >= 0; $i-- ) { // 총 길이에서 역으로 배열을 가져온다

		for ( $r=0; $r < $arrCount[$i]; $r++) { // 각 2차 배열의 길이만큼
			
			if( count( $opt_s ) == 0 ) { // 소팅 배열이 없으면
				$opt_e[]	= $arrContent[$i][$r]; // 현제 배열을값을 지정
			} else { // 시작배열이 있으면
				for ( $e = 0; $e < count( $opt_s ); $e++ ) { // 소팅배열의 길이 * 현제 배열의 값을 현제 배열값으로 넣어줌
					$opt_e[]	= $arrContent[$i][$r].chr(30).$opt_s[$e];	
				}
			}
		}
		$opt_s	= $opt_e; // 총 depth 의 내용을 소팅배열에 올려줌
		$opt_e	= array(); // 현제값 초기화
	}

	if( strlen( $prcode ) > 0 ){
		$sql = "SELECT option_num, option_code, productcode, option_price, option_quantity, option_quantity_noti, option_type, option_use, self_goods_code ";
		$sql.= "FROM tblproduct_option WHERE productcode ='".$prcode."' AND option_type = 0 ORDER BY option_num ASC ";
		$result = pmysql_query( $sql, get_db_conn() );
		while( $row = pmysql_fetch_object( $result ) ){
			$selectCode[] = $row->option_code;
			$selectArr[] = $row;
		}
		pmysql_free_result( $result );
	}

	for( $i = 0; $i < count( $opt_s ); $i++ ) {
		$selectKey = array_search( $opt_s[$i], $selectCode );
		if( $selectKey ){
			$codeArr[] = array(
				'option_num'           => $selectArr[$selectKey]->option_num,
				'option_code'          => $selectArr[$selectKey]->option_code,
				'option_price'         => (int) $selectArr[$selectKey]->option_price,
				'option_quantity'      => (int) $selectArr[$selectKey]->option_quantity,
				'option_quantity_noti' => (int) $selectArr[$selectKey]->option_quantity_noti,
				'option_type'          => $selectArr[$selectKey]->option_type,
				'option_use'           => (int) $selectArr[$selectKey]->option_use,
				'self_goods_code'      => $selectArr[$selectKey]->self_goods_code
			);
		} else {
			$codeArr[] = array(
				'option_num'           => null,
				'option_code'          => $opt_s[$i],
				'option_price'         => 0,
				'option_quantity'      => 9999,
				'option_quantity_noti' => 100,
				'option_type'          => 0,
				'option_use'           => 1,
				'self_goods_code'      => null
			);
		}
	}

?>
<div class="sit_option_frm_wrapper">
    <table>
    <caption>옵션 목록</caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="opt_chk_all" class="sound_only">전체 옵션</label>
            <input type="checkbox" name="opt_chk_all" value="1" id="opt_chk_all">
        </th>
        <th scope="col">옵션</th>
        <th scope="col">추가금액</th>
        <th scope="col">재고수량</th>
        <th scope="col">통보수량</th>
        <th scope="col">자체품목코드</th>
        <th scope="col">사용여부</th>
    </tr>
    </thead>
    <tbody>
<?php
	foreach( $codeArr as $codeKey=>$codeVal ){
		$tmpCode = explode( chr(30), $codeVal['option_code'] );
?>
		<tr>
			<td class="td_chk">
				<input type="hidden" name="opt_num[]" value="<?=$codeVal['option_num']?>">
				<input type="hidden" name="opt_id[]" value="<?=$codeVal['option_code']?>">
				<label for="opt_chk_<?=$codeKey?>" class="sound_only"></label>
				<input type="checkbox" name="opt_chk[]" id="opt_chk_<?=$codeKey?>" value="1">
			</td>
			<td class="opt-cell">
<?php 
		foreach( $tmpCode as $tmpKey=>$tmpVal ){
				if( $tmpKey == 0 ) echo $tmpVal;
				else echo ' <small>&gt;</small> '.$tmpVal;
		}
?>
			</td>
			<td class="td_numsmall">
				<label for="opt_price_<?=$codeKey?>" class="sound_only"></label>
				<input type="text" name="opt_price[]" value="<?=$codeVal['option_price']?>" id="opt_price_<?=$codeKey?>" class="frm_input" size="9">
			</td>
			<td class="td_num">
				<label for="opt_stock_qty_<?=$codeKey?>" class="sound_only"></label>
				<input type="text" name="opt_stock_qty[]" value="<?=$codeVal['option_quantity']?>" id="op_stock_qty_<?=$codeKey?>" class="frm_input" size="5">
			</td>
		    <td class="td_num">
				<label for="opt_noti_qty_<?=$codeKey?>" class="sound_only"></label>
				<input type="text" name="opt_noti_qty[]" value="<?=$codeVal['option_quantity_noti']?>" id="opt_noti_qty_<?=$codeKey?>" class="frm_input" size="5">
			</td>
			<td class="td_num">
				<label for="opt_goods_code_<?php echo $codeKey; ?>" class="sound_only"></label>
				<input type="text" name="opt_goods_code[]" value="<?=$codeVal['self_goods_code']?>" id="opt_goods_code_<?php echo $codeKey; ?>" class="frm_input" size="50">
			</td>
			<td class="td_mng">
				<label for="opt_use_<?=$codeKey?>" class="sound_only"></label>
				<select name="opt_use[]" id="opt_use_<?=$codeKey?>">
					<option value="1" <?php echo get_selected('1', $codeVal['option_use']); ?>>사용함</option>
					<option value="0" <?php echo get_selected('0', $codeVal['option_use']); ?>>사용안함</option>
				</select>
			</td>
		</tr>
<?php
	} // $codeArr foreach
?>
	</tbody>
    </table>
</div>


<div class="btn_list01 btn_list">
    <input type="button" value="선택삭제" id="sel_option_delete">
</div>

<fieldset>
    <legend>옵션 일괄 적용</legend>
    <?php echo help('전체 옵션의 추가금액, 재고/통보수량 및 사용여부를 일괄 적용할 수 있습니다. 단, 체크된 수정항목만 일괄 적용됩니다.'); ?>
    <label for="opt_com_price">추가금액</label>
    <label for="opt_com_price_chk" class="sound_only">추가금액일괄수정</label><input type="checkbox" name="opt_com_price_chk" value="1" id="opt_com_price_chk" class="opt_com_chk">
    <input type="text" name="opt_com_price" value="0" id="opt_com_price" class="frm_input" size="5">
    <label for="opt_com_stock">재고수량</label>
    <label for="opt_com_stock_chk" class="sound_only">재고수량일괄수정</label><input type="checkbox" name="opt_com_stock_chk" value="1" id="opt_com_stock_chk" class="opt_com_chk">
    <input type="text" name="opt_com_stock" value="0" id="opt_com_stock" class="frm_input" size="5">
    <label for="opt_com_noti">통보수량</label>
    <label for="opt_com_noti_chk" class="sound_only">통보수량일괄수정</label><input type="checkbox" name="opt_com_noti_chk" value="1" id="opt_com_noti_chk" class="opt_com_chk">
    <input type="text" name="opt_com_noti" value="0" id="opt_com_noti" class="frm_input" size="5">
    <label for="opt_com_use">사용여부</label>
    <label for="opt_com_use_chk" class="sound_only">사용여부일괄수정</label><input type="checkbox" name="opt_com_use_chk" value="1" id="opt_com_use_chk" class="opt_com_chk">
    <select name="opt_com_use" id="opt_com_use">
        <option value="1">사용함</option>
        <option value="0">사용안함</option>
    </select>
    <button type="button" id="opt_value_apply" class="btn_frmline">일괄적용</button>
</fieldset>

<?php
}
?>
