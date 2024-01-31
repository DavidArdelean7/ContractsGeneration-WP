
<div class="wrap">
    <h2>Contracte DespreSpa</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Număr</th>
                <th>Număr contract</th>
                <th>ID comandă</th>
                <th>ID client</th>
                <th>Nume firmă</th>
                <th>Status contract</th>
                <th>Contracte</th>
                <th>Încarcă contract</th>
              
            </tr>
        </thead>
        <tbody>
            <?php
            
            $data_rows = $this->get_contracts();
            foreach ($data_rows as $row) {
                ?>
                <tr>
                    <td><?php echo esc_html($row->id); ?></td>
                    <td><?php echo esc_html($row->numar_contract); ?></td>
                    <td><?php echo esc_html($row->id_comanda); ?></td>
                    <td><?php echo esc_html($row->id_client); ?></td>
                    <td><?php echo esc_html($row->nume_firma); ?></td>
                    <td class='status' id="<?php echo esc_html($row->status_contract); ?>"><?php echo esc_html($row->status_contract); ?></td>
                    <td><a target="_blank" href="<?php echo esc_html($row->link_contract); ?>">Vezi contractul</td>
                    <td>
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="order_id" value="<?php echo esc_attr($row->id_comanda); ?>">
                            <input type="file" name="pdf_file">
                            <input type="submit" name="upload_pdf" value="Trimite contractul" <?php echo $row->status_contract !== 'Semnat' ? 'disabled' : ''; ?>>
                        </form>
                    </td>
                
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    
</div>