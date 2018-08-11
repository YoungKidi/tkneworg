<?php
/**
 *	为统计查询导出excel的类
 *
 */

use think\Log;
use think\Request;

class Exportexcel{
	public function index($sdate,$edate,$companyfullname,$data,$count){
		// var_dump($sdate);
		// var_dump($edate);
		// die;
		include 'PHPExcel/Classes/PHPExcel.php';
		include 'PHPExcel/Classes/PHPExcel/IOFactory.php';
		include 'PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

		//创建一个excel
		$objPHPExcel = new PHPExcel();
		//保存excel—2007格式
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		//或者$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); 非2007格式
		//$objWriter->save("xxx.xlsx");

		//创建人
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
		//最后修改人
		$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
		//标题
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
		//题目
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
		//描述
		$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
		//关键字
		$objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
		//种类
		$objPHPExcel->getProperties()->setCategory("Test result file");

		//设置当前的sheet
		$objPHPExcel->setActiveSheetIndex(0);
		//设置sheet的name
		$objPHPExcel->getActiveSheet()->setTitle('Simple');


		//设置单元格宽度
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

		//设置单元格的值
		$objPHPExcel->getActiveSheet()->setCellValue('A1', $sdate.'至'.$edate.$companyfullname);
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', '');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', '');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', '');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', '');


		$objPHPExcel->getActiveSheet()->setCellValue('A2', '日期');
		$objPHPExcel->getActiveSheet()->setCellValue('A3', '');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', '机构名称');
		$objPHPExcel->getActiveSheet()->setCellValue('B3', '');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', '在线教室数量');
		$objPHPExcel->getActiveSheet()->setCellValue('C3', '小班课1对1');
		$objPHPExcel->getActiveSheet()->setCellValue('D2', '');
		$objPHPExcel->getActiveSheet()->setCellValue('D3', '小班课1对多');
		$objPHPExcel->getActiveSheet()->setCellValue('E2', '');
		$objPHPExcel->getActiveSheet()->setCellValue('E3', '大班课');
		$objPHPExcel->getActiveSheet()->setCellValue('F2', '在线人员数量');
		$objPHPExcel->getActiveSheet()->setCellValue('F3', '小班课1对1');
		$objPHPExcel->getActiveSheet()->setCellValue('G2', '');
		$objPHPExcel->getActiveSheet()->setCellValue('G3', '小班课1对多');
		$objPHPExcel->getActiveSheet()->setCellValue('H2', '');
		$objPHPExcel->getActiveSheet()->setCellValue('H3', '大班课');

		// var_dump($data[0]['historydate']);
		// die;
		//循环生成数据
		for ($i = 4; $i <= $count+3; $i++) {
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $i,$data[$i-4]['historydate']);
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $i,$data[$i-4]['companyfullname']);
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $i,$data[$i-4]['onotoone_roomnum']);
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $i,$data[$i-4]['onotomore_roomnum']);
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $i,$data[$i-4]['live_roomnum']);
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $i,$data[$i-4]['onotoone_usernum']);
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $i,$data[$i-4]['onotomore_usernum']);
			$objPHPExcel->getActiveSheet()->setCellValue('H' . $i,$data[$i-4]['live_usernum']);
		}

		//合并单元格
		$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:A3');
		$objPHPExcel->getActiveSheet()->mergeCells('B2:B3');
		$objPHPExcel->getActiveSheet()->mergeCells('C2:E2');
		$objPHPExcel->getActiveSheet()->mergeCells('F2:H2');



		//设置水平居中
		$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A2:A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//设置水平居中
		$objPHPExcel->getActiveSheet()->getStyle('C2:E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F2:H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		//设置垂直居中
		$objPHPExcel->getActiveSheet()->getStyle('A2:A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



		//设置font
		$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setSize(25);
		$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->getStyle('A2:A3')->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('A2:A3')->getFont()->setSize(15);
		$objPHPExcel->getActiveSheet()->getStyle('A2:A3')->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getFont()->setSize(15);
		$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->getStyle('C2:E2')->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('C2:E2')->getFont()->setSize(15);
		$objPHPExcel->getActiveSheet()->getStyle('C2:E2')->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->getStyle('F2:H2')->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('F2:H2')->getFont()->setSize(15);
		$objPHPExcel->getActiveSheet()->getStyle('F2:H2')->getFont()->setBold(true);
		// $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		// $objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
		// $objPHPExcel->getActiveSheet()->getStyle('E1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
		// $objPHPExcel->getActiveSheet()->getStyle('D13')->getFont()->setBold(true);
		// $objPHPExcel->getActiveSheet()->getStyle('E13')->getFont()->setBold(true);

		//设置填充颜色
		$color = 'FF999999';
		$objPHPExcel->getActiveSheet()->getStyle('A2:A3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A2:A3')->getFill()->getStartColor()->setARGB($color);
		$objPHPExcel->getActiveSheet()->getStyle('A2:A3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A2:A3')->getFill()->getStartColor()->setARGB($color);

		$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getFill()->getStartColor()->setARGB($color);
		$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('B2:B3')->getFill()->getStartColor()->setARGB($color);

		$objPHPExcel->getActiveSheet()->getStyle('C2:E2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('C2:E2')->getFill()->getStartColor()->setARGB($color);
		$objPHPExcel->getActiveSheet()->getStyle('C2:E2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('C2:E2')->getFill()->getStartColor()->setARGB($color);

		$objPHPExcel->getActiveSheet()->getStyle('F2:H2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('F2:H2')->getFill()->getStartColor()->setARGB($color);
		$objPHPExcel->getActiveSheet()->getStyle('F2:H2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('F2:H2')->getFill()->getStartColor()->setARGB($color);
		$objPHPExcel->getActiveSheet()->getStyle('C3:H3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('C3:H3')->getFill()->getStartColor()->setARGB($color);

		/****直接输出到浏览器****/
		//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		//$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		// header("Pragma: public");
		// header("Expires: 0");
		// header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		// header("Content-Type:application/force-download");
		// header("Content-Type:application/vnd.ms-execl");
		// header("Content-Type:application/octet-stream");
		// header("Content-Type:application/download");;
		// header('Content-Disposition:attachment;filename="resume.xls"');
		// header("Content-Transfer-Encoding:binary");
		// $objWriter->save('php://output');
		/****直接输出到浏览器****/

		//直接生成1个excel文件
		$request = Request::instance();
		$domain = $request->domain();
		$str = Date('YmdHis',time());
		$filename = $str.\Particle::generateParticle();
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		//或者$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); 非2007格式
		$folder = 'excel';
		$filepath = $folder."/".$filename.".xlsx";
		$realfilepath = "./".$filepath;
		$showfilepath = $domain."/".$filepath;
		//生成excel文件
		$objWriter->save($filepath);
		
		$data = [];
		$data['url'] = urlencode($showfilepath);
	    //var_dump($data);
		return $data;
	}
}
