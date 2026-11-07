<?php $__env->startSection('title', 'Create account'); ?>
<?php $__env->startSection('content'); ?>
<div class="split">
  <aside class="split-art">
    <div class="brand"><span class="brand-mark">📣</span> Sumbungan ng Bayan</div>
    <div>
      <h2>Become a voice for change<?php echo e("\n"); ?>in Barangay Maslog.</h2>
      <p>Register as a citizen of Barangay Maslog and start filing concerns today.</p>
    </div>
    <small>Barangay Maslog — Balilihan Bohol</small>
  </aside>
  <main class="split-form">
    <div class="form-box">
      <a class="back-link" href="<?php echo e(route('landing')); ?>">← Back</a>
      <h1>Join Your Community</h1>
      <p class="sub">Register as a citizen of Barangay Maslog</p>
      <?php if($errors->any()): ?><div class="alert alert-error"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?>
      <form method="POST" action="<?php echo e(url('/register')); ?>" novalidate>
        <?php echo csrf_field(); ?>
        <div class="field"><label for="name">Full name</label>
          <input class="input" id="name" name="name" value="<?php echo e(old('name')); ?>" autocomplete="name" required autofocus></div>
        <div class="field"><label for="email">Email address</label>
          <input class="input" id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" autocomplete="email" required></div>
        <div class="field"><label for="password">Password</label>
          <div class="pw-wrap"><input class="input" id="password" type="password" name="password" autocomplete="new-password" minlength="6" required>
          <button type="button" class="pw-toggle" data-toggle-pw="password" aria-label="Show password">👁</button></div></div>
        <div class="field"><label for="password_confirmation">Confirm password</label>
          <input class="input" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
        <button class="btn btn-primary btn-block" type="submit">Create Account</button>
      </form>
      <p class="form-alt">Already have an account? <a href="<?php echo e(route('login')); ?>">Sign in</a></p>
    </div>
  </main>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Sumbungan-ng-Bayan\resources\views/auth/register.blade.php ENDPATH**/ ?>