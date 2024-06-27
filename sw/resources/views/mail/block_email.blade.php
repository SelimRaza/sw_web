<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FMS UAE AUTO</title>
    <link rel="stylesheet" href="http://mdr.sihirfms.com/css/email.css" media="all" />
</head>
<body>
<header class="clearfix">
    <div id="logo">
        <img src="http://mdr.sihirfms.com/uploads/logo/logo.png">
    </div>
</header>
<main>
    <?php echo $pendingMail->text ?>
    <div id="notices">
        <div>SO : {{$employee->name.' - '.$employee->mobile}}</div>
        <div class="notice">Note:This is Auto generated </div>
    </div>
</main>
<footer>

</footer>
</body>
</html>



