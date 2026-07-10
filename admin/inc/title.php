<?php
?>

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin | <?php echo isset($configs) ? htmlspecialchars($configs->getConfig('APP_NAME'), ENT_QUOTES, 'UTF-8') : 'FLY CASH'; ?></title>
    <meta name="theme-color" content="#6366f1">

    <!--favicon-->
    <link href="./assets/images/favicon.ico" rel="shortcut icon" />
    <!--Modern Admin CSS-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/modern-admin.css?ver=1.0" />
