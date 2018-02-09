<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/jungbo_code.php"); //정보고시 코드를 가져온다

// 파일 존재유무체크
function remoteFileExist($filepath) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$filepath);
	curl_setopt($ch, CURLOPT_NOBODY, 1);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if(curl_exec($ch)!==false) {
		return true;
	} else {
		return false;
	}
}

// ERP 상품 상품명 구하기
function getErpProdName($prodcd, $colorcd, $season_year, $season, $db_conn='') {
	if (!$db_conn) {
		$conn = GetErpDBConn();
	} else {
		$conn = $db_conn;
	}
/* 2017년12월13일 ERP 상품 테이블 변경으로 주석 처리
	$sql = "select MAX(NVL(PROD_NAME,'')) PRODNM
                from ( SELECT * FROM TA_OM001
				WHERE STYLE_NO = '".$prodcd."'        -- 품번 8자리 필수 
			    AND SEASON_YEAR = '".$season_year."'          -- 시즌년도 필수 
			    AND COLOR_CODE = '".$colorcd."'            --옵션 
				AND SEASON = '".$season."' 
				ORDER BY SEQ DESC ) where rownum = 1 
            ";
*/

    $sql = "SELECT 
				MAX(NVL(PROD_NAME,'')) PRODNM
				FROM TA_OM001
				WHERE STYLE_NO = '".$prodcd."'        -- 품번 8자리 필수 
			    AND SEASON_YEAR = '".$season_year."'          -- 시즌년도 필수 
			    AND COLOR_CODE = '".$colorcd."'            --옵션 
				AND SEASON = '".$season."' 
				GROUP BY STYLE_NO, SEASON_YEAR, SEASON, COLOR_CODE
			  ORDER BY STYLE_NO, SEASON_YEAR, SEASON, COLOR_CODE
            ";
			//echo $sql;
    $smt_opt = oci_parse($conn, $sql);
    oci_execute($smt_opt);
    //echo $sql."\r\n";

    $size_opt = array();
    //$ssizeopt = array();
    while($data = oci_fetch_array($smt_opt, OCI_BOTH+OCI_RETURN_NULLS+OCI_RETURN_LOBS)) {

		foreach($data as $k => $v)
		{
			$data[$k] = utf8encode($v);
		}

        $prodnm         = trim($data[PRODNM]);
    }
    oci_free_statement($smt_opt);
    if (!$db_conn) GetErpDBClose($conn);

    return $prodnm;
}

$type		= $_REQUEST['type'];
$prlist	= $_REQUEST['productcodelist'];

