<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />
<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />

<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
<script src="template/js/function.js?v=<?=VERID?>" type="text/javascript"></script>
<style  type="text/css">
.border_line {
width:94% !important;
}
.main_car{
width:1000px;
}

</style>
<script type="text/javascript">
function do_order_status(oid,ty)
{	
if(ty == "cancel")
{
var altmsg = "确定要取消该订单吗?";
}else if(ty == "uncancel"){
var altmsg = "确定要恢复该订单吗?";
}else{
var altmsg = "你确认已收到货物了吗?";
}

if(confirm(altmsg))
{
$.post("myorder.php",
{m:ty, ID: oid, Content: $('#data_OrderContent').val()},
function(data){
if(data == "ok"){						
$.growlUI("提交成功，正在载入页面...");
window.location.reload();
}else{
$.growlUI(data);
}
}			
);
}	
}

function do_order_guestbook(oid)
{
if($('#data_OrderContent').val() == "")
{
$.growlUI("请输入留言内容！");
}else{
$.post("myorder.php",
{m:"sub_guestbook", ID: oid, Content: $('#data_OrderContent').val()},
function(data){	
if(data == "ok"){						
$.growlUI("提交成功，正在载入页面...");
window.location.reload();
}else{
$.growlUI(data);
}
}			
);
}
}

