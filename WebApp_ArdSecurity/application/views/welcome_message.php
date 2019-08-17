
<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=9"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css" media="screen">

    html, body {
    	width: 100%;
    	height: 100%;
    	padding: 0;
    	margin: 0;

    }

    #mapa{
    	background-image: url( '<?= base_url('assets/plano.gif') ?>');
    	background-repeat: no-repeat;
    	background-position: center;
    	width: 100%;
    	height: 100%;
    }

    </style>
    <script type="text/javascript">
    </script>
  </head>
  <body>





<div id="mapa">
<svg height="1000px" width="1000px">	
  <rect id="myRect" height="100px" width="100px" fill="blue"/>
</svg>	
</div>


 </body>
</html>
