<?
include_once dirname(__FILE__)."/file.class.php";
include_once dirname(__FILE__)."/page.class.php";

class RECIPE extends PAGE{

	function RECIPE(){
		global $_POST, $_FILES;
	}

	function setSearch($param){
		if(is_array($param)){foreach($param as $f=>$v){
			$this->$f = $v;
		}}
	}
	
	function getRecipeBanner(){
		$banner_arr=array();
		$qry="select banner_img,banner_link from tblmainbannerimg where banner_no='10' order by banner_sort";
		$res=pmysql_query($qry);

		while($row=pmysql_fetch_array($res)){
			$banner_arr['img'][]=$row['banner_img'];
			$banner_arr['link'][]=$row['banner_link'];
		}

		return $banner_arr;
	}

	function getRecipeBanner2(){
		$banner_arr2=array();
		$qry="select banner_img,banner_link from tblmainbannerimg where banner_no='11' order by banner_sort";
		$res=pmysql_query($qry);

		while($row=pmysql_fetch_array($res)){
			$banner_arr2['img'][]=$row['banner_img'];
			$banner_arr2['link'][]=$row['banner_link'];
		}

		return $banner_arr2;
	}

	function getRecipeList(){
		$group[] = "r.no";
		$field[] = "distinct (r.no)";
		$field[] = "r.*";
		$table[] = "tblrecipe r left join tblrecipelink rl on r.no = rl.no ";
		$sort[] = "r.no desc";
		
		if($this->code) $where[] = "rl.category like '".str_replace("000","",$this->code)."%'";
		if($this->search_word){
			if(in_array("subject",$this->search_field)||in_array("all",$this->search_field)) $search[] = "r.subject like '%".$this->search_word."%'";
			if(in_array("name",$this->search_field)||in_array("all",$this->search_field)) $search[] = "r.name like '%".$this->search_word."%'";
			if(in_array("contents",$this->search_field)||in_array("all",$this->search_field)) $search[] = "r.contents like '%".$this->search_word."%'";
			$where[] = "(".implode(" or ",$search).")";
		}

		$this->distinct = "r.no";
		$this->field = $field;
		$this->table = $table;
		$this->where = $where;
		$this->sort = $sort;
		$this->group = $group;
		
		$this->setQuery();
//		echo $this->query;
		$result = pmysql_query($this->query,get_db_conn());
		$vnum = $this->vnum;
		while($row = pmysql_fetch_array($result)){
			$row[vnum] = $vnum--;
			$vfile=explode("|",$row[vfile]);
			$row[timg_src]="/admin/images/recipe/".$vfile[0];
//			echo $row[timg_src];
			$data[] = $row;
		}
		return $data;
		
	}
	function getRecipeOtherList($no){
		$field[] = "distinct (r.no)";
		$field[] = "r.name";
		$field[] = "r.subject";
		$field[] = "r.no";
		$table[] = "tblrecipe r left join tblrecipelink rl on r.no = rl.no ";
		$sort[] = "r.no desc";

		if($this->code) $where[] = "rl.category like '".str_replace("000","",$this->code)."%'";
		if($this->search_word){
			if(in_array("subject",$this->search_field)||in_array("all",$this->search_field)) $search[] = "r.subject like '%".$this->search_word."%'";
			if(in_array("name",$this->search_field)||in_array("all",$this->search_field)) $search[] = "r.name like '%".$this->search_word."%'";
			if(in_array("contents",$this->search_field)||in_array("all",$this->search_field)) $search[] = "r.contents like '%".$this->search_word."%'";
			$where[] = "(".implode(" or ",$search).")";
		}

		#### 이전글 정보 ####
		$prevwhere = $where;
		$prevwhere[] = "r.no > ".$no;
		$query = "select ".implode(",",$field)." from ".implode(",",$table)." where ".implode(" and ",$prevwhere)."  order by no asc limit 1 ";
		$row = pmysql_fetch_array(pmysql_query($query,get_db_conn()));
		$data[prev] = $row;

		#### 다음글 정보 ####
		$nextwhere = $where;
		$nextwhere[] = "r.no < ".$no;
		$query = "select ".implode(",",$field)." from ".implode(",",$table)." where ".implode(" and ",$nextwhere)." order by no desc limit 1 ";
		$row = pmysql_fetch_array(pmysql_query($query,get_db_conn()));
		$data[next] = $row;

		return $data;


	}


