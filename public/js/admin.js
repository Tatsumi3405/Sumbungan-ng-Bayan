(function () {
  'use strict';
  const { api, esc, reportCard, emptyState, openReport, poll, CFG } = SNB;
  let all = [], status = 'all', cat = '', q = '', modalOpen = false;

  const $ = (id) => document.getElementById(id);

  // ── hand-rolled SVG pie (no external chart library) ──
  function pie(counts, total) {
    const cx = 100, cy = 100, r = 90;
    const entries = Object.entries(CFG.statuses);
    let svg = '';
    if (!total) {
      svg = `<circle cx="${cx}" cy="${cy}" r="${r}" fill="#E2E8F4"/><circle cx="${cx}" cy="${cy}" r="52" fill="#fff"/>`;
    } else {
      let a0 = -Math.PI / 2;
      entries.forEach(([k, s]) => {
        const n = counts[k] || 0; if (!n) return;
        if (n === total) { svg += `<circle cx="${cx}" cy="${cy}" r="${r}" fill="${s.color}" data-s="${k}"><title>${s.label}: ${n}</title></circle>`; return; }
        const a1 = a0 + (n / total) * Math.PI * 2;
        const x0 = cx + r * Math.cos(a0), y0 = cy + r * Math.sin(a0);
        const x1 = cx + r * Math.cos(a1), y1 = cy + r * Math.sin(a1);
        svg += `<path d="M${cx} ${cy} L${x0.toFixed(2)} ${y0.toFixed(2)} A${r} ${r} 0 ${a1 - a0 > Math.PI ? 1 : 0} 1 ${x1.toFixed(2)} ${y1.toFixed(2)} Z" fill="${s.color}" stroke="#fff" stroke-width="2" data-s="${k}"><title>${s.label}: ${n} (${Math.round(n / total * 100)}%)</title></path>`;
        a0 = a1;
      });
      svg += `<circle cx="${cx}" cy="${cy}" r="52" fill="#fff"/>`;
    }
    svg += `<text x="100" y="98" text-anchor="middle" font-family="Outfit" font-size="30" font-weight="700" fill="#1E293B">${total}</text><text x="100" y="118" text-anchor="middle" font-size="12" fill="#64748B">reports</text>`;
    const legend = entries.map(([k, s]) => `<div><i style="background:${s.color}"></i>${s.label}<b>${counts[k] || 0}</b></div>`).join('');
    return `<svg class="pie" viewBox="0 0 200 200" role="img" aria-label="Status distribution">${svg}</svg><div class="legend">${legend}</div>`;
  }

  function overview() {
    const counts = {};
    all.forEach((r) => { counts[r.status] = (counts[r.status] || 0) + 1; });
    const tiles = [['Total', all.length, '#166534']].concat(Object.entries(CFG.statuses).map(([k, s]) => [s.label, counts[k] || 0, s.color]));
    $('stats').innerHTML = tiles.slice(0, 4).map(([l, n, c]) => `<div class="card stat"><div class="bar" style="background:${c}"></div><span class="num" style="color:${c}">${n}</span><span class="lbl">${l}</span></div>`).join('');
    $('totalChip').textContent = all.length + ' total';
    $('pie').innerHTML = pie(counts, all.length);

    const byCat = {};
    all.forEach((r) => { byCat[r.category] = (byCat[r.category] || 0) + 1; });
    const max = Math.max(1, ...Object.values(byCat));
    $('catBars').innerHTML = Object.entries(CFG.categories).map(([k, c]) => {
      const n = byCat[k] || 0;
      return `<div style="margin-bottom:12px"><div style="display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:4px"><span>${c.icon} ${esc(c.label)}</span><b>${n}</b></div>
        <div class="progress" style="margin:0"><div style="width:${n / max * 100}%;background:linear-gradient(90deg,#166534,#4ADE80)"></div></div></div>`;
    }).join('');
  }

  function filtered() {
    const needle = q.trim().toLowerCase();
    return all.filter((r) =>
      (status === 'all' || r.status === status) &&
      (!cat || r.category === cat) &&
      (!needle || [r.title, r.user_name, r.address, r.ref, String(r.id), r.description].some((v) => String(v || '').toLowerCase().includes(needle))));
  }

  function renderFilters() {
    const opts = [['all', 'All']].concat(Object.entries(CFG.statuses).map(([k, v]) => [k, v.label]));
    $('filters').innerHTML = opts.map(([k, l]) => `<button class="fbtn ${status === k ? 'on' : ''}" data-f="${k}">${l}</button>`).join('');
  }

  function renderList() {
    const rows = filtered();
    $('countChip').textContent = rows.length + (rows.length === 1 ? ' report' : ' reports');
    $('list').innerHTML = rows.length ? rows.map((r) => reportCard(r, { showUser: true })).join('')
      : emptyState('📭', 'No reports', status === 'all' && !q && !cat ? 'Nothing has been submitted yet.' : 'No reports match your filters.');
  }

  async function load() {
    if (modalOpen) return;
    try { all = (await api('/api/grievances')).data; overview(); renderFilters(); renderList(); }
    catch (e) { if (!all.length) $('list').innerHTML = emptyState('⚠️', 'Could not load reports', e.message); }
  }

  $('filters').addEventListener('click', (e) => { const b = e.target.closest('[data-f]'); if (b) { status = b.dataset.f; renderFilters(); renderList(); } });
  $('pie').addEventListener('click', (e) => { const s = e.target.closest('[data-s]'); if (s) { status = s.dataset.s; renderFilters(); renderList(); $('filters').scrollIntoView({ behavior: 'smooth', block: 'center' }); } });
  $('q').addEventListener('input', (e) => { q = e.target.value; renderList(); });
  $('catFilter').addEventListener('change', (e) => { cat = e.target.value; renderList(); });
  $('list').addEventListener('click', (e) => {
    const c = e.target.closest('[data-id]'); if (!c) return;
    const r = all.find((x) => String(x.id) === c.dataset.id); if (!r) return;
    modalOpen = true;
    openReport(r, {
      admin: true, canChat: true, canDelete: true,
      onChange: () => { modalOpen = false; load(); },
      onDeleted: () => { modalOpen = false; load(); },
      onClose: () => { modalOpen = false; load(); },
    });
  });

  poll(load, 8000);
})();
