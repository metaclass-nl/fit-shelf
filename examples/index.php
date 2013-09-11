<?php
require('config/config.php');
?>

<html>
<head>
<title>PHPFIT & Shelf Examples</title>
</head>
<body>
	<?php foreach ($fitConfig->exampleDirs as $label => $path)
          {
		      print "<h1>$label</h1>";
		      $files = scandir($path);
		      foreach ($files as $key => $val) 
			  {
                  if (strstr($val, ".html")) {
				      print "<a href=\"run-web.php?input_filename=$path/$val\">$val</a><br>";
                  }
	          }
          }
	?>
</table>
</body>
</html>