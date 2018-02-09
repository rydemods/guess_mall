<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

@set_time_limit(0);
//$del_sql = "DELETE FROM tblmultiimages ";
//pmysql_query($del_sql,get_db_conn());

$imagepath = "http://img.shinwonmall.com/images/";
 
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

$cnt = 0;
$sql = "SELECT productcode, substr(tag_style_no,1,2) tag_prodcd_f, substr(tag_style_no,1,9) tag_prodcd, colorcode  
			FROM tblproduct WHERE maximage IS NULL AND join_yn != 'Y' ORDER BY date ASC
		"; // LIMIT 100
$result = pmysql_query($sql, get_db_conn());
echo $sql."\r\n";
while($row = pmysql_fetch_array($result)) {
	$productcode	= $row['productcode'];
	$tag_prodcd_f	= $row['tag_prodcd_f'];
	$tag_prodcd	= $row['tag_prodcd'];
	$colorcode		= $row['colorcode'];
	$remot_file_path		= $imagepath."{$tag_prodcd_f}/{$tag_prodcd}/{$tag_prodcd}";
	$remot_file_path2	= $imagepath."{$tag_prodcd_f}/{$tag_prodcd}/{$tag_prodcd}_{$colorcode}";
	$remot_file_path3	= $imagepath."{$tag_prodcd_f}/{$tag_prodcd}/{$tag_prodcd}_COM";

	$remot_file_img_b	= $remot_file_path2.'_B.jpg';
	if(remoteFileExist($remot_file_img_b) == 1) {
	$remot_file_arr	 = array(
									'max' => array(
													'img' => $remot_file_path2.'_B.jpg',
													'size' => '900X900'
												),
									'min' => array(
													'img' => $remot_file_path2.'_M.jpg',
													'size' => '600X600'
												),
									'tiny' => array(
													'img' => $remot_file_path2.'_S.jpg',
													'size' => '600X600'
												),
									'primg1' => array(
													'img' => $remot_file_path2.'_2.jpg',
													'size' => '900X900'
												),
									'primg2' => array(
													'img' => $remot_file_path2.'_3.jpg',
													'size' => '900X900'
												),
									'primg3' => array(
													'img' => $remot_file_path2.'_4.jpg',
													'size' => '900X900'
												),
									'primg4' => array(
													'img' => $remot_file_path2.'_5.jpg',
													'size' => '900X900'
												),
									'primg5' => array(
													'img' => $remot_file_path2.'_6.jpg',
													'size' => '900X900'
												),
									'primg6' => array(
													'img' => $remot_file_path2.'_7.jpg',
													'size' => '900X900'
												),
									'primg7' => array(
													'img' => $remot_file_path2.'_8.jpg',
													'size' => '900X900'
												),
									'primg8' => array(
													'img' => $remot_file_path2.'_9.jpg',
													'size' => '900X900'
												),
									'primg9' => array(
													'img' => $remot_file_path2.'_10.jpg',
													'size' => '900X900'
												),
									'primg10' => array(
													'img' => $remot_file_path2.'_11.jpg',
													'size' => '900X900'
												),
									'detail' => array(
													'img' => $remot_file_path.'_02.jpg',
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

				echo "원격지에 {$k}파일({$remot_file_img})이 존재합니다.\r\n";
				$primg[$k]		= $remot_file_img;
				if ($k != 'max' && $k != 'min' && $k != 'tiny' && $k != 'detail') {
					$primg_size	.= $primg_size?"".$remot_file_size:$remot_file_size;
				}
			} else {
				echo "원격지에 {$k}파일({$remot_file_img})이 존재 하지 않습니다.\r\n";
				if ($k != 'max' && $k != 'min' && $k != 'tiny' && $k != 'detail') {
					$primg_size	.= $primg_size?"":"";
				}
			}
		}

		$content	= $primg['detail']?"<img src=\"".$primg['detail']."\" title=\"상세정보\">":"";
		$up_sql = "UPDATE tblproduct SET 
						display = 'Y', 
						maximage='".$primg['max']."',
						minimage='".$primg['min']."',
						tinyimage='".$primg['tiny']."',
						content='".$content	."'
						";
		$up_sql.= "WHERE productcode='{$productcode}'";
		echo "sql=>".$up_sql."\r\n";
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
		echo "sql=>".$mi_sql."\r\n";
		pmysql_query($mi_sql,get_db_conn());

	} else {

		$remot_file_img_b	= $remot_file_path3.'_B.jpg';
		if(remoteFileExist($remot_file_img_b) == 1) {
		$remot_file_arr	 = array(
										'max' => array(
														'img' => $remot_file_path3.'_B.jpg',
														'size' => '900X900'
													),
										'min' => array(
														'img' => $remot_file_path3.'_M.jpg',
														'size' => '600X600'
													),
										'tiny' => array(
														'img' => $remot_file_path3.'_S.jpg',
														'size' => '600X600'
													),
										'primg1' => array(
														'img' => $remot_file_path3.'_2.jpg',
														'size' => '900X900'
													),
										'primg2' => array(
														'img' => $remot_file_path3.'_3.jpg',
														'size' => '900X900'
													),
										'primg3' => array(
														'img' => $remot_file_path3.'_4.jpg',
														'size' => '900X900'
													),
										'primg4' => array(
														'img' => $remot_file_path3.'_5.jpg',
														'size' => '900X900'
													),
										'primg5' => array(
														'img' => $remot_file_path3.'_6.jpg',
														'size' => '900X900'
													),
										'primg6' => array(
														'img' => $remot_file_path3.'_7.jpg',
														'size' => '900X900'
													),
										'primg7' => array(
														'img' => $remot_file_path3.'_8.jpg',
														'size' => '900X900'
													),
										'primg8' => array(
														'img' => $remot_file_path3.'_9.jpg',
														'size' => '900X900'
													),
										'primg9' => array(
														'img' => $remot_file_path3.'_10.jpg',
														'size' => '900X900'
													),
										'primg10' => array(
														'img' => $remot_file_path3.'_11.jpg',
														'size' => '900X900'
													),
										'detail' => array(
														'img' => $remot_file_path.'_02.jpg',
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

					echo "원격지에 {$k}파일({$remot_file_img})이 존재합니다.\r\n";
					$primg[$k]		= $remot_file_img;
					if ($k != 'max' && $k != 'min' && $k != 'tiny' && $k != 'detail') {
						$primg_size	.= $primg_size?"".$remot_file_size:$remot_file_size;
					}
				} else {
					echo "원격지에 {$k}파일({$remot_file_img})이 존재 하지 않습니다.\r\n";
					if ($k != 'max' && $k != 'min' && $k != 'tiny' && $k != 'detail') {
						$primg_size	.= $primg_size?"":"";
					}
				}
			}

			$content	= $primg['detail']?"<img src=\"".$primg['detail']."\" title=\"상세정보\">":"";
			$up_sql = "UPDATE tblproduct SET 
							display = 'Y', 
							maximage='".$primg['max']."',
							minimage='".$primg['min']."',
							tinyimage='".$primg['tiny']."',
							content='".$content	."'
							";
			$up_sql.= "WHERE productcode='{$productcode}'";
			echo "sql=>".$up_sql."\r\n";
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
			echo "sql=>".$mi_sql."\r\n";
			pmysql_query($mi_sql,get_db_conn());

		} else {
			echo "원격지에 기본파일({$remot_file_img_b})이 존재 하지 않습니다.\r\n";
			$up_sql = "UPDATE tblproduct SET display = 'N' WHERE productcode='{$productcode}'";
			echo "sql=>".$up_sql."\r\n";
			pmysql_query($up_sql,get_db_conn());
		}
	}
	
    $cnt++;
	echo "cnt = ".$cnt."\r\n";
    if( ($cnt%1000) == 0) {
		sleep(5);
	}
}
exdebug('end');
?>