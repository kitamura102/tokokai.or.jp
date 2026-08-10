<?php
     $options = get_design_plus_option();

     // スプラッシュ画面
     if( (is_front_page() && $options['show_splash'] && !isset($_COOKIE['splash_screen']) && $options['splash_display_time'] == 'type1') || (is_front_page() && $options['show_splash'] && $options['splash_display_time'] == 'type2') ){
?>
#splash_screen {
  position:relative; overflow:hidden; background:#fff;
  position:fixed; top:0px; left:0px; width:100%; height:100%; width:100%; height:100vh; z-index:9999999;
  opacity:1; transition: opacity 2.0s ease 0s;
  display:flex; flex-wrap:wrap; justify-content:center; align-items:center;
}
body.end_loading #splash_screen { opacity:0; pointer-events:none; }
#splash_screen .catch { z-index:3; color:<?php echo esc_html($options['splash_catch_font_color']); ?>; font-size:<?php echo esc_html($options['splash_catch_font_size']); ?>px !important; text-align:center; line-height:1.7; z-index:100; position:relative; }
#splash_screen .catch.direction_type2 { writing-mode:vertical-rl; display:inline-block; text-align:left; }
#splash_screen .catch span { display:block; position:relative; transform: translate3d(0,30px,0); opacity:0; }
#splash_screen.animate .catch span { transform: translate3d(0,0,0); opacity:1; }
#splash_screen.animate .catch span:nth-child(1) { transition: transform 1.8s ease 1.0s, opacity 1.8s ease 1.0s; }
#splash_screen.animate .catch span:nth-child(2) { transition: transform 1.8s ease 1.5s, opacity 1.8s ease 1.5s; }
#splash_screen.animate .catch span:nth-child(3) { transition: transform 1.8s ease 2.0s, opacity 1.8s ease 2.0s; }
#splash_screen.animate .catch span:nth-child(4) { transition: transform 1.8s ease 2.5s, opacity 1.8s ease 2.5s; }
#splash_screen .logo { z-index:3; opacity:0; transition: opacity 1.8s ease 0.8s; position:absolute; left:50%; top:50%; transform: translate(-50%, -50%); }
#splash_screen.animate .logo { opacity:1; }
#splash_screen .overlay { width:100%; height:100%; position:absolute; top:0; left:0; z-index:2; }
#splash_screen.use_bg_blur .overlay { backdrop-filter:blur(10px); }
#splash_screen .bg_image { position:absolute; top:0; left:0; width:100%; height:100%; z-index:1; }
#splash_screen .bg_image img { opacity:0; transition: opacity 2s ease; width:100%; height:100%; position:absolute; top:0; left:0; object-fit:cover; }
#splash_screen.animate .bg_image img { opacity:1; }
@media screen and (max-width:1300px) {
  #splash_screen .catch { font-size:<?php echo esc_html(floor( (( int )$options['splash_catch_font_size'] + ( int )$options['splash_catch_font_size_sp']) / 2)); ?>px  !important; }
}
@media screen and (max-width:800px) {
  #splash_screen .catch { font-size:<?php echo esc_html($options['splash_catch_font_size_sp']); ?>px !important; }
}
<?php
     };

     // ロード画面 ------------------------------------------------------------
?>
#site_loader_overlay {
  position:relative; overflow:hidden;
  position:fixed; top:0px; left:0px; width:100%; height:100%; width:100%; height:100vh; z-index:99999;
  opacity:1;
  transition: opacity 0.7s ease 0s;
  background:<?php echo esc_attr($options['loading_bg_color']); ?>;
}
body.end_loading #site_loader_overlay { opacity:0; pointer-events:none; }
#site_loader_overlay > div { opacity:1; transition: opacity 0.2s ease; }
body.end_loading #site_loader_overlay > div { }
body.end_loading #site_loader_overlay.move_next_page {
  pointer-events:auto;
  transition: opacity 0.4s ease 0s;
  opacity:1;
}
body.end_loading #site_loader_overlay.move_next_page > div { opacity:1; transition: opacity 0.2s ease 0.4s; }
@media screen and (max-width:1024px) {
  #site_loader_overlay > div { transition: opacity 1s ease; }
  body.end_loading #site_loader_overlay { transition: opacity 1s ease 0s; opacity:0; }
}
<?php
     // コンテンツ ----------------------------------------
