<?php 
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ('../class/PHPExcel.php');
$objPHPExcel = new PHPExcel();

if(empty($in['selectedID']))
{
	Error::AlertJs('请选择您要导出的订单!');
	exit;
}else{
	$idmsg = implode(",",$in['selectedID']);
}


$objPHPExcel->getProperties()->setCreator("订货宝 订货管理系统 DHB.HK")
							 ->setLastModifiedBy("DingHuoBao")
							 ->setTitle("订货宝-订单数据")
							 ->setSubject("订货宝-订单数据")
							 ->setDescription("订货宝-订单数据")
							 ->setKeywords("订货宝 订货管理系统")
							 ->setCategory("订单数据");

$objPHPExcel->setActiveSheetIndex(0);
$objActSheet = $objPHPExcel->getActiveSheet();
$objActSheet->setTitle('订单详细');
$objActSheet->getDefaultRowDimension()->setRowHeight(20);

$objActSheet->setCellValue('A1', $_SESSION['uc']['CompanyName'].' 在线订单');
$objActSheet->mergeCells('A1:K1');
$objStyleA5 = $objActSheet->getStyle('A1'); 
//设置对齐方式
$objStyleA5->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//设置字体    
$objFontA5 = $objStyleA5->getFont();   
$objFontA5->setName('黑体' );   
$objFontA5->setSize(18);   
$objFontA5->setBold(true);

//$objActSheet->getColumnDimension('A' )->setAutoSize(true);
$objActSheet->getColumnDimension('B' )->setWidth(36);
$objActSheet->getColumnDimension('K' )->setWidth(15);
$objActSheet->getRowDimension('1')->setRowHeight(32);
$objActSheet->freezePane('A2');

$clientdata = $db->get_results("select ClientID,ClientCompanyName from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientID asc");
foreach($clientdata as $cvar)
{
	$clientarr[$cvar['ClientID']] = $cvar['ClientCompanyName'];
}

