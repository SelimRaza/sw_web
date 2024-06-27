<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FMS MDR AUTO</title>
    <link rel="stylesheet" href="http://mdr.sihirfms.com/css/email.css" media="all" />
</head>
<body>
<header class="clearfix">
    <div id="logo">
        <img src="http://mdr.sihirfms.com/uploads/logo/logo.png">
    </div>
    <h1>{{$pendingMail->title}}</h1>
</header>
<main>
    <div id="company" class="clearfix">
        <div style="color: #9c8338">{{$employee->name}}</div>
        <div style="color: #9c8338">{{$employee->mobile}}</div>
        <div style="color: #9c8338"><a href="mailto:{{$employee->address}}">{{$employee->address}}</a></div>
        <div style="color: #9c8338">Company:{{$employee->company_name}}</div>
        <div style="color: #9c8338">Department:{{$employee->department}}</div>
        <div style="color: #9c8338">Group:{{$employee->group_name}}</div>
    </div>
    <hr style="border-top: 1px solid red;">
    <?php echo $pendingMail->text ?>
    <hr style="border-top: 1px dashed red;">



    <div id="notices">
        <div class="notice">Note:This is Auto generated </div>
    </div>

</main>
<footer>

</footer>
</body>
</html>



