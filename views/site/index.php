<?php

/** @var yii\web\View $this */

$this->title = 'BAP WEB PAGE';



?>

<style>
    .chat-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 15px;
        border-radius: 50%;
        font-size: 20px;
        z-index: 1000;
    }

    .chat-dialog {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 300px;
        height: 400px;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 10px;
        display: none;
        flex-direction: column;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .chat-header {
        background-color: #007bff;
        color: white;
        padding: 10px;
        text-align: center;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .chat-body {
        padding: 15px;
        flex: 1;
        overflow-y: auto;
        background-color: #f9f9f9;
    }

    .chat-footer {
        padding: 10px;
        background-color: #fff;
        border-top: 1px solid #ccc;
    }

    .chat-footer input {
        width: 80%;
        display: inline-block;
    }

    .btn-close {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
    }

    .radio-group {
      display: flex;
      flex-direction: column; /* Ensures one below the other */
      gap: 10px; /* Adds spacing between radio buttons */
    }
    label {
      font-size: 16px; /* Optional: Adjust label size */
    }
</style>

<div class="wrapper bodyWrapper no_padding" >
            <div class="container home-3">
               <div id="SkipContent"></div>
               <div class="row">
                  <div class="col-12">
                     <div id="post-17697" class="post-17697 page type-page status-publish hentry">
                        <div data-vc-full-width="true" data-vc-full-width-init="false" data-vc-stretch-content="true" class="vc_row wpb_row vc_row-fluid vc_row-no-padding">
                           <div class="wpb_column vc_column_container vc_col-sm-12">
                              <div class="vc_column-inner ">
                                 <div class="wpb_wrapper">
                                    <div id="slide" class="home-slider full-cntrl-bottom-caption-bottom nav-white  flexslider main-banner-three">
                                       <ul class="slides">
                                          <li>
                                             <img src="website/images/banner2.jpg" alt='bne12'/>                       
                                             <div class="container">
                                                <div class="slide-caption">
                                                   <p class="heading3">BANNER HEADING DISPLAYED HERE</p>
                                                   <p>Banner description appears here</p>
                                                </div>
                                             </div>
                                          </li>                                          
                                          <li>
                                             <img src="website/images/banner1.jpg" alt='bne12'/>                       
                                             <div class="container">
                                                <div class="slide-caption">
                                                   <p class="heading3">BANNER HEADING DISPLAYED HERE</p>
                                                   <p>Banner description appears here</p>
                                                </div>
                                             </div>
                                          </li>
                                          
                                          <li>
                                             <img src="website/images/2017072176.jpg" alt='banner1'/>                            
                                             <div class="container">
                                                <div class="slide-caption">
                                                   <p class="heading3">WELCOME TO AMRAVATI</p>
                                                   <p>Banner description appears here</p>
                                                </div>
                                             </div>
                                          </li>
                                       </ul>
                                    </div>
                                    <script>
                                       jQuery(document).ready(function($) {
                                        // Slider
                                        
                                        $('.home-slider').flexslider({
                                            animation: ($('body').hasClass('rtl'))?"fade":"slide",
                                            directionNav: true,
                                            prevText: "<span class='hide'>Previous</span>",
                                            nextText: "<span class='hide'>Next</span>",
                                            pausePlay: true,
                                            pauseText: "<span class='hide'>Pause</span>",
                                            playText: "<span class='hide'>Play</span>",
                                            controlNav: false,
                                            start: function(slider){
                                            $('body').find('.flexslider').resize();
                                                if(slider.count==1){
                                                    slider.pausePlay.parent().remove();
                                                }
                                                $('.home-slider ul.slides li.clone a').each(function() {
                                                    $(this).replaceWith($(this).html());
                                                })
                                            }
                                       
                                        });
                                       });
                                       
                                    </script>
                                    <div class="wrapper" id="skipCont"></div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="vc_row-full-width vc_clearfix"></div>
                        <div class="vc_row wpb_row vc_row-fluid location-info vc_custom_1499428996867">
                           <div class="wpb_column vc_column_container vc_col-sm-12">
                              <div class="vc_column-inner ">
                                 <div class="wpb_wrapper">
                                    <div class="gen-list no-border no-bg padding-0 border-radius-none iconTop-textBottom-list   normal-font ">
                                       <ul>
                                          <li class="  ">
                                             <a href="#"   style="">
                                                <span class="list-icon blue-bg icon-ngo-2 border-radius-round"></span>
                                                <div class="list-text">Public representative        </div>
                                             </a>
                                          </li>
                                          <li class="  ">
                                             <a href="#"   style="">
                                                <span class="list-icon blue-bg icon-phone border-radius-round"></span>
                                                <div class="list-text">Important telephone numbers        </div>
                                             </a>
                                          </li>
                                          <li class="  ">
                                             <a href="#"   style="">
                                                <span class="list-icon blue-bg icon-support border-radius-round"></span>
                                                <div class="list-text">Emergency services       </div>
                                             </a>
                                          </li>
                                          <li class="  ">
                                             <a href="#"   style="">
                                                <span class="list-icon blue-bg icon-tourism border-radius-round"></span>
                                                <div class="list-text">Tourism        </div>
                                             </a>
                                          </li>
                                          <li class="  ">
                                             <a href="#"   style="">
                                                <span class="list-icon blue-bg icon-document border-radius-round"></span>
                                                <div class="list-text">Documents        </div>
                                             </a>
                                          </li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <br><br>
                        <div class="vc_row wpb_row vc_row-fluid vc_custom_1499927004251 vc_row-o-equal-height vc_row-flex">
                           <div class="wpb_column vc_column_container vc_col-sm-5">
                              <div class="vc_column-inner ">
                                 <div class="wpb_wrapper">
                                    <div class="wpb_text_column wpb_content_element " >
                                       <div class="wpb_wrapper">
                                          <h3 class="uppercase">About Industry Department</h3>
                                          <div class="row">
                                             <div>
                                                <p>The Department of Commerce and Industries is tasked for overall development of various industrial and commerce activities in the state. The Department plays developmental and facilitation role to attract industrial investments in the state. It focuses on creating an industry friendly environment and formulation suitable policies in the State aimed at propelling fast pace modernization and strengthening of industrial units. The Department provides an interactive platform for synergistic coordination between investors and the State Government. The Department is supported by its subsidiaries in the form of Agency, Board and Corporative.</p>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="wpb_column vc_column_container vc_col-sm-3">
                              <div class="vc_column-inner ">
                                 <div class="wpb_wrapper">
                                    <div class="content-view gen-list  no-border no-bg  padding-0 border-radius-none arrow-list ">
                                       <h2 class="list-heading heading3">NEWS & UPDATES</h2>
                                       <ul>
                                          <li>
                                             <a href="#">Dummy News 5</a>
                                          </li>
                                          <li>
                                             <a href="#">Dummy News 4</a>
                                          </li>
                                          <li>
                                             <a href="#">Dummy News 3</a>
                                          </li>
                                          <li>
                                             <a href="#">Dummy News 2</a>
                                          </li>
                                          <li>
                                             <a href="#">Dummy News 1</a>
                                          </li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="wpb_column vc_column_container vc_col-sm-4">
                              <div class="vc_column-inner ">
                                 <div class="wpb_wrapper">
                                    <div class="col-3 singlebox border office-barier">
                                       <div class="box-1 ">
                                          <div class="khowMinisterBox">
                                             <div class="khowMinisterBoxImg">
                                                <img decoding="async" class="" src="website/images/chiefminister.jpg" alt="minis2">
                                             </div>
                                             <div class="MinisterProfile">
                                                <span class="Pname">Hon'ble Chief Minister</span>
                                                <span class="Pdesg">Conrad Sangma</span>
                                                <ul>
                                          
                                                </ul>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="box-1 ">
                                          <div class="khowMinisterBox">
                                             <div class="khowMinisterBoxImg">
                                                <img decoding="async" class="" src="website/images/secretary.jpg" alt="minister-female">
                                             </div>
                                             <div class="MinisterProfile">
                                                <span class="Pname">Secretary, Industries Department.</span>
                                                <span class="Pdesg">Shri. Sanjay Goyal, IAS</span>
                                                <ul>
   
                                                </ul>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                       <!--  <div data-vc-full-width="true" data-vc-full-width-init="false" data-vc-stretch-content="true" class="vc_row wpb_row vc_row-fluid vc_custom_1499927724038 vc_row-has-fill">
                           <div class="wpb_column vc_column_container vc_col-sm-12">
                              <div class="vc_column-inner ">
                                 <div class="wpb_wrapper">
                                    <div class="gen-list no-border no-bg padding-0 border-radius-none iconTop-textBottom-box-list find-services-boxstyle  normal-font ">
                                       <h2 class='heading3'>FOCUS SECTORS</h2>
                                       <ul>
                                          <li class="green-bg  border-radius-none">
                                             <a href="#" title="Certificates">
                                                <span class="list-icon icon-supply border-radius-round"></span>
                                                <div class="list-text"><span>Agri & Food Processing</span>
                                                </div>
                                             </a>
                                          </li>
                                          <li class="red-bg border-radius-none">
                                             <a href="#" title="Certificates">
                                                <span class="list-icon icon-certificate border-radius-round"></span>
                                                <div class="list-text"><span>Education & Skill Development</span>
                                                </div>
                                             </a>
                                          </li>
                                          <li class="blue-bg  border-radius-none">
                                             <a href="#" title="Certificates">
                                                <span class="list-icon icon-magisterial border-radius-round"></span>
                                                <div class="list-text"><span>Housing & Urban Development</span>
                                                </div>
                                             </a>
                                          </li>
                                          <li class="red-bg  border-radius-none">
                                             <a href="#" title="Certificates">
                                                <span class="list-icon icon-certificate border-radius-round"></span>
                                                <div class="list-text"><span>Hydro & Renewable Energy</span>
                                                </div>
                                             </a>
                                          </li>
                                          <li class="green-bg  border-radius-none">
                                             <a href="#" title="Supply">
                                                <span class="list-icon icon-supply border-radius-round"></span>
                                                <div class="list-text"><span>Wellness, Healthcare & Ayush</span>
                                                </div>
                                             </a>
                                          </li>
                                          <li class="red-bg  border-radius-none">
                                             <a href="#" title="Magisterial">
                                                <span class="list-icon icon-magisterial border-radius-round"></span>
                                                <div class="list-text"><span>Information Technology</span>
                                                </div>
                                             </a>
                                          </li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div> -->
                        <!-- <div class="vc_row-full-width vc_clearfix"></div>
                        <div class="vc_row wpb_row vc_row-fluid public-important-helpline vc_custom_1516263996570">
                           <div class="wpb_column vc_column_container vc_col-sm-5">
                              <div class="vc_column-inner ">
                                 <div class="wpb_wrapper">
                                    <h2 class='heading3'>PUBLIC UTILITIES</h2>
                                    <div class="gen-list no-bg no-border normal-font  col-two padding-0 statistics-list ">
                                       <ul>
                                          <li>
                                             <a href="#" title="Banks">
                                                <span class="list-icon border-radius-large blue-bg">1</span>
                                                <div class="list-text">Banks</div>
                                             </a>
                                          </li>
                                          <li>
                                             <a href="#" title="Colleges / Universities">
                                                <span class="list-icon border-radius-large green-bg">1</span>
                                                <div class="list-text">Colleges / Universities</div>
                                             </a>
                                          </li>
                                          <li>
                                             <a href="#" title="Electricity">
                                                <span class="list-icon border-radius-large red-bg">0</span>
                                                <div class="list-text">Electricity</div>
                                             </a>
                                          </li>
                                          <li>
                                             <a href="#" title="Hospitals">
                                                <span class="list-icon border-radius-large orange-bg">0</span>
                                                <div class="list-text">Hospitals</div>
                                             </a>
                                          </li>
                                          <li>
                                             <a href="#" title="Municipalities">
                                                <span class="list-icon border-radius-large gray-bg">0</span>
                                                <div class="list-text">Municipalities</div>
                                             </a>
                                          </li>
                                          <li>
                                             <a href="#" title="NGOs">
                                                <span class="list-icon border-radius-large light-grey-bg">0</span>
                                                <div class="list-text">NGOs</div>
                                             </a>
                                          </li>
                                          <li>
                                             <a href="#" title="Postal">
                                                <span class="list-icon border-radius-large blue-bg">0</span>
                                                <div class="list-text">Postal</div>
                                             </a>
                                          </li>
                                          <li>
                                             <a href="#" title="Schools">
                                                <span class="list-icon border-radius-large green-bg">0</span>
                                                <div class="list-text">Schools</div>
                                             </a>
                                          </li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="wpb_column vc_column_container vc_col-sm-3">
                              <div class="vc_column-inner ">
                                 <div class="wpb_wrapper">
                                    <div class="gen-list no-border no-bg padding-0 border-radius-none default-list important-links-three  normal-font ">
                                       <h2 class="heading3">QUICK LINKS</h2>
                                       <ul>
                                          <li class="  ">
                                             <a href="#"   style="">
                                                <span class="list-icon -bg  border-radius-round"></span>
                                                <div class="list-text">E Office           </div>
                                             </a>
                                          </li>
                                          <li class="  ">
                                             <a href="#"   style="">
                                                <span class="list-icon -bg  border-radius-round"></span>
                                                <div class="list-text">Aadhaar Enabled PDS            </div>
                                             </a>
                                          </li>
                                          <li class="  ">
                                             <a href="#"   style="">
                                                <span class="list-icon -bg  border-radius-round"></span>
                                                <div class="list-text">Right to Information Act          </div>
                                             </a>
                                          </li>
                                          <li class="  ">
                                             <a href="#"   style="">
                                                <span class="list-icon -bg  border-radius-round"></span>
                                                <div class="list-text">Public Grievance            </div>
                                             </a>
                                          </li>
                                          <li class="  ">
                                             <a href="#"   style="">
                                                <span class="list-icon -bg  border-radius-round"></span>
                                                <div class="list-text">State Portal          </div>
                                             </a>
                                          </li>
                                          <li class="  ">
                                             <a href="#"   style="">
                                                <span class="list-icon -bg  border-radius-round"></span>
                                                <div class="list-text">Government Order            </div>
                                             </a>
                                          </li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="wpb_column vc_column_container vc_col-sm-4">
                              <div class="vc_column-inner ">
                                 <div class="wpb_wrapper">
                                    <div class="gen-list no-border no-bg padding-0 border-radius-none no-list helpline-no col-two normal-font ">
                                       <h2 class="heading3">HELPLINE NUMBERS</h2>
                                       <ul>
                                          <li class="  ">
                                             <div class="list-anchor">
                                                <span class="list-icon -bg  border-radius-round"></span>
                                                <div class="list-text">Citizen's Call center  -<b> 155300</b>           </div>
                                             </div>
                                          </li>
                                          <li class="  ">
                                             <div class="list-anchor">
                                                <span class="list-icon -bg  border-radius-round"></span>
                                                <div class="list-text">Child Helpline -<b> 1098</b>            </div>
                                             </div>
                                          </li>
                                          <li class="  ">
                                             <div class="list-anchor">
                                                <span class="list-icon -bg  border-radius-round"></span>
                                                <div class="list-text">Women Helpline -<b> 1091</b>            </div>
                                             </div>
                                          </li>
                                          <li class="  ">
                                             <div class="list-anchor">
                                                <span class="list-icon -bg  border-radius-round"></span>
                                                <div class="list-text">Crime Stopper -<b> 1090</b>          </div>
                                             </div>
                                          </li>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div> -->
                        <div data-vc-full-width="true" data-vc-full-width-init="false" data-vc-stretch-content="true" class="vc_row wpb_row vc_row-fluid vc_custom_1499927042202 vc_row-no-padding">
                           <div class="wpb_column vc_column_container vc_col-sm-12">
                              <div class="vc_column-inner ">
                                 <div class="wpb_wrapper">
                                    <section id="footerScrollbarWrapper" class="footerlogocarousel withbg withborder" aria-label="Other Important Links">
                                       <div class="footerlogocarousel-outer item-count-8">
                                          <div id="footerScrollbar" class="flexslider">
                                             <ul class="slides" aria-label="Important Sites">
                                                <li>
                                                   <a href="https://data.gov.in/" target="_blank" title="Open Government Data (OGD)  Platform India">
                                                   <img decoding="async" src="website/images/2017053014.png" alt="data.gov.in"/>                                    </a>
                                                </li>
                                                <li>
                                                   <a href="https://incredibleindia.org/" target="_blank" title="Incredible India">
                                                   <img decoding="async" src="website/images/2017053094.png" alt="Incredible India Site"/>                                    </a>
                                                </li>
                                                <li>
                                                   <a href="http://www.makeinindia.com/home" target="_blank" title="Make in India">
                                                   <img decoding="async" src="website/images/2017053052.png" alt="make in India"/>                                    </a>
                                                </li>
                                                <li>
                                                   <a href="https://www.mygov.in/" target="_blank" title="My Government">
                                                   <img decoding="async" src="website/images/2017053017.png" alt="mygov"/>                                    </a>
                                                </li>
                                                <li>
                                                   <a href="https://www.pmnrf.gov.in/" target="_blank" title="Prime Minister&#8217;s National Relief Fund">
                                                   <img decoding="async" src="website/images/2017053039.png" alt="PMNRF"/>                                    </a>
                                                </li>
                                                <li>
                                                   <a href="http://www.pmindia.gov.in/en/" target="_blank" title="Prime Minister of India">
                                                   <img decoding="async" src="website/images/2017110781.png" alt="pmindia"/>                                    </a>
                                                </li>
                                                <li>
                                                   <a href="https://www.india.gov.in/" target="_blank" title="The National Portal of India">
                                                   <img decoding="async" src="website/images/2019052293.png" alt="india.gov.in"/>                                    </a>
                                                </li>
                                                <li>
                                                   <a href="http://www.digitalindia.gov.in/" target="_blank" title="Digital India">
                                                   <img decoding="async" src="website/images/2017072418.png" alt="digital-india"/>                                    </a>
                                                </li>
                                             </ul>
                                          </div>
                                       </div>
                                    </section>
                                    <script type="text/javascript">
                                       jQuery(document).ready(function(){
                                          jQuery("#footerScrollbar").flexslider({
                                          animation: "slide",
                                          animationLoop: true,
                                          itemWidth: 201,
                                          minItems: 2,
                                       slideshow: 1,
                                       move: 1,
                                       controlNav: false,
                                       pausePlay: true,
                                          prevText: "<span class='hide'>Previous</span>",
                                          nextText: "<span class='hide'>Next</span>",
                                          pauseText: "<span class='hide'>Pause</span>",
                                          playText: "<span class='hide'>Play</span>",
                                       
                                       })
                                       });
                                    </script>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="vc_row-full-width vc_clearfix"></div>
                     </div>
                     <!-- end of the loop -->
                  </div>
               </div>
            </div>
         </div>

<button id="chatButton" class="btn btn-primary chat-button">
    <i class="bi bi-chat-dots"></i> 
</button>

<div >
	<form id="chatDialog" class="chat-dialog" action="<?= Yii::$app->urlManager->createUrl('site/incentive-result') ?>" method="post">
		<input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>">
	    <div class="chat-header">
	        <h5>Know Your Incentive</h5>
	        <button id="closeChat" class="btn-close">&times;</button>
	    </div>
	    <div class="chat-body" id="bot_body">
	        
	        

	    </div>
	    <div class="chat-footer">
	    	<i id="bot_loading"></i>
	        <!-- <input type="text" class="form-control" placeholder="Type your message..."> -->
	        <button type="submit" class="btn btn-primary mt-2">Search</button>
	    </div>
    </form>
</div>

<!-- Chatbot Styles and Script -->


<script>
    // Check if the button and chat dialog elements exist
    const chatButton = document.getElementById('chatButton');
    const chatDialog = document.getElementById('chatDialog');
    const closeChat = document.getElementById('closeChat');

    // Make sure these elements are available
    if (chatButton && chatDialog && closeChat) {
        // Open chat dialog
        chatButton.addEventListener('click', function() {
            console.log("Button clicked, opening chat...");
            chatDialog.style.display = 'flex';  // Show the chat dialog
            getfirstquestion(0);
        });

        // Close chat dialog
        closeChat.addEventListener('click', function() {
            chatDialog.style.display = 'none';  // Hide the chat dialog
        });
    } else {
        console.error("Required elements not found!");
    }

    function getfirstquestion(id){
    	$.ajax({
        url: "<?= Yii::$app->urlManager->createUrl('othermodule/getincentivequestions') ?>",
        data : {old_id:id},
        method: 'GET', 
        dataType: 'json', 
        beforeSend: function () {
          // Show the loader before making the request
          $("#bot_loading").text('Loading...');
        },
        success: function (response) {
        	if(response.fields_data.ff_id){
        		$('#bot_body').append('<p><strong>Bot:</strong> '+response.fields_data.search_label+'</p>'+response.fields_data.answers);
          		$("#bot_loading").text('');
        	}else{
        		$("#bot_loading").text('');
        	}
          
        },
        error: function (xhr, status, error) {
          $('#bot_loading').html(`<p>Error: ${error}</p>`);
          $("#bot_loading").text('');
        },
      });
    }

    function nextquestion(id){
    	getfirstquestion(id);
    }
</script>
