(function () {
  'use strict';
  const { api, reportCard, emptyState, openReport, poll, CFG } = SNB;
  let reports = [], filter = 'all', modalOpen = false;

  const $list = document.getElementById('list');
  const $stats = document.getElementById('stats');
  const $filters = document.getElementById('filters');

  function render() {
    const count = (s) => reports.filter((r) => r.status === s).length;
    const tiles = [
      ['Total', reports.length, '#1A3C6E'],
      ['Pending', count('pending'), '#F59E0B'],
      ['In Progress', count('inProgress'), '#3B82F6'],
      ['Resolved', count('resolved'), '#16A34A'],
    ];
    $stats.innerHTML = tiles.map(([l, n, c]) => `<div class="card stat"><div class="bar" style="background:${c}"></div><span class="num" style="color:${c}">${n}</span><span class="lbl">${l}</span></div>`).join('');

    const opts = [['all', 'All']].concat(Object.entries(CFG.statuses).map(([k, v]) => [k, v.label]));
    $filters.innerHTML = opts.map(([k, l]) => `<button class="fbtn ${filter === k ? 'on' : ''}" data-f="${k}">${l}</button>`).join('');

    const shown = filter === 'all' ? reports : reports.filter((r) => r.status === filter);
    if (!reports.length) {
      $list.innerHTML = emptyState('📭', 'No reports yet', 'Tap "Submit a Report" above to file your first concern with the barangay.');
    } else if (!shown.length) {
      $list.innerHTML = emptyState('🔍', 'Nothing here', 'No reports match this filter.');
    } else {
      $list.innerHTML = shown.map((r) => reportCard(r)).join('');
    }
  }

  async function load() {
    if (modalOpen) return; // don't re-render under an open dialog
    try { reports = (await api('/api/grievances')).data; render(); }
    catch (e) { if (!reports.length) $list.innerHTML = emptyState('⚠️', 'Could not load reports', e.message); }
  }

  $filters.addEventListener('click', (e) => {
    const b = e.target.closest('[data-f]'); if (!b) return;
    filter = b.dataset.f; render();
  });
  $list.addEventListener('click', (e) => {
    const c = e.target.closest('[data-id]'); if (!c) return;
    const r = reports.find((x) => String(x.id) === c.dataset.id); if (!r) return;
    modalOpen = true;
    openReport(r, {
      canChat: true, canDelete: true,
      onClose: () => { modalOpen = false; load(); },
      onDeleted: () => { reports = reports.filter((x) => x.id !== r.id); render(); },
    });
  });

  poll(load, 10000);
})();
