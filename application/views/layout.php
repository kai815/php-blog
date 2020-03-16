<!DOCTYPE html PUBLIC "-//W#C//DTD XHTML 1.0 Transitionas//EN"http://w3.org/TR/xhtml1/DTD/xhtml1-trasitiona.dtd">
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if (isset($title)): echo $this->escape($title) . ' - '; endif; ?>Mini Blog</title>
<head>
</head>
<body>
    <div id="header">
        <h1><a href="<?php echo $base_url; ?>/">Mini Blog</a></h1>
    </div>
    <div id="main">
        <?php echo $_content; ?>>
    </div>
</body>
</html>