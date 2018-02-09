<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}

$imagepath=$Dir.DataDir."shopimages/multi/";

if($type=="codedel") {				//분류삭제
	if(strlen($code)==12) {
		$code_a=substr($code,0,3);
		$code_b=substr($code,3,3);
		$code_c=substr($code,6,3);
		$code_d=substr($code,9,3);

		$likecode=$code_a;
		if($code_b!="000") {
			$likecode.=$code_b;
			if($code_c!="000") {
				$likecode.=$code_c;
				if($code_d!="000") {
					$likecode.=$code_d;
				}
			}
		}
		$sql = "DELETE FROM tblmultiimages WHERE productcode LIKE '".$likecode."%' ";
		pmysql_query($sql,get_db_conn());
		/* 20150408 카테고리는 상품과 관련되지 않음
		if(!pmysql_errno()) {
			proc_matchfiledel($imagepath."*_".$likecode."*");
		}
		*/
	}
} else if($type=="prdelete") {		//상품삭제
	if(strlen($productcode)==18) {
		$sql = "DELETE FROM tblmultiimages WHERE productcode='".$productcode."' ";
		pmysql_query($sql,get_db_conn());
		if(!pmysql_errno()) {
			proc_matchfiledel($imagepath."*_".$productcode."*");
		}
	}
} else if($type=="copy" || $type=="move") {			//상품복사 / 상품이동
	$fromprlist=explode("|",$code);
	$copyprlist=explode("|",$productcode);

	for($i=0;$i<count($fromprlist);$i++) {
		$sql = "SELECT * FROM tblmultiimages WHERE productcode='".$fromprlist[$i]."' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
		
			$plusfiles=array($row->primg01,$row->primg02,$row->primg03,$row->primg04,$row->primg05,$row->primg06,$row->primg07,$row->primg08,$row->primg09,$row->primg10);

			$productcode=$copyprlist[$i];
			$sql = "INSERT INTO tblmultiimages ";
			$sql.= "(productcode,primg01,primg02,primg03,primg04,primg05,primg06,primg07,primg08,primg09,primg10,size) ";
			$sql.= "VALUES ('".$productcode."', ";
			for($y=0;$y<count($plusfiles);$y++) {
				if($fromprlist[$i][0]!='/')
					$fromprlist[$i] = '/'.$fromprlist[$i].'/';
				$imgfile=preg_replace($fromprlist[$i],$productcode,$plusfiles[$y]);
				$sql.= "'".$imgfile."', ";

				if(strlen($plusfiles[$y])>0) {
					copy($imagepath.$plusfiles[$y], $imagepath.$imgfile);
					copy($imagepath."s".$plusfiles[$y], $imagepath."s".$imgfile);
					if($type=="move" && file_exists($imagepath.$plusfiles[$y])) {
						unlink($imagepath.$plusfiles[$y]);
					}
					if($type=="move" && file_exists($imagepath."s".$plusfiles[$y])) {
						unlink($imagepath."s".$plusfiles[$y]);
					}
				}
			}
			$sql.= "'".$row->size."') ";
			pmysql_query($sql,get_db_conn());
			if(!pmysql_errno()) {
				if($type=="move") {
					$sql = "DELETE FROM tblmultiimages WHERE productcode='".$fromprlist[$i]."' ";
					pmysql_query($sql,get_db_conn());
				}
			}
		}
		pmysql_free_result($result);
	}
}

