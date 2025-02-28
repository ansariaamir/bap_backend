<style type="text/css">
    .container {
    width: 100%;
    text-align: center;
}

.container > div {
    display: inline-block;    /* This makes the divs to be side by side */
    width: 45%;     /* Adjust this value according to your needs */
    margin: 10px;
    text-align: center;
    font-size: 30px;
}
    .logo-img {
        width: 220px;
        height: auto;
    }
</style>
<div style="border: 1px solid black; height: 950px; padding:20px;">
    <br>
   
<div style="text-align: center;">
   <!-- <img src="<?= Yii::$app->urlManager->baseUrl ?>/img/logo.png" alt="logo" class='logo-img'> -->
<br>
            <span style="font-size: 30px;"><?= $model->scpm->dept->dept_name ?></span>
        </div>
<br><br>
<div style="font-size: 32px; text-align: center;"><b>Certification</b></div><br>
<br>
<p style="font-size: 24px; text-align:justify;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    I here by certified that the applicant <b><?= 'Test Name' ?></b> has been successfully registered to <b><?= $model->scpm->service->service_name ?></b> on <b><?= date('d M Y h:i a',strtotime($model->created_on)) ?></b> 
</p>


</div>

