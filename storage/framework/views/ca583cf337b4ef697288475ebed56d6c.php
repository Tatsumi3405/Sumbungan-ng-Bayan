<header class="topbar <?php echo e(($admin ?? false) ? 'admin' : ''); ?>">
  <div class="topbar-in">
    <a class="brand" href="<?php echo e(($admin ?? false) ? route('admin.dashboard') : route('citizen.dashboard')); ?>" style="color:#fff">
      <span class="brand-mark"><?php echo e(($admin ?? false) ? '🏛️' : '📣'); ?></span>
      <span><?php echo e(($admin ?? false) ? 'Admin Panel' : 'Sumbungan ng Bayan'); ?></span>
    </a>
    <span class="spacer"></span>
    <div class="who"><b><?php echo e(auth()->user()->name); ?></b><?php echo e(($admin ?? false) ? 'Administrator' : 'Citizen'); ?></div>
    <form method="POST" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?>
      <button class="btn btn-outline-light btn-sm" type="submit">Sign out</button>
    </form>
  </div>
</header>
<?php /**PATH C:\laragon\www\Sumbungan-ng-Bayan\resources\views/layouts/topbar.blade.php ENDPATH**/ ?>