	function getRecipeCategoryList($parent=''){
		$type = array("L","LM","LMX","LMXY");
		$field[] = "concat(code_a,code_b,code_c,code_d) code";
		$field[] = "code_name";
		$field[] = "cate_sort";
		$where[] = "concat(code_a,code_b,code_c,code_d)!='".$parent."' ";
		if(!$parent) $where[] = "code_b='000'";
		else $where[]= "concat(code_a,code_b,code_c,code_d) like '".str_replace("000","",$parent)."%'";

		$len =  3-(strlen(str_replace("000","",$parent))/3);
		$where[] = "concat(code_a,code_b,code_c,code_d) like '%".str_pad(0,$len*3,0,STR_PAD_LEFT)."'";
		$query = "select ".implode(",",$field)." from tblrecipecode rc where ".implode(" and ", $where)." order by cate_sort, code";
		
		$result = pmysql_query($query);
		while($row = pmysql_fetch_array($result)){
			$data[] = $row;
		}
		return $data;
	}

	function getRecipeCategoryListOnRecipe($recipe_no){
		$cate_query="select * from tblrecipelink where no='".$recipe_no."' ";
		$cate_result=pmysql_query($cate_query);
		$i=0;
		
		while($cate_row=pmysql_fetch_array($cate_result)){
			$cate_array[$i]["category"]=$cate_row[category];
			$cate_cut="";
			$catename="";
			$cate_cut[]=str_pad(substr($cate_row[category],0,3), 12, "0");
			if(substr($cate_row[category],3,3)!='000')$cate_cut[]=str_pad(substr($cate_row[category],0,6), 12, "0");
			if(substr($cate_row[category],6,3)!='000')$cate_cut[]=str_pad(substr($cate_row[category],0,9), 12, "0");
			if(substr($cate_row[category],9,3)!='000')$cate_cut[]=str_pad(substr($cate_row[category],0,12), 12, "0");
			
			foreach($cate_cut as $k){
				$catename_qry="select * from tblrecipecode where code_a='".substr($k,0,3)."' and code_b='".substr($k,3,3)."' and code_c='".substr($k,6,3)."' and code_d='".substr($k,9,3)."'";
				$catename_result=pmysql_query($catename_qry);
				$catename_data=pmysql_fetch_array($catename_result);
				$catename[]=$catename_data[code_name];
			}
			$cate_array[$i]["c_codename"]=implode(" > ",$catename);
		$i++;
		}
		return $cate_array;
	}

	function getRecipeCategoryDetail($code){
		$field[] = "concat(code_a,code_b,code_c,code_d) code";
		$field[] = "code_name";
		$where[] = "concat(code_a,code_b,code_c,code_d)='".$code."' ";

		$query = "select ".implode(",",$field)." from tblrecipecode rc where ".implode(" and ", $where)." order by cate_sort, code";
		
		$result = pmysql_query($query);
		$data = pmysql_fetch_array($result);
		return $data;
	}


