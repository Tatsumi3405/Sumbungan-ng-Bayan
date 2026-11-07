(function () {
  'use strict';
  const { toast, CFG } = SNB;
  const form = document.getElementById('form');
  const cats = document.getElementById('cats');
  const catInput = document.getElementById('category');
  const photo = document.getElementById('photo');
  const drop = document.getElementById('drop');
  const preview = document.getElementById('preview');
  const bar = document.querySelector('#progress > div');
  const progress = document.getElementById('progress');
  const btn = document.getElementById('submitBtn');
  const errBox = document.getElementById('formError');
  const MAX = 5 * 1024 * 1024;

  cats.addEventListener('click', (e) => {
    const b = e.target.closest('.cat'); if (!b) return;
    cats.querySelectorAll('.cat').forEach((x) => x.classList.remove('on'));
    b.classList.add('on'); catInput.value = b.dataset.key;
  });

  function setPhoto(file) {
    const fe = document.querySelector('[data-f="photo"] .error-text');
    fe.textContent = '';
    if (!file) { photo.value = ''; preview.classList.add('hidden'); drop.classList.remove('hidden'); return; }
    if (!/^image\/(jpeg|png|webp)$/.test(file.type)) { fe.textContent = 'Please choose a JPG, PNG or WEBP image.'; return; }
    if (file.size > MAX) { fe.textContent = 'The photo must be 5 MB or smaller.'; return; }
    const dt = new DataTransfer(); dt.items.add(file); photo.files = dt.files;
    preview.querySelector('img').src = URL.createObjectURL(file);
    preview.classList.remove('hidden'); drop.classList.add('hidden');
  }
  drop.addEventListener('click', () => photo.click());
  photo.addEventListener('change', () => setPhoto(photo.files[0]));
  document.getElementById('removePhoto').addEventListener('click', () => setPhoto(null));
  ['dragover', 'dragenter'].forEach((ev) => drop.addEventListener(ev, (e) => { e.preventDefault(); drop.classList.add('over'); }));
  ['dragleave', 'drop'].forEach((ev) => drop.addEventListener(ev, (e) => { e.preventDefault(); drop.classList.remove('over'); }));
  drop.addEventListener('drop', (e) => setPhoto(e.dataTransfer.files[0]));

  function clearErrors() {
    errBox.classList.add('hidden');
    form.querySelectorAll('.field').forEach((f) => { f.classList.remove('has-error'); const t = f.querySelector('.error-text'); if (t) t.textContent = ''; });
  }
  function showErrors(errors) {
    Object.entries(errors || {}).forEach(([k, msgs]) => {
      const f = form.querySelector(`[data-f="${k}"]`);
      if (f) { f.classList.add('has-error'); f.querySelector('.error-text').textContent = msgs[0]; }
    });
  }

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    clearErrors();
    // quick client checks (server re-validates everything)
    const local = {};
    if (form.title.value.trim().length < 5) local.title = ['Title must be at least 5 characters'];
    if (form.description.value.trim().length < 20) local.description = ['Please describe the issue in at least 20 characters'];
    if (!form.address.value.trim()) local.address = ['Please enter the location'];
    if (Object.keys(local).length) { showErrors(local); return; }

    const fd = new FormData(form);
    if (!photo.files.length) fd.delete('photo');

    const xhr = new XMLHttpRequest();
    xhr.open('POST', form.dataset.action || '/grievances');
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    xhr.upload.onprogress = (ev) => { if (ev.lengthComputable) bar.style.width = Math.round(ev.loaded / ev.total * 100) + '%'; };
    xhr.onloadstart = () => { btn.disabled = true; btn.textContent = 'Submitting…'; if (photo.files.length) { progress.classList.remove('hidden'); bar.style.width = '0'; } };
    xhr.onerror = () => { btn.disabled = false; btn.textContent = 'Submit Report'; progress.classList.add('hidden'); toast('Network error. Check your connection.', 'err'); };
    xhr.onload = () => {
      let data = {}; try { data = JSON.parse(xhr.responseText); } catch (_) {}
      if (xhr.status === 201) {
        toast('Report submitted! Thank you.', 'ok');
        setTimeout(() => { location.href = data.redirect || CFG.urls.dashboard; }, 700);
        return;
      }
      btn.disabled = false; btn.textContent = 'Submit Report'; progress.classList.add('hidden');
      if (xhr.status === 401 || xhr.status === 419) { location.href = CFG.urls.base; return; }
      if (xhr.status === 413) { toast('That photo is too large for the server (see README: upload limits).', 'err'); return; }
      if (xhr.status === 422) { showErrors(data.errors); errBox.textContent = data.message || 'Please fix the highlighted fields.'; errBox.classList.remove('hidden'); return; }
      toast(data.message || 'Could not submit the report.', 'err');
    };
    xhr.send(fd);
  });
})();
