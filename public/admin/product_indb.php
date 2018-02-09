<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/recipe.class.php");

		#### 데이터 이전 작업 STR ####
		if($_REQUEST["module"]=="product_option_restore"){
			$sqlfile = file_get_contents("http://soap.soapschool.co.kr/shop/admin/member/goods_dump.php?mode=product_option");
			$sqlarray = explode("update ", $sqlfile);
			for($i=1; $i< count($sqlarray); $i++){
				$sql = "update ".$sqlarray[$i];
//				echo pmysql_query("update tblproduct set option_consumer = '0,0' where goodsno=22",get_db_conn());

				echo $i." : ";
				echo pmysql_query($sql, get_db_conn());
				echo ": ";
				echo $sql;
				echo "<br>";
			}

		}

		
		if($_REQUEST["module"]=="product_contents_restore"){
			$sqlfile = file_get_contents("http://soap.soapschool.co.kr/shop/admin/member/goods_dump.php?mode=product_contents");
			$sqlarray = explode("update ", $sqlfile);
			for($i=1; $i< count($sqlarray); $i++){
				$sql = "update ".$sqlarray[$i];
//				echo pmysql_query("update tblproduct set option_consumer = '0,0' where goodsno=22",get_db_conn());

//				echo $i." : ";
				$status = pmysql_query($sql, get_db_conn());
				if($status) $success ++;
				else echo $sql."<br>";
			}
			echo $success."/".(count($sqlarray)-1)." 건 완료";

		}

		
		#### 데이터 이전 작업 END ####



		if($_REQUEST["module"]=="product_option_stock"){
			$query = "select distinct concat(op.productcode,op.ordercode), p.productcode, op.opt1_name, op.quantity, p.option1, p.option_quantity from tblorderinfo oi 
			left join tblorderproduct op on oi.ordercode  = op.ordercode
			left join tblproduct p on op.productcode = p.productcode
			where oi.ordercode like '20140213%' and substr(oi.ordercode,1,12) < '201402131030' 
			and op.productcode not in ('99999999999X','99999999990X')
			";
			$result = pmysql_query($query,get_db_conn());
			while($row =  pmysql_fetch_array($result)){
				
				$opt1_name = explode(" : ",$row[opt1_name]);
				$option1 = explode(" : ",$row[option1]);
				echo $opt1_name[1]."<br>";
				echo $row[option_quantity]."<br>";
				echo array_search($opt1_name[1]);

				$sql = "update tblproduct set quantity = quantity - ".$row[quantity]." where productcode='".$row[productcode]."'";

				echo $sql."<br>";
			}
		}


?>