	#### 레시피 상세 STR ####	
	function getRecipeDetail($no){
		
		$field[] = "r.no";
		$field[] = "r.name";
		$field[] = "r.id";
		$field[] = "r.subject";
		$field[] = "r.contents";
		$field[] = "r.regdt";
		$field[] = "r.moddt";
		$field[] = "r.vfile";
		$field[] = "r.rfile";

		$field[] = "r.cate";
		$field[] = "r.m_no";
		$field[] = "r.groupid";

		$query = "select ".implode(",",$field)." from tblrecipe r where r.no = '".$no."' ";
		$result = pmysql_query($query, get_db_conn());
		$row = pmysql_fetch_array($result);
		
		$data  = $row;
		$data[vfile_tag] = $row[vfile];
		$data[rfile_tag] = $row[rfile];
		$data[vfile] = explode("|",$row[vfile]);
		$data[rfile] = explode("|",$row[rfile]);
		
		#### contents_recipe ####
		if(strstr($data[contents],"[:시작:]")&&strstr($data[contents],"[:끝:]")){
			$contents_recipe=$data[contents];
			$contents_recipe = explode("[:시작:]",$contents_recipe);
			$contents_recipe = explode("[:끝:]",$contents_recipe[1]);
			$contents_recipe = $contents_recipe[0];
		}
//		echo $data[contents_recipe];
		
		#### contents_tag ####
		$contents_tag = $data[contents];
		$contents_tag = str_replace('[:시작:]','<div id="startDiv">',$contents_tag);
		$contents_tag = str_replace('[:끝:]','</div">',$contents_tag);
		$i=0;
		foreach($data[vfile] as $v){
			$i++;
			$str = "[:이미지".$i.":]";
			$img = "<img src='/admin/images/recipe/".$v."'>";
			$contents_tag = str_replace($str,$img, $contents_tag);
			$contents_recipe = str_replace($str,$img, $contents_recipe);
		}

		$data[contents_tag] = $contents_tag;
		$data[contents_recipe] = $contents_recipe;

		$data[timg_src]="/admin/images/recipe/".$data[vfile][0];
		

		#### 레시피 카테고리 ####
		$data[category] = $this->getRecipeCategory($no);

		return $data;
	}
	
	function getRecipeCategory($no){
		#### 레시피 카테고리 ####
		$query = "select * from tblrecipelink where no = '".$no."' ";
		$result = pmysql_query($query, get_db_conn());
		while($row = pmysql_fetch_array($result)){
			$data[] = $row;
		}
		return $data;
	}

	function getRecipeProductList($no){
		#### 레시피 상품 리스트 ####
		$query = "select p.img_s as img, p.productcode, p.productname, p.consumerprice, p.quantity stock, p.option_quantity opt1_stock, p.option1 opt1, p.option2 opt2, p.option_price opt1_price,  rp.* from tblrecipeproduct rp
		left join tblproduct p on p.productcode = rp.productcode 
		where rp.recipe_no = '".$no."' and p.display='Y'";
		$result = pmysql_query($query, get_db_conn());
		while($row = pmysql_fetch_array($result)){

			$row[img_src] = "/data/shopimages/product/".$row[img];


			$opt1_price = explode(",",$row[opt1_price]);
			$opt1_stock = explode(",",$row[opt1_stock]);
			$opt1 = explode(",",$row[opt1]);
			$opt2 = explode(",",$row[opt2]);
			$opt1=array_flip($opt1);
			$opt2=array_flip($opt2);
			
			$row[opt1_idx] = $opt1[$row[option1]];
			$row[opt2_idx] = $opt2[$row[option2]];
			$row[price] = $opt1_price[($row[opt1_idx]-1)];
			$row[stock] = $row[stock]?$row[stock]:$opt1_stock[($row[opt1_idx]-1)];
			if(!$row[price]) $row[price] = $row[consumerprice];
			$data[] = $row;
		}
		return $data;
	}

	function addRecipe(){

		$sql = "INSERT INTO tblrecipe DEFAULT VALUES RETURNING no";
		$row = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
		$no = $row[0];
		$this->modRecipe($no);

		return $no;
	}

	function addRecipeProduct(){
	
		$recipe_no = $_REQUEST[recipe_no];
		if(is_array($_POST[index])){
			foreach($_POST[index] as $idx){
				$productcode = $_REQUEST[productcode][$idx];
				$option = explode(",",$_REQUEST[option][$idx]);
				$option1 = $option[0];
				$option2 = $option[1];
				$query = " insert into tblrecipeproduct (recipe_no, productcode, option1, option2) values ('".$recipe_no."','".$productcode."','".$option1."','".$option2."' )";
//				echo $query;
				pmysql_query($query, get_db_conn());
				/*
				$productcode = $_REQUEST[productcode];
				$option = explode(",",$_REQUEST[option]);
				$query = " insert into tblrecipeproduct (recipe_no, productcode, option1, option2) values ('".$recipe_no."','".$productcode."','".$option1."','".$option2."' )";
				
				*/
			}
		}

	}

