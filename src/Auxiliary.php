<?php
function replace($string, $data){
		
    $placeholders_values=array(
        "{order_id}" => $data["order_id"],
        "{prenume}" => $data['customer_first_name'],
        "{nume}" =>  $data['customer_last_name'],
        "{total}" => $data['total'],
        "{companie}" => $data['company'],
        "{adresa}" => $data['address'],
        "{oras}" => $data['city'],
        "{judet}" => $data['state'],
        "{cod_postal}" => $data['postcode'],
        "{tara}" => $data['country'],
        "{nr_telefon}" => $data['phone'],
        "{cui}" => $data['cui'],
        "{cui_comert}"=> $data['reg_com'],
        "{data}" => $data['date'],
        "{reprezentant_legal}" => $data['legal_representative'],
        "{iban}" => $data['iban'],
        '{nr_contract}' => $data['contract_number'],
        '{numar}' =>$data['quantity']
    );
    $search=array_keys($placeholders_values);
    $replace = array_values($placeholders_values);
    $new_string = str_replace($search, $replace, $string);
    return $new_string;
}

function get_order_data($order, $quantity){

    $data = array(
        'contract_number' => get_option('numar_contract'),
        'order_id' => $order->get_id(),
        'customer_id' => $order->get_customer_id(),
        'customer_first_name' =>$order->get_billing_first_name(),
        'customer_last_name' =>$order->get_billing_last_name(),
        'customer_email' =>$order->get_billing_email(),
        'total' => $order->get_total(),
        'company' =>  $order->get_billing_company(),
        'address' => $order->get_billing_address_1() .' '.$order->get_billing_address_2(),
        'city' => $order->get_billing_city(),
        'state' => $order->get_billing_state(),
        'postcode' => $order ->get_billing_postcode(),
        'country' => $order->get_billing_country(),
        'phone' => $order->get_billing_phone(),
        'cui' => $order->get_meta("_billing_cui"),
        'reg_com' => $order->get_meta('_billing_nr_reg_comertului'),
        'date' => $order->get_date_created()->format('d.m.Y'),
        'legal_representative' => $order->get_meta("_legal_representative_field"),
        'iban' => $order->get_meta("_iban_field"),
        'quantity' =>$quantity
    );
    return $data;
}

function pdf_contract( $order_id ) {
	if ( ! $order_id ) {
		return;
	}
	
    $taxonomy_name = 'product_cat'; 
    $term_slug = 'contracte';

    $order = wc_get_order( $order_id );
    $order_items = $order->get_items();
    foreach( $order_items as $item_id => $item ){
        $product_id = $item->get_product_id();
        if(has_term($term_slug, $taxonomy_name, $product_id)){
            
            $quantity = $item->get_quantity();
            $data = get_order_data($order, $quantity);
            $contract = new PDFGeneration($data);
            $contractUrl = $contract->generate_contract_url();
            $send = new EmailSend($order_id, 'draft', $contractUrl);
            $send-> send_email();
            return;
        }
    }
}

function add_user_endpoint($items) {
    $items['contracte'] = __('Contracte', 'textdomain');
	$a=array_slice($items,0,1);
	$b=array_slice($items,-1);
	$c=array_slice($items,1,-1);
	$items = array_merge($a, $b, $c);
    return $items;
}


function rewrite_user_endpoint() {
    add_rewrite_endpoint( 'contracte', EP_ROOT | EP_PAGES );
}

function add_custom_checkout_fields_awards($fields) {
	
    $taxonomy_name = 'product_cat'; 
	$term_slug = 'contracte';

    $product_in_cart = false;
	$cart = WC()->cart->get_cart();
    foreach ($cart as $cart_item) {
        if (has_term($term_slug, $taxonomy_name, $cart_item['product_id'])) {
            $product_in_cart = true;
            break;
        }
    }

    if ($product_in_cart) {
		$fields['legal_representative_field'] = array(
            'type' => 'text',
            'class' => array('form-row-wide'),
            'label' => __('Reprezentant legal companie (necesar pentru completare automata a contractului)'),
            'placeholder' => __(''),
            'required' => true,
        );
		
		$fields['iban_field'] = array(
            'type' => 'text',
            'class' => array('form-row-wide'),
            'label' => __('IBAN (necesar pentru completare automata a contractului)'),
            'placeholder' => __(''),
            'required' => true,
        );

        //add script for mandatory user account creation on checkout
        wp_enqueue_script('custom-checkout-script', plugins_url('/../js/script.js', __FILE__), array('jquery'), '1.3', true);
    }
	
	return $fields;
}

function save_custom_checkout_fields($order) {
    if (!empty($_POST['legal_representative_field'])) {
        $order->update_meta_data('_legal_representative_field', sanitize_text_field($_POST['legal_representative_field']));
    }
	if (!empty($_POST['iban_field'])) {
        $order->update_meta_data('_iban_field', sanitize_text_field($_POST['iban_field']));
    }
}