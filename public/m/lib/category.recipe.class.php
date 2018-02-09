<?
class CATEGORYLIST{

	function CATEGORYLIST(){
		
	}

	function getCateTree(){ //상품 카테고리 리스트

		$sql = "SELECT * FROM tblrecipecode where code_b='000' ORDER BY cate_sort, code_name ";
		$result  = pmysql_query($sql,get_db_conn());

		$html='';	
		while ($row = pmysql_fetch_object($result)) {
		$clz='';
			$div_class= (strstr($row->type,'X'))?'doc':'folder'; //상위 하위 결정
			
			if($row->group_code=='NO') $vtype=1; //보이기 감추기
			else $vtype=0;

			if(strstr($row->list_type,'BL'))$templet_type="(공구형)";  
			else $templet_type="";

			$html.="<div class='".$div_class."' id='".$row->code_a."' vtype='".$vtype."'>";
			$html.=$row->code_name.$templet_type;
			
			if(strstr($row->type,'X')){
				$html.="</div>";
			}else{
				

				$sql_b = "SELECT * FROM tblrecipecode where code_a='".$row->code_a."' and code_b!='000' and code_c='000' ORDER BY cate_sort, code_name ";

				$result_b  = pmysql_query($sql_b,get_db_conn());

				while ($row_b = pmysql_fetch_object($result_b)) {

					$div_class= (strstr($row_b->type,'X'))?'doc':'folder';
					if($row_b->group_code=='NO') $vtype=1;
					else $vtype=0;

					if(strstr($row_b->list_type,'BL'))$templet_type="(공구형)";  
					else $templet_type="";


					$html.="<div class='".$div_class."' id='".$row_b->code_a.$row_b->code_b."' vtype='".$vtype."'>";
					$html.=$row_b->code_name.$templet_type;
					//$html.="</div>";
					if(strstr($row_b->type,'X')){
						$html.="</div>";
					}else{
						

						$sql_c = "SELECT * FROM tblrecipecode where code_a='".$row_b->code_a."' and code_b='".$row_b->code_b."' and code_c!='000' and code_d='000' ORDER BY cate_sort, code_name";

						$result_c  = pmysql_query($sql_c,get_db_conn());

						while ($row_c = pmysql_fetch_object($result_c)) {
					
							$div_class= (strstr($row_c->type,'X'))?'doc':'folder';
							if($row_c->group_code=='NO') $vtype=1;
							else $vtype=0;

							if(strstr($row_c->list_type,'BL'))$templet_type="(공구형)";  
							else $templet_type="";

							$html.="<div class='".$div_class."' id='".$row_c->code_a.$row_c->code_b.$row_c->code_c."' vtype='".$vtype."'>";
							$html.=$row_c->code_name.$templet_type;
							//$html.="</div>";

							if(strstr($row_c->type,'X')){
								$html.="</div>";
							}else{
								

								$sql_d = "SELECT * FROM tblrecipecode where code_a='".$row_c->code_a."' and code_b='".$row_c->code_b."' and code_c='".$row_c->code_c."' and code_d!='000' ORDER BY cate_sort, code_name";

								$result_d  = pmysql_query($sql_d,get_db_conn());

								while ($row_d = pmysql_fetch_object($result_d)) {
							
									$div_class= (strstr($row_d->type,'X'))?'doc':'folder';
									if($row_d->group_code=='NO') $vtype=1;
									else $vtype=0;

									if(strstr($row_d->list_type,'BL'))$templet_type="(공구형)";  
									else $templet_type="";

									$html.="<div class='".$div_class."' id='".$row_d->code_a.$row_d->code_b.$row_d->code_c.$row_d->code_d."' vtype='".$vtype."'>";
									$html.=$row_d->code_name.$templet_type;
									$html.="</div>";
								}
								$html.="</div>";
							}
							
						}
						$html.="</div>";
					}
					
				}
				$html.="</div>";
			}
			
		}
		
		
		return $html;
	}



		function getDesignCateTree(){
			$fp = file(dirname(__FILE__)."/../admin/menu_design_u.txt");

			foreach($fp as $v){
				if(trim($v))$v = trim($v);
				if(substr($v,0,1) == "[" && substr($v,-1,1) == "]"){
					$menu['main_title'][] = str_replace(array('[',']'),"",$v);
				}else if(substr($v,0,1) == "<" && substr($v,-1,1) == ">"){
					$menu['title'][] = str_replace(array('<','>'),"",$v);
				}else{
					$k = count($menu[title]) - 1;
					$tmp = explode('= ',$v);
					if(trim($tmp[0])){
						$menu['subject'][$k][] = $tmp[0];
						$url = trim(str_replace('"','',$tmp[1]));
						if (preg_match("/^..\//", $url)) $menu['value'][$k][] = $url;
						else if (preg_match("/^javascript/i", $url)) $menu['value'][$k][] = $url;
						else $menu['value'][$k][] = $url;
						
					}
				}
			}
		
			$html='';
			for ($i=0,$m=sizeof($menu['title']);$i<$m;$i++) {
				if($menu['title'][$i] && count($menu['subject'][$i])){
					
					$div_class='folder';
					$html.="<div class='".$div_class."' vtpye=''>";
					$html.=$menu['title'][$i];

					for ($j=0;$j<count($menu['subject'][$i]);$j++){
						if($menu['subject'][$i][$j]){

							$div_class='doc';
							$html.="<div class='".$div_class."' vtpye=''>";
							$html.="<a href='".$menu['value'][$i][$j]."'>";
							$html.=trim($menu['subject'][$i][$j]);
							$html.="</a></div>";
							
						}
					}
					$html.="</div>";
				}
			}
	

		return $html;
		}

}
?>