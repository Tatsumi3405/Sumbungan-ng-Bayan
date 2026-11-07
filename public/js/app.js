/* Sumbungan ng Bayan — shared helpers (vanilla JS) */
(function () {
  'use strict';
  const CFG = window.SNB_CONFIG || {};
  const csrf = () => (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

  const esc = (s) => String(s == null ? '' : s).replace(/[&<>"']/g, (c) =>
    ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  /** fetch wrapper: JSON in/out, CSRF header, throws {message, errors, status} */
  async function api(url, opts = {}) {
    const o = Object.assign({ method: 'GET', headers: {} }, opts);
    o.headers = Object.assign({ Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf() }, o.headers);
    if (o.body && !(o.body instanceof FormData) && typeof o.body !== 'string') {
      o.headers['Content-Type'] = 'application/json';
      o.body = JSON.stringify(o.body);
    }
    o.credentials = 'same-origin';
    let res;
    try { res = await fetch(url, o); }
    catch (e) { throw { message: 'Network error. Check your connection.', status: 0 }; }
    if (res.status === 401 || res.status === 419) { location.href = CFG.urls ? CFG.urls.base : '/'; throw { message: 'Session expired.', status: res.status }; }
    let data = null;
    try { data = await res.json(); } catch (e) { /* no body */ }
    if (!res.ok) throw { message: (data && data.message) || 'Something went wrong.', errors: data && data.errors, status: res.status };
    return data;
  }

  function toast(msg, type) {
    const t = document.createElement('div');
    t.className = 'toast ' + (type || '');
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3200);
  }

  /** Run fn now and every `ms`, but only while the tab is visible. */
  function poll(fn, ms) {
    fn();
    const id = setInterval(() => { if (!document.hidden) fn(); }, ms);
    return () => clearInterval(id);
  }

  function badge(r) {
    return `<span class="badge" style="color:${r.status_color};background:${r.status_bg};border-color:${r.status_color}55">${esc(r.status_label)}</span>`;
  }

  function reportCard(r, opts = {}) {
    const by = opts.showUser ? `<span>👤 ${esc(r.user_name)}</span>` : '';
    const sector = r.sector_name ? `<span class="chip">${esc(r.sector_icon)} ${esc(r.sector_name)}</span>` : '';
    const msgs = r.message_count > 0 ? `<span class="chip msg-count">💬 ${r.message_count}</span>` : '';
    return `<button type="button" class="card report" data-id="${r.id}">
      <div class="report-top"><h3>${esc(r.title)}</h3>${badge(r)}</div>
      <div class="meta"><span>${esc(r.category_icon)} ${esc(r.category_label)}</span><span>📍 ${esc(r.address || '—')}</span>${by}<span>🕒 ${esc(r.submitted_label)}</span></div>
      <p class="desc">${esc(r.description)}</p>
      <div class="report-foot"><span class="chip">${esc(r.ref)}</span><span style="display:flex;gap:6px">${sector}${msgs}</span></div>
    </button>`;
  }

  function emptyState(icon, title, sub) {
    return `<div class="card empty"><div class="big">${icon}</div><h3>${esc(title)}</h3><p>${esc(sub || '')}</p></div>`;
  }

  // ── Modal ──────────────────────────────────────────────
  function openModal(html, opts = {}) {
    const ov = document.createElement('div');
    ov.className = 'overlay';
    ov.innerHTML = `<div class="modal ${opts.small ? 'small' : ''}" role="dialog" aria-modal="true">${html}</div>`;
    document.body.appendChild(ov);
    document.body.style.overflow = 'hidden';
    const cleanups = [];
    const close = () => {
      cleanups.forEach((f) => f());
      ov.remove();
      document.body.style.overflow = '';
      document.removeEventListener('keydown', onKey);
      if (opts.onClose) opts.onClose();
    };
    const onKey = (e) => { if (e.key === 'Escape') close(); };
    document.addEventListener('keydown', onKey);
    ov.addEventListener('mousedown', (e) => { if (e.target === ov) close(); });
    ov.querySelectorAll('[data-close]').forEach((b) => b.addEventListener('click', close));
    return { el: ov, close, onCleanup: (f) => cleanups.push(f) };
  }

  function confirmDialog(title, message, okLabel) {
    return new Promise((resolve) => {
      const m = openModal(`<div class="modal-body"><h3 style="margin-bottom:8px">${esc(title)}</h3><p style="color:var(--muted)">${esc(message)}</p></div>
        <div class="modal-foot"><button class="btn btn-ghost btn-sm" data-close>Cancel</button><button class="btn btn-danger btn-sm" id="okBtn">${esc(okLabel || 'Delete')}</button></div>`,
        { small: true, onClose: () => resolve(false) });
      m.el.querySelector('#okBtn').addEventListener('click', () => { resolve(true); m.close(); });
    });
  }

  // ── Chat (polls for new messages) ──────────────────────
  function mountChat(container, grievanceId, opts = {}) {
    const me = CFG.user || {};
    container.innerHTML = `<div class="chat ${me.role === 'admin' ? 'admin-chat' : ''}">
      <div class="chat-head">💬 Conversation</div>
      <div class="chat-list"><div class="chat-empty">No messages yet. Start the conversation!</div></div>
      <form class="chat-form"><input maxlength="2000" placeholder="Type a message…" autocomplete="off"><button type="submit">Send</button></form></div>`;
    const list = container.querySelector('.chat-list');
    const form = container.querySelector('form');
    const input = form.querySelector('input');
    let lastId = 0, first = true, busy = false;
    const seen = new Set();

    function add(m) {
      if (seen.has(m.id)) return;
      seen.add(m.id);
      if (first) { list.innerHTML = ''; first = false; }
      const mine = m.sender_id === me.id;
      const isAdmin = m.sender_role === 'admin';
      const el = document.createElement('div');
      el.className = `msg ${mine ? 'me' : ''} ${isAdmin ? 'admin' : 'citizen'}`;
      el.innerHTML = `<div class="who">${esc(m.sender_name)}${isAdmin ? ' (Admin)' : ''} · ${esc(m.time)}</div><div class="bubble">${esc(m.text)}</div>`;
      list.appendChild(el);
      lastId = Math.max(lastId, m.id);
    }
    const atBottom = () => list.scrollHeight - list.scrollTop - list.clientHeight < 60;

    async function load() {
      if (busy) return;
      busy = true;
      try {
        const res = await api(`/api/grievances/${grievanceId}/messages?after=${lastId}`);
        const stick = atBottom() || lastId === 0;
        res.data.forEach(add);
        if (stick && res.data.length) list.scrollTop = list.scrollHeight;
        if (opts.onCount) opts.onCount(res.message_count);
      } catch (e) { /* keep quiet; next tick retries */ }
      busy = false;
    }
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const text = input.value.trim();
      if (!text) return;
      input.disabled = true;
      try {
        const res = await api(`/api/grievances/${grievanceId}/messages`, { method: 'POST', body: { text } });
        input.value = '';
        add(res.data);
        list.scrollTop = list.scrollHeight;
      } catch (err) { toast(err.message || 'Could not send message.', 'err'); }
      input.disabled = false;
      input.focus();
    });
    return poll(load, 4000);
  }

  // ── Report detail modal (citizen + admin) ──────────────
  /**
   * opts: { admin:bool, canChat:bool, canDelete:bool, onChange(), onDeleted() }
   */
  function openReport(r, opts = {}) {
    const admin = !!opts.admin;
    const canChat = opts.canChat !== false;
    const sectorOpts = (CFG.sectors || []).map((s) =>
      `<option value="${esc(s.id)}" ${r.responding_sector === s.id ? 'selected' : ''}>${esc(s.icon)} ${esc(s.name)} – ${esc(s.full)}</option>`).join('');
    const statusOpts = Object.entries(CFG.statuses || {}).map(([k, v]) =>
      `<option value="${k}" ${r.status === k ? 'selected' : ''}>${esc(v.label)}</option>`).join('');

    const m = openModal(`
      <div class="modal-head ${admin ? 'admin' : ''}">
        <div><h3>${esc(r.title)}</h3><small>${esc(r.ref)} · ${esc(r.submitted_label)}</small></div>
        <button class="x" data-close aria-label="Close">✕</button>
      </div>
      <div class="modal-body">
        <div style="margin-bottom:12px">${badge(r)}</div>
        <dl class="kv">
          <dt>Category</dt><dd>${esc(r.category_icon)} ${esc(r.category_label)}</dd>
          <dt>Location</dt><dd>${esc(r.address || '—')}</dd>
          ${admin ? `<dt>Reported by</dt><dd>${esc(r.user_name)}</dd>` : ''}
          <dt>Last update</dt><dd>${esc(r.updated_label)}</dd>
        </dl>
        <p style="white-space:pre-wrap;word-break:break-word">${esc(r.description)}</p>
        ${r.image_url ? `<img class="photo" src="${esc(r.image_url)}" alt="Report photo" onerror="this.outerHTML='<p class=&quot;chip&quot;>Could not load image</p>'">` : ''}
        ${r.sector_name ? `<div class="sector-box">Credited Sector: <b>${esc(r.sector_name)} – ${esc(r.sector_full)}</b></div>` : ''}
        ${r.admin_feedback ? `<div class="feedback-box"><b>Admin feedback:</b> ${esc(r.admin_feedback)}</div>` : ''}
        ${admin ? `<div class="divider"></div><h4 style="margin-bottom:10px;color:var(--admin-3)">Manage Report</h4>
          <div class="form-grid">
            <div class="field"><label>Status</label><select class="select" id="mStatus">${statusOpts}</select></div>
            <div class="field"><label>Responding sector</label><select class="select" id="mSector"><option value="">— None —</option>${sectorOpts}</select></div>
          </div>
          <div class="field"><label>Feedback note (optional)</label><textarea class="textarea" id="mFeedback" style="min-height:70px" placeholder="Short note shown to the citizen…">${esc(r.admin_feedback || '')}</textarea></div>` : ''}
        ${canChat ? `<div class="divider"></div><div id="chatMount"></div>` : `<p class="chip" style="margin-top:14px">Only the reporter and barangay officials can chat on this report.</p>`}
      </div>
      <div class="modal-foot">
        ${opts.canDelete !== false ? `<button class="btn btn-danger btn-sm" id="mDelete" style="margin-right:auto">🗑 Delete</button>` : ''}
        <button class="btn btn-ghost btn-sm" data-close>${admin ? 'Cancel' : 'Close'}</button>
        ${admin ? `<button class="btn btn-admin btn-sm" id="mSave">Save Changes</button>` : ''}
      </div>`, { onClose: () => { if (opts.onClose) opts.onClose(); } });

    if (canChat) {
      const stop = mountChat(m.el.querySelector('#chatMount'), r.id, { onCount: () => {} });
      m.onCleanup(stop);
    }

    const del = m.el.querySelector('#mDelete');
    if (del) del.addEventListener('click', async () => {
      if (!(await confirmDialog('Delete Report', 'Are you sure you want to delete this report? This action cannot be undone.'))) return;
      try {
        await api(`/api/grievances/${r.id}`, { method: 'DELETE' });
        toast('Report deleted.', 'ok');
        m.close();
        if (opts.onDeleted) opts.onDeleted(r);
      } catch (e) { toast(e.message || 'Failed to delete report.', 'err'); }
    });

    const save = m.el.querySelector('#mSave');
    if (save) save.addEventListener('click', async () => {
      save.disabled = true;
      try {
        await api(`/admin/grievances/${r.id}`, { method: 'PATCH', body: {
          status: m.el.querySelector('#mStatus').value,
          responding_sector: m.el.querySelector('#mSector').value || null,
          admin_feedback: m.el.querySelector('#mFeedback').value.trim() || null,
        } });
        toast('Report updated.', 'ok');
        m.close();
        if (opts.onChange) opts.onChange();
      } catch (e) { toast(e.message || 'Update failed.', 'err'); save.disabled = false; }
    });
    return m;
  }

  // password show/hide on auth pages
  document.addEventListener('click', (e) => {
    const b = e.target.closest('[data-toggle-pw]');
    if (!b) return;
    const inp = document.getElementById(b.dataset.togglePw);
    if (inp) inp.type = inp.type === 'password' ? 'text' : 'password';
  });

  window.SNB = { CFG, api, esc, toast, poll, badge, reportCard, emptyState, openModal, confirmDialog, mountChat, openReport };
})();
