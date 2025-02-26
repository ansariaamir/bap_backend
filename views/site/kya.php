<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\ContactForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;


$this->title = 'Know your approval';
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

        .question {
            margin-bottom: 20px;
        }
        .question-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .options {
            margin-left: 20px;
        }

     
.loader-container {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    font-size: 28px;
    font-weight: 1000;
    color: red;
}

/* Flash message container */
.flash-message {
  
  background-color: #d59c1b; /* Green background */
  color: white; /* White text */
  padding: 15px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
  font-size: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;  
  animation: fadeIn 0.5s ease;
}

/* Close button */
.flash-close {
  background: none;
  border: none;
  color: white;
  font-size: 18px;
  font-weight: bold;
  cursor: pointer;
  padding: 0;
  margin-left: 15px;
}

.flash-close:hover {
  color: #ffcccc; /* Light red on hover */
}

}





    </style>

    <div class="loader-container hidden" id="loader">
	    Loading...
	</div>

<div class="container content">
    <?php  
            if(Yii::$app->session->hasFlash('warning')){ ?>
                
                <div class="flash-message" id="flashMessage">
                  <span class="flash-text"><?= Yii::$app->session->getFlash('warning'); ?></span>
                  <button class="flash-close" onclick="closeFlashMessage()">×</button>
                </div>

           <?php }

    ?>
    <div class="card">
    	<div class="card-header">
    		<h3>Know Your Approval</h3>
    	</div>
    	<div class="card-body">
    		<?php if($data){  ?>
	<?php $form = ActiveForm::begin(['id' => 'kya-form','action'=>Yii::$app->urlManager->createUrl('/site/kya-submit')]); ?>

           <?php foreach($data as $question){ 
           	$q_no = $question['question_no'];
           	$is_hide = $question['depend_option_id'] ? 'hidden' : '';
           	?>
           	<div class="question <?= $is_hide ?>" id="q<?= $q_no ?>">
                <div class="question-title">
                    <?= $q_no ?>. <?= $question['questions'] ?>
                </div>
                <div class="options">
                	<?php if($question['fieldtype']=='radio'){ ?>
                		<?php foreach ($question['options'] as $option): ?>
	                        <label>
	                            <input 
	                                type="radio" 
	                                name="question_<?= $question['q_id'] ?>" 
	                                value="<?= $option['o_id'] ?>"
	                                onclick="handleOptionClick('radio', this)">
	                            <?= $option['options'] ?>
	                        </label><br>
                    	<?php endforeach; ?>
                	<?php } ?>

                	<?php if($question['fieldtype']=='checkbox'){ ?>
                		<?php foreach ($question['options'] as $option): ?>
	                       <label>
							    <input 
							        type="checkbox" 
							        name="question_<?= $question['q_id'] ?>[]" 
							        value="<?= $option['o_id'] ?>"
							        onclick="handleOptionClick('checkbox', this)">
							    <?= $option['options'] ?>
							</label><br>
                    	<?php endforeach; ?>
                    <?php } ?>
                </div>
            </div>
           <?php } ?>
            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>

        <?php ActiveForm::end(); ?>
<?php } ?>
    	</div>
	</div>




</div>

<script type="text/javascript">
	function handleOptionClick(type, element) {
	    const value = element.value; // Selected value
	    const name = element.name;  // Input's name
	    const checked = element.checked; // Whether it's checked (true/false)

	    console.log(`Type: ${type}, Name: ${name}, Value: ${value}, Checked: ${checked}`);

	    // Example Logic
	    if (type === 'radio') {
	    	 $(`input[name='${name}']`).each(function() {
	            const radioValue = $(this).val();
	            managequestionbyoption(type, 'hide', radioValue);
	        });
	        managequestionbyoption(type,'show', value);	
	    } else if (type === 'checkbox') {
	        if (checked) {
	        	managequestionbyoption(type,'show', value);	            
	        } else {
	            managequestionbyoption(type,'hide', value);
	        }
	    }
	}

	function managequestionbyoption(type, action, id){
    	$.ajax({
        url: "<?= Yii::$app->urlManager->createUrl('othermodule/getcheckmappedquestion') ?>",
        data : {o_id:id},
        method: 'GET', 
        dataType: 'json', 
        beforeSend: function () {
          $("#loader").removeClass('hidden');
        },
        success: function (response) {   
        	 if (response.status) {
                if (action === 'show') {
                    // Show the dependent field
                    $(`#q${response.q_no}`).removeClass('hidden').show();
                } else if (action === 'hide') {
                    // Hide the dependent field
                    $(`#q${response.q_no}`).addClass('hidden').hide();
                }
            } else {
                console.log(response.message);
            }    	
          $("#loader").addClass('hidden');
        },
        error: function (xhr, status, error) {
          $("#loader").removeClass('hidden');
        },
      });
    }

    function closeFlashMessage() {
  const flashMessage = document.getElementById('flashMessage');
  flashMessage.style.animation = 'fadeOut 0.5s ease';
  setTimeout(() => {
    flashMessage.style.display = 'none';
  }, 500); // Match with fade-out animation duration
}

</script>