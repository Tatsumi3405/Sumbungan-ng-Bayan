<?php $__env->startSection('title', 'Submit a Report'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="container narrow">
  <a class="back-link" href="<?php echo e(route('citizen.dashboard')); ?>">← Back to dashboard</a>
  <h1 class="page-title">Submit a Report</h1>
  <p class="page-sub">Tell the barangay what needs attention.</p>

  <form id="form" class="card card-pad" novalidate>
    <div class="alert alert-error hidden" id="formError"></div>

    <div class="field"><label>Category</label>
      <div class="cats" id="cats">
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <button type="button" class="cat <?php echo e($loop->first ? 'on' : ''); ?>" data-key="<?php echo e($key); ?>"><span class="ci"><?php echo e($c['icon']); ?></span><?php echo e($c['label']); ?></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <input type="hidden" name="category" id="category" value="<?php echo e(array_key_first($categories)); ?>">
    </div>

    <div class="field" data-f="title"><label for="title">Title</label>
      <input class="input" id="title" name="title" maxlength="150" placeholder="e.g. Broken streetlight near the basketball court"><div class="error-text"></div></div>

    <div class="field" data-f="description"><label for="description">Description</label>
      <textarea class="textarea" id="description" name="description" maxlength="3000" placeholder="Describe the issue in detail (at least 20 characters)…"></textarea><div class="error-text"></div></div>

    <div class="field" data-f="address"><label for="address">Location / Address</label>
      <input class="input" id="address" name="address" maxlength="255" placeholder="Purok, street or landmark"><div class="error-text"></div></div>

    <div class="field" data-f="photo"><label>Photo (optional, max 5 MB)</label>
      <div class="drop" id="drop">📷 Click or drop a photo here<div style="font-size:12px;margin-top:4px">JPG, PNG or WEBP</div></div>
      <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" class="hidden">
      <div class="preview hidden" id="preview"><img alt="Preview"><button type="button" class="btn btn-light btn-sm" id="removePhoto">Remove</button></div>
      <div class="error-text"></div>
    </div>

    <div class="progress hidden" id="progress"><div></div></div>
    <button class="btn btn-primary btn-block" id="submitBtn" type="submit" style="margin-top:16px">Submit Report</button>
  </form>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?><script src="<?php echo e(asset('js/submit.js')); ?>"></script><?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Sumbungan-ng-Bayan\resources\views/citizen/submit.blade.php ENDPATH**/ ?>