<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<link href="<?=CONF_PATH_IMG?>css/showpage.css" rel="stylesheet" type="text/css">

<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
<script type="text/javascript">
function do_return_guestbook(rtype)
{	
if($('#ReturnAbout').val() == "")
{
$.growlUI("请输入退货原因！");
}else if($('#ReturnProductW').val() == "" || $('#ReturnProductB').val() == ""){
$.growlUI("请选择产品外观和包装情况！");
}else{
$('#returnsubbutton').attr("disabled","disabled");
if(rtype=="order")
{
$.post("return.php?m=sub_returnadd", $("#formreturn").serialize(),
function(data){
data = Trim(data);
if(data == "ok"){
$.growlUI("提交成功，请等待供货商审核！");
window.location.href='return.php';					
}else{
$.growlUI(data);
$('#returnsubbutton').attr("disabled","");
}
}			
);
}else{
$.post("return.php?m=sub_returnadd_product", $("#formreturn").serialize(),
function(data){
data = Trim(data);
if(data == "ok"){
$.growlUI("提交成功，请等待供货商审核！");	
window.location.href='return.php';					
}else{
$.growlUI(data);
$('#returnsubbutton').attr("disabled","");
}
}			
);
}
}
}

function closewindowui()
{
$.unblockUI();
}

