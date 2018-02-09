<?
$fp = file("menu_design.txt");

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

for ($i=0,$m=sizeof($menu['title']);$i<$m;$i++) {
	if($menu['title'][$i] && count($menu['subject'][$i])){	
		for ($j=0;$j<count($menu['subject'][$i]);$j++){
			if($menu['subject'][$i][$j]){
				if(preg_match('/'.str_replace('/','\/',str_replace('?','',$menu['value'][$i][$j])).'/',str_replace('?','',$_SERVER['REQUEST_URI']))){
					if (!$subPage_dept2_title) $subPage_dept2_title	= $menu['title'][$i];
					if (!$subPage_dept3_title) $subPage_dept3_title	= $menu['subject'][$i][$j];								
				}						
			}
		}
	}
}

$menu	= null;
?>