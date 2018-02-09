<?
	$alphabet_title = array("A"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">A</span><br><br>",
	"B"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">B</span><br><br>",
	"C"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">C</span><br><br>",
	"D"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">D</span><br><br>",
	"E"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">E</span><br><br>",
	"F"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">F</span><br><br>",
	"G"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">G</span><br><br>",
	"H"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">H</span><br><br>",
	"I"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">I</span><br><br>",
	"J"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">J</span><br><br>",
	"K"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">K</span><br><br>",
	"L"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">L</span><br><br>",
	"M"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">M</span><br><br>",
	"N"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">N</span><br><br>",
	"O"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">O</span><br><br>",
	"P"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">P</span><br><br>",
	"Q"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">Q</span><br><br>",
	"R"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">R</span><br><br>",
	"S"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">S</span><br><br>",
	"T"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">T</span><br><br>",
	"U"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">U</span><br><br>",
	"V"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">V</span><br><br>",
	"W"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">W</span><br><br>",
	"X"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">X</span><br><br>",
	"Y"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">Y</span><br><br>",
	"Z"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">Z</span><br><br>",
	"ETC"=>"<span style=\"font-family:Tahoma;font-size:40px;font-weight:bold;\">ETC</span><br><br>");
	$hangul_title = array("ㄱ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㄱ</span><br><br>",
	"ㄴ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㄴ</span><br><br>",
	"ㄷ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㄷ</span><br><br>",
	"ㄹ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㄹ</span><br><br>",
	"ㅁ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㅁ</span><br><br>",
	"ㅂ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㅂ</span><br><br>",
	"ㅅ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㅅ</span><br><br>",
	"ㅇ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㅇ</span><br><br>",
	"ㅈ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㅈ</span><br><br>",
	"ㅊ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㅊ</span><br><br>",
	"ㅋ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㅋ</span><br><br>",
	"ㅌ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㅌ</span><br><br>",
	"ㅍ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㅍ</span><br><br>",
	"ㅎ"=>"<span style=\"font-family:돋움,굴림;font-size:30px;font-weight:bold;\">ㅎ</span><br><br>",
	"기타"=>"<span style=\"font-family:돋움,굴림;font-size:24px;font-weight:bold;\">기타</span><br><br>");

	$alphabet_search = array("<a href=\"".$_SERVER[PHP_SELF]."\"><span style=\"font-family:Tahoma;font-size:20px;\">TOTAL</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=A\"><span style=\"font-family:Tahoma;font-size:20px;\">A</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=B\"><span style=\"font-family:Tahoma;font-size:20px;\">B</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=C\"><span style=\"font-family:Tahoma;font-size:20px;\">C</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=D\"><span style=\"font-family:Tahoma;font-size:20px;\">D</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=E\"><span style=\"font-family:Tahoma;font-size:20px;\">E</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=F\"><span style=\"font-family:Tahoma;font-size:20px;\">F</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=G\"><span style=\"font-family:Tahoma;font-size:20px;\">G</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=H\"><span style=\"font-family:Tahoma;font-size:20px;\">H</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=I\"><span style=\"font-family:Tahoma;font-size:20px;\">I</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=J\"><span style=\"font-family:Tahoma;font-size:20px;\">J</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=K\"><span style=\"font-family:Tahoma;font-size:20px;\">K</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=L\"><span style=\"font-family:Tahoma;font-size:20px;\">L</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=M\"><span style=\"font-family:Tahoma;font-size:20px;\">M</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=N\"><span style=\"font-family:Tahoma;font-size:20px;\">N</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=O\"><span style=\"font-family:Tahoma;font-size:20px;\">O</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=P\"><span style=\"font-family:Tahoma;font-size:20px;\">P</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=Q\"><span style=\"font-family:Tahoma;font-size:20px;\">Q</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=R\"><span style=\"font-family:Tahoma;font-size:20px;\">R</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=S\"><span style=\"font-family:Tahoma;font-size:20px;\">S</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=T\"><span style=\"font-family:Tahoma;font-size:20px;\">T</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=U\"><span style=\"font-family:Tahoma;font-size:20px;\">U</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=V\"><span style=\"font-family:Tahoma;font-size:20px;\">V</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=W\"><span style=\"font-family:Tahoma;font-size:20px;\">W</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=X\"><span style=\"font-family:Tahoma;font-size:20px;\">X</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=Y\"><span style=\"font-family:Tahoma;font-size:20px;\">Y</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=Z\"><span style=\"font-family:Tahoma;font-size:20px;\">Z</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ETC\"><span style=\"font-family:Tahoma;font-size:20px;\">ETC</span></a>");
	$hangul_search = array("<a href=\"".$_SERVER[PHP_SELF]."\"><span style=\"font-family:돋움,굴림;font-size:14px;font-weight:bold;\">전체</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㄱ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㄱ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㄴ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㄴ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㄷ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㄷ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㄹ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㄹ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㅁ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㅁ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㅂ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㅂ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㅅ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㅅ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㅇ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㅇ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㅈ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㅈ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㅊ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㅊ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㅋ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㅋ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㅌ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㅌ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㅍ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㅍ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=ㅎ\"><span style=\"font-family:돋움,굴림;font-size:20px;font-weight:bold;\">ㅎ</span></a>",
	"<a href=\"".$_SERVER[PHP_SELF]."?searchValue=기타\"><span style=\"font-family:돋움,굴림;font-size:14px;font-weight:bold;\">기타</span></a>");
	
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
			$alphabet_list[strtoupper($returnvalue[0])][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			$al_i++;
		} else if(($row->brandname>="ㄱ"&&$row->brandname<"ㅏ") || ($row->brandname>="가"&&$row->brandname<"")) {
			if(($row->brandname>="ㄱ"&&$row->brandname<"ㄴ") || ($row->brandname>="가"&&$row->brandname<"나")) {
				$hangul_list["ㄱ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㄴ"&&$row->brandname<"ㄷ") || ($row->brandname>="나"&&$row->brandname<"다")) {
				$hangul_list["ㄴ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㄷ"&&$row->brandname<"ㄹ") || ($row->brandname>="다"&&$row->brandname<"라")) {
				$hangul_list["ㄷ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㄹ"&&$row->brandname<"ㅁ") || ($row->brandname>="라"&&$row->brandname<"바")) {
				$hangul_list["ㄹ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㅁ"&&$row->brandname<"ㅂ") || ($row->brandname>="마"&&$row->brandname<"바")) {
				$hangul_list["ㅁ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㅂ"&&$row->brandname<"ㅅ") || ($row->brandname>="바"&&$row->brandname<"사")) {
				$hangul_list["ㅂ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㅅ"&&$row->brandname<"ㅇ") || ($row->brandname>="사"&&$row->brandname<"아")) {
				$hangul_list["ㅅ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㅇ"&&$row->brandname<"ㅈ") || ($row->brandname>="아"&&$row->brandname<"자")) {
				$hangul_list["ㅇ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㅈ"&&$row->brandname<"ㅊ") || ($row->brandname>="자"&&$row->brandname<"차")) {
				$hangul_list["ㅈ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㅊ"&&$row->brandname<"ㅋ") || ($row->brandname>="차"&&$row->brandname<"카")) {
				$hangul_list["ㅊ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㅋ"&&$row->brandname<"ㅌ") || ($row->brandname>="카"&&$row->brandname<"타")) {
				$hangul_list["ㅋ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㅌ"&&$row->brandname<"ㅍ") || ($row->brandname>="타"&&$row->brandname<"파")) {
				$hangul_list["ㅌ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else if(($row->brandname>="ㅍ"&&$row->brandname<"ㅎ") || ($row->brandname>="파"&&$row->brandname<"하")) {
				$hangul_list["ㅍ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			} else {
				$hangul_list["ㅎ"][] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			}
			$han_j++;
		} else {
			$other_list[] = "<A HREF=\"".$Dir.FrontDir."productblist.php?brandcode=".$row->bridx."\">".$row->brandname."</a>\n";
			$al_i++;
			$han_j++;
		}
	}
	
	if(($al_i+$han_j)>0) {
		$alphabet_list["ETC"] = $other_list; // 기타 내용 영문/한글 모두 출력
		$hangul_list["기타"] = $other_list; // 기타 내용 영문/한글 모두 출력

		$colval = "4";	// td 갯수
		$tdWidth = 100/$colval;

		if($al_i>0) {
			$brandalphabet.= "";
			$brandalphabet.= "<tr>\n";
			$brandalphabet.= "	<td>\n";
			$brandalphabet.= "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$brandalphabet.= "	<tr>\n";
			$brandalphabet.= "		<td style=\"border:1px #C6C0AE solid;padding:5px;\" bgcolor=\"#E7E4DB\">\n";
			$brandalphabet.= "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$brandalphabet.= "		<tr align=\"center\">\n";

			for($i=0; $i<count($alphabet_search); $i++) {
				$brandalphabet.= "			<td width=\"3%\">".$alphabet_search[$i]."</td>\n";
			}

			$brandalphabet.= "		</tr>\n";
			$brandalphabet.= "		</table>\n";
			$brandalphabet.= "		</td>\n";
			$brandalphabet.= "	</tr>\n";
			$brandalphabet.= "	<tr>\n";
			$brandalphabet.= "		<td height=\"30\"></td>\n";
			$brandalphabet.= "	</tr>\n";
			$brandalphabet.= "	<tr>\n";
			$brandalphabet.= "		<td>\n";
			$brandalphabet.= "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$brandalphabet.= "		<tr align=\"center\" valign=\"top\">\n";
			$brandalphabet.= "			<td width=\"".$tdWidth."%\" style=\"padding:10px;word-break:break-all;\">\n";

			$colal = ceil($al_i/$colval);

			$i=0;
			while(list($key1, $val1)=each($alphabet_list)) {
				if(count($alphabet_list[$key1])>0) {
					sort($alphabet_list[$key1]);
					$k=0;
					while(list($key2, $val2)=each($alphabet_list[$key1])) {
						if($colal==$i) {
							$brandalphabet.= "			</td>\n<td width=\"".$tdWidth."%\" style=\"padding:10px;word-break:break-all;\">";
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
					$brandalphabet.= "	</td>\n<td width=\"".$tdWidth."%\" style=\"padding:10px;word-break:break-all;\">";
				}
			}

			$brandalphabet.= "			</td>\n";
			$brandalphabet.= "		</tr>\n";
			$brandalphabet.= "		</table>\n";
			$brandalphabet.= "		</td>\n";
			$brandalphabet.= "	</tr>\n";
			$brandalphabet.= "	</table>\n";
			$brandalphabet.= "	</td>\n";
			$brandalphabet.= "</tr>\n";
			$brandalphabet.= "<tr>\n";
			$brandalphabet.= "	<td height=\"20\"></td>\n";
			$brandalphabet.= "</tr>\n";
		}

		if($han_j>0) {
			$brandhangul.= "<tr>\n";
			$brandhangul.= "	<td>\n";
			$brandhangul.= "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$brandhangul.= "	<tr>\n";
			$brandhangul.= "		<td style=\"border:1px #C6C0AE solid;padding:5px;\" bgcolor=\"#E7E4DB\">\n";
			$brandhangul.= "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$brandhangul.= "		<tr align=\"center\">\n";

			for($i=0; $i<count($hangul_search); $i++) {
				$brandhangul.= "			<td width=\"6%\">".$hangul_search[$i]."</td>\n";
			}

			$brandhangul.= "		</tr>\n";
			$brandhangul.= "		</table>\n";
			$brandhangul.= "		</td>\n";
			$brandhangul.= "	</tr>\n";
			$brandhangul.= "	<tr>\n";
			$brandhangul.= "		<td height=\"30\"></td>\n";
			$brandhangul.= "	</tr>\n";
			$brandhangul.= "	<tr>\n";
			$brandhangul.= "		<td>\n";
			$brandhangul.= "		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
			$brandhangul.= "		<tr align=\"center\" valign=\"top\">\n";
			$brandhangul.= "			<td width=\"".$tdWidth."%\" style=\"padding:10px;word-break:break-all;\">\n";

			$colhan = ceil($han_j/$colval);

			$i=0;
			while(list($key1, $val1)=each($hangul_list)) {
				if(count($hangul_list[$key1])>0) {
					$k=0;
					while(list($key2, $val2)=each($hangul_list[$key1])) {
						if($colhan==$i) {
							$brandhangul.= "			</td>\n<td width=\"".$tdWidth."%\" style=\"padding:10px;word-break:break-all;\">";
							$i=0;
						}
						if($k==0) {
							$brandhangul.= $hangul_title[$key1];
						}
						$brandhangul.= $val2."<br>";
						$i++;
						$k++;
					}
					$brandhangul.= "<br><br>";
				}
			}

			if($han_j<$colval) {
				for($i=0; $i<$colval-$han_j; $i++) {
					$brandhangul.= "	</td>\n<td width=\"".$tdWidth."%\" style=\"padding:10px;word-break:break-all;\">";
				}
			}

			$brandhangul.= "			</td>\n";
			$brandhangul.= "		</tr>\n";
			$brandhangul.= "		</table>\n";
			$brandhangul.= "		</td>\n";
			$brandhangul.= "	</tr>\n";
			$brandhangul.= "	</table>\n";
			$brandhangul.= "	</td>\n";
			$brandhangul.= "</tr>\n";
			$brandhangul.= "<tr>\n";
			$brandhangul.= "	<td height=\"20\"></td>\n";
			$brandhangul.= "</tr>\n";
		}
?>

<table cellpadding="0" cellspacing="0" width="100%">
<?
		if(strlen($searchValue)>0) {
			if($searchValue == "ETC") {
				$searchType=1;
			} else if(preg_match("/^[[:alpha:]]/", $searchValue, $returnvalue)) {
				$searchValue = strtoupper($returnvalue[0]);
				$searchType=1;
			} else if($searchValue>="ㄱ"&&$searchValue<"ㅏ") {
			} else {
				$searchValue = "기타";
			}
?>
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td style="border:1px #CCCCCC solid;padding:10px;padding-left:5px;padding-right:5px;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="border:1px #C6C0AE solid;padding:5px;" bgcolor="#E7E4DB">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr align="center">
<?
			if($searchType>0) {
				for($i=0; $i<count($alphabet_search); $i++) {
						echo "<td width=\"3%\">".$alphabet_search[$i]."</td>\n";
				}
			} else {
				for($i=0; $i<count($hangul_search); $i++) {
						echo "<td width=\"6%\">".$hangul_search[$i]."</td>\n";
				}
			}
?>

				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td height="30"></td>
			</tr>
			<tr>
				<td>
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<col width="100"></col>
				<col></col>
				<tr>
					<td align="center" valign="top" style="padding:10px;"><?=($searchType>0?$alphabet_title[$searchValue]:$hangul_title[$searchValue])?></td>
					<td>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr align="center" valign="top">
						<td width="<?=$tdWidth?>%" style="padding:10px;word-break:break-all;">
<?
			if($searchType>0) {
				$result_count = count($alphabet_list[$searchValue]);
				if($result_count>0) {
					$colalsearch = ceil($result_count/$colval);
					sort($alphabet_list[$searchValue]);
					$i=0;
					for($j=0; $j<$result_count; $j++) {
						if($colalsearch==$i) {
							echo "</td>\n<td width=\"".$tdWidth."%\" style=\"padding:10px;word-break:break-all;\">";
							$i=0;
						}
						echo $alphabet_list[$searchValue][$j]."<br>";
						$i++;
					}
				}
			} else {
				$result_count = count($hangul_list[$searchValue]);
				if($result_count>0) {
					$colhansearch = ceil($result_count/$colval);
					$i=0;
					for($j=0; $j<$result_count; $j++) {
						if($colhansearch==$i) {
							echo "</td>\n<td width=\"".$tdWidth."%\" style=\"padding:10px;word-break:break-all;\">";
							$i=0;
						}
						echo $hangul_list[$searchValue][$j]."<br>";
						$i++;
					}
				}
			}

			if($result_count<$colval) {
				for($i=0; $i<$colval-$result_count; $i++) {
					echo "</td>\n<td width=\"".$tdWidth."%\" style=\"padding:10px;word-break:break-all;\">";
				}
			}
?>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
	</td>
</tr>
<?
		}
?>
<tr>
	<td style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td height="20"></td>
	</tr>
	<tr>
		<td style="border:1px #CCCCCC solid;padding:10px;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="20"></td>
		</tr>
<?
		if($_data->ETCTYPE["BRANDMAPT"]=="Y") {
			echo $brandhangul.$brandalphabet;
		} else {
			echo $brandalphabet.$brandhangul;
		}
?>
		</table>
		</td>
	</tr>
	<tr>
		<td height="20"></td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?
	}
?>
