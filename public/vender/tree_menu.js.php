<?php
if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST'])===FALSE) {
	header("HTTP/1.0 404 Not Found");
	exit;
} 
?>
// ********************************************************* 
// Auxiliary Variables for User Customize Folder-Trees
// ********************************************************* 
USR_Base_Target = "f_dw";                           //target을 생략할 경우 사용될 기본 target frame
USR_Frame_Col_Format = ",*";               //프레임 크기 재조정시 프레임 정의 문자열 포멧
USR_Base_Img_Directory = "images/tree/";                //관련아이콘의 기본디렉토리
USR_Use_Text_links = true;                              //글자에 Link를 적용할것인지의 여부

// ********************************************************* 
// Auxiliary Variables for User Customize Frame Auto resize
// ********************************************************* 
USR_Frame_Auto_Resize = false;               //메뉴의 크기에 따라 프레임크기를 재 조정할것인지의 여부
USR_Frame_Top = "";           //프레임 크기 재조정시 사용할 프레임을 정의한다.
USR_Frame_Interval = 30;                     //프레임 크기 재조정시 움직이는 단위를 조절한다.
USR_Frame_Margin = 40;                       //프레임 크기 재조정시 오른쪽 마진을 설정한다.

// ********************************************************* 
// Auxiliary Variables for User Customize Folder-Trees Icons
// ********************************************************* 
USR_Icon_Node_Last_Minus = USR_Base_Img_Directory + "last_minus.gif";
USR_Icon_Node_Last_Normal = USR_Base_Img_Directory + "last_normal.gif";
USR_Icon_Node_Last_Plus = USR_Base_Img_Directory + "last_plus.gif";
USR_Icon_Node_Blank = USR_Base_Img_Directory + "blank.gif";

USR_Icon_Node_Mid_Minus = USR_Base_Img_Directory + "mid_minus.gif";
USR_Icon_Node_Mid_Normal = USR_Base_Img_Directory + "mid_normal.gif";
USR_Icon_Node_Mid_Plus = USR_Base_Img_Directory + "mid_plus.gif";
USR_Icon_Node_Mid_Blank = USR_Base_Img_Directory + "mid_blank.gif";

USR_Icon_Folder_Open = USR_Base_Img_Directory + "open.gif";
USR_Icon_Folder_Close = USR_Base_Img_Directory + "close.gif";
USR_Icon_Folder_Blank = USR_Base_Img_Directory + "close.gif";


// ************************************************************* 
// Global variables 
// ************************************************************* 
doc = document;
browserVersion = (doc.all) ?  1 : 2; //브라우져를 체크한다. 1:IE, 2:NS

indexOfEntries = new Array;
nEntries = 0;
selectedFolder=0; 
rootFolderNode = null;
slideOpenInterval = null;
slideCloseInterval = null;

 
// ************************************************************* 
// Definition of class Folder (Capability Open or close) 
// ************************************************************* 
function Folder(description, fullLinkPath) //constructor 
{
    //constant data;
    this.description = description;
    this.hreference = fullLinkPath;
    
    this.id = -1;
    this.navObj = 0;
    this.iconImg = 0;
    this.nodeImg = 0;
    this.isLastNode = 0;
                                                             
    // dynamic data                                           
    this.isOpen = true;
    this.openIconSrc = null;
    this.closeIconSrc = null;
    this.children = new Array;
    this.nChildren = 0;
                                                              
    // methods
    this.addChild = addChild;
    this.initialize = initializeFolder;
    this.outputLink = outputFolderLink;

    //Pseudo-inheritance Method
    this.createIndex = createEntryIndex;
	
	if ( browserVersion == 1 ) //IE 5인경우
	{
		this.renderDisplay = drawFolder;
		this.setState = setStateFolder;
    	this.hide = hideFolder;
    	this.display = displayNode;
	}
	else // Netscape 4인경우
	{
    	this.renderDisplay = drawFolderNS;
    	this.setState = setStateFolderNS;
    	this.subEntries = folderSubEntriesNS;
    	this.hide = hideFolderNS;
    	this.display = displayNodeNS;
	}

}


// ************************************************************* 
// Definition of class Item (Node Inserted to Folder class) 
// ************************************************************* 
 