	function delRecipeProduct(){
		foreach($_POST[index] as $no){
			$query = " delete from tblrecipeproduct where no='".$no."' ";
			pmysql_query($query, get_db_conn());
		}

	}

	function modRecipe($no){
		$file = new FILE();
		try{
			$fdata = $file->upFiles();
			$rfile = explode("|",$_POST[rfile]);
			$vfile = explode("|",$_POST[vfile]);
			for($i=0; $i<count($fdata[file]); $i++){
				if(!$_POST[del_file][$i]){
					if($fdata[file][$i][error]){
						if($vfile[$i]) $vfilename[]=$vfile[$i];
						if($rfile[$i]) $rfilename[]=$rfile[$i];
					}else{
						if($fdata[file][$i][v_file]) $vfilename[]=$fdata[file][$i][v_file];
						if($fdata[file][$i][r_file]) $rfilename[]=$fdata[file][$i][r_file];
					}
				}
			}
			$vfile = implode("|",$vfilename);
			$rfile = implode("|",$rfilename);

		}catch(Exception $e){
			echo $e;
		}



		$field[] = "name='{$_POST[up_name]}'";
		$field[] = "id='{$_POST[id]}'";
		$field[] = "subject='".pg_escape_string($_POST[up_subject])."'";
		$field[] = "contents='".pg_escape_string($_POST[contents])."'";
		$field[] = "regdt='now()'";
		$field[] = "rfile='{$rfile}'";
		$field[] = "vfile='{$vfile}'";

		$sql = "update tblrecipe set ".implode(",",$field);
		$sql.= "where no={$no}";
		$result = pmysql_query($sql,get_db_conn());

		#### 레시피 카테고리 수정 ####
		$this->setRecipeCategory($_POST[category],$no);

		return $no;



	}

	function setRecipeCategory($category, $no){

		#### 기존 카테고리 삭제 처리
		$query = "delete from tblrecipelink where no = '".$no."' ";
		pmysql_query($query,get_db_conn());

		#### 카테고리 입력 처리
		if(is_array($category)){foreach($category as $code){
			$query = "insert into tblrecipelink values('".$code."', '".$no."')";
			pmysql_query($query,get_db_conn());
		}}
	}

	#### 레시피 상세 END ####	


	#### 레시피 삭제 STR ####
	function delRecipe($no){
		#### 파일 삭제 처리 ####
		$this->delRecipeFile($no);

		#### 카테고리 삭제 처리 ####
		$this->delRecipeCategory($no);

		#### 뎃글 삭제 처리 ####
		$this->delRecipeComment($no);

		#### 레시피 삭제 처리 ####
		$query = "delete from tblrecipe where no = '".$no."' ";
		pmysql_query($query,get_db_conn());
	}

	function delRecipeCategory($no){
		#### 기존 카테고리 삭제 처리
		$query = "delete from tblrecipelink where no = '".$no."' ";
		pmysql_query($query,get_db_conn());
	}

	function delRecipeFile($no){
		#### 파일 삭제 처리 ####
		$query = "select vfile from tblrecipe where no='".$no."' ";
		$result = pmysql_query($query,get_db_conn());
		list($vfile) = pmysql_fetch($result);
		$vfile = explode("|",$vfile);
		if(is_array($vfile)){
			foreach($vfile as $v){
				unlink($config[file][path].$v);
			}
		}
	}
	#### 레시피 삭제 END ####

	#### 레시피 후기 STR ####