if ($type	=='name') {
	$conn = GetErpDBConn();
	$sql = "SELECT productcode, prodcode, colorcode, season_year , season 
				FROM tblproduct WHERE join_yn != 'Y' AND productcode in ('".str_replace(",","','", $prlist)."') ORDER BY date ASC
			"; // LIMIT 100
			//echo $sql;
	$result = pmysql_query($sql, get_db_conn());
	while($row = pmysql_fetch_array($result)) {
		$productcode		= $row['productcode']; 
		$prodcd				= $row['prodcode']; 
		$colorcd			= $row['colorcode'];
		$season_year		= $row['season_year'];
		$season			= $row['season'];

		$productname	= getErpProdName($prodcd, $colorcd, $season_year, $season, $conn);

		$sql = "update  tblproduct 
					set productname = '".$productname."' 
					where	productcode = '".$productcode."' 
				";
		//exdebug($sql);
		pmysql_query($sql, get_db_conn());
	}
	oci_free_statement($smt);
	oci_close($conn);

	pmysql_free_result($result);

	$res_code	= '0';
	$res_msg	= '상품명 업데이트가 완료되었습니다.';

} else if ($type == 'img') {
	$imagepath = "http://img.shinwonmall.com/images/";

	$cnt = 0;
	$sql = "SELECT productcode, substr(tag_style_no,1,2) tag_prodcd_f, substr(tag_style_no,1,9) tag_prodcd, colorcode  
				FROM tblproduct WHERE join_yn != 'Y' AND productcode in ('".str_replace(",","','", $prlist)."') ORDER BY date ASC
			"; // LIMIT 100
	$result = pmysql_query($sql, get_db_conn());
	while($row = pmysql_fetch_array($result)) {
		$productcode	= $row['productcode'];
		$tag_prodcd_f	= $row['tag_prodcd_f'];
		$tag_prodcd	= $row['tag_prodcd'];
		$colorcode		= $row['colorcode'];
		$remot_file_path		= $imagepath."{$tag_prodcd_f}/{$tag_prodcd}/{$tag_prodcd}";
		$remot_file_path2	= $imagepath."{$tag_prodcd_f}/{$tag_prodcd}/{$tag_prodcd}_{$colorcode}";
		$remot_file_path3	= $imagepath."{$tag_prodcd_f}/{$tag_prodcd}/{$tag_prodcd}_COM";

		$remot_file_img_B	= $remot_file_path2.'_B.jpg';
		$remot_file_img_B_C	= $remot_file_path2.'_B.JPG';
		$remot_file_img3_B	= $remot_file_path3.'_B.jpg';
		$remot_file_img3_B_C	= $remot_file_path3.'_B.JPG';
		$remot_file_img_EB	= $remot_file_path2.'_AAA.jpg';
		$remot_file_img_EB_C	= $remot_file_path2.'_AAA.JPG';

		$remot_file_img_M	= $remot_file_path2.'_M.jpg';
		$remot_file_img_M_C	= $remot_file_path2.'_M.JPG';
		$remot_file_img3_M	= $remot_file_path3.'_M.jpg';
		$remot_file_img3_M_C	= $remot_file_path3.'_M.JPG';
		$remot_file_img_EM	= $remot_file_path2.'_AA.jpg';
		$remot_file_img_EM_C	= $remot_file_path2.'_AA.JPG';

		$remot_file_img_S	= $remot_file_path2.'_S.jpg';
		$remot_file_img_S_C	= $remot_file_path2.'_S.JPG';
		$remot_file_img3_S	= $remot_file_path3.'_S.jpg';
		$remot_file_img3_S_C	= $remot_file_path3.'_S.JPG';
		$remot_file_img_ES	= $remot_file_path2.'_A.jpg';
		$remot_file_img_ES_C	= $remot_file_path2.'_A.JPG';
		
		$remot_file_img_2	= $remot_file_path2.'_2.jpg';
		$remot_file_img_2_C	= $remot_file_path2.'_2.JPG';
		$remot_file_img3_2	= $remot_file_path3.'_2.jpg';
		$remot_file_img3_2_C	= $remot_file_path3.'_2.JPG';

		$remot_file_img_3	= $remot_file_path2.'_3.jpg';
		$remot_file_img_3_C	= $remot_file_path2.'_3.JPG';
		$remot_file_img3_3	= $remot_file_path3.'_3.jpg';
		$remot_file_img3_3_C	= $remot_file_path3.'_3.JPG';

		$remot_file_img_4	= $remot_file_path2.'_4.jpg';
		$remot_file_img_4_C	= $remot_file_path2.'_4.JPG';
		$remot_file_img3_4	= $remot_file_path3.'_4.jpg';
		$remot_file_img3_4_C	= $remot_file_path3.'_4.JPG';

		$remot_file_img_5	= $remot_file_path2.'_5.jpg';
		$remot_file_img_5_C	= $remot_file_path2.'_5.JPG';
		$remot_file_img3_5	= $remot_file_path3.'_5.jpg';
		$remot_file_img3_5_C	= $remot_file_path3.'_5.JPG';

		$remot_file_img_6	= $remot_file_path2.'_6.jpg';
		$remot_file_img_6_C	= $remot_file_path2.'_6.JPG';
		$remot_file_img_6	= $remot_file_path3.'_6.jpg';
		$remot_file_img_6_C	= $remot_file_path3.'_6.JPG';

		$remot_file_img_7	= $remot_file_path2.'_7.jpg';
		$remot_file_img_7_C	= $remot_file_path2.'_7.JPG';
		$remot_file_img3_7	= $remot_file_path3.'_7.jpg';
		$remot_file_img3_7_C	= $remot_file_path3.'_7.JPG';

		$remot_file_img_8	= $remot_file_path2.'_8.jpg';
		$remot_file_img_8_C	= $remot_file_path2.'_8.JPG';
		$remot_file_img3_8	= $remot_file_path3.'_8.jpg';
		$remot_file_img3_8_C	= $remot_file_path3.'_8.JPG';

		$remot_file_img_9	= $remot_file_path2.'_9.jpg';
		$remot_file_img_9_C	= $remot_file_path2.'_9.JPG';
		$remot_file_img3_9	= $remot_file_path3.'_9.jpg';
		$remot_file_img3_9_C	= $remot_file_path3.'_9.JPG';

		$remot_file_img_10	= $remot_file_path2.'_10.jpg';
		$remot_file_img_10_C	= $remot_file_path2.'_10.JPG';
		$remot_file_img3_10	= $remot_file_path3.'_10.jpg';
		$remot_file_img3_10_C	= $remot_file_path3.'_10.JPG';

		$remot_file_img_11	= $remot_file_path2.'_11.jpg';
		$remot_file_img_11_C	= $remot_file_path2.'_11.JPG';
		$remot_file_img3_11	= $remot_file_path3.'_11.jpg';
		$remot_file_img3_11_C	= $remot_file_path3.'_11.JPG';
		
		$remot_file_img_02	= $remot_file_path.'_02.jpg';
		$remot_file_img_02_C	= $remot_file_path.'_02.JPG';
		$remot_file_path_B = '';
		$remot_file_path_M = '';
		$remot_file_path_S = '';
		$remot_file_path_2 = '';
		$remot_file_path_3 = '';
		$remot_file_path_4 = '';
		$remot_file_path_5 = '';
		$remot_file_path_6 = '';
		$remot_file_path_7 = '';
		$remot_file_path_8 = '';
		$remot_file_path_9 = '';
		$remot_file_path_10 = '';
		$remot_file_path_11 = '';
		$remot_file_path_detail = '';

		if(remoteFileExist($remot_file_img_B) == 1) {
			if(remoteFileExist($remot_file_img_EB) == 1) {
				$remot_file_path_B = $remot_file_img_EB;
			}else{
				$remot_file_path_B = $remot_file_img_B;
			}
		}else if(remoteFileExist($remot_file_img_B_C) == 1){
			if(remoteFileExist($remot_file_img_EB) == 1) {
				$remot_file_path_B = $remot_file_img_EB_C;
			}else{
				$remot_file_path_B = $remot_file_img_B_C;
			}
		}else if(remoteFileExist($remot_file_img3_B) == 1){
			$remot_file_path_B = $remot_file_img3_B;
		}else if(remoteFileExist($remot_file_img3_B_C) == 1){
			$remot_file_path_B = $remot_file_img3_B_C;
		}

		if(remoteFileExist($remot_file_img_M) == 1) {
			if(remoteFileExist($remot_file_img_EM) == 1) {
				$remot_file_path_M = $remot_file_img_EM;
			}else{
				$remot_file_path_M = $remot_file_img_M;
			}
		}else if(remoteFileExist($remot_file_img_M_C) == 1) {
			if(remoteFileExist($remot_file_img_EM_C) == 1) {
				$remot_file_path_M = $remot_file_img_EM_C;
			}else{
				$remot_file_path_M = $remot_file_img_M_C;
			}
		}else if(remoteFileExist($remot_file_img3_M) == 1) {
			$remot_file_path_M = $remot_file_img3_M;
		}else if(remoteFileExist($remot_file_img3_M_C) == 1) {
			$remot_file_path_M = $remot_file_img3_M_C;
		}

		if(remoteFileExist($remot_file_img_S) == 1) {
			if(remoteFileExist($remot_file_img_ES) == 1) {
				$remot_file_path_S = $remot_file_img_ES;
			}else{
				$remot_file_path_S = $remot_file_img_S;
			}
		}else if(remoteFileExist($remot_file_img_S_C) == 1) {
			if(remoteFileExist($remot_file_img_ES_C) == 1) {
				$remot_file_path_S = $remot_file_img_ES_C;
			}else{
				$remot_file_path_S = $remot_file_img_S_C;
			}
		}else if(remoteFileExist($remot_file_img3_S) == 1) {
			$remot_file_path_S = $remot_file_img3_S;
		}else if(remoteFileExist($remot_file_img3_S_C) == 1) {
			$remot_file_path_S = $remot_file_img3_S_C;
		}

		if(remoteFileExist($remot_file_img_2) == 1) {
			$remot_file_path_2 = $remot_file_img_2;
		}else if(remoteFileExist($remot_file_img_2_C) == 1) {
			$remot_file_path_2 = $remot_file_img_2_C;
		}else if(remoteFileExist($remot_file_img3_2) == 1) {
			$remot_file_path_2 = $remot_file_img3_2;
		}else if(remoteFileExist($remot_file_img3_2_C) == 1) {
			$remot_file_path_2 = $remot_file_img3_2_C;
		}
		if(remoteFileExist($remot_file_img_3) == 1) {
			$remot_file_path_3 = $remot_file_img_3;
		}else if(remoteFileExist($remot_file_img_3_C) == 1) {
			$remot_file_path_3 = $remot_file_img_3_C;
		}else if(remoteFileExist($remot_file_img3_3) == 1) {
			$remot_file_path_3 = $remot_file_img3_3;
		}else if(remoteFileExist($remot_file_img3_3_C) == 1) {
			$remot_file_path_3 = $remot_file_img3_3_C;
		}
		if(remoteFileExist($remot_file_img_4) == 1) {
			$remot_file_path_4 = $remot_file_img_4;
		}else if(remoteFileExist($remot_file_img_4_C) == 1) {
			$remot_file_path_4 = $remot_file_img_4_C;
		}else if(remoteFileExist($remot_file_img3_4) == 1) {
			$remot_file_path_4 = $remot_file_img3_4;
		}else if(remoteFileExist($remot_file_img3_4_C) == 1) {
			$remot_file_path_4 = $remot_file_img3_4_C;
		}
		if(remoteFileExist($remot_file_img_5) == 1) {
			$remot_file_path_5 = $remot_file_img_5;
		}else if(remoteFileExist($remot_file_img_5_C) == 1) {
			$remot_file_path_5 = $remot_file_img_5_C;
		}else if(remoteFileExist($remot_file_img3_5) == 1) {
			$remot_file_path_5 = $remot_file_img3_5;
		}else if(remoteFileExist($remot_file_img3_5_C) == 1) {
			$remot_file_path_5 = $remot_file_img3_5_C;
		}
		if(remoteFileExist($remot_file_img_6) == 1) {
			$remot_file_path_6 = $remot_file_img_6;
		}else if(remoteFileExist($remot_file_img_6_C) == 1) {
			$remot_file_path_6 = $remot_file_img_6_C;
		}else if(remoteFileExist($remot_file_img3_6) == 1) {
			$remot_file_path_6 = $remot_file_img3_6;
		}else if(remoteFileExist($remot_file_img3_6_C) == 1) {
			$remot_file_path_6 = $remot_file_img3_6_C;
		}
		if(remoteFileExist($remot_file_img_7) == 1) {
			$remot_file_path_7 = $remot_file_img_7;
		}else if(remoteFileExist($remot_file_img_7_C) == 1) {
			$remot_file_path_7 = $remot_file_img_7_C;
		}else if(remoteFileExist($remot_file_img3_7) == 1) {
			$remot_file_path_7 = $remot_file_img3_7;
		}else if(remoteFileExist($remot_file_img3_7_C) == 1) {
			$remot_file_path_7 = $remot_file_img3_7_C;
		}
		if(remoteFileExist($remot_file_img_8) == 1) {
			$remot_file_path_8 = $remot_file_img_8;
		}else if(remoteFileExist($remot_file_img_8_C) == 1) {
			$remot_file_path_8 = $remot_file_img_8_C;
		}else if(remoteFileExist($remot_file_img3_8) == 1) {
			$remot_file_path_8 = $remot_file_img3_8;
		}else if(remoteFileExist($remot_file_img3_8_C) == 1) {
			$remot_file_path_8 = $remot_file_img3_8_C;
		}
		if(remoteFileExist($remot_file_img_9) == 1) {
			$remot_file_path_9 = $remot_file_img_9;
		}else if(remoteFileExist($remot_file_img_9_C) == 1) {
			$remot_file_path_9 = $remot_file_img_9_C;
		}else if(remoteFileExist($remot_file_img3_9) == 1) {
			$remot_file_path_9 = $remot_file_img3_9;
		}else if(remoteFileExist($remot_file_img3_9_C) == 1) {
			$remot_file_path_9 = $remot_file_img3_9_C;
		}
		if(remoteFileExist($remot_file_img_10) == 1) {
			$remot_file_path_10 = $remot_file_img_10;
		}else if(remoteFileExist($remot_file_img_10_C) == 1) {
			$remot_file_path_10 = $remot_file_img_10_C;
		}else if(remoteFileExist($remot_file_img3_10) == 1) {
			$remot_file_path_10 = $remot_file_img3_10;
		}else if(remoteFileExist($remot_file_img3_10_C) == 1) {
			$remot_file_path_10 = $remot_file_img3_10_C;
		}
		if(remoteFileExist($remot_file_img_11) == 1) {
			$remot_file_path_11 = $remot_file_img_11;
		}else if(remoteFileExist($remot_file_img_11_C) == 1) {
			$remot_file_path_11 = $remot_file_img_11_C;
		}else if(remoteFileExist($remot_file_img3_11) == 1) {
			$remot_file_path_11 = $remot_file_img3_11;
		}else if(remoteFileExist($remot_file_img3_11_C) == 1) {
			$remot_file_path_11 = $remot_file_img3_11_C;
		}
		if(remoteFileExist($remot_file_img_02) == 1) {
			$remot_file_path_detail = $remot_file_img_02;
		}else{
			$remot_file_path_detail = $remot_file_img_02_C;
		}

//		if(remoteFileExist($remot_file_img_B) == 1 || remoteFileExist($remot_file_img_B_C) == 1 || remoteFileExist($remot_file_img3_B) == 1 || remoteFileExist($remot_file_img3_B_C) == 1) {
		if(remoteFileExist($remot_file_img_B) == 1 || remoteFileExist($remot_file_img_B_C) == 1) {
		$remot_file_arr	 = array(
										'max' => array(
														'img' => $remot_file_path_B,
														'size' => '900X900'
													),
										'min' => array(
														'img' => $remot_file_path_M,
														'size' => '600X600'
													),
										'tiny' => array(
														'img' => $remot_file_path_S,
														'size' => '600X600'
													),
										'primg1' => array(
														'img' => $remot_file_path_2,
														'size' => '900X900'
													),
										'primg2' => array(
														'img' => $remot_file_path_3,
														'size' => '900X900'
													),
										'primg3' => array(
														'img' => $remot_file_path_4,
														'size' => '900X900'
													),
										'primg4' => array(
														'img' => $remot_file_path_5,
														'size' => '900X900'
													),
										'primg5' => array(
														'img' => $remot_file_path_6,
														'size' => '900X900'
													),
										'primg6' => array(
														'img' => $remot_file_path_7,
														'size' => '900X900'
													),
										'primg7' => array(
														'img' => $remot_file_path_8,
														'size' => '900X900'
													),
										'primg8' => array(
														'img' => $remot_file_path_9,
														'size' => '900X900'
													),
										'primg9' => array(
														'img' => $remot_file_path_10,
														'size' => '900X900'
													),
										'primg10' => array(
														'img' => $remot_file_path_11,
														'size' => '900X900'
													),
										'detail' => array(
														'img' => $remot_file_path_detail,
														'size' => ''
													)
									);
			$primg = array();
			$primg_size = "";

			foreach($remot_file_arr as $k => $v) {
				$remot_file_img = $v['img'];
				$remot_file_size = $v['size'];
				//exdebug($remot_file_img);
				if(remoteFileExist($remot_file_img) == 1) {
					$primg[$k]		= $remot_file_img;
					if ($k != 'max' && $k != 'min' && $k != 'tiny' && $k != 'detail') {
						$primg_size	.= $primg_size?"".$remot_file_size:$remot_file_size;
					}
				} else {
					if ($k != 'max' && $k != 'min' && $k != 'tiny' && $k != 'detail') {
						$primg_size	.= $primg_size?"":"";
					}
				}
			}

			$content	= $primg['detail']?"<img src=\"".$primg['detail']."\" title=\"상세정보\">":"";
			$up_sql = "UPDATE tblproduct SET 
							maximage='".$primg['max']."',
							minimage='".$primg['min']."',
							tinyimage='".$primg['tiny']."',
							content='".$content	."'
							";
			$up_sql.= "WHERE productcode='{$productcode}'";
			pmysql_query($up_sql,get_db_conn());
			
			$mi_sql = "
				WITH upsert as (
					update  tblmultiimages 
					set 	
						primg01 = '".$primg['primg1']."',
						primg02 = '".$primg['primg2']."',
						primg03 = '".$primg['primg3']."',
						primg04 = '".$primg['primg4']."',
						primg05 = '".$primg['primg5']."',
						primg06 = '".$primg['primg6']."',
						primg07 = '".$primg['primg7']."',
						primg08 = '".$primg['primg8']."',
						primg09 = '".$primg['primg9']."',
						primg10 = '".$primg['primg10']."',
						size = '".$primg_size."'
					where	productcode = '".$productcode."' 
					RETURNING * 
				)
				INSERT INTO tblmultiimages(
						productcode,
						primg01,
						primg02,
						primg03,
						primg04,
						primg05,
						primg06,
						primg07,
						primg08,
						primg09,
						primg10,
						size
						) 
				Select  
						'{$productcode}',
						'".$primg['primg1']."',
						'".$primg['primg2']."',
						'".$primg['primg3']."',
						'".$primg['primg4']."',
						'".$primg['primg5']."',
						'".$primg['primg6']."',
						'".$primg['primg7']."',
						'".$primg['primg8']."',
						'".$primg['primg9']."',
						'".$primg['primg10']."',
						'".$primg_size."'
				WHERE NOT EXISTS ( select * from upsert ) ";
			pmysql_query($mi_sql,get_db_conn());

		}
	
	}
	$res_code	= '0';
	$res_msg	= '상품이미지 업데이트가 완료되었습니다.';
	//$res_msg	= $up_sql;
	
}
$tmpMsgArray = array("code"=>$res_code,"msg"=>$res_msg);
$msg = json_encode($tmpMsgArray);
echo $msg;
?>