function Item(itemDescription, fullLinkPath) // Constructor 
{ 

  // constant data 
  this.description = itemDescription 

  this.hreference = fullLinkPath 
 
  this.id = -1     //initialized in initalize() 
  this.navObj = 0  //initialized in render() 
  this.iconImg = 0 //initialized in render() 

  this.iconSrc = null;
 
  // methods 
  this.initialize = initializeItem 

  //Pseudo-inheritance Method
  this.createIndex = createEntryIndex 


	if ( browserVersion == 1 ) //IE 5인경우
	{
  		this.renderDisplay = drawItem 
  		this.hide = hideItem 
  		this.display = displayNode 

	}
	else // Netscape 4인경우
	{
  		this.renderDisplay = drawItemNS 
  		this.totalHeight = totalHeightNS 
  		this.hide = hideItemNS 
  		this.display = displayNodeNS

	}
} 
 
// ************************************************************* 
// Definition method of class Folder
// ************************************************************* 

function addChild(childNode) 
{ 
  this.children[this.nChildren] = childNode ;
  this.nChildren++ ;
  return childNode ;
} 

function initializeFolder(level, lastNode, leftSide) 
{ 
    var i = 0;

    var nc;
    
    var htmlString = ""; //String For use Render Folder Node
    var nodeIcon = "";   //Source which Icon will be used - 바로옆에 붙는 작대기 (+,-)
    var leftIcon = "";   //Source which Icon will be used - 부모밑에 붙는 작대기
      
    nc = this.nChildren; //The Number of Child which folder has.

    this.createIndex();  //make special unique id (this.id)
 
	if (level > 0) {  //사용할 노드 아이콘을 결정한다.
		if(lastNode) {
			this.isLastNode = 1;
			nodeIcon = nc ? USR_Icon_Node_Last_Minus : USR_Icon_Node_Last_Normal;
			leftIcon = USR_Icon_Node_Blank;
		}
		else {
			this.isLastNode = 0;
			nodeIcon = nc ? USR_Icon_Node_Mid_Minus : USR_Icon_Node_Mid_Normal;
			leftIcon = USR_Icon_Node_Mid_Blank;
		}

	htmlString = leftSide + "<a href='javascript:clickOnNode("+this.id+")'>" 
  		         + "<img name='nodeIcon" + this.id + "' "
   		         + "src='" + nodeIcon + "' border=0></a>"

    leftSide = leftSide 
               + "<img src='" + leftIcon + "' border=0>";
  	}
	
    this.renderDisplay(htmlString);
  	
    if(nc > 0) {
        level = level + 1;
        for(i = 0; i < this.nChildren; i++) { 
            if(i == this.nChildren-1)
                this.children[i].initialize(level, 1, leftSide); //Last Node
            else 
                this.children[i].initialize(level, 0, leftSide); //Normal Node
        } 
    } 
} 

function drawFolder(leftBranch) 
{ 
   
  doc.write("<table ") 
  doc.write(" id='folder" + this.id + "' style='position:block;' ") 
  doc.write(" border=0 cellspacing=0 cellpadding=0>") 
  doc.write("<tr>")

  doc.write("<td>") 
  doc.write(leftBranch) 
  this.outputLink("clickOnNode"); 
  doc.write("<img name='folderIcon" + this.id + "' ") 
  doc.write("src='" + this.openIconSrc  + "' border=0></a>") 
  doc.write("</td>")

  //doc.write("<td nowrap>") 
  doc.write("<td nowrap id='currClick' onClick=\"bolderDisplay(this);\"  >") //2005.01.07 추가
  doc.write("<DIV CLASS=\"fldrfldr\">");

  if (USR_Use_Text_links) 
  { 
    this.outputLink("clickOnFolder"); 
    //doc.write(this.description +"(" +this.nChildren + ")</a>") 
    doc.write(this.description + "</a>") 
  } 
  else 
    doc.write(this.description) 

  doc.write("</DIV>");
  doc.write("</td></tr>")  
  doc.write("</table>") 
   
  this.navObj = doc.all["folder"+this.id] 
  this.iconImg = doc.all["folderIcon"+this.id] 
  this.nodeImg = doc.all["nodeIcon"+this.id] 
  
} 


