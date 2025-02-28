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
    <table width="100%" style="text-align: center; background-color: lightgreen;">
        <tr>
            <td>
                <strong>Applicant Name:</strong><br>
                Test Name
            </td>
            <td>
                <strong>Applicant Status:</strong><br>
                <?= $model->application_status ?>
            </td>
            <td>
                <strong>Date:</strong><br>
                <?= date('d M Y H:i a',strtotime($model->created_on)) ?>
            </td>
        </tr>
    </table>
    <br>
    
    <?php 

    if(is_array($data) && !empty($data)){ 
        foreach ($data as $key => $value) {   // tab loop  
               foreach ($value as $k => $val) {   // section loop  
        ?>
        <strong><?= $k ?></strong>
        <table class="styled-table">
        <?php  
        $row_start = 1;
        $cell_start = 1;
        $change_row_cell = 1;
        $print_started = 0;
        foreach ($val as $fk => $fv) { ?>
            <?php if($fv['field_is_array_value']==1){
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
                                <b><?= $fv['field_name'] ?></b><br>
                                <?php if($fv['field_type']=='addmore'){ ?>
                                    <br>
                                    <table class="styled-table">
                                        <tr>
                                        <?php foreach ($fv['field_value']['thead'] as $th_val) {
                                            echo '<td><b>'.$th_val.'</b></td>';
                                        }?>
                                        </tr>
                                        
                                        <?php foreach ($fv['field_value']['tbody'] as $td_val) { ?>
                                            <tr>
                                                <?php foreach ($td_val as $cell_val) { ?>
                                                    <td>
                                                        <?= $cell_val['text'] ?>
                                                    </td>                                     
                                                    <?php } ?>

                                            </tr>
                                            <?php } ?>
                                        </tr>
                                    </table>
                                   

                                <?php }else{
                                    print_r($fv['field_value']);
                                } ?>
                               
                            </td>
                        </tr>
                    <?php }else{ ?>

                        <?php if($row_start==1){ ?>
                            <tr>
                        <?php } ?>
                        
                        <td width="50%">
                            <b><?= $fv['field_name'] ?></b><br>
                            
                                <?= $fv['field_value']; ?>
                             
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
    <?php }
        }
    } ?>
</div>

   