function upload_file(fildname)
{
$("#windowtitle").html('上传图片');
$('#windowContent').html('<iframe src="plugin/jqUploader/uploadfile.php" width="500" marginwidth="0" height="250" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
$.blockUI({ 
message: $('#windowForm'),
css:{ 
                width: '540px',height:'280px',top:'15%'
            }			
});
    $('#set_filename').val(fildname);
$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}
function setinputfile(fpn)
{

var filevalue = $('#set_filename').val();
if(fpn!='' && fpn!=null)
{
$("#"+filevalue).val(fpn);
//$("#"+filevalue+"_text").html('[<a href="<?=RESOURCE_PATH?>'+fpn+'" target="_blank">预览图片</a>]');
}

$.unblockUI();
}
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置： <a href="home.php">首页</a> / <a href="return.php">退货管理</a> / <a href="javascript:;">退货申请</a></div>
<div class="main_left">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>   退货管理</div>
  <div class="news_info">
  <ul>
    <li><a href="return.php?m=returnadd" ><span class="ali-small-circle iconfont icon-next-s"></span>退货申请</a>
<li><a href="return.php" ><span class="ali-small-circle iconfont icon-next-s"></span>退货单查询</a>
<? if(is_array($return_status_arr)) { foreach($return_status_arr as $skey => $svar) { if($skey==$in['status'] && isset($in['status'])) { ?>
<dd><a href="return.php?status=<?=$skey?>" ><strong><span class="ali-small-sanjiao iconfont icon-icon-copy-copy1"></span><?=$svar?></strong></a></dd>
<? } else { ?>
<dd><a href="return.php?status=<?=$skey?>" >  <?=$svar?></a></dd>
<? } } } ?>
</li>

             <li><a href="myorder.php?m=product" ><span class="ali-small-circle iconfont icon-next-s"></span>我订过的商品</a>	</li>
  </ul>

  </div>
<div class="fenlei_bottom"><img src="<?=CONF_PATH_IMG?>images/info_bottom.jpg" /></div>

</div>

<div class="main_right">

<div class="right_product_tit">
<div class="xs_0"><span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   退货申请</div>
</div>

<div class="right_product_main">
<div class="list_line">
<? if($rtype=="order") { ?>
<br class="clear" />
<div class="border_line">
<form name="searchform" id="searchform" action="return.php?m=returnadd" method="post">
<input type="hidden" name="set_filename" id="set_filename" value="" />
<div class="line"><span class="font14">&nbsp;订单号：</span>&nbsp;<input name="sn" id="sn" type="text" class="inputsearch global-border" onfocus="this.select();" value="<?=$in['sn']?>" />&nbsp;<input name="searchbutton" value="查询" type="submit" class="button_6" /> &nbsp;&nbsp; <font class=test_1>（请输入订单号点击查询,选择您要退货的商品.）</font> </div>
</form>
</div>
<? if(!empty($in['id'])) { ?>
 <form id="formreturn" name="formreturn" method="post" action="">
    <input name="orderid" id="orderid" type="hidden" value="<?=$in['sn']?>"  />
<br class="clear" />
<div class="border_line">
<div class="line bgw">
<div class="line"><span class="font14">&nbsp;订单商品</span> (请选择您要退货的商品,退货数量必需少于可退数)</div>
<div class="line2">

  <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
  <thead>
  <tr>
    <td width="6%" height="28">&nbsp;ID</td>
    <td>&nbsp;商品名称</td>
    <td width="10%">颜色</td>
<td width="10%">规格</td>
    <td width="12%" align="right">可退数</td>
<td width="12%" align="right">退货数</td>
    <td width="12%" align="right">订购价</td>
  </tr>
   </thead>
   <tbody>
<? if(is_array($order['ordercart'])) { foreach($order['ordercart'] as $cartkey => $cartvar) { ?>
    <tr id="linegoods_<?=$cartvar['ID']?>" 
<? if(fmod($cartkey,2)==0) { ?>
 style="background-color:#f9f9f9;"
<? } else { ?>
style="background-color:#ffffff;" 
<? } ?>
  >
    <td height="30" >&nbsp;<input type="hidden" value="<?=$cartvar['ID']?>" name="cartid[]" id="cartid_<?=$cartvar['ID']?>" /><? echo $cartkey+1; ?></td>
    <td><div title="<?=$cartvar['ContentName']?>"><a href="content.php?id=<?=$cartvar['ContentID']?>" target="_blank"><?=$cartvar['ContentName']?></a></div></td>
    <td>
<? if(!empty($cartvar['ContentColor'])) { ?>
<?=$cartvar['ContentColor']?>
<? } ?>
&nbsp;</td>
<td>
<? if(!empty($cartvar['ContentSpecification'])) { ?>
<?=$cartvar['ContentSpecification']?>
<? } ?>
&nbsp;</td>
    <td align="right"><?=$cartvar['rnumber']?>	</td>
<td align="right"><input name="cart_num[]" id="cart_num_<?=$cartvar['ID']?>" type="text" size="6" maxlength="6"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" style="text-align:right; width:50px;" value="0"  /> </td>
    <td class="font12" align="right">¥ <?=$cartvar['Price_End']?></td>
    </tr>
   
<? } } ?>
   </tbody>
</table>
</div>
</div>
</div>
<? } else { if(!empty($in['sn'])) { ?>
<br class="clear" />
<div class="border_line" align="center"><font color=test_1>无符合条件的数据!</font></div>
<? } } } else { ?>
<br class="clear" />
<div class="border_line">
<form name="searchform" id="searchform" action="return.php?m=returnadd" method="post">
<div class="line"><span class="font14">&nbsp;商&nbsp;&nbsp; 品：</span>&nbsp;<input name="kw" id="kw" type="text" class="inputsearch" onfocus="this.select();" value="<?=$in['kw']?>" />&nbsp;<input name="searchbutton" value="查询" type="submit" class="button_6" /> &nbsp;&nbsp; <font color=test_1>（请输入商品名称点击查询,选择您要退货的商品.）</font> </div>
</form>
</div>
<? if(!empty($in['pid'])) { ?>
 <form id="formreturn" name="formreturn" method="post" action="">
    <input name="orderid" id="orderid" type="hidden" value="<?=$in['sn']?>"  />
<br class="clear" />
<div class="border_line">
<div class="line bgw">
<div class="line"><span class="font14">&nbsp;订单商品</span> (请选择您要退货的商品,退货数量必需少于可退数)</div>
<div class="line2">

  <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
  <thead>
  <tr>
    <td width="6%" height="28">&nbsp;ID</td>
    <td>&nbsp;商品名称</td>
    <td width="10%">颜色</td>
<td width="10%">规格</td>
    <td width="12%" align="right">可退数</td>
<td width="12%" align="right">退货数</td>
    <td width="12%" align="right">订购价</td>
  </tr>
   </thead>
   <tbody>
<? if(is_array($pinfo)) { foreach($pinfo as $cartkey => $cartvar) { if($cartvar['rnumber'] > 0) { ?>
    <tr id="linegoods_<?=$n?>" 
<? if(fmod($n,2)==0) { ?>
 style="background-color:#f9f9f9;"
<? } else { ?>
style="background-color:#ffffff;" 
<? } ?>
  >
    <td height="30" >
<input type="hidden" value="<?=$cartkey?>" name="cartid[]"  />
<input type="hidden" value="<? echo urlencode(serialize($cartvar));; ?>" name="cartdata[]"  />
&nbsp;<? echo $n++; ?></td>
    <td><div title="<?=$cartvar['ContentName']?>"><a href="content.php?id=<?=$cartvar['ContentID']?>" target="_blank"><?=$cartvar['ContentName']?></a></div></td>
    <td>
<? if(!empty($cartvar['ContentColor'])) { ?>
<?=$cartvar['ContentColor']?>
<? } ?>
&nbsp;</td>
<td>
<? if(!empty($cartvar['ContentSpecification'])) { ?>
<?=$cartvar['ContentSpecification']?>
<? } ?>
&nbsp;</td>
    <td align="right"><?=$cartvar['rnumber']?>	</td>
<td align="right"><input name="cart_num[]"  type="text" size="6" maxlength="6"  onKeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" onfocus="this.select();" style="text-align:right; width:50px;" value="0"  /> </td>
    <td class="font12" align="right">¥ <?=$cartvar['Price_End']?></td>
    </tr>
<? } ?>
    
<? } } ?>
   </tbody>
</table>
</div>
</div>
</div>
<? } else { if(!empty($in['kw'])) { ?>
<br class="clear" />
<div class="border_line" align="center"><font color=test_1>无符合条件的数据!</font></div>
<? } } } ?>
<br class="clear" />
<div class="border_line">
<div class="line bgw">
<div class="line2">

  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td width="12%">&nbsp;<strong>货运方式:</strong></td>
<td>

<span id="rblQuery"><span ><input id="ReturnSendType1" type="radio" name="ReturnSendType" value="送货"  /><label for="rblQuery_0">送货 （直接到公司退货）</label></span><br />
<span ><input id="ReturnSendType2" type="radio" name="ReturnSendType" value="发货" checked="checked"  /><label for="rblQuery_1">发货 （通过快递，货运把商品寄公司库房）</label></span><br />

</td>
  </tr>
  <tr>
    <td >&nbsp;<strong>货运说明:</strong></td>
<td><textarea name="ReturnSendAbout" rows="3"  id="ReturnSendAbout" style="width:80%; height:48px;"></textarea></td>
  </tr>
  <tr>
    <td >&nbsp;<strong>外观包装:</strong></td>
<td>                                        产品外观：<select name="ReturnProductW" id="ReturnProductW" style="width:100px;">
<option value="">---请选择---</option>
<option value="良好">良好</option>
<option value="有划痕">有划痕</option>
<option value="外观有破损">外观有破损</option> 
</select>&nbsp;<font color=test_1>*</font>
                                        &nbsp;&nbsp; 包装情况：<select name="ReturnProductB" id="ReturnProductB" style="width:100px;">
<option value="">---请选择---</option>
<option value="无包装">无包装</option>
<option value="包装破损">包装破损</option>
<option value="包装完整">包装完整</option> 
</select>&nbsp;<font color=test_1>*</font>
</td>
  </tr>
<tr>
  <td >&nbsp;<strong>上传图片:</strong></div></td>
  <td ><label>
<input type="text" name="ReturnPicture" id="ReturnPicture"  class="input1" style="width:80%;" />
&nbsp;<input name="bt_Picture" type="button" class="button"  onClick="upload_file('ReturnPicture');" value="..." title="上传" style="width:40px; font-size:12px;">
  </label></td>
</tr>
  <tr>
    <td >&nbsp;<strong>退货原因:</strong></td>
<td><textarea name="ReturnAbout" rows="5"  id="ReturnAbout" style="width:80%;"></textarea>&nbsp;<font color="test_1">*</font></td>
  </tr>

</table>
</div>
</div>
</div>
<? if(!empty($in['id'])) { ?>
<br class="clear" />
<div class="border_line">
<div class="line bgw">
<div class="line2" align="right"><input type="button" value=" 提 交 " class="button_3" name="returnsubbutton" id="returnsubbutton" onclick="do_return_guestbook('order');"  /></div>				
</div></div>
<? } if(!empty($in['pid'])) { ?>
<br class="clear" />
<div class="border_line">
<div class="line bgw">
<div class="line2" align="right"><input type="button" value=" 提 交 " class="button_3" name="returnsubbutton" id="returnsubbutton" onclick="do_return_guestbook('product');"  /></div>				
</div></div>
<? } ?>
</form>				
<br />&nbsp;
</div>

</div>
</div>
</div>
<? include template('bottom'); ?>
    <div id="windowForm">
<div class="windowHeader">
<h3 id="windowtitle">请选择退货商品</h3>
<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
</div>
<div id="windowContent">
正在载入数据...       
        </div>
</div>

</body>
</html>