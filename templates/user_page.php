<?php  $data_rows = $this->get_user_contracts();
if($data_rows){
    ?>
    <h2 class='contracts_heading'>Contracte DespreSpa</h2>
    <div class="user_contracts_wrap">
        
        <table id="customer_contracts" class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID comandă</th>
                    <th>Status contract</th>
                    <th>Contracte</th>
                    <th>Încarcă contract</th>
                  
                </tr>
            </thead>
            <tbody>
                <?php
                
                foreach ($data_rows as $row) {
                    ?>
                    <tr>
                        <td><?php echo esc_html($row->id_comanda); ?></td>
                        <td class="<?php echo esc_html($row->status_contract); ?>"><?php echo esc_html($row->status_contract); ?></td>
                        <td><a target="_blank" href="<?php echo esc_html($row->link_contract); ?>">Vezi contractul</td>
                        <td>
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="order_id" value="<?php echo esc_attr($row->id_comanda); ?>">
                                <div class="file-input-wrapper">
                                    <input type="file" name="pdf_file">
                                </div>
                                <input type="submit" name="upload_pdf" value="Trimite contractul" <?php echo $row->status_contract !== 'Nesemnat' ? 'disabled' : ''; ?>>
                            </form>
                        </td>
                    
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        
    </div>
    <?php
    }
    else{
        ?>
       <h2 class='contracts_heading'>Contracte DespreSpa</h2>
       <p class='contracts-notify'>Contractele se întocmesc pentru sponsorii despreSpa. Poți afla mai multe despre oferta noastră de sponsorizare accesând următorul link:</p>
       <a class='sponsorship-offer' href='/oferta-sponsorizare-conferinta-spa/'>Vezi oferta</a>

     <?php  
    }
?>


