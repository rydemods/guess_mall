<?php 

if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
		header("HTTP/1.0 404 Not Found");
		exit;
}
	$alphabet_title = array("A"=>"<span id=\"engtitleidx\">A</span><br><br>",
	"B"=>"<span id=\"engtitleidx\">B</span><br><br>",
	"C"=>"<span id=\"engtitleidx\">C</span><br><br>",
	"D"=>"<span id=\"engtitleidx\">D</span><br><br>",
	"E"=>"<span id=\"engtitleidx\">E</span><br><br>",
	"F"=>"<span id=\"engtitleidx\">F</span><br><br>",
	"G"=>"<span id=\"engtitleidx\">G</span><br><br>",
	"H"=>"<span id=\"engtitleidx\">H</span><br><br>",
	"I"=>"<span id=\"engtitleidx\">I</span><br><br>",
	"J"=>"<span id=\"engtitleidx\">J</span><br><br>",
	"K"=>"<span id=\"engtitleidx\">K</span><br><br>",
	"L"=>"<span id=\"engtitleidx\">L</span><br><br>",
	"M"=>"<span id=\"engtitleidx\">M</span><br><br>",
	"N"=>"<span id=\"engtitleidx\">N</span><br><br>",
	"O"=>"<span id=\"engtitleidx\">O</span><br><br>",
	"P"=>"<span id=\"engtitleidx\">P</span><br><br>",
	"Q"=>"<span id=\"engtitleidx\">Q</span><br><br>",
	"R"=>"<span id=\"engtitleidx\">R</span><br><br>",
	"S"=>"<span id=\"engtitleidx\">S</span><br><br>",
	"T"=>"<span id=\"engtitleidx\">T</span><br><br>",
	"U"=>"<span id=\"engtitleidx\">U</span><br><br>",
	"V"=>"<span id=\"engtitleidx\">V</span><br><br>",
	"W"=>"<span id=\"engtitleidx\">W</span><br><br>",
	"X"=>"<span id=\"engtitleidx\">X</span><br><br>",
	"Y"=>"<span id=\"engtitleidx\">Y</span><br><br>",
	"Z"=>"<span id=\"engtitleidx\">Z</span><br><br>",
	"ETC"=>"<span id=\"engtitleidx\">ETC</span><br><br>");
	$hangul_title = array("ㄱ"=>"<span id=\"kortitleidx\">ㄱ</span><br><br>",
	"ㄴ"=>"<span id=\"kortitleidx\">ㄴ</span><br><br>",
	"ㄷ"=>"<span id=\"kortitleidx\">ㄷ</span><br><br>",
	"ㄹ"=>"<span id=\"kortitleidx\">ㄹ</span><br><br>",
	"ㅁ"=>"<span id=\"kortitleidx\">ㅁ</span><br><br>",
	"ㅂ"=>"<span id=\"kortitleidx\">ㅂ</span><br><br>",
	"ㅅ"=>"<span id=\"kortitleidx\">ㅅ</span><br><br>",
	"ㅇ"=>"<span id=\"kortitleidx\">ㅇ</span><br><br>",
	"ㅈ"=>"<span id=\"kortitleidx\">ㅈ</span><br><br>",
	"ㅊ"=>"<span id=\"kortitleidx\">ㅊ</span><br><br>",
	"ㅋ"=>"<span id=\"kortitleidx\">ㅋ</span><br><br>",
	"ㅌ"=>"<span id=\"kortitleidx\">ㅌ</span><br><br>",
	"ㅍ"=>"<span id=\"kortitleidx\">ㅍ</span><br><br>",
	"ㅎ"=>"<span id=\"kortitleidx\">ㅎ</span><br><br>",
	"기타"=>"<span id=\"kortitleidx\">기타</span><br><br>");

	$alphabet_search = array("<a href=\"{$_SERVER['PHP_SELF']}\"><span id=\"engbaridx\">TOTAL</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=A\"><span id=\"engbaridx\">A</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=B\"><span id=\"engbaridx\">B</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=C\"><span id=\"engbaridx\">C</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=D\"><span id=\"engbaridx\">D</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=E\"><span id=\"engbaridx\">E</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=F\"><span id=\"engbaridx\">F</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=G\"><span id=\"engbaridx\">G</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=H\"><span id=\"engbaridx\">H</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=I\"><span id=\"engbaridx\">I</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=J\"><span id=\"engbaridx\">J</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=K\"><span id=\"engbaridx\">K</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=L\"><span id=\"engbaridx\">L</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=M\"><span id=\"engbaridx\">M</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=N\"><span id=\"engbaridx\">N</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=O\"><span id=\"engbaridx\">O</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=P\"><span id=\"engbaridx\">P</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=Q\"><span id=\"engbaridx\">Q</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=R\"><span id=\"engbaridx\">R</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=S\"><span id=\"engbaridx\">S</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=T\"><span id=\"engbaridx\">T</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=U\"><span id=\"engbaridx\">U</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=V\"><span id=\"engbaridx\">V</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=W\"><span id=\"engbaridx\">W</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=X\"><span id=\"engbaridx\">X</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=Y\"><span id=\"engbaridx\">Y</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=Z\"><span id=\"engbaridx\">Z</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ETC\"><span id=\"engbaridx\">ETC</span></a>");
	$hangul_search = array("<a href=\"{$_SERVER['PHP_SELF']}\"><span id=\"korbaridx\">전체</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㄱ\"><span id=\"korbaridx\">ㄱ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㄴ\"><span id=\"korbaridx\">ㄴ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㄷ\"><span id=\"korbaridx\">ㄷ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㄹ\"><span id=\"korbaridx\">ㄹ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㅁ\"><span id=\"korbaridx\">ㅁ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㅂ\"><span id=\"korbaridx\">ㅂ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㅅ\"><span id=\"korbaridx\">ㅅ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㅇ\"><span id=\"korbaridx\">ㅇ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㅈ\"><span id=\"korbaridx\">ㅈ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㅊ\"><span id=\"korbaridx\">ㅊ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㅋ\"><span id=\"korbaridx\">ㅋ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㅌ\"><span id=\"korbaridx\">ㅌ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㅍ\"><span id=\"korbaridx\">ㅍ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=ㅎ\"><span id=\"korbaridx\">ㅎ</span></a>",
	"<a href=\"{$_SERVER['PHP_SELF']}?searchValue=기타\"><span id=\"korbaridx\">기타</span></a>");
	
	$alphabet_list = array();
	$hangul_list = array();
	$other_list = array();

	$sql = "SELECT bridx, brandname FROM tblproductbrand ";
	$sql.= "ORDER BY brandname ";
	get_db_cache($sql, $resval, "tblproductbrand.cache");
	
	$al_i=0;
	$han_j=0;
	while(list($key, $row)=each($resval)) {
		if(preg_match("/^[[:alpha:]]/", $row->brandname, $returnvalue)) {
			$alphabet_list[strtoupper($returnvalue[0])][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"engbrandidx\">{$row->brandname}</span></a>\n";
			$al_i++;
		} else if(($row->brandname>="ㄱ"&&$row->brandname<"ㅏ") || ($row->brandname>="가"&&$row->brandname<"")) {
			if(($row->brandname>="ㄱ"&&$row->brandname<"ㄴ") || ($row->brandname>="가"&&$row->brandname<"나")) {
				$hangul_list["ㄱ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㄴ"&&$row->brandname<"ㄷ") || ($row->brandname>="나"&&$row->brandname<"다")) {
				$hangul_list["ㄴ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㄷ"&&$row->brandname<"ㄹ") || ($row->brandname>="다"&&$row->brandname<"라")) {
				$hangul_list["ㄷ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㄹ"&&$row->brandname<"ㅁ") || ($row->brandname>="라"&&$row->brandname<"바")) {
				$hangul_list["ㄹ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㅁ"&&$row->brandname<"ㅂ") || ($row->brandname>="마"&&$row->brandname<"바")) {
				$hangul_list["ㅁ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㅂ"&&$row->brandname<"ㅅ") || ($row->brandname>="바"&&$row->brandname<"사")) {
				$hangul_list["ㅂ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㅅ"&&$row->brandname<"ㅇ") || ($row->brandname>="사"&&$row->brandname<"아")) {
				$hangul_list["ㅅ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㅇ"&&$row->brandname<"ㅈ") || ($row->brandname>="아"&&$row->brandname<"자")) {
				$hangul_list["ㅇ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㅈ"&&$row->brandname<"ㅊ") || ($row->brandname>="자"&&$row->brandname<"차")) {
				$hangul_list["ㅈ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㅊ"&&$row->brandname<"ㅋ") || ($row->brandname>="차"&&$row->brandname<"카")) {
				$hangul_list["ㅊ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㅋ"&&$row->brandname<"ㅌ") || ($row->brandname>="카"&&$row->brandname<"타")) {
				$hangul_list["ㅋ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㅌ"&&$row->brandname<"ㅍ") || ($row->brandname>="타"&&$row->brandname<"파")) {
				$hangul_list["ㅌ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else if(($row->brandname>="ㅍ"&&$row->brandname<"ㅎ") || ($row->brandname>="파"&&$row->brandname<"하")) {
				$hangul_list["ㅍ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			} else {
				$hangul_list["ㅎ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"korbrandidx\">{$row->brandname}</span></a>\n";
			}
			$han_j++;
		} else {
			$other_list[] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode={$row->bridx}\"><span id=\"engbrandidx\">{$row->brandname}</span></a>\n";
			$al_i++;
			$han_j++;
		}
	}

	$engsearchbar.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	$engsearchbar.= "<tr align=\"center\">\n";
	for($i=0; $i<count($alphabet_search); $i++) {
		$engsearchbar.= "	<td width=\"3%\">{$alphabet_search[$i]}</td>\n";
	}
	$engsearchbar.= "</tr>\n";
	$engsearchbar.= "</table>\n";

	$korsearchbar.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	$korsearchbar.= "<tr align=\"center\">\n";
	for($i=0; $i<count($hangul_search); $i++) {
		$korsearchbar.= "	<td width=\"6%\">{$hangul_search[$i]}</td>\n";
	}
	$korsearchbar.= "</tr>\n";
	$korsearchbar.= "</table>\n";
	
	if(($al_i+$han_j)>0) {
		$alphabet_list["ETC"] = $other_list; // 기타 내용 영문/한글 모두 출력
		$hangul_list["기타"] = $other_list; // 기타 내용 영문/한글 모두 출력

		$colval = "4";	// td 갯수
		$tdWidth = 100/$colval;

		if($al_i>0) {
			$brandalphabet = "";
			$brandalphabet.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$brandalphabet.= "<tr align=\"center\" valign=\"top\">\n";
			$brandalphabet.= "	<td width=\"{$tdWidth}%\" style=\"padding:10px;word-break:break-all;\">\n";

			$colal = ceil($al_i/$colval);

			$i=0;
			while(list($key1, $val1)=each($alphabet_list)) {
				if(count($alphabet_list[$key1])>0) {
					sort($alphabet_list[$key1]);
					$k=0;
					while(list($key2, $val2)=each($alphabet_list[$key1])) {
						if($colal==$i) {
							$brandalphabet.= "	</td>\n<td width=\"{$tdWidth}%\" style=\"padding:10px;word-break:break-all;\">";
							$i=0;
						}
						if($k==0) {
							$brandalphabet.= $alphabet_title[$key1];
						}
						$brandalphabet.= $val2."<br>";
						$i++;
						$k++;
					}
					$brandalphabet .= "<br><br>";
				}
			}

			if($al_i<$colval) {
				for($i=0; $i<$colval-$al_i; $i++) {
					$brandalphabet.= "	</td>\n<td width=\"{$tdWidth}%\" style=\"padding:10px;word-break:break-all;\">";
				}
			}

			$brandalphabet.= "		</td>\n";
			$brandalphabet.= "	</tr>\n";
			$brandalphabet.= "	</table>\n";
		}

		if($han_j>0) {
			$brandhangul = "";
			$brandhangul.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$brandhangul.= "<tr align=\"center\" valign=\"top\">\n";
			$brandhangul.= "	<td width=\"{$tdWidth}%\" style=\"padding:10px;word-break:break-all;\">\n";

			$colhan = ceil($han_j/$colval);

			$i=0;
			while(list($key1, $val1)=each($hangul_list)) {
				if(count($hangul_list[$key1])>0) {
					$k=0;
					while(list($key2, $val2)=each($hangul_list[$key1])) {
						if($colhan==$i) {
							$brandhangul.= "	</td>\n<td width=\"{$tdWidth}%\" style=\"padding:10px;word-break:break-all;\">";
							$i=0;
						}
						if($k==0) {
							$brandhangul.= $hangul_title[$key1];
						}
						$brandmap_list[$key1].= $val2."<br>";
						$brandhangul.= $val2."<br>";
						$i++;
						$k++;
					}
					$brandhangul.= "<br><br>";
				}
			}
			
			if($han_j<$colval) {
				for($i=0; $i<$colval-$han_j; $i++) {
					$brandhangul.= "	</td>\n<td width=\"{$tdWidth}%\" style=\"padding:10px;word-break:break-all;\">";
				}
			}
			$brandhangul.= "	</td>\n";
			$brandhangul.= "</tr>\n";
			$brandhangul.= "</table>\n";
		}

		if(ord($searchValue)) {
			if($searchValue == "ETC") {
				$searchType=1;
			} else if(preg_match("/^[[:alpha:]]/", $searchValue, $returnvalue)) {
				$searchValue = strtoupper($returnvalue[0]);
				$searchType=1;
			} else if($searchValue>="ㄱ"&&$searchValue<"ㅏ") {
			} else {
				$searchValue = "기타";
			}

			if($searchType>0) {
				$searchbar = $engsearchbar;
			} else {
				$searchbar = $korsearchbar;
			}
			
			$searchresult = "";
			$searchresult.= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$searchresult.= "<col width=\"100\"></col>\n";
			$searchresult.= "<col></col>\n";
			$searchresult.= "<tr>\n";
			$searchresult.= "	<td align=\"center\" valign=\"top\" style=\"padding:10px;\">".($searchType>0?$alphabet_title[$searchValue]:$hangul_title[$searchValue])."</td>\n";
			$searchresult.= "	<td>\n";
			$searchresult.= "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$searchresult.= "	<tr align=\"center\" valign=\"top\">\n";

			if($searchType>0) {
				$searchresult.= "		<td width=\"{$tdWidth}%\" style=\"padding:10px;word-break:break-all;\" id=\"engbrandidx\">\n";
				$result_count = count($alphabet_list[$searchValue]);
				if($result_count>0) {
					$colalsearch = ceil($result_count/$colval);
					sort($alphabet_list[$searchValue]);
					$i=0;
					for($j=0; $j<$result_count; $j++) {
						if($colalsearch==$i) {
							$searchresult.= "</td>\n<td width=\"{$tdWidth}%\" style=\"padding:10px;word-break:break-all;\" id=\"engbrandidx\">";
							$i=0;
						}
						$brandsearch_list[$searchValue].= $alphabet_list[$searchValue][$j]."<br>";
						$searchresult.= $alphabet_list[$searchValue][$j]."<br>";
						$i++;
					}
				}
			} else {
				$searchresult.= "		<td width=\"{$tdWidth}%\" style=\"padding:10px;word-break:break-all;\" id=\"korbrandidx\">\n";
				$result_count = count($hangul_list[$searchValue]);
				if($result_count>0) {
					$colhansearch = ceil($result_count/$colval);
					$i=0;
					for($j=0; $j<$result_count; $j++) {
						if($colhansearch==$i) {
							$searchresult.= "</td>\n<td width=\"{$tdWidth}%\" style=\"padding:10px;word-break:break-all;\" id=\"korbrandidx\">";
							$i=0;
						}
						$brandsearch_list[$searchValue].= $hangul_list[$searchValue][$j]."<br>";
						$searchresult.= $hangul_list[$searchValue][$j]."<br>";
						$i++;
					}
				}
			}

			if($result_count<$colval) {
				for($i=0; $i<$colval-$result_count; $i++) {
					$searchresult.= "	</td>\n<td width=\"{$tdWidth}%\" style=\"padding:10px;word-break:break-all;\">";
				}
			}

			$searchresult.= "		</td>\n";
			$searchresult.= "	</tr>\n";
			$searchresult.= "	</table>\n";
			$searchresult.= "	</td>\n";
			$searchresult.= "</tr>\n";
			$searchresult.= "</table>\n";
		}
	}
