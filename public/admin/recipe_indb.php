<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/recipe.class.php");
		
		$file = new FILE();
		$recipe = new RECIPE();
		
		
		#### 데이터 이전 작업 STR ####
		if($_REQUEST["module"]=="myrecipe_restore"){
			$sqlfile = file_get_contents("http://www.soapschool.co.kr/shop/admin/member/recipe_dump.php?mode=myrecipe");
			$sqlarray = explode("insert into", $sqlfile);
			for($i=1; $i< count($sqlarray); $i++){
				$sql = "insert into ".$sqlarray[$i];
				echo $i." : ";
				echo pmysql_query($sql, get_db_conn());
				echo ": ";
				echo $sql;
				echo "<br>";
			}

		}
		if($_REQUEST["module"]=="myrecipe_match"){

			$query = "select * from tblrecipe ";
			$result = pmysql_query($query, get_db_conn());
			while($row = pmysql_fetch_array($result)){
				$no = $row[no];
				$cate = explode(",",$row[cate]);
				$bbs_no = explode(",",$row[bd_no]);

				for($i=0; $i<count($cate); $i++){
					$query = "update tblmyrecipe set recipe_no=".$row[no]." where bdno = '".$cate[$i].$bbs_no[$i]."' ";
					echo pmysql_query($query);
					echo $query."<br>";
				}
			}
		}

		if($_REQUEST["module"]=="myrecipe_mem_match"){

			$query = "select mr.no, m.id, m.m_no from tblmyrecipe mr left join tblmember m on m.m_no = mr.m_no";
			$result = pmysql_query($query, get_db_conn());
			while($row = pmysql_fetch_array($result)){
				$sql = " update tblmyrecipe set member_id = '".$row[id]."' where no='".$row[no]."'";
				echo pmysql_query($sql);
				echo "<br>";

			}
		}

		if($_REQUEST["module"]=="recipe_product_restore"){
			$sqlfile = file_get_contents("http://soapschool.co.kr/shop/admin/member/recipe_dump.php?mode=product");
			$sqlarray = explode("insert into", $sqlfile);
			for($i=1; $i< count($sqlarray); $i++){
				$sql = "insert into ".$sqlarray[$i];
				echo $i." : ";
				echo pmysql_query($sql, get_db_conn());
				echo ": ";
				echo $sql;
				echo "<br>";
			}

		}


		if($_REQUEST["module"]=="recipe_product_match"){
			$query = "select * from tblrecipe ";
			$result = pmysql_query($query, get_db_conn());
			while($row = pmysql_fetch_array($result)){
				$no = $row[no];
				$cate = explode(",",$row[cate]);
				$bbs_no = explode(",",$row[bd_no]);

				for($i=0; $i<count($cate); $i++){
					$query = "update tblrecipeproduct set recipe_no='".$row[no]."' where bbs_id = '".$cate[$i]."' and bbs_no='".$bbs_no[$i]."' and recipe_no is null ";
					echo pmysql_query($query);
					echo $query."<br>";
				}


			}

		}

		if($_REQUEST["module"]=="recipe_comment_match"){

			$query = "select * from tblrecipe ";
			$result = pmysql_query($query, get_db_conn());
			while($row = pmysql_fetch_array($result)){
				$no = $row[no];
				$cate = explode(",",$row[cate]);
				$bbs_no = explode(",",$row[bd_no]);

				for($i=0; $i<count($cate); $i++){
					$query = "update tblrecipecomment set parent=".$row[no]." where board_uni = '".$cate[$i].$bbs_no[$i]."' ";
					echo pmysql_query($query);
					echo $query."<br>";
				}
			}
		}

		if($_REQUEST["module"]=="recipe_comment_mem_match"){

			$query = "select rc.num, m.m_no, m.id from tblrecipecomment rc left join tblmember m on m.m_no = rc.m_no";
			$result = pmysql_query($query, get_db_conn());
			while($row = pmysql_fetch_array($result)){
				$sql = " update tblrecipecomment set c_mem_id = '".$row[id]."' where num='".$row[num]."'";
				echo pmysql_query($sql);
				echo "<br>";

			}
		}

		if($_REQUEST["module"]=="recipe_comment_restore"){

			$sqlfile = file_get_contents("http://soapschool.co.kr/shop/admin/member/recipe_dump.php?mode=comment");
			$sqlarray = explode("insert into", $sqlfile);
			for($i=1; $i< count($sqlarray); $i++){
				$sql = "insert into ".$sqlarray[$i];

				echo $i." : ";
				$status = pmysql_query($sql, get_db_conn());
				echo $status?$status:$sql;
				echo "<br>";
			}


		}
		if($_REQUEST["module"]=="recipe_restore"){
//			if($_REQUEST[page]) $sqlfile = file_get_contents("http://soapschool.co.kr/shop/admin/member/recipe_dump.php?page=".$_REQUEST[page]);
			$sqlfile = file_get_contents("http://soapschool.co.kr/shop/admin/member/recipe_dump.php?mode=recipe");
			$sqlarray = explode("insert into", $sqlfile);
			for($i=1; $i< count($sqlarray); $i++){
				$sql = "insert into ".$sqlarray[$i];

				echo $i." : ";
				echo pmysql_query($sql, get_db_conn());
//				echo $sql;
				echo "<br>";
			}
			echo "완료";
			exit;

		}

		if($_REQUEST["module"]=="recipe_category_restore"){
			$query = "select * from tblrecipe ";
			$result = pmysql_query($query, get_db_conn());
			while($row = pmysql_fetch_array($result)){
				$no = $row[no];
				$cate = explode(",",$row[cate]);

				foreach($cate as $v){
					$query = " insert into tblrecipelink (category, no) 
					select concat(code_a, code_b, code_c, code_d) as category , '".$no."' as no  from tblrecipecode where bd_id='".$v."'
					";
					pmysql_query($query);
					echo $query;
					echo "<br>";
				}

			}
		}
		#### 데이터 이전 작업 END ####
		if($_REQUEST[module]=="recipe_contents"){
			
			if($_REQUEST[mode]=="add_comment_reply"){
				if($_ShopInfo->memid || $_REQUEST[admin]){
					if($recipe->addRecipeCommentReply($_POST)) alert_go("등록되었습니다",$_POST[returnUrl]);
					else alert_go("등록 실패하였습니다.",$_POST[returnUrl]);
				}else{
					alert_go("회원 전용입니다.",$_POST[returnUrl]);
				}
				exit;
			}
			if($_REQUEST[mode]=="add_comment"){
				if($_ShopInfo->memid){
					if($recipe->addRecipeComment($_POST)) alert_go("등록되었습니다",$_POST[returnUrl]);
					else alert_go("등록 실패하였습니다.",$_POST[returnUrl]);
				}else{
					alert_go("회원 전용입니다.",$_POST[returnUrl]);
				}
				exit;
			}
			if($_REQUEST[mode]=="del_comment"){
				if($recipe->delRecipeComment($_POST[num]))	alert_go("삭제되었습니다.",$_POST[returnUrl]);
				else alert_go("삭제 실패하였습니다..",$_POST[returnUrl]);
				exit;
			}
			if($_REQUEST[mode]=="set_my_recipe"){
				if($recipe->getMyRecipe($_REQUEST[recipe_no])) $msg = "이미 담긴 레시피 입니다";
				else if($recipe->setMyRecipe($_REQUEST[recipe_no])) $msg = "레시피 담기가 완료되었습니다.";
				else $msg="레시피 담기가 실패하였습니다";
				alert_go($msg);
				exit;
			}
			if($_REQUEST[mode]=="del_my_recipe"){
				foreach($_REQUEST[myrecipe_no] as $no){
					$recipe->delMyRecipe($no);
				}
				alert_go("삭제되었습니다.", $_REQUEST[returnUrl]);
				exit;
			}
			if($_REQUEST[mode]=="addrecipeproduct"){
				$status = $recipe->addRecipeProduct();
				echo "<script> opener.location.href=opener.location;</script>";
				alert_go("등록되었습니다.", $_REQUEST[returnUrl]);
			}

			if($_REQUEST[mode]=="delrecipeproduct"){
				$status = $recipe->delRecipeProduct();
				alert_go("삭제되었습니다.", $_REQUEST[returnUrl]);
				exit;
			}
			if($_REQUEST[mode]=="delrecipeproducts"){
				foreach($_POST[index] as $idx){
					echo $idx;
				}
				exit;
//				$status = $recipe->delRecipeProduct();
				alert_go("삭제되었습니다.", $_REQUEST[returnUrl]);
				exit;
			}

			if($_REQUEST[mode]=="delete_list"){
				if(is_array($_REQUEST[no])){
					foreach($_REQUEST[no] as $no){
						$recipe->delRecipe($no);
					}
				}
				alert_go("삭제되었습니다.", "/admin/recipe_contents_list.php?exec=list");
				exit;
			}

			if($_REQUEST[mode]=="delete"){

				$recipe->delRecipe($_REQUEST[no]);
				alert_go("삭제되었습니다.", "/admin/recipe_contents_list.php?exec=list");
				exit;
			}
			if($_POST[mode]=="write"){
				$no = $recipe->addRecipe();
				alert_go("등록되었습니다.", "/admin/recipe_contents_list.php?exec=view&no=".$no);
				exit;
			}

			if($_POST[mode]=="modify"){
				$no = $recipe->modRecipe($_POST[no]);
				alert_go("수정되었습니다.", "/admin/recipe_contents_list.php?exec=view&no=".$no);
				exit;
			}

		}
		if($_POST[module]=="recipe_category"){
			if($_POST[mode]=="write"){

				$level = (int)$_POST[level];

				$query = " select max(code) code from tblrecipe_category where level='".$level."' and code like '".$_POST[parent]."%' ";
				$result = pmysql_query($query,get_db_conn());

				$row = pmysql_fetch_array($result);
				$parent = substr($row[code],0,$level*3);
				$max = (int)substr($row[code],$level*3,3)+1;
				$code = $parent.str_pad((int)$max,3,"0",STR_PAD_LEFT);

				$query = " insert into tblrecipe_category (code, name, level, active) values ('".$code."','".$_POST[name]."', 0, 0)";
				pmysql_query($query,get_db_conn());
			}
		}
		exit;
		### 분류트리순서 저장
		if ($_POST[cate1]) foreach ($_POST[cate1] as $k=>$v){
			 $qry="update tblproductcode set cate_sort=$k where code_a='$v' and code_b='000'";
			
			$res = pmysql_query($qry,get_db_conn());
		}

		if ($_POST[cate2]) foreach ($_POST[cate2] as $k=>$v){
			 $qry="update tblproductcode set cate_sort=$k where code_a || code_b = '$v' and code_c='000'";
			
			$res = pmysql_query($qry,get_db_conn());
		}
		if ($_POST[cate3]) foreach ($_POST[cate3] as $k=>$v){
			 $qry="update tblproductcode set cate_sort=$k where code_a || code_b || code_c = '$v' and code_d='000'";
			
			$res = pmysql_query($qry,get_db_conn());
		}
		if ($_POST[cate4]) foreach ($_POST[cate4] as $k=>$v){
			 $qry="update tblproductcode set cate_sort=$k where code_a || code_b || code_c || code_d = '$v'";
			
			$res = pmysql_query($qry,get_db_conn());
		}
		
		//go("product_code.property.php?code=$_POST[code]&mode=modify");
		//echo "<script>document.location.href='product_code.php?category=".$_POST[cate]."'</script>";
		echo "<script>parent.document.location.href='product_code.php?category=".$_POST[cate]."'</script>";
		

?>