<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\ContactForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;


$this->title = 'Know your approval - Search Result';
$this->params['breadcrumbs'][] = $this->title;


?>
<style>
       /* Card container */
.card {
    width: 100%;
    margin: 20px auto;
    border: 1px solid #ddd; /* हल्की बॉर्डर */
    border-radius: 8px; /* गोल कोने */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* हल्का शैडो */
    overflow: hidden; /* सामग्री को बॉक्स के अंदर रखने के लिए */
    background-color: #fff; /* सफेद बैकग्राउंड */
    transition: transform 0.2s, box-shadow 0.2s; /* Hover animation */
}

/* Hover effect for card */
.card:hover {
    transform: translateY(-5px); /* ऊपर की ओर हल्का उठाव */
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* गहरा शैडो */
}

/* Card header */
.card-header {
    background-color: #f5f5f5; /* हल्का ग्रे बैकग्राउंड */
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
    color: #333; /* टेक्स्ट रंग */
    border-bottom: 1px solid #ddd; /* नीचे की बॉर्डर */
    text-align: center;
}

/* Card body */
.card-body {
    padding: 20px;
    font-size: 16px;
    line-height: 1.5;
    color: #555; /* बॉडी टेक्स्ट रंग */
}

      

}





    </style>


<div class="container content">
  
    <div class="card">
    	<div class="card-header">
    		<h3>Know Your Approval Search Result</h3>
    	</div>
    	<div class="card-body">
    		<?php if($data){  ?>
	         <table class="table" width="100%">
             <tr>
               <th width="5%">#</th>
               <th>Service Detail</th>
               <th>Related Documents</th>
             </tr>
             <?php  
              foreach ($data as $key => $value) { ?>
                <tr>
                  <td><?= $key+1 ?></td>
                  <td>
                    <b>Service Name: </b><?= $value['service_name'] ?><br>
                    <b>Service Description: </b><?= $value['service_desc'] ?><br>
                    <b>Service ID: </b><?= $value['id'] ?><br>
                  </td>
                  <td>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; text-align: center;">
                    <div style="border: 1px solid #ccc; padding: 10px;">
                      <b>Fee (if any)</b><br> <?= $value['fee'] ?>
                    </div>
                    <div style="border: 1px solid #ccc; padding: 10px;">
                      <b>Procedure</b><br> 
                       <?php if($value['procedure']){ ?>
                          <a href="<?= $value['procedure'] ?>" target='_blank'>
                          <img src="<?= Yii::$app->urlManager->baseUrl ?>/img/pdf.png" alt="logo" style='height: 50px;'>
                        </a>
                       <?php } ?>
                    </div>
                    <div style="border: 1px solid #ccc; padding: 10px;">
                      <b>Checklist</b><br>
                      <?php if($value['checklist']){ ?>
                        <a href="<?= $value['checklist'] ?>" target='_blank'>
                          <img src="<?= Yii::$app->urlManager->baseUrl ?>/img/pdf.png" alt="logo" style='height: 50px;'>
                        </a>
                       <?php } ?> 
                    </div>
                    <div style="border: 1px solid #ccc; padding: 10px;">
                      <b>PSG Timeline</b><br> <?= $value['psg_timelines'] ?>
                    </div>
                    <div style="border: 1px solid #ccc; padding: 10px;">
                      <b>To Apply</b><br> 
                      <a href="<?= $value['to_apply'] ?>" target='_blank'>
                          Click Here
                      </a>
                    </div>
                    </div>
                    
                  </td>
                </tr>
              <?php }
             ?>
           </table>
        <?php } ?>
    	</div>
	</div>




</div>
