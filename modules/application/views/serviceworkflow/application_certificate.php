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
    <!-- <table style="width: 100%; text-align: center;">
        <tr>
            <td style="width: 20%; text-align: center;">
                <img src="<?= Yii::$app->urlManager->baseUrl ?>/img/ey_logo.png" alt="logo" class='logo-img'>
            </td>
            <td style="width: 60%; text-align: center;">
                <b style="font-size: 32px;"><?= $model->scpm->service->service_name ?></b><br>
            <span style="font-size: 30px;"><?= $model->scpm->dept->dept_name ?></span>
            </td>
            <td style="width: 20%; text-align: center;">
                
            </td>
        </tr>
    </table> -->
<div style="text-align: center;">
   <img src="<?= Yii::$app->urlManager->baseUrl ?>/img/ey-logo1.png" alt="logo" class='logo-img'>
<br>
            <span style="font-size: 30px;"><?= $model->scpm->dept->dept_name ?></span>
        </div>
<br><br>
<div style="font-size: 32px; text-align: center;"><b>Certificate Of ...</b></div><br>
<br>
<p style="font-size: 24px; text-align:justify;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    I here by certified that the applicant <b>Aamir</b> has been successfully registered to <b><?= $model->scpm->service->service_name ?></b> on <b><?= date('d M Y h:i a',strtotime($model->created_on)) ?></b> 
</p>


</div>

