<?php $__env->startSection('title', 'My Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="container">
  <div class="welcome">
    <h2>Mabuhay, <?php echo e(\Illuminate\Support\Str::of($user->name)->explode(' ')->first()); ?>! 👋</h2>
    <p>Your reports matter. The barangay is listening.</p>
  </div>

  <div class="stats" id="stats"></div>

  <div class="actions">
    <a class="action a1" href="<?php echo e(route('citizen.submit')); ?>"><span class="ico">📝</span><div><b>Submit a Report</b><span>File a new concern</span></div></a>
    <a class="action a2" href="<?php echo e(route('citizen.track')); ?>"><span class="ico">🔎</span><div><b>Track a Report</b><span>Search by report ID</span></div></a>
  </div>

  <div class="section-head"><h2>My Reports</h2></div>
  <div class="filters" id="filters"></div>
  <div id="list"><div class="spinner"></div></div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?><script src="<?php echo e(asset('js/citizen-dashboard.js')); ?>"></script><?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Sumbungan-ng-Bayan\resources\views/citizen/dashboard.blade.php ENDPATH**/ ?>