$odata = $db->get_results("SELECT OrderID,OrderSN,OrderUserID,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,OrderReceiveAdd,OrderRemark,OrderDate,OrderSpecial,OrderTotal,DeliveryDate FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID in (".$idmsg.") limit 0,20");
if(!empty($odata))
{
	$k = 1;
	foreach($odata as $oinfo)
	{
		$cartdata = $db->get_results("select c.*,i.Coding,i.Price1,i.Price2,i.Units,i.CommendID,i.Casing from ".DATATABLE."_order_cart c left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." and c.OrderID=".$oinfo['OrderID']." order by c.ID asc");

		$k++;
		$objActSheet->setCellValue('A'.$k, '订单号：'.$oinfo['OrderSN']);
		$objActSheet->mergeCells('A'.$k.':B'.$k);

		$objActSheet->setCellValue('C'.$k, '客户：'.$clientarr[$oinfo['OrderUserID']]);
		$objActSheet->mergeCells('C'.$k.':F'.$k);

		$objActSheet->setCellValue('G'.$k, '订购时间：'.date("Y-m-d H:i",$oinfo['OrderDate']));  
		$objActSheet->mergeCells('G'.$k.':K'.$k);
		$objActSheet->getStyle('G'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objActSheet->getStyle('A'.$k.':K'.$k)->getFont()->setBold(true);
		
		$k++;
		$objActSheet->setCellValue('A'.$k, '序号');
		$objActSheet->setCellValue('B'.$k, '商品名称');
		$objActSheet->setCellValue('C'.$k, '编号/货号');
		$objActSheet->setCellValue('D'.$k, '颜色/规格');
		$objActSheet->setCellValue('E'.$k, '单位');
		$objActSheet->setCellValue('F'.$k, '数量');
		$objActSheet->setCellValue('G'.$k, '包装');
		$objActSheet->setCellValue('H'.$k, '单价');
		$objActSheet->setCellValue('I'.$k, '折扣');
		$objActSheet->setCellValue('J'.$k, '折后价');
		$objActSheet->setCellValue('K'.$k, '小计(元)');

		$objActSheet->getStyle('A'.$k.':K'.$k)->getFont()->setBold(true);
		$objActSheet->getStyle('F'.$k.':K'.$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
		$alltotal  = 0;
		$allnumber = 0;
		$baseRow   = $k;
		$n=0;
		if(!empty($cartdata))
		{
		foreach($cartdata as $ckey=>$cvar)
		{
			$baseRow++;
			$n++;
			$linetotal = $cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
			$alltotal  = $alltotal + $linetotal;
			$allnumber = $allnumber + $cvar['ContentNumber'];
			$cvar['ContentName'] = html_entity_decode($cvar['ContentName'], ENT_QUOTES,'UTF-8');

			$objActSheet->setCellValueExplicit('A'.$baseRow , $n,PHPExcel_Cell_DataType::TYPE_STRING); 
			
			if($cvar['CommendID'] == '2')
			    $objActSheet->setCellValue('B'.$baseRow, "【特】".$cvar['ContentName']);
			else
			    $objActSheet->setCellValue('B'.$baseRow, $cvar['ContentName']);
			
			$objActSheet->setCellValue('C'.$baseRow, $cvar['Coding']);
			$objActSheet->setCellValue('D'.$baseRow, $cvar['ContentColor'].'/'.$cvar['ContentSpecification']);
			$objActSheet->setCellValue('E'.$baseRow, $cvar['Units']);
			$objActSheet->setCellValue('F'.$baseRow, $cvar['ContentNumber']);
			$objActSheet->setCellValue('G'.$baseRow, $cvar['Casing']);
			$objActSheet->setCellValue('H'.$baseRow, $cvar['ContentPrice']);
			$objActSheet->setCellValue('I'.$baseRow, $cvar['ContentPercent']);
			$objActSheet->setCellValue('J'.$baseRow, $cvar['ContentPrice']*$cvar['ContentPercent']/10);
			$objActSheet->setCellValue('K'.$baseRow, $linetotal);			
		}
		}
		$alltotal = sprintf("%01.2f", round($alltotal,2));
		
		$baseRow++;
		$objActSheet->setCellValue('A'.$baseRow, '合计：');
		//$objActSheet->setCellValue('B'.$baseRow, '大写：'.toCNcap($alltotal));
		$objActSheet->setCellValue('F'.$baseRow, $allnumber);
		
		//$objActSheet->setCellValue('J'.$baseRow, $alltotal);
		if($oinfo['OrderSpecial'] == 'T') {
		    $objActSheet->setCellValue('B'.$baseRow, '大写：'.toCNcap($oinfo['OrderTotal']));
		    $totalVal = "原价 ￥".$alltotal."\n";
		    $totalVal .= "特价 ￥".$oinfo['OrderTotal']."";
		
		    $objActSheet->setCellValue('K'.$baseRow, $totalVal);
		    $objActSheet->getStyle('K'.$baseRow)->getAlignment()->setWrapText(true);
		    $objActSheet->getRowDimension($baseRow)->setRowHeight(30);
		    $objActSheet->getStyle('K'.$baseRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		} else {
		    $objActSheet->setCellValue('K'.$baseRow, $alltotal);
		    $objActSheet->setCellValue('B'.$baseRow, '大写：'.toCNcap($alltotal));
		}
	
		$objActSheet->mergeCells('B'.$baseRow.':D'.$baseRow);
		$objActSheet->getStyle('A'.$baseRow.':K'.$baseRow)->getFont()->setBold(true);

		if(empty($oinfo['DeliveryDate']) || $oinfo['DeliveryDate'] == '0000-00-00')
		    $oinfo['DeliveryDate'] = '';
		
		$baseRow++;
		$objActSheet->setCellValue('A'.$baseRow, '收货人：'.$oinfo['OrderReceiveCompany'].' / '.$oinfo['OrderReceiveName']);
		$objActSheet->mergeCells('A'.$baseRow.':B'.$baseRow);
		$objActSheet->setCellValue('C'.$baseRow, '联系电话：'.$oinfo['OrderReceivePhone']);
		$objActSheet->mergeCells('C'.$baseRow.':G'.$baseRow);
		$objActSheet->setCellValue('H'.$baseRow, '交货日期：'.$oinfo['DeliveryDate']);
		$objActSheet->mergeCells('H'.$baseRow.':K'.$baseRow);
		
		$baseRow++;
		$objActSheet->setCellValue('A'.$baseRow, '收货地址：'.$oinfo['OrderReceiveAdd']);
		$objActSheet->mergeCells('A'.$baseRow.':K'.$baseRow);

		$baseRow++;
		$objActSheet->setCellValue('A'.$baseRow, '备注说明：'.$oinfo['OrderRemark']);
		$objActSheet->mergeCells('A'.$baseRow.':K'.$baseRow);
		$objActSheet->getStyle('A'.$baseRow)->getAlignment()->setWrapText(true);

		//设置边框
		$styleThinBlackBorderOutline = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
					'color' => array('argb' => 'FF666666'),
				),
			),
		);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$k.':K'.$baseRow)->applyFromArray($styleThinBlackBorderOutline);

		$baseRow++;
		$objActSheet->mergeCells('A'.$baseRow.':K'.$baseRow);
		$objActSheet->getRowDimension($baseRow)->setRowHeight(30);
		
		$k = $baseRow;
	}
	$objActSheet->getStyle('A2:K'.$k)->getFont()->setSize(10);
}

$outputFileName = 'dhb_order_'.date("Ymd").'_'.rand(1,999).'.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$outputFileName.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>