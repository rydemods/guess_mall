<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");

	//setlocale(LC_CTYPE, 'ko_KR.UTF-8');
	//setlocale(LC_CTYPE, 'ko_KR.eucKR'); 

    $CurrentTime = time();
    header("Content-type: application/vnd.ms-excel");
    Header("Content-Disposition: attachment; filename=upload_result_".date("YmdHis").".xls"); 
    Header("Pragma: no-cache"); 
    Header("Expires: 0");
    Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
    Header("Content-Description: PHP4 Generated Data");

	echo '
		<html>
		<head>
		<title>list</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<style>.xl31{mso-number-format:"0_\)\;\\\(0\\\)";}</style>
		</head>
		<body>
		<table border="1">
		';
	print '<tr>';
	print '<td>No.</td>';
	print '<td>상품코드</td>';
	print '<td>PRODCD</td>';
	print '<td>COLORCD</td>';
	print '<td>상품입력</td>';
	//print '<td>옵션입력</td>';
	print '<td>등록여부</td>';
	print '<td>비고</td>';
	print '</tr>';


	####################### 페이지 접근권한 check ###############
	$PageCode = "pr-4";
	$MenuCode = "product";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}

	##
	## 구분자는 || => @#으로 치환
	## 구분자는 :: => chr(30)으로 치환 // 구분자 ||로 변경 2016-03-07  
	##


	if($_FILES['upfile_pd'][tmp_name]){
		$fp = fopen( $_FILES['upfile_pd'][tmp_name], 'r' );
		$fields = fgetcsv( $fp, 135000, ',' );
		$fields = fgetcsv( $fp, 135000, ',' );
		$fieldLen = count( $fields );
		$FieldNm = Array();

		for ( $i = 0; $i < $fieldLen; $i++ ){
			if ( $fields[$i] <> '' ){
				$FieldNm[$fields[$i]] = $i;
			}
		}

		// [assembleuse] => 삭제, [addcode] => 삭제, [supply_subject] => 삭제, [overseas_type] => 삭제, [card_benefit] => 삭제
		/*
			이미지는 미리 올려져있다는 가정하에 작업
		*/

		$MSG_RESULT = "";
		$MSG_SPLIT = "||||||";
		$arrOptType = $arrCheckQuantity = $arrInsertResultExcel = array();
		while ( $data = fgetcsv( $fp, 135000, ',' ) ){
			# 변수 생성 및 한글 처리
			foreach($FieldNm as $column => $idx){
				${$column} = mb_convert_encoding($data[$FieldNm[$column]], "UTF-8", "EUC-KR");
			}

            // category, prodcode, colorcode는 앞에 0일 올수도 있어서 ' 붙이기로 했음. 제거작업 추가하자.
			$code = str_replace("'", "", trim($category));
			$prodcode = str_replace("'", "", trim($prodcode));
			$colorcode = str_replace("'", "", trim($colorcode));
            list($in_productcode) = pmysql_fetch("Select productcode From tblproduct Where prodcode = '".$prodcode."' And colorcode = '".$colorcode."'");
			if(!$in_productcode) {
                $arrInsertResultExcel[$prodcode."-".$colorcode]['code'] = $prodcode."-".$colorcode;
                $arrInsertResultExcel[$prodcode."-".$colorcode]['prodcode'] = $prodcode;
                $arrInsertResultExcel[$prodcode."-".$colorcode]['colorcode'] = $colorcode;
                $arrInsertResultExcel[$prodcode."-".$colorcode]['flag'] = "F";
                continue;
            }

			$deli_qty = 0;
			$deli_select = 0;
			$deli_price = 0;
            $vendor_id = strtolower(trim($vender));
			//list($in_vender)=pmysql_fetch("SELECT vender FROM tblvenderinfo WHERE id='{$vendor_id}'");
            list($in_vender)=pmysql_fetch("SELECT vender FROM tblproductbrand where brandname = '".trim($vender)."'");
            $bridx = "";
			if ($in_vender) {
				list($bridx)=pmysql_fetch("SELECT bridx FROM tblproductbrand WHERE vender='{$in_vender}'");
                /*
				if ($bridx>0) {
					$bpSql = "INSERT INTO tblbrandproduct(productcode, bridx, sort) VALUES ('".$in_productcode."','".$bridx."','1')";
					pmysql_query($bpSql,get_db_conn());
				}
                */
			}

			# 업데이트인지 등록인치 상태값
			$isDbFlag = "upd";

			if(strlen($buyprice) < 1 ) $buyprice = 0 ;

			$date1=date("Ym");
			$date=date("dHis");

            // 기타이미지
			if($primg01){
                list($multi_cnt) = pmysql_fetch("Select count(*) as cnt From tblmultiimages where productcode = '".$in_productcode."'");

                if($primg01) {
                    if(strpos($primg01, "http://") !== true) $primg01 = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($primg01);
                }
                if($primg02) {
                    if(strpos($primg02, "http://") !== true) $primg02 = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($primg02);
                }
                if($primg03) {
                    if(strpos($primg03, "http://") !== true) $primg03 = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($primg03);
                }
                if($primg04) {
                    if(strpos($primg04, "http://") !== true) $primg04 = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($primg04);
                }
                if($primg05) {
                    if(strpos($primg05, "http://") !== true) $primg05 = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($primg05);
                }
                if($primg06) {
                    if(strpos($primg06, "http://") !== true) $primg06 = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($primg06);
                }
                if($primg07) {
                    if(strpos($primg07, "http://") !== true) $primg07 = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($primg07);
                }
                if($primg08) {
                    if(strpos($primg08, "http://") !== true) $primg08 = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($primg08);
                }
                if($primg09) {
                    if(strpos($primg09, "http://") !== true) $primg09 = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($primg09);
                }
                if($primg10) {
                    if(strpos($primg10, "http://") !== true) $primg10 = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($primg10);
                }

                if($multi_cnt > 0) {
                    $query="Update tblmultiimages Set 
                                primg01 = '".$primg01."', 
                                primg02 = '".$primg02."', 
                                primg03 = '".$primg03."', 
                                primg04 = '".$primg04."', 
                                primg05 = '".$primg05."', 
                                primg06 = '".$primg06."', 
                                primg07 = '".$primg07."', 
                                primg08 = '".$primg08."', 
                                primg09 = '".$primg09."', 
                                primg10 = '".$primg10."' 
                            Where productcode = '".$in_productcode."' 
                            ";
                } else {
                    $query="insert into tblmultiimages 
                            (productcode, primg01, primg02, primg03, primg04, primg05, primg06, primg07, primg08, primg09, primg10, size) 
                            values 
                            ('".$in_productcode."', '".$primg01."', '".$primg02."', '".$primg03."', '".$primg04."', '".$primg05."', '".$primg06."', '".$primg07."', '".$primg08."', '".$primg09."', '".$primg10."', '')
                            ";
                }
				pmysql_query($query);
                //exdebug($query);
			}

			## || 로 구분해서 여러개 입력
			$arrCode = explode("||", $code);
			foreach($arrCode as $vIndex => $vCode){

                list($code_cnt) = pmysql_fetch("Select count(*) as cnt From tblproductlink where c_productcode = '".$in_productcode."' and c_category = '".$vCode."'");
                if($code_cnt > 0) continue;

				$mainCate = 0;
				if($vIndex == 0) $mainCate = 1;
				$query="insert into tblproductlink 
                        (c_productcode,c_category,c_maincate,c_date,c_date_1,c_date_2,c_date_3,c_date_4) 
                        values 
                        ('".$in_productcode."','".$vCode."','".$mainCate."','".$date1.$date."','".$date1.$date."','".$date1.$date."','".$date1.$date."','".$date1.$date."')
                        ";
				pmysql_query($query);
                //exdebug($query);
			}

			$in_content = pg_escape_string($content);
			$keyword = pg_escape_string($keyword);
            $relation_tag = pg_escape_string($relation_tag);
			$mdcomment = pg_escape_string($mdcomment);
            $pr_content = pg_escape_string($pr_content);

            /*
            if(strpos($maximage, "http://") !== true) $maximage = $in_productcode."/".$maximage;
            if(strpos($minimage, "http://") !== true) $minimage = $in_productcode."/".$minimage;
            if(strpos($tinyimage, "http://") !== true) $tinyimage = $in_productcode."/".$tinyimage;
            if(strpos($over_minimage, "http://") !== true) $over_minimage = $in_productcode."/".$over_minimage;
            */
            if(strpos($maximage, "http://") !== true) $maximage = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($maximage);
            if(strpos($minimage, "http://") !== true) $minimage = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($minimage);
            if(strpos($tinyimage, "http://") !== true) $tinyimage = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($tinyimage);
            if(strpos($over_minimage, "http://") !== true) $over_minimage = trim($vender)."/".trim($prodcode)."-".trim($color_code)."/".trim($over_minimage);

            if($sex == "") $sex = "U";
            else $sex = trim($sex);

            $sabangnet_prop_val = "025||".trim($jungbo1)."||".trim($jungbo2)."||".trim($jungbo3)."||".trim($jungbo4)."||".trim($jungbo5)."||".trim($jungbo6)."||".trim($jungbo7);
            $sabangnet_prop_option = "025||소재||제조자||원산지||수입자||A/S 담당자 연락처||품질보증기준||취급시주의사항";
            /*
            echo "category = ".$code."<br>";
            echo "prodcode= ".$prodcode."<br>";
            echo "colorcode = ".$colorcode."<br>";
            echo "in_productcode = ".$in_productcode."<br>";
            echo "in_vender = ".$in_vender."<br>";
            echo "bridx = ".$bridx."<br>";
            echo "maximage = ".$maximage."<br>";
            echo "minimage = ".$minimage."<br>";
            echo "tinyimage = ".$tinyimage."<br>";
            echo "over_minimage = ".$over_minimage."<br>";
            echo "sex = ".$sex."<br>";
            echo "color_code = ".$color_code."<br>";
            //exit;
            */
			$sql = "Update tblproduct Set 
						sellprice       = {$sellprice}, 
						buyprice        = {$buyprice}, 
						reserve         = '{$reserve}', 
						reservetype     = '{$reservetype}',
						keyword         = '{$keyword}', 
						relation_tag    = '{$relation_tag}',
						maximage        = '".$maximage."', 
						minimage        = '".$minimage."', 
						tinyimage       = '".$tinyimage."',
						over_minimage   = '".$over_minimage."', 
						deli_price      = {$deli_price}, 
						display         = 'N', 
						modifydate      = now(), 
						content         = '{$in_content}',
						color_code      = '".trim($color_code)."', 
						vender          = '{$in_vender}', 
						brand           = '{$bridx}', 
                        sabangnet_prop_val = '{$sabangnet_prop_val}', 
                        sabangnet_prop_option = '{$sabangnet_prop_option}', 
                        pr_content         = '{$pr_content}' 
                    Where   productcode = '".$in_productcode."' 
					";
            //exdebug($sql);
            
			if($insert = pmysql_query($sql,get_db_conn())) {
				$arrInsertResultExcel[$in_productcode]['flag'] = "T";
			}else{
				$arrInsertResultExcel[$in_productcode]['flag'] = "F";
			}
            
			$arrInsertResultExcel[$in_productcode]['code'] = $in_productcode;
            $arrInsertResultExcel[$in_productcode]['prodcode'] = $prodcode;
            $arrInsertResultExcel[$in_productcode]['colorcode'] = $colorcode;

			#debug($sql);
			//debug($data[$FieldNm['productcode']]."////".mb_convert_encoding($data[$FieldNm['productname']], "UTF-8", "EUC-KR"));
		}

		fclose($fp);
	}





	if($arrInsertResultExcel){
		$excelCount = 1;
		foreach($arrInsertResultExcel as $code => $value){
			if($code){
				print '<tr>';
				print '<td>'.$excelCount.'</td>';
				print '<td>\''.$code.'</td>';
				print '<td>\''.$value['prodcode'].'</td>';
				print '<td>\''.$value['colorcode'].'</td>';

				if($value['flag'] == "T"){
					print '<td>성공</td>';
				}else if($value['flag'] == "F"){
					print "<td>실패</td>";
				}else{
					print '<td></td>';
				}
                /*
				if($value['flag_o'] == "T"){
					print '<td>성공</td>';
				}else if($value['flag_o'] == "F" || $value['flag_o'] == "X"){
					print '<td>실패</td>';
				}else{
					print '<td></td>';
				}
                */
				if(($value['flag'] == "T" && $value['flag_o'] == "T") || ($value['flag'] == "T" && !$value['flag_o']) || ( !$value['flag'] && $value['flag_o'] == "T" )){
					print '<td>등록성공</td>';
				}else{
					if($value['msg']){
						print '<td>등록실패 ['.$value['msg'].']</td>';
					}else if($value['msg_o']){
						print '<td>등록실패 ['.$value['msg_o'].']</td>';
					}else{
						print '<td>등록실패</td>';
					}
				}
				
				if($value['memo']){
					print '<td>'.$value['memo'].'</td>';
				}else{
					print '<td></td>';
				}

				print '</tr>';

				$excelCount++;
			}
		}
	}else{
		print '<tr>';
		print '<td colspan = "7">데이터가 없습니다.</td>';
		print '</tr>';
	}
/*
	print '<tr>';
	print '<td>No.</td>';
	print '<td>상품코드</td>';
	print '<td>처리결과</td>';
	print '<td>상품등록</td>';
	print '<td>상품옵션등록</td>';
	print '</tr>';

	print '<tr><td>line ' . $row . ': </td>';
	print '<td>상품번호</td><td>' . $Recode['goodsno'] . '</td>';
	print '<td>처리결과</td><td>' . ( $getScnt == 0 ? 'INSERT' : 'UPDATE' ) . '</td>';
	print '<td>' . $Recode['goodscd'] . '</td>';

	print '<td>' . ( $result1 ? 'T' : 'F' ) . '</td>';
	print '<td>' . ( $result2 ? 'T' : 'F' ) . '</td>';
	print '<td>' . ( $result3 ? 'T' : 'F' ) . '</td>';
	print '<td>' . ( $result4 ? 'T' : 'F' ) . '</td>';
	print '</tr>';

*/

	echo '
		</table>
		</body>
		</html>
		';
	exit;
?>