	function addRecipeComment($param){
		#### 레시피 후기 등록 ####
		$query = "INSERT INTO tblrecipecomment DEFAULT VALUES RETURNING num";
		$row = pmysql_fetch_array(pmysql_query($query,get_db_conn()));
		$num = $row[0];

		$query = " update tblrecipecomment set 
			parent='".$param[recipe_no]."', 
			name='".$param[memname]."', 
			ip='".$_SERVER[REMOTE_ADDR]."', 
			writetime='".time()."', 
			comment='".pg_escape_string($param[comment])."', 
			c_mem_id='".$param[memid]."', 
			parent_comment='".$num."'
			where num='".$num."' ";
//		$query = " insert into tblrecipecomment (parent,name,ip,writetime,comment,c_mem_id) values ('".$param[recipe_no]."','".$param[memname]."','".$_SERVER[REMOTE_ADDR]."','".time()."','".pg_escape_string($param[comment])."','".$param[memid]."')";
		return pmysql_query($query);
	}

	function addRecipeCommentReply($param){
		#### 레시피 후기 등록 ####
		$query = " insert into tblrecipecomment (parent, parent_comment, name,ip,writetime,comment,c_mem_id) values ('".$param[recipe_no]."','".$param[num]."','".$param[memname]."','".$_SERVER[REMOTE_ADDR]."','".time()."','".pg_escape_string($param[comment])."','".$param[memid]."')";
		return pmysql_query($query);
	}
				
	function delRecipeComment($num){
		#### 레시피 후기 삭제 ####
		$query = "delete from tblrecipecomment where num ='".$num."' or parent_comment = '".$num."' ";
		return pmysql_query($query);
	}

	function getRecipeCommentList($recipe_no=''){
		$field[] = "r.no";
		$field[] = "r.subject";
		$field[] = "r.vfile";
		$field[] = "to_char(to_timestamp(rc.writetime),'yyyy-mm-dd') regdt";
		$field[] = "rc.*";
		$table[] = "tblrecipecomment rc left join tblrecipe r on rc.parent = r.no";
		$where[] = "r.no is not null";
		if($recipe_no) $where[] = "rc.parent='".$recipe_no."'";
		if($this->search_word){
			if(in_array("subject",$this->search_field)||in_array("all",$this->search_field)) $search[] = "r.subject like '%".$this->search_word."%'";
			if(in_array("name",$this->search_field)||in_array("all",$this->search_field)) $search[] = "rc.name like '%".$this->search_word."%'";
			if(in_array("contents",$this->search_field)||in_array("all",$this->search_field)) $search[] = "rc.comment like '%".$this->search_word."%'";
			if(is_array($search)) $where[] = "(".implode(" or ",$search).")";
		}
		if(in_array("best",$this->search_field)) $where[] = "rc.best_type='1'";
		if($this->search_start) $where[] = "to_char(to_timestamp(writetime),'YYYY-MM-DD')>='".$this->search_start."'";
		if($this->search_end) $where[] = "to_char(to_timestamp(writetime),'YYYY-MM-DD')<='".$this->search_end."'";

		$sort[] = "rc.parent_comment desc, rc.num ";

		$this->field = $field;
		$this->table = $table;
		$this->where = $where;
		$this->group = $group;
		$this->sort = $sort;

		$this->setQuery();
		$result = pmysql_query($this->query, get_db_conn());

		$vnum = $this->vnum;
		$re_tag = "<img src='/board/images/skin/L_TEM01/re_mark.gif' style='display:inline;'>";
		while($row = pmysql_fetch_array($result)){
			
			$row[name] .= $row[c_mem_id]?"({$row[c_mem_id]})":"";
			$row[vnum] = $vnum--;
			$comment_tag = $row[comment];
			$comment_tag = nl2br($comment_tag);
			
			if($row[parent_comment]!=$row[num]) $comment_tag =" ".$re_tag." ".$comment_tag;
			$row[comment_tag] = $comment_tag;
			
			$vfile=explode("|",$row[vfile]);
			$row[timg_src]="/admin/images/recipe/".$vfile[0];
			
			$data[] = $row;
		}
		return $data;
	}
	