function drawFolderNS(leftBranch) 
{ 
  if (!doc.yPos) 
	  doc.yPos=8 
  doc.write("<layer id='folder" + this.id + "' top=" + doc.yPos + " visibility=hiden>") 
   
  doc.write("<table ") 
  doc.write(" border=0 cellspacing=0 cellpadding=0>") 
  doc.write("<tr><td>") 
  doc.write(leftBranch) 
  this.outputLink("clickOnNode") 
  doc.write("<img name='folderIcon" + this.id + "' ") 
  doc.write("src='" + this.openIconSrc  + "' border=0></a>") 
  doc.write("</td><td nowrap>") 
  doc.write("<DIV CLASS=\"fldrfldr\">");

  if (USR_Use_Text_links) 
  { 
    this.outputLink("clickOnFolder"); 
    doc.write(this.description + "</a>") 
  } 
  else 
    doc.write(this.description) 

  doc.write("</DIV>");
  doc.write("</td></tr>")  
  doc.write("</table>") 
   
  doc.write("</layer>") 

    this.navObj = doc.layers["folder"+this.id] 
    this.iconImg = this.navObj.document.images["folderIcon"+this.id] 
    this.nodeImg = this.navObj.document.images["nodeIcon"+this.id] 
    doc.yPos=doc.yPos+this.navObj.clip.height 
} 


function outputFolderLink(eventCode) 
{
  if (this.hreference) 
  { 
    doc.write("<a href=" + this.hreference ); //include target
    doc.write("onClick='javascript:" + eventCode + "("+this.id+")'") 
    doc.write(">") 
  } 
  else 
    doc.write("<a href='javascript:" + eventCode + "("+this.id+")'>")   
} 

function setStateFolder(isOpen)
{ 
  if(isOpen == this.isOpen) 
      return 
 
  this.isOpen = isOpen; 
  doPropagateChange(this); 
} 


function setStateFolderNS(isOpen)
{ 
  var subEntries; 
  var totalHeight; 
  var fIt = 0; 
  var i = 0; 
 
  if(isOpen == this.isOpen) 
      return 
 
  totalHeight = 0 
  for(i = 0; i < this.nChildren; i++) 
      totalHeight = totalHeight + this.children[i].navObj.clip.height; 
      subEntries = this.subEntries(); 

      if(this.isOpen) 
          totalHeight = 0 - totalHeight; 

      for(fIt = this.id + subEntries + 1; fIt < nEntries; fIt++) 
          indexOfEntries[fIt].navObj.moveBy(0, totalHeight); 

  this.isOpen = isOpen; 
  doPropagateChange(this); 
} 

function folderSubEntriesNS() 
{ 
  var i = 0 
  var se = this.nChildren 
 
  for (i=0; i < this.nChildren; i++){ 
    if (this.children[i].children) //is a folder 
      se = se + this.children[i].subEntries() 
  } 
 
  return se 
} 

function totalHeightNS() 
{ 
  var h = this.navObj.clip.height 
  var i = 0 
   
  if (this.isOpen) //is a folder and _is_ open 
    for (i=0 ; i < this.nChildren; i++)  
      h = h + this.children[i].totalHeight() 
 
  return h 
} 

function hideFolder() 
{ 
	if(this.navObj.style.display == "none")
    	return 
	this.navObj.style.display = "none"
    this.setState(0) 
}

function hideFolderNS() 
{ 
    if (this.navObj.visibility == "hiden") 
      return 
    this.navObj.visibility = "hiden" 

    this.setState(0) 
} 

// ************************************************************* 
// Definition method of class Item
// ************************************************************* 
function initializeItem(level, lastNode, leftSide) 
{ 

    this.createIndex();  //make special unique id (this.id)

    var htmlString = ""; //String For use Render Folder Node
    var nodeIcon = "";   //Source which Icon will be used - 바로옆에 붙는 작대기 (+,-)
      
	if (level > 0) {  //사용할 노드 아이콘을 결정한다.
		if(lastNode) {
			nodeIcon = USR_Icon_Node_Last_Normal;
		}
		else {
			nodeIcon = USR_Icon_Node_Mid_Normal;
		}

	htmlString = leftSide 
  		         + "<img src='" + nodeIcon + "' border=0></a>"
  	}
	
    this.renderDisplay(htmlString);

} 