?>
#site_wrap { display:none; }
<?php
     // サークルアニメーション ------------------------------------------------------------------------------------
     if($options['loading_type'] == 'type1'){
?>
.circular_loader {
  position:absolute; width:60px; z-index:10;
  left:50%; top:50%; -webkit-transform: translate(-50%, -50%); transform: translate(-50%, -50%);
}
.circular_loader:before { content:''; display:block; padding-top:100%; }
.circular_loader .circular {
  width:100%; height:100%;
  -webkit-animation: circular_loader_rotate 2s linear infinite; animation: circular_loader_rotate 2s linear infinite;
  -webkit-transform-origin: center center; -ms-transform-origin: center center; transform-origin: center center;
  position: absolute; top:0; bottom:0; left:0; right:0; margin:auto;
}
.circular_loader .path {
  stroke-dasharray: 1, 200;
  stroke-dashoffset: 0;
  stroke-linecap: round;
  stroke: <?php echo $options['loading_icon_color']; ?>;
  -webkit-animation: circular_loader_dash 1.5s ease-in-out infinite; animation: circular_loader_dash 1.5s ease-in-out infinite;
}
@-webkit-keyframes circular_loader_rotate {
  100% { -webkit-transform: rotate(360deg); transform: rotate(360deg); }
}
@keyframes circular_loader_rotate {
  100% { -webkit-transform: rotate(360deg); transform: rotate(360deg); }
}
@-webkit-keyframes circular_loader_dash {
  0% { stroke-dasharray: 1, 200; stroke-dashoffset: 0; }
  50% { stroke-dasharray: 89, 200; stroke-dashoffset: -35; }
  100% { stroke-dasharray: 89, 200; stroke-dashoffset: -124; }
}
@keyframes circular_loader_dash {
  0% { stroke-dasharray: 1, 200; stroke-dashoffset: 0; }
  50% { stroke-dasharray: 89, 200; stroke-dashoffset: -35; }
  100% { stroke-dasharray: 89, 200; stroke-dashoffset: -124; }
}
@media screen and (max-width:750px) {
  .circular_loader { width:40px; }
}

<?php
     // スクエアアニメーション ------------------------------------------------------------------------------------
     } elseif($options['loading_type'] == 'type2'){
?>
.sk-cube-grid {
  width:60px; height:60px; z-index:10;
  position:absolute; left:50%; top:50%; -ms-transform: translate(-50%, -50%); -webkit-transform: translate(-50%, -50%); transform: translate(-50%, -50%);
}
@media screen and (max-width:750px) {
  .sk-cube-grid { width:40px; height:40px; }
}
.sk-cube-grid .sk-cube {
  background-color: <?php echo $options['loading_icon_color']; ?>;
  width:33%; height:33%; float:left;
  -webkit-animation: sk-cubeGridScaleDelay 1.3s infinite ease-in-out; animation: sk-cubeGridScaleDelay 1.3s infinite ease-in-out; 
}
.sk-cube-grid .sk-cube1 { -webkit-animation-delay: 0.2s; animation-delay: 0.2s; }
.sk-cube-grid .sk-cube2 { -webkit-animation-delay: 0.3s; animation-delay: 0.3s; }
.sk-cube-grid .sk-cube3 { -webkit-animation-delay: 0.4s; animation-delay: 0.4s; }
.sk-cube-grid .sk-cube4 { -webkit-animation-delay: 0.1s; animation-delay: 0.1s; }
.sk-cube-grid .sk-cube5 { -webkit-animation-delay: 0.2s; animation-delay: 0.2s; }
.sk-cube-grid .sk-cube6 { -webkit-animation-delay: 0.3s; animation-delay: 0.3s; }
.sk-cube-grid .sk-cube7 { -webkit-animation-delay: 0s; animation-delay: 0s; }
.sk-cube-grid .sk-cube8 { -webkit-animation-delay: 0.1s; animation-delay: 0.1s; }
.sk-cube-grid .sk-cube9 { -webkit-animation-delay: 0.2s; animation-delay: 0.2s; }
@-webkit-keyframes sk-cubeGridScaleDelay {
  0%, 70%, 100% { -webkit-transform: scale3D(1, 1, 1); transform: scale3D(1, 1, 1); }
  35% { -webkit-transform: scale3D(0, 0, 1); transform: scale3D(0, 0, 1); }
}
@keyframes sk-cubeGridScaleDelay {
  0%, 70%, 100% { -webkit-transform: scale3D(1, 1, 1); transform: scale3D(1, 1, 1); }
  35% { -webkit-transform: scale3D(0, 0, 1); transform: scale3D(0, 0, 1); }
}

<?php
     // ドットアニメーション ------------------------------------------------------------------------------------
     } elseif($options['loading_type'] == 'type3'){
?>
.sk-circle {
  width:60px; height:60px; z-index:10;
  position:absolute; left:50%; top:50%; -ms-transform: translate(-50%, -50%); -webkit-transform: translate(-50%, -50%); transform: translate(-50%, -50%);
}
@media screen and (max-width:750px) {
  .sk-circle { width:40px; height:40px; }
}
.sk-circle .sk-child {
  width: 100%;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
}
.sk-circle .sk-child:before {
  content: '';
  display: block;
  margin: 0 auto;
  width: 15%;
  height: 15%;
  background-color: <?php echo $options['loading_icon_color']; ?>;
  border-radius: 100%;
  -webkit-animation: sk-circleBounceDelay 1.2s infinite ease-in-out both;
          animation: sk-circleBounceDelay 1.2s infinite ease-in-out both;
}
.sk-circle .sk-circle2 {
  -webkit-transform: rotate(30deg);
      -ms-transform: rotate(30deg);
          transform: rotate(30deg); }
.sk-circle .sk-circle3 {
  -webkit-transform: rotate(60deg);
      -ms-transform: rotate(60deg);
          transform: rotate(60deg); }
.sk-circle .sk-circle4 {
  -webkit-transform: rotate(90deg);
      -ms-transform: rotate(90deg);
          transform: rotate(90deg); }
.sk-circle .sk-circle5 {
  -webkit-transform: rotate(120deg);
      -ms-transform: rotate(120deg);
          transform: rotate(120deg); }
.sk-circle .sk-circle6 {
  -webkit-transform: rotate(150deg);
      -ms-transform: rotate(150deg);
          transform: rotate(150deg); }
.sk-circle .sk-circle7 {
  -webkit-transform: rotate(180deg);
      -ms-transform: rotate(180deg);
          transform: rotate(180deg); }
.sk-circle .sk-circle8 {
  -webkit-transform: rotate(210deg);
      -ms-transform: rotate(210deg);
          transform: rotate(210deg); }
.sk-circle .sk-circle9 {
  -webkit-transform: rotate(240deg);
      -ms-transform: rotate(240deg);
          transform: rotate(240deg); }
.sk-circle .sk-circle10 {
  -webkit-transform: rotate(270deg);
      -ms-transform: rotate(270deg);
          transform: rotate(270deg); }
.sk-circle .sk-circle11 {
  -webkit-transform: rotate(300deg);
      -ms-transform: rotate(300deg);
          transform: rotate(300deg); }
.sk-circle .sk-circle12 {
  -webkit-transform: rotate(330deg);
      -ms-transform: rotate(330deg);
          transform: rotate(330deg); }
.sk-circle .sk-circle2:before {
  -webkit-animation-delay: -1.1s;
          animation-delay: -1.1s; }
.sk-circle .sk-circle3:before {
  -webkit-animation-delay: -1s;
          animation-delay: -1s; }
.sk-circle .sk-circle4:before {
  -webkit-animation-delay: -0.9s;
          animation-delay: -0.9s; }
.sk-circle .sk-circle5:before {
  -webkit-animation-delay: -0.8s;
          animation-delay: -0.8s; }
.sk-circle .sk-circle6:before {
  -webkit-animation-delay: -0.7s;
          animation-delay: -0.7s; }
.sk-circle .sk-circle7:before {
  -webkit-animation-delay: -0.6s;
          animation-delay: -0.6s; }
.sk-circle .sk-circle8:before {
  -webkit-animation-delay: -0.5s;
          animation-delay: -0.5s; }
.sk-circle .sk-circle9:before {
  -webkit-animation-delay: -0.4s;
          animation-delay: -0.4s; }
.sk-circle .sk-circle10:before {
  -webkit-animation-delay: -0.3s;
          animation-delay: -0.3s; }
.sk-circle .sk-circle11:before {
  -webkit-animation-delay: -0.2s;
          animation-delay: -0.2s; }
.sk-circle .sk-circle12:before {
  -webkit-animation-delay: -0.1s;
          animation-delay: -0.1s; }

@-webkit-keyframes sk-circleBounceDelay {
  0%, 80%, 100% {
    -webkit-transform: scale(0);
            transform: scale(0);
  } 40% {
    -webkit-transform: scale(1);
            transform: scale(1);
  }
}

@keyframes sk-circleBounceDelay {
  0%, 80%, 100% {
    -webkit-transform: scale(0);
            transform: scale(0);
  } 40% {
    -webkit-transform: scale(1);
            transform: scale(1);
  }
}
<?php } ?>