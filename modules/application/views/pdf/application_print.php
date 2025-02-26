<style type="text/css">
.logo-img {
    width: 80px;
    height: auto;
}
.body{
    color: black;
}
.styled-table {
    border-collapse: collapse;
    width: 100%;
}

.styled-table td {
    border: 1px solid black;
    padding: 10px;
}

</style>
<div class="body">
    <table width="100%" style="text-align: center; background-color: #356cc0; color:#fff;">
        <tr>
            <td>
                <strong>Applicant Name:</strong><br>
                <?= $model->ssouser->name ?>
            </td>
            <td>
                <strong>Applicant Status:</strong><br>
                <?= \app\models\transactions\TApplicationSubmission::applicationStatus($model->application_status)['application_status_label'] ?>
            </td>
            <td>
                <strong>Date:</strong><br>
                <?= date('d M Y H:i a',strtotime($model->created_on)) ?>
            </td>
        </tr>
    </table>
    <br>
    

        <table class="styled-table">
        <?php  
        $row_start = 1;
        $cell_start = 1;
        $change_row_cell = 1;
        $print_started = 0;
        foreach ($data['form_field_data'] as $fk => $fv) { ?>
            <?php if(is_array($fv)){
                    if($cell_start==2){
                        echo '<td></td>';
                    }
                    if($print_started==1){
                        echo '</tr>';
                    }
                    $change_row_cell = 0;
                    $row_start = 1;
                    $cell_start = 1; ?>
                        <tr>
                            <td colspan="2" style="border: 1px solid black; padding: 10px;">
                                <b><?= $fk ?></b><br>

                                    <br>
                                    <table class="styled-table">
                                        
                                        <?php foreach ($fv as $mk => $th_val) {
                                            if(is_array($th_val)){
                                                foreach ($th_val as $addkey => $addval) { ?>
                                                    <tr>
                                                    <?php echo '<td><b>'.$addkey.'</b></td><td><b>'.$addval.'</b></td>'; ?>
                                                </tr>
                                                <?php }
                                            }else{

                                                echo '<tr><td><b>'.$th_val.'</b></td></tr>';
                                            }
                                            
                                        }?>
                                        </tr>
                                        
                                        
                                </tr>
                            </table>
                                   

                                
                               
                            </td>
                        </tr>
                    <?php }else{ ?>

                        <?php if($row_start==1){ ?>
                            <tr>
                        <?php } ?>
                        
                        <td width="50%">
                            <b><?= $fk ?></b><br>
                            
                                <?= $fv  ?>
                             
                        </td>
                        <?php if($row_start==0){ ?>
                            </tr>
                        <?php } 
                        }
            ?>
                      
        <?php 
            if($change_row_cell==1){
                $row_start = $cell_start == 1 ? 0 : 1;
                $cell_start = $cell_start == 1 ? 2 : 1;   
            }      
            $print_started =1;   
         } ?>
        </table>
        <br><br>
    
</div>

   
