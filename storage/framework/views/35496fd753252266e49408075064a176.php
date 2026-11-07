<?php $__env->startSection('title', 'Admin Portal'); ?>
<?php $__env->startSection('content'); ?>
<div class="split">
  <aside class="split-art admin">
    <div class="brand"><span class="brand-mark">🏛️</span> Admin Portal</div>
    <div>
      <h2>Barangay<?php echo e("\n"); ?>Admin Portal</h2>
      <p>Restricted access — authorised personnel only.</p>
    </div>
    <small>Brgy. Maslog · Balilihan, Bohol</small>
  </aside>
  <main class="split-form">
    <div class="form-box">
      <a class="back-link" href="<?php echo e(route('landing')); ?>">← Back</a>
      <h1 style="color:var(--admin-3)">Admin Portal</h1>
      <p class="sub">Restricted access — authorised personnel only</p>
      <?php if($errors->any()): ?><div class="alert alert-error"><?php echo e($errors->first()); ?></div><?php endif; ?>
      <form method="POST" action="<?php echo e(url('/admin/login')); ?>" novalidate>
        <?php echo csrf_field(); ?>
        <div class="field"><label for="email">Admin Email</label>
          <input class="input" id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="admin@maslog.gov.ph" autocomplete="username" required autofocus></div>
        <div class="field"><label for="password">Password</label>
          <div class="pw-wrap"><input class="input" id="password" type="password" name="password" autocomplete="current-password" required>
          <button type="button" class="pw-toggle" data-toggle-pw="password" aria-label="Show password">👁</button></div></div>
        <button class="btn btn-admin btn-block" type="submit">Sign in as Admin</button>
      </form>
      <p class="notice">This portal is monitored. Unauthorised access<?php echo e("\n"); ?>attempts are logged and reported.</p>
    </div>
  </main>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Sumbungan-ng-Bayan\resources\views/auth/admin-login.blade.php ENDPATH**/ ?>