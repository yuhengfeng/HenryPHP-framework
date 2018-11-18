<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>错误与异常</title>
    <meta name="robots" content="noindex,nofollow" />
    <link rel="stylesheet" href="http://henryphp.test/css/error_message.css">
</head>
<body>
<?php if(isset($source)){ ?>
<div class="exception">
    <div class="info"><h1><?= $name.':'.$message; ?></h1></div>
</div>
<div class="echo">
        <pre><?= $file.'&nbsp;'.$line;?><pre>
            <?php if(count($source)){ ?>
            <pre><?= $source['source']; ?><pre>
            <?php }?>
        <pre>异常过程: <?= $trace;?><pre>

</div>
<?php  }else{ ?>

    <div class="echo">
        <pre><pre><pre><pre><pre><pre><pre><pre>
        <div class="info"><h1><?= $message; ?></h1></div>
    </div>

<?php } ?>
<div class="copyright">
    <a title="官方网站" href="http://www.henryphp.cn">HenryPHP</a>
    <span>V<?= app()->version(); ?></span>
    <span>{ Henry MVC测试框架 }</span>
</div>
</body>
</html>