<?php
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/>
  <title>text Change</title>
  <link rel="stylesheet" href="/css/wide_style.css" type="text/css" />
<script type="text/javascript">
function showKey(e) {
    var Char = e.which;
    // Char++;
    alert("You pressed: " + String.fromCharCode(Char) + ". ["+Char+"]");
}
window.captureEvents(Event.KEYPRESS); 
window.onKeyPress = showKey;
</script>
 </head>
 <body onKeyPress="showKey(event)">
  <div style="font-size: 18px; color: darkgreen;">
   Press A Key
  </div>
 </body>
</html>