	function getRecipeCommentList2($recipe_no=''){
		$field[] = "r.no";
		$field[] = "r.subject";
		$field[] = "r.vfile";
		$field[] = "to_char(to_timestamp(rc.writetime),'yyyy-mm-dd') regdt";
		$field[] = "rc.*";
		$table[] = "tblrecipecomment rc left join tblrecipe r on rc.parent = r.no";
		$where[] = "r.no is not null";
		$where[] = "rc.best_type='1'";
		$sort[] = "rc.parent_comment desc, rc.num ";

		$this->field = $field;
		$this->table = $table;
		$this->where = $where;
		$this->group = $group;
		$this->sort = $sort;

		$this->setQuery();
		$result = pmysql_query($this->query, get_db_conn());

		$vnum = $this->vnum;
		$re_tag = "<img src='/board/images/skin/L_TEM01/re_mark.gif' style='display:inline;'>";
		while($row = pmysql_fetch_array($result)){
			
			$row[name] .= $row[c_mem_id]?"({$row[c_mem_id]})":"";
			$row[vnum] = $vnum--;
			$comment_tag = $row[comment];
			$comment_tag = nl2br($comment_tag);
			
			if($row[parent_comment]!=$row[num]) $comment_tag =" ".$re_tag." ".$comment_tag;
			$row[comment_tag] = $comment_tag;
			
			$vfile=explode("|",$row[vfile]);
			$row[timg_src]="/admin/images/recipe/".$vfile[0];
			
			$data[] = $row;
		}
		return $data;
	}

	function getRecipeCommentDetail($num){
		$query = " select r.no recipe_no, r.subject, rc.* from tblrecipecomment rc left join tblrecipe r 
		on rc.parent = r.no
		where rc.num='".$num."'";
		$result = pmysql_query($query, get_db_conn());
		$row = pmysql_fetch_array($result);

		$row[comment_tag] = nl2br($row[comment_tag]);

		return $row;

	}
	#### 레시피 후기 END ####

	#### 레시피보관함 STR ####
	function getMyRecipeList(){
		global $_ShopInfo;

		$member_id = $_ShopInfo->memid;

		$field[] = "mr.no";
		$field[] = "mr.recipe_no";
		$field[] = "r.vfile";
		$field[] = "r.subject";
		$field[] = "mr.regdt";

		$table[] = "tblmyrecipe mr left join tblrecipe r on mr.recipe_no=r.no";
		$where[] = "mr.member_id='".$member_id."'";
		$sort[] = "mr.no desc";


		$this->field = $field;
		$this->table = $table;
		$this->where = $where;
		$this->sort = $sort;
		
		$this->setQuery();

		$result = pmysql_query($this->query, get_db_conn());
		while($row = pmysql_fetch_array($result)){
			$vfile=explode("|",$row[vfile]);
			$row[timg_src]="/admin/images/recipe/".$vfile[0];
			$data[] = $row;
		}
		return $data;
//		$query = "insert into tblmyrecipe (recipe_no, member_id) values (".$recipe_no.", '".$member_id."')";
//		return pmysql_query($query, get_db_conn());
	}

	function getMyRecipe($recipe_no){
		global $_ShopInfo;
		$member_id = $_ShopInfo->memid;
		$query = "select count(*) from tblmyrecipe where recipe_no='".$recipe_no."' and member_id='".$member_id."' ";
		list($status) = pmysql_fetch_array(pmysql_query($query, get_db_conn()));
		return $status;
	}

	function delMyRecipe($no){
		$query = "delete from tblmyrecipe where no='".$no."' ";
		pmysql_query($query, get_db_conn());
	}

	function setMyRecipe($recipe_no){
		global $_ShopInfo;
		$member_id = $_ShopInfo->memid;
		$query = "insert into tblmyrecipe (recipe_no, member_id, regdt) values (".$recipe_no.", '".$member_id."', now())";
		return pmysql_query($query, get_db_conn());
	}

	#### 레시피보관함 END ####

}