function show_bak_order(oid,ty)
{

if(ty=="show")
{
$('#showoldorder').css("width","100%");
if($('#showoldorder').html() == "")
{
$('#showoldorder').html('<iframe src="myorder.php?m=showoldorder&id='+oid+'" width="100%" marginwidth="0" height="400" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
}
$("#showoldorder").animate({opacity: 'show'}, 'slow');
$("#show_oldbutton").html('<input type="button" value="隐藏原始订单数据" class="button_4" name="viewoldorder" id="viewoldorder"  onclick="show_bak_order(\'<?=$order['orderinfo']['OrderID']?>\',\'hide\')" />');
}else{
$("#showoldorder").animate({opacity: 'hide'}, 'slow');
$("#show_oldbutton").html('<input type="button" value="查看原始订单数据" class="button_4" name="viewoldorder" id="viewoldorder"  onclick="show_bak_order(\'<?=$order['orderinfo']['OrderID']?>\',\'show\')" />');
}

}

function closewindowui()
{
$.unblockUI();
//window.setTimeout($.unblockUI, 1000);
}

function do_order_copy(oid)
{
if(oid != "")
{
window.location.href='myorder.php?m=copyorder&oid='+oid;
}
}
</script>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置： <a href="home.php">首页</a> / <a href="myorder.php">我的订单</a> / <a href="#">订单详细</a></div>

<div class="car_tit"><span class="iconfont icon-gouwuche" style="margin-left:10px;font-size:18px;color: #ffb236"></span>   我的订单</div>
<div class="main_car">

<br class="clear" />
<div class="border_line">
<div class="line font14">订单号：
                        <span class="font14h"><?=$order['orderinfo']['OrderSN']?> 
<? if($order['orderinfo']['OrderType']=="M") { ?>
&nbsp;&nbsp;(管理端代下单)
<? } ?>
</span>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        状态：<span class="font14h"><?=$order_status_arr[$order['orderinfo']['OrderStatus']]?></span>
                        
<? if($order['orderinfo']['OrderSpecial']=='T') { ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        订单类型：<span class="font14h">特价订单</span>
                        
<? } ?>
                    </div>
<div class="line" align="right">下单时间：<? echo date("Y-m-d H:i",$order['orderinfo']['OrderDate']); ?></div>
</div>

<br class="clear" />
<div class="border_line">
<div class="line font14" style="padding-bottom:8px;">订单信息：</div>

<div class="line bgw">
<div class="bgw2" >
<div class="line2 font12">收货信息：</div>
<div class="line2">
<strong>收货人/公司：</strong><?=$order['orderinfo']['OrderReceiveCompany']?><br />
<strong>联 系 人：</strong><?=$order['orderinfo']['OrderReceiveName']?><br />
<strong>联系电话：</strong><?=$order['orderinfo']['OrderReceivePhone']?><br />
<strong>收货地址：</strong><?=$order['orderinfo']['OrderReceiveAdd']?><br />						
</div>
</div>

<div class="bgw2">
<div class="line2 font12">支付及配送方式：</div>
<div class="line2">
<strong>配送方式：</strong><?=$sendtypearr[$order['orderinfo']['OrderSendType']]?><br />
<strong>配送状态：</strong><span class="font12h"><?=$send_status_arr[$order['orderinfo']['OrderSendStatus']]?></span><br />
<strong>支付方式：</strong><?=$paytypearr[$order['orderinfo']['OrderPayType']]?><br />
<strong>支付状态：</strong><span class="font12h"><?=$pay_status_arr[$order['orderinfo']['OrderPayStatus']]?></span>&nbsp;&nbsp;
<? if($order['orderinfo']['OrderPayStatus']=="3") { ?>
¥ <?=$order['orderinfo']['OrderIntegral']?>
<? } ?>
&nbsp;&nbsp;
<? if($order['orderinfo']['OrderStatus']!="8" && $order['orderinfo']['OrderStatus'] != "9") { if(empty($order['orderinfo']['OrderPayStatus']) || $order['orderinfo']['OrderPayStatus']=="3") { ?>
<a href="finance.php?m=new&id=<?=$order['orderinfo']['OrderID']?>" style="width:80px; height:26px;border-radius:2px;text-align:center;color: white; padding:2px; background-color:#FF8E32;" >+ 添加付款单</a>
<? } } ?>
<br />
</div>
</div>
<? if($order['orderinfo']['InvoiceType'] == "P" || $order['orderinfo']['InvoiceType'] == "Z") { ?>
    <br class="clear" />
    <div class="line2">
<span class="font12">开票信息：</span>
</div>
<div class="bgw2" style="height:114px;">
<div class="line2">
<strong>开票类型：</strong><font color=red>
<? if($order['invoice']['InvoiceType']=="P") { ?>
普通发票
<? } else { ?>
增值税发票
<? } ?>
</font><br />
<strong>开票抬头：</strong><?=$order['invoice']['InvoiceHeader']?><br />
<strong>开票内容：</strong><?=$order['invoice']['InvocieContent']?><br />
<strong>开票状态：</strong>
<? if($order['invoice']['InvoiceFlag']=="T") { ?>
<font color="red">已开票</font> 日期：<? echo date("Y-m-d",$order['invoice']['InvoiceSendDate']); ?></font>
<? } else { ?>
未开
<? } ?>
</div>
</div>

    <div class="bgw2" style="height:114px;">
    
<? if($order['orderinfo']['InvoiceType'] == "Z") { ?>
<div class="line2">
<strong>纳税人识别号：</strong><?=$order['invoice']['TaxpayerNumber']?><br />
<strong>开户名称：</strong><?=$order['invoice']['AccountName']?><br />
<strong>开户银行：</strong><?=$order['invoice']['BankName']?><br />
<strong>银行账号：</strong><?=$order['invoice']['BankAccount']?>
</div>
<? } ?>
</div>
<? } if(!empty($order['orderinfo']['DeliveryDate']) && $order['orderinfo']['DeliveryDate'] != "0000-00-00") { ?>
<div class="line2" style="height:30px;">
<span class="font12">交货日期：</span><font color="red"><?=$order['orderinfo']['DeliveryDate']?></font>
</div>
<? } ?>
<div class="line2">
<span class="font12">特殊要求说明：</span><br /><? echo nl2br($order['orderinfo']['OrderRemark']); ?></div>
<br class="clear" />
</div>
</div>

<br class="clear" />
<div class="line bgw">
<div class="line font14" style="height: 38px;line-height:38px;text-align:left;background-color: #F4FAF6;">&nbsp;&nbsp;商品清单：</div>
<div class="line">

  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <thead>
  <tr>
    <td width="6%" height="28">&nbsp;序号</td>
    <td>&nbsp;商品名称</td>
    <!-- <td width="18%">生产厂家</td> -->
    <!-- <td width="10%">规格</td> -->
    <td width="18%">品牌</td>
    <td width="10%">规格</td>
    <td width="8%" style="display:none">&nbsp;颜色/规格</td>
    <td width="6%" align="right">数量</td>
    <td width="6%" align="right">单位</td>
    <td width="6%" align="right">单价</td>
<!--     <td width="6%" align="right">折扣</td>
    <td width="6%" align="right">折后价</td> -->
    <td width="12%" align="right">小计(元)&nbsp;&nbsp;</td> 
  </tr>
   </thead>
   <tbody>
    
<? if(is_array($order['ordercart'])) { foreach($order['ordercart'] as $cartkey => $cartvar) { ?>
    <tr id="linegoods_<?=$cartvar['kid']?>" 
<? if(fmod($cartkey,2)==0) { ?>
 style="background-color:#F4FAF6;"
<? } else { ?>
style="background-color:#ffffff;" 
<? } ?>
  >
    <td height="30">&nbsp;<? echo $cartkey+1; ?></td>
    <td><div  title="<?=$cartvar['ContentName']?>"><a href="content.php?id=<?=$cartvar['ContentID']?>" target="_blank"><?=$cartvar['ContentName']?></a></div></td>
    <td ><?=$cartvar['BrandName']?>&nbsp;</td>
    <td ><?=$cartvar['Model']?>&nbsp;</td>
    <td style="display:none">&nbsp;
        
<? if(strlen($cartvar['ContentColor']) > 0 ) { ?>
<?=$cartvar['ContentColor']?>
<? } ?>
        /
        
<? if(strlen($cartvar['ContentSpecification']) > 0) { ?>
<?=$cartvar['ContentSpecification']?>
<? } ?>
    </td>
    <td align="right"><?=$cartvar['ContentNumber']?> </td>
    <td align="right"><?=$cartvar['Units']?></td>
<!--     <td align="right">¥ <?=$cartvar['ContentPrice']?> </td>
    <td align="right"><?=$cartvar['ContentPercent']?></td> -->
    <td align="right">¥ <?=$cartvar['Price_End']?></td>
    <td class="font12" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;¥ <?=$cartvar['notetotal']?></td>
  </tr>
   
<? } } ?>
 
  <tr>
    <td>&nbsp;</td>
    
    <td height="28" class="font14">合计：</td>
    <td>&nbsp;</td>
    <td >&nbsp;</td>
    <td class="font12" align="right"><?=$order['totalnumber']?></td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
<!--     <td>&nbsp;</td>
    <td>&nbsp;</td> -->
    <td class="font12" align="center">

        
<? if($order['orderinfo']['OrderSpecial']=='T') { ?>
        <span style="text-decoration:line-through;">省：¥ <?=$order['stair_count']?></span><br/>
        <span style="color:#F60;">特价：¥ <? echo $order['totalpure'] - $order['stair_count']; ?></span>&nbsp;
        
<? } else { ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;¥ <?=$order['totalprice']?>&nbsp;
        
<? } ?>
    </td>
  </tr>
<? if($order['totaltax'] > 0) { ?>
  <tr>
    <td>&nbsp;</td>
    
    <td height="28" class="font14">税点：</td>
    <td>&nbsp;</td>
    <td >&nbsp;</td>
    <td class="font12" align="right"></td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td class="font12" align="right">¥ <?=$order['totaltax']?>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    
    <td height="28" class="font14">总计：</td>
    <td>&nbsp;</td>
    <td >&nbsp;</td>
    <td class="font12" align="right"></td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td class="font12h" align="right">¥ <? echo ($order['orderinfo']['OrderTotal']);; ?>&nbsp;</td>
  </tr>
<? } ?>
   </tbody>
</table>
</div>
<? if($ordergifts['ordercart']) { ?>
<div class="line font12" style="padding:2px;">&nbsp;赠品清单：</div>
<div class="line">

  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <thead>
  <tr>
    <td width="6%" height="28">&nbsp;序号</td>
    <td width="12%">编号/货号</td>
    <td>&nbsp;商品名称</td>
    <td width="16%">&nbsp;颜色/规格</td>
    <td width="8%" align="right">数量</td>
    <td width="4%" align="right">单位</td>
    <td width="12%" align="right">单价</td>
    <td width="14%" align="right">小计(元)&nbsp;&nbsp;</td> 
  </tr>
   </thead>
   <tbody>
    
<? if(is_array($ordergifts['ordercart'])) { foreach($ordergifts['ordercart'] as $cartkey => $cartvar) { ?>
    <tr id="linegoods_<?=$cartvar['kid']?>" 
<? if(fmod($cartkey,2)==0) { ?>
 style="background-color:#f9f9f9;"
<? } else { ?>
style="background-color:#ffffff;" 
<? } ?>
  >
    <td height="30">&nbsp;<? echo $cartkey+1; ?></td>
    <td ><?=$cartvar['Coding']?> &nbsp;</td>
    <td><div  title="<?=$cartvar['ContentName']?>"><a href="content.php?id=<?=$cartvar['ContentID']?>" target="_blank"><?=$cartvar['ContentName']?></a></div></td>
    <td>&nbsp;
<? if(!empty($cartvar['ContentColor'])) { ?>
<?=$cartvar['ContentColor']?>
<? } ?>
 / 
<? if(!empty($cartvar['ContentSpecification'])) { ?>
<?=$cartvar['ContentSpecification']?>
<? } ?>
</td>
    <td align="right"><?=$cartvar['ContentNumber']?> </td>
    <td align="right"><?=$cartvar['Units']?> </td>
    <td align="right">¥ <?=$cartvar['ContentPrice']?> </td>
    <td class="font12" align="right">¥ <?=$cartvar['notetotal']?></td>
  </tr>
   
<? } } ?>
 
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td height="28" class="font14">合计：</td>
    <td>&nbsp;</td>
    <td class="font12" align="right"><?=$ordergifts['totalnumber']?></td>
    <td >&nbsp;</td>
    <td>&nbsp;</td>
    <td class="font12" align="right">¥ <?=$ordergifts['totalprice']?>&nbsp;</td>
  </tr>
   </tbody>
</table>
</div>
<? } ?>
<div class="line2 " align="right" >
<input type="button" value="导出订单" class="button_2" name="copybutton" id="copybutton"  onclick="window.location.href = 'order_content_excel.php?ID=<?=$order['orderinfo']['OrderID']?>'"/>

<input type="button" value="复制订单,再次订购" class="button_5" name="copybutton" id="copybutton"  onclick="do_order_copy('<?=$order['orderinfo']['OrderID']?>')"/>
<? if(!empty($isbak)) { ?>
<span id="show_oldbutton">
<input type="button" value="查看原始订单数据" class="button_4" name="viewoldorder" id="viewoldorder"  onclick="show_bak_order('<?=$order['orderinfo']['OrderID']?>','show')" />
</span>
<? } ?>
</div>
<div class="line" id="showoldorder"></div>
</div>

<br class="clear" />
<div class="border_line">
<div class="line font14" style="padding-bottom:8px;">订单跟踪：<a name="showsubmitlocation"></a></div>
<div class="line bgw">
<div class="line">
  <table width="99%" border="0" cellspacing="0" cellpadding="0" >
  <thead>
  <tr>
    <td width="16%" height="28">&nbsp;时间</td>
    <td width="18%">&nbsp;操作人</td>
    <td width="18%">&nbsp;动作</td>
    <td >说明</td>
  </tr>
   </thead>
   <tbody>
<? if(is_array($ordersubmit)) { foreach($ordersubmit as $skey => $svar) { ?>
  <tr id="linesub_<?=$svar['ID']?>" 
<? if(fmod($skey,2)==0) { ?>
 style="background-color:#f9f9f9;"
<? } else { ?>
style="background-color:#ffffff;" 
<? } ?>
  >
    <td height="28">&nbsp;<? echo date("Y-m-d H:i",$svar['Date']); ?></td>
<td><?=$svar['AdminUser']?> / <?=$svar['Name']?> </td>
    <td class="font12"><?=$svar['Status']?>	</td>
<td ><?=$svar['Content']?>&nbsp;</td>
  </tr>
  
<? } } ?>
  </tbody>
  </table>
</div></div>
<br class="clearfloat" />
<div class="line bgw">
<div class="line2 font12">操作(说明/原因/留言)</div>
<div class="line2">
<textarea name="data_OrderContent" rows="4"  id="data_OrderContent" style="padding-left:10px;width:100%;height:80px;border:1px solid #ABADB3;"></textarea>
          				</div>
<div class="line2">
<input type="button" value="我要留言" class="button_3" name="guestbookconfirmbtn" id="guestbookconfirmbtn"  onclick="do_order_guestbook('<?=$order['orderinfo']['OrderID']?>')"/>
<? if($order['orderinfo']['OrderStatus']=="0") { ?>
<input type="button" value="取消订单" class="button_2" name="confirmbtn1"  id="confirmbtn1"  onclick="do_order_status('<?=$order['orderinfo']['OrderID']?>','cancel')"/>
<? } if($order['orderinfo']['OrderSendStatus']=="2") { ?>
<input type="button" value="确认已收货" class="button_1" name="confirmbtn2" id="confirmbtn2"  onclick="do_order_status('<?=$order['orderinfo']['OrderID']?>','confirmincept')"/>
<? } ?>
</div>
<br class="clearfloat" />&nbsp;
</div>
</div>		
<br class="clearfloat" />&nbsp;
</div>
</div>

    <div id="windowForm">
<div class="windowHeader">
<h3 id="windowtitle">查看原始订单数据：</h3>
<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
</div>
<div id="windowContent">
              
        </div>
</div>
<? include template('bottom'); ?>
</body>
</html>