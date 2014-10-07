<?php
if ( !isset( $_GET['edition_id'] ) || !strlen( $_GET['edition_id'] ) ) {
  return;
}

$preview_url = 'preview.php?edition_id=' . $_GET['edition_id'];
?>
<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Pressroom - Preview</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/reset.css">
  <link rel="stylesheet" href="assets/css/ish.css">
</head>
<body>
<header class="sg-header" role="banner">
  <a class="sg-nav-toggle" href="#sg-nav-container"><span class="icon-menu"></span>Menu</a>
  <div id="sg-nav-container" class="sg-nav-container">
    <ol class="sg-nav">
      <li id="phone" >
      <a class="logo" title="PressRoom">pressroom</a>
      </li>
      <li id="phone" >
          <a class="sg-acc-handle group-device" title="Phone"><i class="fa fa-2x fa-mobile"></i>Phone</a>
          <ol class="sg-acc-panel">
            <li class="tdevice"><a class="sg-acc-handle" data-width="320" data-height="480" href="#">iPhone <small></small><span>3.5"</span></a></li>
            <li class="tdevice"><a class="sg-acc-handle" data-width="640" data-height="960" href="#">iPhone 4 <small></small><span>3.5"</span></a></li>
            <li class="tdevice"><a class="sg-acc-handle" data-width="640" data-height="1136" href="#">iPhone 5 <small></small><span>4.0"</span></a></li>
            <li class="divider"></li>
          </ol>
      </li>
      <li id="tablet" >
          <a class="sg-acc-handle group-device" title="Tablet"><i class="fa fa-2x fa-tablet"></i>Tablet</a>
        <ol class="sg-acc-panel">
          <li class="tdevice"><a class="sg-acc-handle" data-width="1024" data-height="768" href="#">iPad Mini <small></small><span>7.9"</span></a></li>
          <li class="tdevice"><a class="sg-acc-handle" data-width="1024" data-height="768" href="#">iPad 1 &amp; 2 <small></small><span>9.7"</span></a></li>
          <li class="tdevice"><a class="sg-acc-handle" data-width="2048 data-height="1536" href="#">iPad 3 &amp; 4 <small></small><span>9.7"</span></a></li>
          <li class="divider"></li>
        </ol>
      </li>
      <li id="laptop" >
          <a class="sg-acc-handle group-device" title="Laptop"><i class="fa fa-2x fa-laptop"></i>Laptop</a>
          <ol class="sg-acc-panel">
            <li class="tdevice"><a class="sg-acc-handle" data-width="1366" data-height="768" href="#">11' Macbook Air <small></small><span>11"</span></a></li>
            <li class="tdevice"><a class="sg-acc-handle" data-width="1440" data-height="900" href="#">13' Macbook Air <small></small><span>13"</span></a></li>
            <li class="tdevice"><a class="sg-acc-handle" data-width="1440" data-height="900" href="#">15' Macbook Pro <small></small><span>15"</span></a></li>
            <li ><a class="sg-acc-handle" data-width="2880" data-height="1800" >15' Macbook Pro Retina <small></small><span>15"</span></a></li>
            <li class="divider"></li>
          </ol>
      </li>
      <li id="desktop" >
          <a id="reset" class="sg-acc-handle group-device " title="Desktop">Reset</a>
      </li>
    <ol>
    <div class="sg-controls" id="sg-controls">
      <div class="sg-control-content">
        <ul class="sg-control">
          <li class="sg-size">
            <div class="sg-current-size">
              <form id="sg-form">
                Width <input type="text" class="sg-input sg-size-px" value="">
                Height <input type="text" class="sg-input sg-size-height" value="">
              </form>
            </div><!--end #sg-current-size-->
          </li>
        </ul>
      </div>
    </div>
  </div>
</header>
<div id="sg-vp-wrap">
  <div id="sg-cover"></div>
  <div id="sg-gen-container">
    <iframe id="sg-viewport" src="<?php echo $preview_url ?>" sandbox="allow-same-origin allow-scripts allow-top-navigation"></iframe>
  </div>
</div>
<script src="assets/js/jquery-2.0.3.min.js"></script>
<script src="assets/js/ish_init.js"></script>
</body>
</html>
