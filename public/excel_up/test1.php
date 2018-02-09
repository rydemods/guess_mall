<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
$query = "select productname from sewoncode group by productname order by productname ";
$res=pmysql_query($query);
$cnt = 0;
$scnt = 0;
while($data=pmysql_fetch_object($res)){
$cnt++;

	$productname_cnt = 0;
	$sewonquery = "select option1,productname,productcode from tblproduct where productname LIKE '".$data->productname."' ";
	$sewonresult = pmysql_query($sewonquery,get_db_conn());
	while ($sewonrows = pmysql_fetch_object($sewonresult)) {
$scnt++;
	/*
		if($sewonrows -> option1){
			$option = explode(',',$sewonrows->option1);

			for($i=1 ; $i < count($option) ; $i++ ){
				$check = str_replace('(','',$option[$i]);
				$size = explode(')',$check);

				$subquery = "select * from sewoncode where productname = '".$sewonrows->productname."' and itemname like '%".$size[0]."%' ";
				$subresult = pmysql_query($subquery,get_db_conn());
				while ($subrows = pmysql_fetch_object($subresult)) {

					$itemno[] = $subrows->itemno;
					$itemcolor[] = $subrows->color;
					$itemsize[] = $subrows->size;

				}
			}

			if(count($itemno)){
				$temno = implode(',',$itemno);
				$temcolor = implode(',',$itemcolor);
				$temsize = implode(',',$itemsize);

				$updatequery = "update tblproduct set sewon_option_no = '".$temno."', sewon_option_code1 = '".$temcolor."' , sewon_option_code2 = '".$temsize."' where productcode = '".$sewonrows -> productcode."' ";

//				pmysql_query($updatequery);
//				echo $updatequery;
//				echo "<br/>";

			}else{
//				echo $sewonrows -> productcode;
//				echo "<br/>";
			}

			unset($itemno);
			unset($itemcolor);
			unset($itemsize);
		}
		$productname_cnt++;
	}
	if(!$productname_cnt){
		echo $data -> productname;
		echo "<br/>";
	}
	*/}
}
echo $scnt;
?>