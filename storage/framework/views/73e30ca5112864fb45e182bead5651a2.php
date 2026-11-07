<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title><?php echo $__env->yieldContent('title', 'Sumbungan ng Bayan'); ?> · Brgy. Maslog</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
</head>
<body class="<?php echo $__env->yieldContent('body-class'); ?>">
  <?php echo $__env->yieldContent('content'); ?>
  <?php
    $snbConfig = [
      'user' => auth()->check()
        ? ['id' => auth()->id(), 'name' => auth()->user()->name, 'role' => auth()->user()->role]
        : null,
      'urls' => [
        'base'      => url('/'),
        'dashboard' => route('citizen.dashboard'),
        'track'     => route('citizen.track'),
        'submit'    => route('citizen.submit'),
      ],
      'sectors'    => \App\Support\Grievance::SECTORS,
      'statuses'   => \App\Support\Grievance::STATUSES,
      'categories' => \App\Support\Grievance::CATEGORIES,
    ];
  ?>
  <script>window.SNB_CONFIG = <?php echo json_encode($snbConfig, 15, 512) ?>;</script>
  <script src="<?php echo e(asset('js/app.js')); ?>"></script>
  <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\Sumbungan-ng-Bayan\resources\views/layouts/app.blade.php ENDPATH**/ ?>