function drawItem(leftBranch) 
{ 

  doc.write("<table ") 
  doc.write(" id='item" + this.id + "' style='position:block;' ") 
  doc.write(" border=0 cellspacing=0 cellpadding=0>") 
  doc.write("<tr><td>") 
  doc.write(leftBranch) 

  if(this.hreference)
      doc.write("<a href=" + this.hreference + ">") 

  if(this.iconSrc)
  {
    doc.write("<img id='itemIcon"+this.id+"' ") 
    doc.write("src='"+this.iconSrc+"' align=absMiddle  border=0>") 
  }

  if(this.hreference)
      doc.write("</a>") 

  //doc.write("</td><td nowrap>") 
    doc.write("</td><td nowrap id='currClick' onClick=\"bolderDisplay(this);\"  >") //2005.01.07 추가
  
  doc.write("<DIV CLASS=\"fldritem\">");

  if (USR_Use_Text_links) {
  	if(this.hreference)
    	    doc.write("<a href=" + this.hreference + ">" + this.description + "</a>") 
  	else
    	doc.write(this.description)       
  }
  else {
    doc.write(this.description) 
  }

  doc.write("</DIV>");

  doc.write("</table>") 
  
  this.navObj = doc.all["item"+this.id] 
  this.iconImg = doc.all["itemIcon"+this.id] 
} 

function drawItemNS(leftBranch) 
{ 
  doc.write("<layer id='item" + this.id + "' top=" + doc.yPos + " visibility=hiden>") 
    
  doc.write("<table ") 
  doc.write(" border=0 cellspacing=0 cellpadding=0>") 
  doc.write("<tr><td>") 
  doc.write(leftBranch) 

  if(this.hreference)
      doc.write("<a href=" + this.hreference + ">") 

  if(this.iconSrc)
  {
    doc.write("<img id='itemIcon"+this.id+"' ") 
    doc.write("src='"+this.iconSrc+"' border=0>") 
  }

  if(this.hreference)
      doc.write("</a>") 

  doc.write("</td><td nowrap>") 
  
  doc.write("<DIV CLASS=\"fldritem\">");
  if (USR_Use_Text_links) {
  	if(this.hreference)
    	    doc.write("<a href=" + this.hreference + ">" + this.description + "</a>") 
  	else
    	doc.write(this.description)       
  }
  else {
    doc.write(this.description) 
  }

  doc.write("</DIV>");
  doc.write("</table>") 
  doc.write("</layer>") 
 
  this.navObj = doc.layers["item"+this.id] 
  this.iconImg = this.navObj.document.images["itemIcon"+this.id] 
  doc.yPos=doc.yPos+this.navObj.clip.height 
} 


function hideItem() 
{ 
	if(this.navObj.style.display == "none")
    	return 
	this.navObj.style.display = "none"
} 

function hideItemNS() 
{ 
    if (this.navObj.visibility == "hiden") 
      return 
    this.navObj.visibility = "hiden" 
} 
// ************************************************************* 
// Definition method of both Folder and Item (pseudo-inheritance)
// ************************************************************* 

function createEntryIndex() 
{ 
  this.id = nEntries 
  indexOfEntries[nEntries] = this 
  nEntries++ 
} 

function displayNode() 
{ 
  this.navObj.style.display = "block" 
} 

function displayNodeNS() 
{ 
    this.navObj.visibility = "show" 
} 
 
// ************************************************************* 
// Definition Events method 
// ************************************************************* 

function clickOnFolder(folderId) 
{ 
  var clicked = indexOfEntries[folderId] 
 
  if (!clicked.isOpen) 
    clickOnNode(folderId) 
 
  //return  
 
  //if (clicked.isSelected) 
    //return 
} 
 

function clickOnNode(folderId) 
{ 
  var clickedFolder = 0 
  var state = 0 
 
  clickedFolder = indexOfEntries[folderId] 
  state = clickedFolder.isOpen 
 
  clickedFolder.setState(!state) //open<->close  
  
  if (USR_Frame_Auto_Resize) //프레임 재조정설정이 되어 있다면
		frameSlideResize(!state);
} 

