<?php

class PDFGeneration{

    private $data, $contractUrl;
    public function __construct($data){

            $this->data = $data;

        
    }

    public function generate_contract_url() : string{

        $text = $this->get_contract_text();
        $text = replace($text, $this->data);

        $upload_dir = wp_upload_dir();
        $subpath = '/contracte/contract_'. $this->data['order_id'] . '.pdf';
        $path = $upload_dir['basedir'] . $subpath;

        $this->generate_pdf($path, $text);
        
        $this->contractUrl = $upload_dir['baseurl'] . $subpath;

        $this->register_contract();

        $newNumber = get_option('numar_contract') +1;
        update_option('numar_contract', $newNumber);

        return $this->contractUrl;
    }
    

    public function get_contract_text() : string{
        $order = wc_get_order($this->data['order_id']);
        $line_items = $order->get_items();

        foreach ($line_items as $item_id => $item) {

            $product_id = $item->get_product_id();
            $text = get_post_meta($product_id, 'text-contract', true);
            
        }
        return $text;
    }

    public function generate_pdf($path, $text) : void{

        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->setPdfTitle("EXCLUSIVE ABOUT SPA");

        $pdf->setFooterData(array(0,64,0), array(0,64,128));
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->AddPage();
        
        $htmlContent = $text;
        $pdf->writeHTML($htmlContent, true, false, true, false, '');
       
        $pdf->Output($path, 'F');

    }
	

    public function register_contract() : void{

        global $wpdb;
    
        $table = $wpdb-> prefix . CONTRACTS_TABLE;
        $wpdb -> insert(
            $table,
            array(
                "id_comanda" => $this-> data['order_id'],
                "status_contract" => "Nesemnat",
                "id_client" => $this -> data['customer_id'],
                "link_contract" => $this->contractUrl,
                "nume_firma" =>$this->data['company'],
                "numar_contract"=> $this->data['contract_number']
            ),
            array("%s", "%s", "%s")
        );
    }
}


class MYPDF extends TCPDF {

    private $logoFile;
    private $pdfTitle;
        
    public function setLogoFile($file) {
        $this->logoFile = $file;
    }

    public function setPdfTitle($title) {
        $this->pdfTitle = $title;
    }

    public function Header() {

        //$this->Image($this->logoFile, 15, 15, 30, '', 'PNG', '/', 'L', false, 300, '', false, false, 0, true, false, false);
        
        $this->SetFont('dejavusans', '', 12);

        $this->SetY(10);

        $this->Cell(0, 10, $this->pdfTitle, 0, false, 'L', 0, '', 0, false, 'M', 'M');
    }
}

