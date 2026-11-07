<?php $__env->startSection('title', 'Admin Dashboard'); ?>
<?php $__env->startSection('body-class', 'admin-page'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.topbar', ['admin' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="container">
  <div class="welcome" style="background:var(--admin-hero)">
    <h2>Grievance Overview</h2>
    <p>Review, respond to and resolve reports from Barangay Maslog.</p>
  </div>

  <div class="stats" id="stats"></div>

  <div class="overview">
    <div class="card card-pad">
      <div class="section-head" style="margin-top:0"><h2 style="color:var(--admin-3)">Status Distribution</h2><span class="chip" id="totalChip"></span></div>
      <div class="pie-wrap" id="pie"></div>
    </div>
    <div class="card card-pad">
      <div class="section-head" style="margin-top:0"><h2 style="color:var(--admin-3)">By Category</h2></div>
      <div id="catBars"></div>
    </div>
  </div>

  <div class="section-head"><h2 style="color:var(--admin-3)">All Reports</h2><span class="chip" id="countChip"></span></div>
  <div class="filters" id="filters"></div>
  <div class="toolbar">
    <input class="input" id="q" placeholder="Search title, name, address or ID…">
    <select class="select" id="catFilter">
      <option value="">All categories</option>
      <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($key); ?>"><?php echo e($c['icon']); ?> <?php echo e($c['label']); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>
  <div id="list"><div class="spinner"></div></div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?><script src="<?php echo e(asset('js/admin.js')); ?>"></script><?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Sumbungan-ng-Bayan\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>