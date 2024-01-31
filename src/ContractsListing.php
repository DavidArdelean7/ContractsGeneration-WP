<?php

class ContractsListing{

    public function contracts_menu_page_content() {
        

        if (isset($_POST['upload_pdf'])) {
            $this->handle_complete_contract_upload();
        } 

        include(__DIR__ . '/../templates/menu_page.php');
    }

    public function user_dashboard_content() {
        
        if (isset($_POST['upload_pdf'])) {
            $this->handle_signed_contract_upload();
        } 
        include(__DIR__ . '/../templates/user_page.php');
    }


    public function handle_signed_contract_upload() {
        
        $upload_dir = wp_upload_dir();
        $subpath = '/contracte/';
        
        if (isset($_FILES['pdf_file']) && isset($_POST['order_id'])) {
            $order_id = sanitize_text_field($_POST['order_id']);
            $filename = sanitize_file_name($_FILES['pdf_file']['name']) ;
            $tmp_name = $_FILES['pdf_file']['tmp_name'];
    
            $destination = $upload_dir['basedir'] . $subpath . $filename ;
    
            if (move_uploaded_file($tmp_name, $destination)) {

                $link = $upload_dir['baseurl'] . $subpath . $filename;
                $this->update_contract_status_send(1, $order_id, $link);
            }
        }
    }

    public function handle_complete_contract_upload() {
        
        $upload_dir = wp_upload_dir();
        $subpath = '/contracte/';
        
        if (isset($_FILES['pdf_file']) && isset($_POST['order_id'])) {
            $order_id = sanitize_text_field($_POST['order_id']);
            $filename = sanitize_file_name($_FILES['pdf_file']['name']);
            $tmp_name = $_FILES['pdf_file']['tmp_name'];
    
            $destination = $upload_dir['basedir'] . $subpath . $filename ;
    
            if (move_uploaded_file($tmp_name, $destination)) {

                $link = $upload_dir['baseurl'] . $subpath . $filename;
                $this->update_contract_status_send(2, $order_id, $link);
            }
        }
    }

    public function update_contract_status_send($curr_status, $order_id, $contract_link){

        global $wpdb;
        $table = $wpdb-> prefix . CONTRACTS_TABLE;
        $status = "signed_by_customer";
        switch($curr_status){

            case 1:{

                $wpdb-> update(
                    $table,
                    array(
                        "status_contract" => "Semnat",
                        "link_contract" => $contract_link
                    ),
                    array(
                        "id_comanda" => $order_id
                    )
                    );

            }
            break;
            case 2:{
                
                $wpdb-> update(
                    $table,
                    array(
                        "status_contract" => "Complet",
                        "link_contract" => $contract_link
                    ),
                    array(
                        "id_comanda" => $order_id
                    )
                    );
                    $status = "completed";
                
            }
        }
        $send = new EmailSend($order_id, $status, $contract_link);
        $send-> send_email();
        $status ==="signed_by_customer" ? $send->send_email_to_admin() : $send->send_email_to_admin();
    }

    public function get_contracts() {

        global $wpdb;
        $table = $wpdb->prefix . CONTRACTS_TABLE;
        $contracts = $wpdb->get_results(
            "SELECT * FROM {$table}"

        );

        return $contracts;
    }

    public function get_user_contracts(){

        global $wpdb;

        $curr_user = get_current_user_id();
        $table = $wpdb->prefix . CONTRACTS_TABLE;
        $contracts = $wpdb->get_results(
            "SELECT * FROM {$table} WHERE id_client={$curr_user}"

        );

        return $contracts;
        
        
    }

    

}
    

