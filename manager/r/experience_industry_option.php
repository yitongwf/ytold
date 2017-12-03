<?php 
$menu_flag = "manager";
include_once ("header.php");

if(DHB_RUNTIME_MODE !== 'experience'){
	exit('not experience error!');
}

$arrIndustryOption = array();
$arrTempOption = $db->get_row("select *from ".DATABASEU.DATATABLE."_ty_option where Name='industry' ");
if(!empty($arrTempOption['Value'])){
	$arrIndustryOption = json_decode($arrTempOption['Value'],true);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>
    

    <div id="bodycontent">
    	<div class="lineblank"></div>        

		<div id="searchline">
        	<div class="leftdiv">
        	 <div class="locationl"><a name="editname"></a><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250;<a href="experience.php">体验入口</a>&#8250;&#8250; <a href="experience_industry.php">行业管理</a>&#8250;&#8250; 行业配置</div>
          </div>
         
        </div>

    	
        <div class="line2"></div>
        
        <div class="warning">
			注：这里用于设置新的体验模式下面每个行业初始化生成数量和启用那些行业。
		</div>
        
        <div class="bline" >
       

<div id="">
 <form id="MainForm" name="MainForm" method="post" target=""  action="">
             <table width="100%" cellspacing="0" cellpadding="0" border="0">
               <thead>
                <tr>
                  <td width="10%" class="bottomlinebold">行业号</td>
                  <td width="40%" class="bottomlinebold">行业</td>
                  <td width="30%" class="bottomlinebold">数量</td>
				  <td width="10%" class="bottomlinebold">启用</td>
                </tr>
     		 </thead>      		
      		<tbody>
		      	<?php 
		      	$sortarr = $db->get_results("SELECT IndustryID,IndustryName,IndustryAbout,IndustryOrder FROM ".DATABASEU.DATATABLE."_order_industry ORDER BY IndustryID ASC ");
				foreach($sortarr as $svar): ?>
               <tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" id="line_<?php echo $svar['IndustryID'];?>" style="">
                  <td><?php echo $svar['IndustryID'];?></td>
                  <td><?php echo $svar['IndustryName'];?></td>
                  <td><input type="text" name="num_value[<?php echo $svar['IndustryID'];?>]" value="<?php echo array_key_exists($svar['IndustryID'], $arrIndustryOption) ? $arrIndustryOption[$svar['IndustryID']] : 10;?>" /></td>
				  <td><input type="checkbox" name="set_value[<?php echo $svar['IndustryID'];?>]" value="1" <?php if(array_key_exists($svar['IndustryID'], $arrIndustryOption)):?>checked<?php endif;?> /></td>
                </tr>
                <?php endforeach; ?>
 				</tbody>                
              </table>
            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_ty_industry();" />
			</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
      </form>

        </div>
              
          </div>    
        <br style="clear:both;" />

    </div>
    
    <? include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>