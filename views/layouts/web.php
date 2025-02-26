<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;


AppAsset::register($this);

?>

<!DOCTYPE html>
<html lang="en-US">
   <head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>
        Directorate of Industry
      </title>
      <style>
         @font-face {
         font-family: 'icomoon';
         src: url("cssfonts/icomooncb5a.eot?y6palq");
         src: url("website/css/fonts/icomooncb5a.eot?y6palq#iefix") format("embedded-opentype"), url("website/css/fonts/icomooncb5a.ttf?y6palq") format("truetype"), url("website/css/fonts/icomooncb5a.woff?y6palq") format("woff"), url("website/css/fonts/icomooncb5a.svg?y6palq#icomoon") format("svg");
         font-weight: normal;
         font-style: normal; }
      </style>
      <link rel="profile" href="http://gmpg.org/xfn/11" />
      <meta name='robots' content='max-image-preview:large' />
      <meta name="description" content="Secure, Scalable and Sugamya Website as a Service" />
      <meta name="keywords" content="Home" />
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
      <link rel='stylesheet' id='theme-my-login-css' href="<?= Yii::$app->urlManager->baseUrl ?>/website/css/mainstyle.css" media='all' />
      <link rel='stylesheet' id='theme-my-login-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/theme-my-login.css' media='all' />
      <!-- <link rel='stylesheet' id='wp-block-library-css' href='website/css/style.min.css' media='all' /> -->
      <link rel='stylesheet' id='base-css-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/base.css' media='all' />
      <link rel='stylesheet' id='extra-feature-css-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/extra.features.css' media='all' />
      <link rel='stylesheet' id='contact-form-7-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/styles.css' media='all' />
      <link rel='stylesheet' id='wsl-widget-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/style.css' media='all' />
      <link rel='stylesheet' id='sliderhelper-css-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/sliderhelper.css' media='all' />
      <link rel='stylesheet' id='main-css-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/style1.css' media='all' />
      <link rel='stylesheet' id='js_composer_front-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/js_composer.min.css' media='all' />
      <link rel='stylesheet' id='fontawesome-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/font-awsome.css' media='all' />
      <link rel='stylesheet' id='extra_css-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/extra.css' media='screen' />
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/jquery.min.js" id="jquery-core-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/jquery-migrate.min.js" id="jquery-migrate-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/themed-profiles.js" id="tml-themed-profiles-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/core.min.js" id="jquery-ui-core-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/external.js" id="external-link-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/main.js" id="external-link-js"></script>
      <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

   </head>
   <body class="home page-template-default page page-id-17697 mva7-thc-activetheme-district-theme lang-en wpb-js-composer js-comp-ver-5.4.7 vc_responsive">
      <header>
         <div id="topBar" class="wrapper make-accessible-header">
            <div class="container">
               <div aria-label="Primary">
                  <div id="accessibility">
                     <ul id="accessibilityMenu">
                        <li><a href="#SkipContent" class="skip-to-content" title="Skip to main content"><span class="icon-skip-to-main responsive-show"></span><strong class="responsive-hide">SKIP TO MAIN CONTENT</strong></a></li>
                        <li>
                        </li>
                        <li><a lang="en" href="#">Government of Meghalaya</a></li>
                        <li class="searchbox">
                           <a href="javascript:void(0);" title="Site Search" aria-label="Site Search" role="button" data-toggle="dropdown">
                           <img class="show-con" src="<?= Yii::$app->urlManager->baseUrl ?>/website/images/search-icon.png" title="Search Icon" alt="Search Icon" />
                           </a>
                           <div class="goiSearch">
                              <form onsubmit="return search_validation()" action="#" method="get">
                                 <label for="search" class="hide">Search</label>
                                 <input type="hidden" id="csrf_nonce" name="csrf_nonce" value="9f91cfc867" /><input type="hidden" name="_wp_http_referer" value="/" />                        <input type="search" title="Enter Text" name="s" id="search" value="" />
                                 <button type="submit" title="Search"><small class="tcon">Search</small><span class="icon-search" aria-hidden="true"></span></button>
                              </form>
                           </div>
                        </li>
                        <li>
                           <a href="#" title="Social Media Links" class="show-social-links" role="button" data-toggle="dropdown">
                           <img class="show-con" src="<?= Yii::$app->urlManager->baseUrl ?>/website/images/social-icon.png" title="Social Icon" alt="Social Icon" />
                           <span class="off-css">Social Media Links</span>
                           </a>                  
                           <ul class="socialIcons">
                              <li><a href="#" target="_blank" aria-label="Facebook | External site that opens in a new window"><img src="<?= Yii::$app->urlManager->baseUrl ?>/website/images/ico-facebook.png" title="Facebook | External site that opens in a new window" alt="Facebook, External Link that opens in a new window"></a></li>
                              <li><a href="#" target="_blank" aria-label="Twitter | External site that opens in a new window"><img src="<?= Yii::$app->urlManager->baseUrl ?>/website/images/ico-twitter.png" title="Twitter | External site that opens in a new window" alt="Twitter | External site that opens in a new window"></a></li>
                              <li><a href="#" target="_blank" aria-label="Youtube | External site that opens in a new window"><img src="<?= Yii::$app->urlManager->baseUrl ?>/website/images/ico-youtube.png" title="Youtube | External site that opens in a new window" alt="Youtube | External site that opens in a new window"></a></li>
                           </ul>
                        </li>
                        <li>
                           <a href="#" title="Sitemap">
                           <img class="show-con" src="<?= Yii::$app->urlManager->baseUrl ?>/website/images/sitemap-icon.png" title="Sitemap Icon" alt="Sitemap Icon" />
                           <span class="off-css">Site Map</span>
                           </a>
                        </li>
                        <li>
                           <a href="javascript:void(0);" title="Accessibility Links" aria-label="Accessibility Links" class="accessible-icon" role="button" data-toggle="dropdown">
                           <img class="show-con" src="<?= Yii::$app->urlManager->baseUrl ?>/website/images/accessibility.png" title="Accessibility Icon" alt="Accessibility Icon" />
                           <span class="off-css">Accessibility Links</span>
                           </a>
                           <ul class="accessiblelinks textSizing" aria-label="Font size and Contrast controls">
                              <li class="fontSizeEvent"><a data-selected-text="selected" data-event-type="increase" href="javascript:void(0);" data-label="Font Size Increase" aria-label="Font Size Increase" title="Font Size Increase"><span aria-hidden="true">A+</span><span class="off-css"> Font Size Increase</span></a></li>
                              <li class="fontSizeEvent"><a data-selected-text="selected" data-event-type="normal" href="javascript:void(0);" data-label="Normal Font" aria-label="Normal Font - Selected" title="Normal Font - Selected"><span aria-hidden="true">A</span><span class="off-css"> Normal Font - Selected</span></a></li>
                              <li class="fontSizeEvent"><a data-selected-text="selected" data-event-type="decrease" href="javascript:void(0);" data-label="Font Size Decrease" aria-label="Font Size Decrease" title="Font Size Decrease"><span aria-hidden="true">A-</span><span class="off-css"> Font Size Decrease</span></a></li>
                              <li class="highContrast dark tog-con">
                                 <a href="javascript:void(0);" aria-label="High Contrast" title="High Contrast"><span aria-hidden="true">A</span> <span class="tcon">High Contrast</span></a>
                              </li>
                              <li class="highContrast light">
                                 <a class="link-selected" href="javascript:void(0);" aria-label="Normal Contrast - Selected" title="Normal Contrast - Selected"><span aria-hidden="true">A</span> <span class="tcon">Normal Contrast - Selected</span></a>
                              </li>
                           </ul>
                        </li>
                        <li>
                           <a href="javascript:void(0);" class="change-language link-selected" aria-label="English - Selected" title="English - Selected" role="button" data-toggle="dropdown">
                           English                  </a>
                           <ul class="socialIcons select-lang">
                              <li class="lang-item lang-item-134 lang-item-hi mFocus"><a lang="hi" hreflang="hi-IN" href="#" aria-label="हिन्दी" title="हिन्दी">हिन्दी</a></li>                           
                           </ul>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
         <div class="wrapper header-wrapper">
            <div class="container header-container">
               <div class="logo">
                  <a href="#" title="Go to home" class="emblem" rel="home">
                     <img class="site_logo" height="100" id="logo" src="<?= Yii::$app->urlManager->baseUrl ?>/website/images/logo.png" alt="State Emblem of India" >
                     <div class="logo-text">
                        
                        <h6 class="site_name_english">Commerce and Industry Department Meghalaya</h6>
                     </div>
                  </a>
               </div>
               <div class="header-right clearfix">
                  <div class="right-content clearfix">
                     <div class="float-element">
                        <a aria-label="Digital India - External site that opens in a new window" href="http://digitalindia.gov.in/" target= "_blank" title="Digital India">
                        <img class="sw-logo" height="95" src="<?= Yii::$app->urlManager->baseUrl ?>/website/images/digital-india.png" alt="Digital India">
                        </a>
                     </div>
                  </div>
               </div>
               <a class="menuToggle" href="javascript:void(0);" aria-label="Mobile Menu">Menu Toggle</a>
            </div>
         </div>
         <div class="menuWrapper">
            <div class="menuMoreText hide">More</div>
            <div class="container">
               <nav class="menu">
                  <ul id="menu-header-en" class="nav clearfix">
                     <li id="menu-item-2658" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-2658 active "><a href="<?= Yii::$app->urlManager->createUrl('/') ?>" aria-current="page">Home</a></li>
                     <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children">
                        <a href="#">About </a>
                        <ul class="sub-menu">
                           <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2736"><a href="history.html">History</a></li>
                           <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2492"><a href="who-who.html">Who’s Who</a></li>
                           <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2742"><a href="#">Organisation Chart</a></li>
                           <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children">
                              <a href="#">Administrative Setup</a>
                              <ul class="sub-menu">
                                 <li id="menu-item-2758" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2758"><a href="#">Tehsil</a></li>
                                 <li id="menu-item-2774" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2774"><a href="#">Subdivision &#038; Blocks</a></li>
                                 <li id="menu-item-2773" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2773"><a href="#">Village &#038; Panchayats</a></li>
                                 <li id="menu-item-2770" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2770"><a href="#">Constituencies</a></li>
                              </ul>
                           </li>
                           <li id="menu-item-2750" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2750"><a href="#">Economy</a></li>
                          
                        </ul>
                     </li>
                     <li id="menu-item-2804" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-2804">
                        <a href="#">Departments </a>
                        <ul class="sub-menu">
                           <li id="menu-item-2803" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2803"><a href="#">Health</a></li>
                           <li id="menu-item-2802" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2802"><a href="#">Education</a></li>
                           <li id="menu-item-2801" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2801"><a href="#">Handicraft</a></li>
                           <li id="menu-item-2800" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2800"><a href="#">Animal Husbandry</a></li>
                        </ul>
                     </li>
                     <li id="menu-item-2829" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-2829">
                        <a href="#">Tourism </a>
                        <ul class="sub-menu">
                           <li id="menu-item-2828" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2828"><a href="#">How to Reach</a></li>
                           <li id="menu-item-2827" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2827"><a href="#">Places of Interest</a></li>
                           <li id="menu-item-2826" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2826"><a href="#/">Culture &#038; Heritage</a></li>
                           <li id="menu-item-2825" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2825"><a href="#">Accommodation (Hotel/Resort/Dharamsala)</a></li>
                           <li id="menu-item-2824" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2824"><a href="#">Adventures</a></li>
                           <li id="menu-item-2823" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2823"><a href="#">Handicraft</a></li>
                           <li id="menu-item-2822" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2822"><a href="#">Tourist Packages</a></li>
                           <li id="menu-item-3364" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-3364"><a href="#">Tourist Places</a></li>
                           <li id="menu-item-27233" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-27233"><a href="#">Culinary Delights</a></li>
                           <li id="menu-item-27232" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-27232"><a href="#">Festivals</a></li>
                           <li id="menu-item-27231" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-27231"><a href="#">Where To Stay</a></li>
                        </ul>
                     </li>
                     <li id="menu-item-3218" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-3218">
                        <a href="#">Documents </a>
                        <ul class="sub-menu">
                           <li id="menu-item-2845" class="menu-item menu-item-type-taxonomy menu-item-object-document-category menu-item-2845"><a href="annual-report.html">Annual Report</a></li>
                           <li id="menu-item-2851" class="menu-item menu-item-type-taxonomy menu-item-object-document-category menu-item-2851"><a href="#">Office Order</a></li>
                           <li id="menu-item-2846" class="menu-item menu-item-type-taxonomy menu-item-object-document-category menu-item-2846"><a href="#">Census</a></li>
                           <li id="menu-item-2849" class="menu-item menu-item-type-taxonomy menu-item-object-document-category menu-item-2849"><a href="#">Guidelines</a></li>
                           <li id="menu-item-2848" class="menu-item menu-item-type-taxonomy menu-item-object-document-category menu-item-2848"><a href="#">District Profile</a></li>
                           <li id="menu-item-2853" class="menu-item menu-item-type-taxonomy menu-item-object-document-category menu-item-2853"><a href="#">Plan Report</a></li>
                           <li id="menu-item-2854" class="menu-item menu-item-type-taxonomy menu-item-object-document-category menu-item-2854"><a href="#">Statistical Report</a></li>
                           <li id="menu-item-2847" class="menu-item menu-item-type-taxonomy menu-item-object-document-category menu-item-2847"><a href="#">Citizen Charter</a></li>
                           <li id="menu-item-2852" class="menu-item menu-item-type-taxonomy menu-item-object-document-category menu-item-2852"><a href="#">Others</a></li>
                           <li id="menu-item-2850" class="menu-item menu-item-type-taxonomy menu-item-object-document-category menu-item-2850"><a href="#">Notification</a></li>
                        </ul>
                     </li>
                     <li id="menu-item-2833" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2833"><a href="forms.html">Forms</a></li>
                     <li id="menu-item-2466" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-2466">
                        <a href="#">Notices </a>
                        <ul class="sub-menu">
                           <li id="menu-item-2493" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2493"><a href="#">Events</a></li>
                           <li id="menu-item-2661" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2661"><a href="#">Upcoming Events</a></li>
                           <li id="menu-item-2468" class="menu-item menu-item-type-taxonomy menu-item-object-notice_category menu-item-2468"><a href="#">Announcements</a></li>
                           <li id="menu-item-2469" class="menu-item menu-item-type-taxonomy menu-item-object-notice_category menu-item-2469"><a href="#">Recruitment</a></li>
                           <li id="menu-item-2467" class="menu-item menu-item-type-taxonomy menu-item-object-notice_category menu-item-2467"><a href="#">Tenders</a></li>
                        </ul>
                     </li>
                     <li id="menu-item-2494" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2494"><a href="services.html">Citizen Services</a></li>
                     <li id="menu-item-2495" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2495">
                        <a href="<?= Yii::$app->urlManager->createUrl('/site/kya') ?>">Know Your Approval</a>
                     </li>
                    
                     <li id="menu-item-2477" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-2477">
                        <a href="#">Media Gallery</a>
                        <ul class="sub-menu">
                           <li id="menu-item-2496" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2496"><a href="#">Photo Gallery</a></li>
                           <li id="menu-item-18345" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-18345"><a href="#">Audio Gallery</a></li>
                           <li id="menu-item-2497" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2497"><a href="#">Video Gallery</a></li>
                           <li id="menu-item-3167" class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-3167"><a href="#">Press Release</a></li>
                        </ul>
                     </li>                    
                  </ul>
               </nav>
            </div>
         </div>
         <div class="clearfix"></div>
         <div id="overflowMenu">
            <div class="ofMenu">
               <ul>
               </ul>
            </div>
            <a title="Close" href="javascript:void(0);" class="closeMenu"><span class="icon-close" aria-hidden="true"></span> Close</a>
         </div>
      </header>
      <main>
         <?= $content ?>
      </main>
      <footer id="footer" class="footer-home">
         <div class="container">
            <div class="footerMenu">
               <ul id="menu-footer-en" class="menu">
                  <li id="menu-item-2501" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2501"><a href="#/">Website Policies</a></li>
                  <li id="menu-item-2503" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2503"><a href="#">Help</a></li>
                  <li id="menu-item-2506" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2506"><a href="#">Contact Us</a></li>
                  <li id="menu-item-2535" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2535"><a href="#">Feedback</a></li>
               </ul>
            </div>
            <div class="copyRights">
               <div class="pd-bottom5 color-white ctnt-ownd-dis">Content Owned by District Administration</div>
               <div class="copyRightsText">
                  <p> Developed and hosted by <a href="http://www.nic.in/" target="_blank">Deloitte</a>,<br><a href="http://meity.gov.in/" target="_blank">Ministry of Electronics & Information Technology</a>, Government of Meghalaya</p>
                  <p>Last Updated: <strong>Dec 4, 2024</strong></p>
                  <div class="certification-logo ">
                     <div class="certification-cont">
                        <img src="<?= Yii::$app->urlManager->baseUrl ?>/website/images/certification-logo.png" alt="STQC Accessible Logo" />
                        <strong>Verified Accessible Theme</strong>
                     </div>
                  </div>
               </div>
               <div class="copyRightsLogos">
               <a href="http://www.digitalindia.gov.in/"><img src="<?= Yii::$app->urlManager->baseUrl ?>/website/images/digitalIndia.png" alt="Digital India opens a new window"></a>
               </div>
            </div>
         </div>
      </footer>
      <link rel='stylesheet' id='flexslider-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/flexslider.min.css' media='all' />
      <link rel='stylesheet' id='custom-flexslider-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/custom-flexslider.css' media='all' />
      <link rel='stylesheet' id='list-style-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/list-style.min.css' media='all' />
      <link rel='stylesheet' id='component-helper-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/component-helper.css' media='all' />
      <link rel='stylesheet' id='min-profile-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/profile-hm.css' media='all' />
      <link rel='stylesheet' id='services-style-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/list-style.min1.css' media='all' />
      <link rel='stylesheet' id='services-tabs-style-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/service-tabs.css' media='all' />
      <link rel='stylesheet' id='footer-style-css' href='<?= Yii::$app->urlManager->baseUrl ?>/website/css/footer-logo-carousel.css' media='all' />
     
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/common.js" id="utility-common-js-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/jquery.flexslider.js" id="jquery-flexslider-js-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/easyResponsiveTabs.js" id="easyResponsiveTabs-js-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/jquery.fancybox.js" id="jquery-fancybox-js-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/style.switcher.js" id="style-switcher-js-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/menu.js" id="mega-menu-js-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/table.min.js" id="table-min-js-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/custom.js" id="custom-js-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/extra.js" id="extra-js-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/js_composer_front.min.js" id="wpb_composer_front_js-js"></script>
      <script src="<?= Yii::$app->urlManager->baseUrl ?>/website/js/jquery.flexslider-min.js" id="flexslider-js"></script>
      




      
   </body>
</html>   