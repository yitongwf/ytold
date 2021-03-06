<?php
$menu_flag = "statistics";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

$clientdata = $db->get_results("select c.ClientID,c.ClientCompanyName,c.ClientCompanyPinyi from ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID  where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." and c.ClientFlag=0 order by c.ClientCompanyPinyi asc");
$clientIds = array();
foreach($clientdata as $val){
    $clientIds[] = $val['ClientID'];
}
$sdmsg = '';
$locationmsg = '';
$valuearr = get_set_arr('product');
setcookie("backurl", $_SERVER['REQUEST_URI']);
if(empty($in['beginDate'])){
    $in['beginDate'] = date('Y-m-d',strtotime('-1 months'));
}
if(empty($in['endDate'])){
    $in['endDate'] = date('Y-m-d');
}
$where = "";

$goods_sql = "SELECT * FROM rsung_order_content_index WHERE ID=".$in['ID'];

$goods = $db->get_row($goods_sql);

$dataSql = "SELECT cli.ClientID,cli.ClientCompanyName,SUM(c.ContentNumber) AS ContentNumber,SUM(c.ContentSend) AS ContentSend
            FROM ".DATATABLE."_order_cart AS c
            LEFT JOIN ".DATATABLE."_order_orderinfo AS o
            ON o.OrderID = c.OrderID
            LEFT JOIN ".DATATABLE."_order_client AS cli
            ON cli.ClientID = c.ClientID
            WHERE
            c.CompanyID=".$_SESSION['uinfo']['ucompany']."
            AND FROM_UNIXTIME(o.OrderDate) BETWEEN '".$in['beginDate']." 00:00:00' AND '".$in['endDate']." 23:59:59'
            AND c.ContentID=".$in['ID']."
            AND o.OrderUserID IN(".implode(',',$clientIds).")
            GROUP BY ClientID
            ORDER BY ContentNumber DESC";

$stataData = $db->get_results($dataSql);

$gift_sql = "SELECT SUM(ContentNumber) as cnum,SUM(ContentSend) as snum,c.ClientID,c.ContentName
             FROM ".DATATABLE."_order_cart_gifts c
             LEFT JOIN ".DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID
             WHERE c.CompanyID=".$_SESSION['uinfo']['ucompany']."
             AND FROM_UNIXTIME(o.OrderDate) BETWEEN '".$in['beginDate']." 00:00:00' AND '".$in['endDate']." 23:59:59'
             AND o.OrderStatus!=8 AND o.OrderStatus!=9
             AND c.ContentID=".$in['ID']."
             GROUP BY c.ClientID ORDER BY cnum DESC";

$gift_data = $db->get_results($gift_sql);
$gift_arr = array();
if(!empty($gift_data)){
    foreach($gift_data as $k=>$v){
        $gift_arr[$v['ClientID']] = $v;
    }
}
$return_sql = "SELECT o.ReturnClient, SUM(ContentNumber) as cnum,c.ContentID,c.ContentName
               FROM ".DATATABLE."_order_cart_return c
               LEFT JOIN ".DATATABLE."_order_returninfo o on c.ReturnID=o.ReturnID
               WHERE c.CompanyID=".$_SESSION['uinfo']['ucompany']."
               AND FROM_UNIXTIME(o.ReturnDate) BETWEEN '".$in['beginDate']." 00:00:00' AND '".$in['endDate']." 23:59:59'
               AND (o.ReturnStatus=2 OR o.ReturnStatus=3 OR o.ReturnStatus=5) ".$returnWhere."
               AND c.ContentID = ".$in['ID']."
               GROUP BY o.ReturnClient
               ORDER BY cnum DESC";

$return_data = $db->get_results($return_sql);
if(!empty($return_data)){
    foreach($return_data as $val){
        $return_arr[$val['ReturnClient']] = $val;
    }
}


$axis = array(); // 药店
$totals = array();//订购数量+赠送数量
$cnts = array();//退货数量
foreach($stataData as $val){
    $kg_num = empty($gift_arr[$val['ClientID']]) ? 0 : $gift_arr[$val['ClientID']]['cnum'];
    $kr_num = empty($return_arr[$val['ClientID']]) ? 0 : $return_arr[$val['ClientID']]['cnum'];
    $axis[] = "'".$val['ClientCompanyName']."'";
    $totals[] = $val['ContentNumber'] + $kg_num;
    $cnts[] = $kr_num;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/showpage.css" />
    <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
    <script src="../scripts/jquery.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
    <script src="js/order.js?v=<? echo VERID;?>" type="text/javascript"></script>

    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
    <script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
    <script type="text/javascript">
        $(function(){
            $("#beginDate,#endDate").datepicker({changeMonth: true,	changeYear: true});
            $("#newbutton").click(function(){
                //查看统计
                $("#MainForm").attr('method','get').attr('action','statistics.php').attr('target','_self').get(0).submit();

            });

            $("#exceltable").click(function(){
                //导出报表
            });
        });
    </script>
</head>

<body>
<? include_once ("top.php");?>

<div class="bodyline" style="height:25px;"></div>
<div class="bodyline" style="height:32px;"><div class="leftdiv" style="margin-top:8px; padding-left:12px;"><span><h4><?php echo $_SESSION['uc']['CompanyName'];?></h4></span><span valign="bottom">&nbsp;&nbsp;<? echo $_SESSION['uinfo']['usertruename']."(".$_SESSION['uinfo']['username'].")";?> 欢迎您！</span>&nbsp;&nbsp;<span>[<a href="change_pass.php">修改密码</a>]</span>&nbsp;&nbsp;<span>[<a href="do_login.php?m=logout">退出</a>]</span></div>
    <div class="rightdiv">
        <span style="width:35px; height:32px; overflow:hidden; float:left;"><img src="img/menu2_left.jpg" /></span>
            <span id="menu2">
            	<ul>
                    <li ><a href="statistics.php">订单统计</a></li>
                    <li class="current2"><a href="statistics_product.php">商品统计</a></li>
                </ul>
            </span>
        <span style="width:9px; height:32px; overflow:hidden; float:left;"><img src="img/menu2_right.jpg" /></span>
    </div>
</div>

<div class="bodyline" style="height:70px; background-image:url(img/bodyline_bg.jpg);">
    <div class="leftdiv"><img src="img/blue_left.jpg" /></div>
    <div class="leftdiv"><h1><? echo $menu_arr[$menu_flag];?></h1></div>
    <div class="rightdiv" style="color:#ffffff; padding-right:20px; padding-top:40px;">此栏目针对单个商品药店分布统计。</div>
</div>

<div id="bodycontent">
<div class="lineblank"></div>
<div id="searchline">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" >
        <form id="FormSearch" name="FormSearch" method="post" action="order.php">
            <tr>
                <!--<td width="80" align="center"><strong>订单搜索：</strong></td>
                    <td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<?/* if(!empty($in['kw'])) echo $in['kw'];*/?>"  onfocus="this.select();" /></td>
                    <td align="center" width="80">起止时间：</td>
                    <td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<?/* if(!empty($in['bdate'])) echo $in['bdate'];*/?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<?/* if(!empty($in['edate'])) echo $in['edate'];*/?>" /></td>
                    <td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>-->
                <td aling="right"><div class="location"><strong>当前位置：</strong><a href="statistics.php">销售统计</a> &#8250;&#8250; 订单统计</div></td>
            </tr>
        </form>
    </table>
</div>

<div class="line2"></div>
<div class="bline">
<div id="sortright" style="width:100% !important;">
    <form id="MainForm" name="MainForm" method="post" action="order_excel.php" target="exe_iframe" >
        <div class="line">
            <fieldset class="fieldsetstyle">
                <legend>订单统计</legend>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <?php
                    if(empty($stataData)) {
                        ?>
                        <tr>
                            <td align="center">暂无相关数据</td>
                        </tr>
                    <?php
                    }else {
                        ?>
                        <tr>
                            <td>
                                <script type="text/javascript">

                                    var chart;
                                    $(document).ready(function() {
                                        chart = new Highcharts.Chart({
                                            chart: {
                                                renderTo: 'container',
                                                zoomType: 'xy'
                                            },
                                            title: {
                                                text: '( <?php echo $goods['Name'] ?> )  订单数据 - 药店'
                                            },
                                            subtitle: {
                                                text: ''
                                            },
                                            xAxis: [{
                                                categories: [<?php echo implode(',',$axis); ?>]
                                            }],
                                            yAxis: [{ // Primary yAxis
                                                labels: {
                                                    formatter: function() {
                                                        return this.value +'个';
                                                    },
                                                    style: {
                                                        color: '#89A54E'
                                                    }
                                                },
                                                title: {
                                                    text: '订单数',
                                                    style: {
                                                        color: '#89A54E'
                                                    }
                                                }
                                            }, { // Secondary yAxis
                                                title: {
                                                    text: '退货数',
                                                    style: {
                                                        color: '#4572A7'
                                                    }
                                                },
                                                labels: {
                                                    formatter: function() {
                                                        return this.value +' 个';
                                                    },
                                                    style: {
                                                        color: '#4572A7'
                                                    }
                                                },
                                                opposite: true
                                            }],
                                            tooltip: {
                                                formatter: function() {
                                                    return ''+
                                                    this.x +': '+ this.y +
                                                    (this.series.name == '订单金额' ? ' 元' : ' 个');
                                                }
                                            },
                                            legend: {
                                                layout: 'vertical',
                                                align: 'left',
                                                x: 120,
                                                verticalAlign: 'top',
                                                y: 100,
                                                floating: true,
                                                backgroundColor: '#FFFFFF'
                                            },
                                            series: [{
                                                name: '订购数',
                                                color: '#4572A7',
                                                type: 'spline',
                                                yAxis: 1,
                                                data: [<?php echo implode(',',$totals); ?>]
                                            }, {
                                                name: '退货数',
                                                color: '#89A54E',
                                                type: 'column',
                                                data: [<?php echo implode(',',$cnts); ?>]
                                            }]
                                        });
                                    });
                                </script>
                                <div id="container"></div>

                            </td>
                        </tr>
                        <tr>
                            <td height="30" bgcolor="#efefef" >&nbsp;&nbsp;&nbsp;&nbsp; <strong>( <?php echo $goods['Name'] ?> )  订单数据 - 药店 </strong></td>
                        </tr>
                        <tr>
                            <td >

                                <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
                                    <thead>
                                    <tr>
                                        <td width="2%" class="bottomlinebold"><label> &nbsp;</label></td>
                                        <td width="20%" class="bottomlinebold">药店</td>
                                        <td width="20%" class="bottomlinebold">商品</td>
                                        <td  class="bottomlinebold">订购数</td>
                                        <td width="10%" class="bottomlinebold">赠送数</td>
                                        <td width="10%" class="bottomlinebold">退货数</td>
                                        <td width="10%" class="bottomlinebold">发货数</td>
                                        <td width="10%" class="bottomlinebold">实际数</td>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    $ctotal = 0;//总订购数
                                    $gtotal = 0;//总赠送数
                                    $gstotal = 0;//总赠送发货数
                                    $stotal = 0;//总发货数
                                    $rtotal = 0;//总退货数
                                    $trueTotal = 0;//实际数
                                    foreach($stataData as $val) {
                                        $ctotal += $val['ContentNumber'];
                                        $val['gnum'] = !empty($gift_arr[$val['ClientID']]) ? $gift_arr[$val['ClientID']]['cnum'] : 0;
                                        $val['gsnum'] = !empty($gift_arr[$val['ClientID']]) ? $gift_arr[$val['ClientID']]['snum'] : 0;
                                        $val['rnum'] = !empty($return_arr[$val['ClientID']]) ? $return_arr[$val['ClientID']]['cnum'] : 0;
                                        $val['trueNum'] = $val['ContentNumber'] + $val['gnum'];
                                        $trueTotal += $val['trueNum'];
                                        $rtotal += $val['rnum'];
                                        $gtotal += $val['gnum'];
                                        $gstotal += $val['gsnum'];
                                        $stotal += $val['ContentSend'] + $val['gsnum'];
                                        ?>
                                        <tr class="bottomline" onmouseover="inStyle(this);" onmouseout="outStyle(this);">
                                            <td>&nbsp;</td>
                                            <td><?php echo $val['ClientCompanyName']; ?></td>
                                            <td><?php echo $goods['Name']; ?></td>
                                            <td><?php echo $val['ContentNumber']; ?></td>
                                            <td><?php echo $val['gnum']; ?></td>
                                            <td><?php echo $val['rnum']; ?></td>
                                            <td><?php echo $val['ContentSend'] + $val['gsnum']; ?></td>
                                            <td><?php echo $val['trueNum']; ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                                        <td>&nbsp;</td>
                                        <td ><strong>合计：</strong></td>
                                        <td ></td>
                                        <td ><strong> <?php echo $ctotal; ?> 个</strong></td>
                                        <td ><strong> <?php echo $gtotal; ?> 个</strong></td>
                                        <td ><strong> <?php echo $rtotal; ?> 个</strong></td>
                                        <td ><strong> <?php echo $stotal; ?> 个</strong></td>
                                        <td ><strong> <?php echo $trueTotal; ?> 个</strong></td>
                                    </tr>
                                    </tbody>
                                </table>

                            </td>
                        </tr>
                    <?php
                    }
                    ?>

                </table>
            </fieldset>
        </div>
    </form>
</div>
</div>
<br style="clear:both;" />
</div>

<? include_once ("bottom.php");?>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>