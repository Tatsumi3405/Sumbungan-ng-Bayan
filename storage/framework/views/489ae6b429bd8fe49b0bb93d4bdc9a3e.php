<?php $__env->startSection('title', 'Welcome'); ?>
<?php $__env->startSection('content'); ?>
<div class="hero">
  <nav class="hero-nav">
    <div class="brand"><span class="brand-mark">📣</span> Sumbungan ng Bayan</div>
    <a class="btn btn-outline-light btn-sm" href="<?php echo e(route('login')); ?>">Sign In</a>
  </nav>
  <div class="hero-body">
    <div class="hero-inner">
      <div>
        <span class="pill">🇵🇭 Barangay Maslog · Balilihan, Bohol</span>
        <h1>Sumbungan<br><span>ng Bayan</span></h1>
        <p class="display" style="font-size:20px;font-weight:600;margin-bottom:10px">Boses ng Mamamayan</p>
        <p class="lead">Your voice matters. Submit grievances, track their progress, and talk directly with barangay officials — all in one place.</p>
        <div class="hero-actions">
          <a class="btn btn-light" href="<?php echo e(route('register')); ?>">Create Account</a>
          <a class="btn btn-outline-light" href="<?php echo e(route('login')); ?>">Sign In</a>
        </div>
        <a class="admin-link" href="<?php echo e(route('admin.login')); ?>">🏛️ Barangay Admin Portal →</a>
      </div>
      <div class="hero-cards">
        <div class="glass"><div class="ico">📝</div><div><h3>Submit</h3><p>File concerns<?php echo e("\n"); ?>in seconds</p></div></div>
        <div class="glass"><div class="ico">🔎</div><div><h3>Track</h3><p>Real-time status<?php echo e("\n"); ?>updates</p></div></div>
        <div class="glass"><div class="ico">✅</div><div><h3>Resolve</h3><p>Transparent<?php echo e("\n"); ?>community action</p></div></div>
      </div>
    </div>
  </div>
  <div class="hero-foot">Barangay Maslog · Balilihan, Bohol</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Sumbungan-ng-Bayan\resources\views/landing.blade.php ENDPATH**/ ?>