function doPropagateChange(folder) //폴더의 상태를 바꾼다.
{   
  var i=0 

  var nc;

  nc = folder.nChildren; //The Number of Child which folder has.
     
  if (folder.isOpen) //폴더를 연다.
  { 
    if (folder.nodeImg) 
      if (folder.isLastNode) 
        folder.nodeImg.src = nc ? USR_Icon_Node_Last_Minus : USR_Icon_Node_Last_Normal;  
      else 
   		folder.nodeImg.src = nc ? USR_Icon_Node_Mid_Minus : USR_Icon_Node_Mid_Normal;
   		
    folder.iconImg.src = folder.openIconSrc 

    for (i=0; i<folder.nChildren; i++) 
      folder.children[i].display() 
      
  } 
  else // 폴더를 닫는다.
  { 
    if (folder.nodeImg) 
      if (folder.isLastNode) 
        folder.nodeImg.src = nc ? USR_Icon_Node_Last_Plus : USR_Icon_Node_Last_Normal;  
      else 
        folder.nodeImg.src = nc ? USR_Icon_Node_Mid_Plus : USR_Icon_Node_Mid_Normal;  

    folder.iconImg.src = folder.closeIconSrc 

    for (i=0; i<folder.nChildren; i++) 
      folder.children[i].hide() 
  }  
} 

function frameSlideResize(state)
{


  if ( browserVersion  == 2 ) return;
  
  var sWidth = document.all["top_table"].scrollWidth ? 
  	( document.all["top_table"].scrollWidth + USR_Frame_Margin ) :
  	( document.all["top_table"][0].scrollWidth + USR_Frame_Margin ) ;
  //alert(USR_Frame_Top);
  var fWidth = parseInt(USR_Frame_Top.cols);

  if (state)
  {
	    //alert("open : " + sWidth + ":" + fWidth);
  	if (sWidth > fWidth) { //프레임을 연다.
  		clearTimeout(slideCloseInterval);
  		slideOpenInterval=setTimeout("frameSlideOpen()", 1);  
  		}
  }
  else 
  {
	    //alert("close : " + sWidth + ":" + fWidth);
  	if (sWidth< fWidth) { //프레임을 닫는다.
  		clearTimeout(slideOpenInterval);
  		slideCloseInterval=setTimeout("frameSlideClose()", 1);  
  		}
  }

}

function frameSlideOpen()
{

  var sWidth = document.all["top_table"].scrollWidth ? 
  	( document.all["top_table"].scrollWidth + USR_Frame_Margin ) :
  	( document.all["top_table"][0].scrollWidth + USR_Frame_Margin ) ;

  var reCol = parseInt(USR_Frame_Top.cols);
  
  if ( sWidth > reCol + USR_Frame_Interval) // 이번에 더하는것으로 만족하지 못한다면
	  reCol = reCol + USR_Frame_Interval;
  else
      reCol = reCol + 2;

  USR_Frame_Top.cols = reCol + USR_Frame_Col_Format;

  if (sWidth > reCol) { // 프레임 재조정 이후에도 여전히 트리메뉴가 넓다면
        clearTimeout(slideCloseInterval);
  		slideOpenInterval=setTimeout("frameSlideOpen()", 1);  
  }

}

function frameSlideClose()
{

  var sWidth = document.all["top_table"].scrollWidth ? 
  	( document.all["top_table"].scrollWidth + USR_Frame_Margin ) :
  	( document.all["top_table"][0].scrollWidth + USR_Frame_Margin ) ;

  var reCol = parseInt(USR_Frame_Top.cols);

  if ( sWidth < reCol - USR_Frame_Interval) // 이번에 빼는것으로 만족하지 못한다면
	  reCol = reCol - USR_Frame_Interval;
  else
      reCol = reCol - 2;

  USR_Frame_Top.cols = reCol + USR_Frame_Col_Format

  if (sWidth < reCol) { // 프레임 재조정 이후에도 여전히 작다면.
        clearTimeout(slideOpenInterval);
  		slideCloseInterval=setTimeout("frameSlideClose()", 1);  
  }

}

// ********************************************************* 
// Auxiliary Functions for User Generated Folder-Trees
// ********************************************************* 

function genFolder(description, hreference, target) 
{ 
    //description은 필수, hreference 및 target은 생략 가능하다.

	var fullLinkPath = null;
	    
    if ( hreference ) {
		if ( target == null ) target = USR_Base_Target;
		fullLinkPath = "'" + hreference + "' target='" + target +"' ";
	}
	
    folder = new Folder("&nbsp;" + description, fullLinkPath);

    //set Icons 
    folder.openIconSrc = USR_Icon_Folder_Open;
    folder.closeIconSrc = USR_Icon_Folder_Close;

    return folder;

} 

