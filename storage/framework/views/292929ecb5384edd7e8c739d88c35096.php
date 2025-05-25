<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700" rel="stylesheet">

    <!-- Favicon и иконки -->
    <link type="image/x-icon" rel="shortcut icon" href="<?php echo e(asset('favicon.ico')); ?>">

    <!-- Дополнительные иконки для десктопных браузеров -->
    <link type="image/png" sizes="16x16" rel="icon" href="<?php echo e(asset('icons/favicon-16x16.png')); ?>">
    <link type="image/png" sizes="32x32" rel="icon" href="<?php echo e(asset('icons/favicon-32x32.png')); ?>">
    <link type="image/png" sizes="96x96" rel="icon" href="<?php echo e(asset('icons/favicon-96x96.png')); ?>">
    <link type="image/png" sizes="120x120" rel="icon" href="<?php echo e(asset('icons/favicon-120x120.png')); ?>">

    <!-- Иконки для Android -->
    <link type="image/png" sizes="72x72" rel="icon" href="<?php echo e(asset('icons/android-icon-72x72.png')); ?>">
    <link type="image/png" sizes="96x96" rel="icon" href="<?php echo e(asset('icons/android-icon-96x96.png')); ?>">
    <link type="image/png" sizes="144x144" rel="icon" href="<?php echo e(asset('icons/android-icon-144x144.png')); ?>">
    <link type="image/png" sizes="192x192" rel="icon" href="<?php echo e(asset('icons/android-icon-192x192.png')); ?>">
    <link type="image/png" sizes="512x512" rel="icon" href="<?php echo e(asset('icons/android-icon-512x512.png')); ?>">
    <link rel="manifest" href="<?php echo e(asset('manifest.json')); ?>">

    <!-- Иконки для iOS (Apple) -->
    <link sizes="57x57" rel="apple-touch-icon" href="<?php echo e(asset('icons/apple-touch-icon-57x57.png')); ?>">
    <link sizes="60x60" rel="apple-touch-icon" href="<?php echo e(asset('icons/apple-touch-icon-60x60.png')); ?>">
    <link sizes="72x72" rel="apple-touch-icon" href="<?php echo e(asset('icons/apple-touch-icon-72x72.png')); ?>">
    <link sizes="76x76" rel="apple-touch-icon" href="<?php echo e(asset('icons/apple-touch-icon-76x76.png')); ?>">
    <link sizes="114x114" rel="apple-touch-icon" href="<?php echo e(asset('icons/apple-touch-icon-114x114.png')); ?>">
    <link sizes="120x120" rel="apple-touch-icon" href="<?php echo e(asset('icons/apple-touch-icon-120x120.png')); ?>">
    <link sizes="144x144" rel="apple-touch-icon" href="<?php echo e(asset('icons/apple-touch-icon-144x144.png')); ?>">
    <link sizes="152x152" rel="apple-touch-icon" href="<?php echo e(asset('icons/apple-touch-icon-152x152.png')); ?>">
    <link sizes="180x180" rel="apple-touch-icon" href="<?php echo e(asset('icons/apple-touch-icon-180x180.png')); ?>">

    <!-- Иконки для MacOS (Apple) -->
    <link color="#e52037" rel="mask-icon" href="<?php echo e(asset('icons/safari-pinned-tab.svg')); ?>">

    <!-- Иконки и цвета для плиток Windows -->
    <meta name="msapplication-TileColor" content="#2b5797">
    <meta name="msapplication-TileImage" content="<?php echo e(asset('icons/mstile-144x144.png')); ?>">
    <meta name="msapplication-square70x70logo" content="<?php echo e(asset('icons/mstile-70x70.png')); ?>">
    <meta name="msapplication-square150x150logo" content="<?php echo e(asset('icons/mstile-150x150.png')); ?>">
    <meta name="msapplication-wide310x150logo" content="<?php echo e(asset('icons/mstile-310x150.png')); ?>">
    <meta name="msapplication-square310x310logo" content="<?php echo e(asset('icons/mstile-310x310.png')); ?>">
    <meta name="application-name" content="<?php echo e(config('app.name', 'Laravel')); ?>">
    <meta name="msapplication-config" content="<?php echo e(asset('browserconfig.xml')); ?>">


    <!-- Fabric.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">


    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/photo-editor.css']); ?>
</head>
<style>
    .editor-header {
        height: 44px;
    display: flex
;
    position: fixed;
    top: 0;
   left: -15px;
    z-index: 99999;
    width: 100%;
    align-items: center;
    justify-content: space-between;
    padding: 0 10px;
    background-color: var(--surface-dark);
    border-bottom: 1px solid var(--border-dark);
    }.editor-header button {
    width: 25px;
    height: 25px;
}.filter-preview {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 5px;
    position: relative;
    background-color: var(--card-dark);
}.tab-button {
    color: var(--text-secondary);
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    padding: 8px 15px;
    position: relative;
}
.editor-toolbar {
     position: fixed;
     width: 100%;
bottom: 0;
  left: 0;
    z-index: 99999;
}
</style>
<body>
    <div id="app">

        <!-- Основное содержимое с отступом для фиксированного меню -->
        <main style="">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

</body>

</html>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/layouts/photo-editor.blade.php ENDPATH**/ ?>