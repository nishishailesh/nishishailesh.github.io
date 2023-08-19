<?php
$GLOBALS['nojunk']='';
require_once 'single_table_edit_common.php';
require_once 'base/verify_login.php';
require_once 'tcpdf/tcpdf.php';

	////////User code below/////////////////////




function get_pdf_link()
{


	class MYPDF extends TCPDF 
	{
		public function Header(){}	//to prevent default header 
		public function Footer(){}	//to prevent default footer
	}

//modify as per choice
	$pdf = new MYPDF('', 'mm', array("50","25"), true, 'UTF-8', true);
	//$pdf = new MYPDF('', 'mm', 'A4', true, 'UTF-8', true);
	$pdf->SetMargins(5,5, $right=-1, $keepmargins=false);
	$pdf->setPrintFooter(false);
	$pdf->setPrintHeader(false);
	$pdf->SetAutoPageBreak(TRUE, 0);
	$pdf->setCellPaddings(0,0,0,0);
	return $pdf;
}

function print_pdf($pdf,$fname)
{	
	$pdf->Output($fname, 'I');
}

$link=get_link($GLOBALS['main_user'],$GLOBALS['main_pass']);

$pdf=get_pdf_link();


$result=run_query($link,$GLOBALS['database'],'select * from `'.$_POST['tname'].'` where id=\''.$_POST['id'].'\'');
$ar=get_single_row($result);

/*
$str='
	<p>Outword No:'.$ar['id'].'</p>
	<p>Date:'.$ar['date'].'</p>
	<p>Department of Biochemistry, GMC Surat</p>
';
*/

$str='
	Outword No: '.$ar['outward_no'].'<br>
	Date: '.$ar['date'].'<br>
	Dept. of Biochemistry<br>
	GMC Surat
';
//prepare_label($pdf,print_r($ar,true));
prepare_label($pdf,$str);

print_pdf($pdf,'print_'.$_POST['id'].'.pdf');


function prepare_label($pdf,$str)
{
		$style = array(
		'position' => '',
		'align' => 'C',
		'stretch' => true,
		'cellfitalign' => '',
		'border' => false,
		'hpadding' => 'auto',
		'vpadding' => '0',
		'fgcolor' => array(0,0,0),
		'bgcolor' => false, //array(255,255,255),
		'text' => true,
		'font' => 'helvetica',
		'fontsize' => 8,
		'stretchtext' => 4
	);

	$pdf->AddPage();
	$pdf->SetFont('helveticaB', '', 9);
	$pdf->writeHTML($str, true, false, true, false, '');
}

?>