function genFolderRoot(description, hreference, target) 
{ 
    //description은 필수, hreference 및 target은 생략 가능하다.

	var fullLinkPath = null;
	    
    if ( hreference ) {
		if ( target == null ) target = USR_Base_Target;
		fullLinkPath = "'" + hreference + "' target='" + target +"' ";
	}
	
    folder = new Folder(description, fullLinkPath);
    
    //set Icons 
    folder.openIconSrc = USR_Icon_Folder_Blank;
    folder.closeIconSrc = USR_Icon_Folder_Blank;
    
	    return folder;

} 

function genFolderIcon(openIcon, closeIcon, description, hreference, target) 
{ 
    //openIcon, closeIcon, description은 필수, hreference 및 target은 생략 가능하다.

	var fullLinkPath = null;
	    
    if ( hreference ) {
		if ( target == null ) target = USR_Base_Target;
		fullLinkPath = "'" + hreference + "' target='" + target +"' ";
	}
	
    folder = new Folder("&nbsp;" + description, fullLinkPath);
    
    //set Icons 
    folder.openIconSrc = USR_Base_Img_Directory + openIcon;
    folder.closeIconSrc = USR_Base_Img_Directory + closeIcon;
    
	    return folder;

} 

function genItem(description, hreference, target) 
{ 

    //description은 필수, hreference 및 target은 생략 가능하다.

	var fullLinkPath = null;
	    
    if ( hreference ) {
		if ( target == null ) target = USR_Base_Target;
		fullLinkPath = "'" + hreference + "' target='" + target +"' ";
	}

   childNode = new Item(description, fullLinkPath);

   return childNode;
} 

function genItemIcon(itemIcon, description, hreference, target) 
{ 

    //itemIcon, description은 필수, hreference 및 target은 생략 가능하다.

	var fullLinkPath = null;
	    
    if ( hreference ) {
		if ( target == null ) target = USR_Base_Target;
		fullLinkPath = "'" + hreference + "' target='" + target +"' ";
	}

   childNode = new Item(description, fullLinkPath);
   
   childNode.iconSrc =USR_Base_Img_Directory + itemIcon;

   return childNode;
} 


function insFolder(parentFolder, childFolder) 
{ 
  return parentFolder.addChild(childFolder);
} 
 
function insItem(parentFolder, item) 
{ 
  parentFolder.addChild(item);
} 
 
// ********************************************************* 
// Auxiliary Functions for User initialized Folder-Trees
// ********************************************************* 

function initializeDocument(rootFolders) 
{ 

    rootFolderNode = rootFolders;  //set root folder for close icon

	if ( browserVersion  == 1 )
	  doc.write("<table id='top_table' border=0 cellspacing=0 cellpadding=0 ><tr><td>");
    
    rootFolders.initialize(0, 1, "");

	if ( browserVersion  == 1 )
	  doc.write("</td></tr></table>");
	
	if ( browserVersion  == 2 )
	    rootFolders.display();

    doc.write("<layer top="+indexOfEntries[nEntries-1].navObj.top+"></layer>");

    // close the whole tree
    clickOnNode(0) 

    // open the root folder 
    clickOnNode(0) 
  
} 
 
function drawAutoResize(htmlString, defaultValue)
{
	if ( browserVersion  == 2 ) return;

	doc.write("<form>");
	doc.write("<DIV CLASS=\"fldritem\">");
	doc.write("<input type='checkbox' onClick='changeResize(this.checked)' ");
	if (defaultValue)
		doc.write(" checked ");
	doc.write(">");
	doc.write(htmlString);
    doc.write("</DIV>");
	doc.write("</form>");
	changeResize(defaultValue);
}

function changeResize(chgValue) {
   USR_Frame_Auto_Resize = chgValue;
}

function setBaseTarget(chgValue) {

   USR_Base_Target = chgValue;
}

function setFreamColFormat(chgValue) {
   USR_Frame_Col_Format = chgValue;
}

function setTop(topObj) {
   USR_Frame_Top = topObj;
}


// 2005.01.07 game093추가, 현재 선택된 노드 진하게.
function bolderDisplay(obj){
	for(var idx=0;idx < doc.all['currClick'].length; idx++){
		currClick[idx].style.fontWeight='lighter';	
	}	
	obj.style.fontWeight='bolder';
}
