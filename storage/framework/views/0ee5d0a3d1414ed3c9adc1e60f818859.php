<!-- Базовые мета-теги -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Подарочный документ для <?php echo e($certificate->recipient_name); ?> на сумму <?php echo e(number_format($certificate->amount, 0, '.', ' ')); ?>₽</title>
<meta name="description" content="Подарочный документ на сумму <?php echo e(number_format($certificate->amount, 0, '.', ' ')); ?>₽. Действителен до <?php echo e(format_date($certificate->valid_until)); ?>.">
<meta name="keywords" content="подарочный документ, подарок, документ, <?php echo e($certificate->user->company ?? config('app.name')); ?>">

<!-- Канонический URL -->
<link rel="canonical" href="<?php echo e(route('certificates.public', $certificate->uuid)); ?>">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo e(route('certificates.public', $certificate->uuid)); ?>">
<meta property="og:title" content="Подарочный документ для <?php echo e($certificate->recipient_name); ?>">
<meta property="og:description" content="Подарочный документ на сумму <?php echo e(number_format($certificate->amount, 0, '.', ' ')); ?>₽. Действителен до <?php echo e(format_date($certificate->valid_until)); ?>.">
<meta property="og:image" content="<?php echo e(asset('storage/' . $certificate->cover_image)); ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="<?php echo e($certificate->user->company ?? config('app.name')); ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="<?php echo e(route('certificates.public', $certificate->uuid)); ?>">
<meta name="twitter:title" content="Подарочный документ для <?php echo e($certificate->recipient_name); ?>">
<meta name="twitter:description" content="Подарочный документ на сумму <?php echo e(number_format($certificate->amount, 0, '.', ' ')); ?>₽. Действителен до <?php echo e(format_date($certificate->valid_until)); ?>.">
<meta name="twitter:image" content="<?php echo e(asset('storage/' . $certificate->cover_image)); ?>">

<!-- Дополнительные мета-теги -->
<meta name="author" content="<?php echo e($certificate->user->company ?? config('app.name')); ?>">
<meta name="robots" content="index, follow">

<!-- Структурированные данные JSON-LD -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "Подарочный документ",
    "description": "Подарочный документ на сумму <?php echo e(number_format($certificate->amount, 0, '.', ' ')); ?>₽",
    "offers": {
        "@type": "Offer",
        "price": "<?php echo e($certificate->amount); ?>",
        "priceCurrency": "RUB",
        "validFrom": "<?php echo e(is_string($certificate->valid_from) ? $certificate->valid_from : $certificate->valid_from->toIso8601String()); ?>",
        "validThrough": "<?php echo e(is_string($certificate->valid_until) ? $certificate->valid_until : $certificate->valid_until->toIso8601String()); ?>",
        "availability": "https://schema.org/InStock"
    },
    "provider": {
        "@type": "Organization",
        "name": "<?php echo e($certificate->user->company ?? config('app.name')); ?>"
    }
}
</script>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/certificates/partials/meta.blade.php ENDPATH**/ ?>