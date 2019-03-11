<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."/third_party/fpdf.php";
class Pdfcreate extends FPDF {
    public function __construct() {
        parent::__construct();
        //$this->load->model('model_transaction');
    }

	public function setReportType($rtype)
	{
		$this->reporttype = $rtype;
    }

    public function setTransactionDate($rdate)
    {
        $this->reportdate = $rdate;
    }
    
	public function docHeaderBngtoGCConvertionReport($storename = "",$date = "")
	{
		$this->SetFont("Helvetica", "B", 11);
		$this->SetTextColor(28, 28, 28);
        $this->Cell(0, 4, 'ALTURAS GROUP OF COMPANIES',0, 0, "C");
        $this->Ln();
		$this->SetFont("Helvetica", "B", 10);
		$this->Cell(0, 6, strtoupper($storename), 0, 0, "C");		
		$this->Ln(0);
		$this->SetFont("times", "B",11);
		$this->SetTextColor(28, 28, 28);
		$this->Ln(5);
		$this->Cell(0, 4, 'Beam and Go to GC Accountability Report', 0, 0, "C");	
        $this->Ln();
		$this->Cell(0, 5, 'Transaction Date: '.$date, 0, 0, "C");	
		$this->Ln(4);
    }
    
	public function subheaderBngtoGCConvertionReport($date = "")
	{
		$this->Ln();
		$this->SetFont("Arial", "B", 10);
		$this->Cell(22,5,'',0,0,'R');
		$this->SetFont("Arial","", 10);
		$this->Cell(94,5,'',0,0,'L');
		$this->SetFont("Arial", "B", 10);
		$this->Cell(34,5,'Date Generated:',0,0,'R');
		$this->SetFont("Arial", "", 10);
		$this->Cell(50,5,_dateFormat($date),0,0,'L');
		$this->Ln(4);		
	}

	public function docHeaderStoreTerminalReport($storename,$text)
	{
		$this->SetFont("Helvetica", "B", 12);
		$this->SetTextColor(28, 28, 28);
		$this->Cell(0, 6, strtoupper($storename), 0, 0, "C");
		$this->Ln();
		$this->Cell(0, 4, 'ALTURAS GROUP OF COMPANIES',0, 0, "C");
		$this->Ln(0);
		$this->SetFont("times", "B",11);
		$this->SetTextColor(28, 28, 28);
		$this->Ln();
		$this->Cell(0, 4, 'Terminal Report', 0, 0, "C");	
		$this->Ln();
		$this->Cell(0, 5, $text, 0, 0, "C");	
		$this->Ln(4);
	}

	public function docHeaderStoreSalesReport($storename)
	{
		$this->SetFont("Helvetica", "B", 12);
		$this->SetTextColor(28, 28, 28);
		$this->Cell(0, 8, ucwords($storename), 0, 0, "C");
		$this->Ln(6);
		$this->Cell(0, 8, 'ALTURAS GROUP OF COMPANIES', 0, 0, "C");
		$this->Ln(1);
		$this->SetFont("times", "B",11);
		$this->SetTextColor(28, 28, 28);
		$this->Ln();
		$this->Cell(0, 1, 'E-loading End of Day Sales Report', 0, 0, "C");	
		$this->Ln(6);
	}

	public function subheaderEODSalesReport($trnum,$eodby,$date)
	{
		$this->Ln();
		$this->SetFont("Arial", "B", 10);
		$this->Cell(22,5,'EOD #:',0,0,'R');
		$this->SetFont("Arial","", 10);
		$this->Cell(94,5,zeroes($trnum,5),0,0,'L');
		$this->SetFont("Arial", "B", 10);
		$this->Cell(34,5,'EOD Date:',0,0,'R');
		$this->SetFont("Arial", "", 10);
		$this->Cell(50,5,_dateFormat($date),0,0,'L');
		$this->Ln(8);
	}

	public function displayAllTransactionsEOD($tr)
	{
		$this->SetFont("Arial", "B", 10);
		$this->Cell(4,5,'',0,0,'R');
		$this->Cell(20,5,'TR #',1,0,'C');
		$this->Cell(70,5,'Item Name',1,0,'C');
		$this->Cell(20,5,'Quantity',1,0,'C');
		$this->Cell(20,5,'Discount',1,0,'C');
		$this->Cell(28,5,'SRP',1,0,'C');
		$this->Cell(28,5,'Total',1,0,'C');
	}


	public function Footer()
	{
		if($this->reporttype=='bng')
		{
			$this->SetY(-15);
			$this->SetTextColor(74, 74, 74);
			$this->SetFont("Arial", "", 7);
			// $this->SetDrawColor(74, 74, 74);
			// $this->SetLineWidth(0.2);
			// $this->Line(10, 265, 205, 265);
			$this->Cell(0, 10, "Page ".$this->PageNo()." - {nb}", 0, 0, "C");
			$this->Cell(0, 10, 'Beam and Go to GC Accountability Report - '.$this->reportdate, 0, 0, "R");			
		}
		elseif ($this->reporttype=='tr') 
		{
			$this->SetY(-15);
			$this->SetTextColor(74, 74, 74);
			$this->SetFont("Arial", "", 7);
			// $this->SetDrawColor(74, 74, 74);
			// $this->SetLineWidth(0.2);
			// $this->Line(10, 265, 205, 265);
			$this->Cell(0, 10, "Page ".$this->PageNo()." - {nb}", 0, 0, "C");
			$this->Cell(0, 10, 'Terminal Report', 0, 0, "R");	
		}
	}
}