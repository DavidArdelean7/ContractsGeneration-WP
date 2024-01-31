<?php

class EmailSend{

    private $order, $email_text_key, $contract_url;
    public function __construct($order_id, $email_type='draft', $contract_url){

        $this-> order = wc_get_order($order_id);
        $this->contract_url = $contract_url;

        switch ($email_type){
            case 'draft': $this->email_text_key = 'text-email-contract';
            break;
            case 'signed_by_customer': $this->email_text_key = 'text-email-semnat';
            break;
            case 'completed': $this->email_text_key = 'text-email-complet';
        }
            
        }
       


    public function get_email_text(){
        
        $line_items = $this->order->get_items();

        foreach ($line_items as $item_id => $item) {

            $product_id = $item->get_product_id();
            $quantity = $item->get_quantity();
            $text = get_post_meta($product_id, $this->email_text_key, true);
            break;
        }

        $data = get_order_data($this->order, $quantity);
        $text = replace($text, $data);
        $text .= '<br><br><a href="'. $this->contract_url . '">Vezi contractul aici</a>';
        
        return $text;
        
    }

    public function send_email(){

        $headers = "From: ardelean.david@growably.ro" . "\r\n" .
        "Content-Type: text/html; charset=UTF-8";
        $subject= "Contract despreSpa pentru comanda ". $this->order->get_id();
    
        $customer_email = $this->order->get_billing_email();
        $message = $this->get_email_text();
        wp_mail($customer_email, $subject, $message, $headers);
    }

    public function send_email_to_admin(){

        $headers = "From: ardelean.david@growably.ro" . "\r\n" .
        "Content-Type: text/html; charset=UTF-8";
        $subject= "Actualizare status contract despreSpa pentru comanda ". $this->order->get_id();
    
        //$admin_email = ADMIN_EMAIL;
        $admin_email = 'ardelean.david@growably.ro';
        $page_link = admin_url("admin.php?page=contracte-despre-spa");
        $message = 'Actualizarea contractului a avut loc cu succes! <br><br>
        <a href="'. esc_url($page_link) .'">Verifică aici</a>'; 
        wp_mail($admin_email, $subject, $message, $headers);
    }

}