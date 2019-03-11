<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('Model_User');	
        $this->load->model('Model_Functions');
        $this->load->model('Model_Transaction');
    }

	public function index()
	{
		
    }
    
    public function beamandgoreport()
    {
		if($this->session->userdata('is_logged_in'))
		{
			$data['title'] = 'Report';
					
			$this->load->view('layout/header',$data);
			$this->load->view('layout/menu',$data);
			$this->load->view('page/beamandgoreport');
			$this->load->view('layout/footer');
			}
		else 
		{
			$this->load->view('login');
		}
    }

    public function checkbngreport()
    {
        $response['st'] = false;

        $trdate = $this->input->post('trdate');

        $trdate = _dateFormatoSql($trdate);

        $data = $this->Model_Transaction->getBNGToGCData($trdate);

        if(count($data) == 0)
        {
            $response['msg'] = 'Empty result.';
        }
        else 
        {
            $this->beamandgoreportcreatepdf($trdate,$data);
            $response['st'] = true;
        }

        echo json_encode($response);
    }

    public function beamandgoreportcreatepdf($date,$data)
    {       

        $st = $this->session->userdata('gc_store');
        $st = strtolower(str_replace(" ","",$st));
        $this->load->library('Pdfcreate');

        $pdf = new Pdfcreate();
        
        //$pdf = new FPDF('p','mm','A4');
        
		$pdf->AliasNbPages();
        $pdf->AddPage("P","Letter");
        
        $pdf->docHeaderBngtoGCConvertionReport($this->session->userdata('gc_store'),_dateFormat($date));
        
        $pdf->setReportType('bng');
        $pdf->setTransactionDate($date);

        $pdf->subheaderBngtoGCConvertionReport(todays_date());

        $pdf->Ln();

        $pdf->SetFont("Arial", "", 10);
        $pdf->Cell(2,7,'',0,0,'R');
        $pdf->Cell(8,7,'',1,0,'C');
        $pdf->Cell(30,7,'Serial #',1,0,'C');
        $pdf->Cell(18,7,'Amount',1,0,'C');
        $pdf->Cell(30,7,'Barcode',1,0,'C');
        $pdf->Cell(54,7,'Beneficiary',1,0,'C');
        $pdf->Cell(54,7,'In Charge',1,0,'C');
        $pdf->Ln();

        $cnt = 1;
        $totamt = 0;
        foreach($data as $d)
        {
            $pdf->SetFont("Arial", "", 9);
            $pdf->Cell(2,6,'',0,0,'L');
            $pdf->Cell(8,6,$cnt.'.',1,0,'L');
            $pdf->Cell(30,6,$d->bngbar_serialnum,1,0,'L');
            $pdf->Cell(18,6,$d->bngbar_value,1,0,'L');
            $pdf->Cell(30,6,$d->bngbar_barcode,1,0,'C');
            $pdf->Cell(54,6,$d->bngbar_beneficiaryname,1,0,'C');
            $pdf->Cell(54,6,$d->incharge,1,0,'C');
            $pdf->Ln();
            $totamt+=$d->bngbar_value;
            $cnt++;
        }
        $pdf->Ln(10);

		$pdf->SetFont("Arial", "", 10);
		$pdf->Cell(10,5,'',0,0,'R');
		$pdf->SetFont("Arial","", 10);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->SetFont("Arial", "", 10);
		$pdf->Cell(20,5,'GC Count:',0,0,'R');
		$pdf->SetFont("Arial", "", 10);
        $pdf->Cell(50,5,count($data),0,0,'L');
        $pdf->Ln();
		$pdf->SetFont("Arial", "", 10);
		$pdf->Cell(10,5,'',0,0,'R');
		$pdf->SetFont("Arial","", 10);
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->SetFont("Arial", "", 10);
		$pdf->Cell(20,5,'Total Amount:',0,0,'R');
		$pdf->SetFont("Arial", "", 10);
        $pdf->Cell(50,5,number_format($totamt,2),0,0,'L');
        $pdf->Ln(10);	
        
		$pdf->Cell(10,8,'',0,0,'L');
		$pdf->Cell(105,8,'Prepared by:',0,0,'L');
		$pdf->Cell(80,8,'Checked by:',0,0,'L');
		$pdf->Ln(8);
		$pdf->SetFont("Arial", "B", 9);
		$pdf->Cell(10,8,'',0,0,'L');
		$pdf->Cell(80,	8,ucwords($this->session->userdata('gc_fullname')),0,0,'C');
		$pdf->Cell(34,8,'',0,0,'C');
		$pdf->Cell(60,8,"",0,0,'C');
		$pdf->Ln(4);
		$pdf->Cell(10,8,'',0,0,'L');
		$pdf->SetFont("Arial", "", 9);
		$pdf->Cell(18,	1,'',0,0,'R');
		$pdf->Cell(50,	1,'______________________________',0,0,'C');
		$pdf->Cell(36,	1,'',0,0,'C');
		$pdf->Cell(80,	1,'______________________________',0,0,'C');
		$pdf->Ln(5);
		$pdf->SetFont("Arial", "B", 7);
		$pdf->Cell(10,8,'',0,0,'L');
		$pdf->Cell(13,	1,'',0,0,'C');
		$pdf->Cell(60,	1,'(Signature over Printed name)',0,0,'C');
		$pdf->Cell(41,	1,'',0,0,'C');
		$pdf->Cell(60,	1,'(Signature over Printed name)',0,0,'C');	

        $pdf->Output($_SERVER['DOCUMENT_ROOT'].'/gc/assets/reports/'.$st.'bng.pdf','F');
        //$pdf->Output();
    }

    public function displayPDF()
    {
        $data['trnum'] = "";

        $this->load->view('page/displayPDF',$data);
    }

	public function checktest()
	{
		echo 'yeah';
	}
}
