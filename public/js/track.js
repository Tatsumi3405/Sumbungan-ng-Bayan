(function () {
  'use strict';
  const { api, reportCard, emptyState, openReport, poll, esc, toast } = SNB;
  let mine = [], modalOpen = false;
  const $list = document.getElementById('list');
  const $result = document.getElementById('result');

  function renderMine() {
    $list.innerHTML = mine.length ? mine.map((r) => reportCard(r)).join('') : emptyState('📭', 'No reports yet', 'Reports you submit will show up here.');
  }
  async function loadMine() {
    if (modalOpen) return;
    try { mine = (await api('/api/grievances')).data; renderMine(); } catch (e) { $list.innerHTML = emptyState('⚠️', 'Could not load reports', e.message); }
  }

  function open(r, canChat, canDelete) {
    modalOpen = true;
    openReport(r, {
      canChat, canDelete,
      onClose: () => { modalOpen = false; loadMine(); },
      onDeleted: () => { $result.innerHTML = ''; mine = mine.filter((x) => x.id !== r.id); renderMine(); },
    });
  }

  document.getElementById('searchForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('searchId').value.trim();
    if (!id) { toast('Enter a report ID to search.', 'err'); return; }
    $result.innerHTML = '<div class="spinner"></div>';
    try {
      const r = (await api('/api/grievances/' + encodeURIComponent(id))).data;
      $result.innerHTML = `<div class="section-head"><h2>Result</h2></div>${reportCard(r)}`;
      $result.firstElementChild.nextElementSibling.addEventListener('click', () => open(r, r.can_chat, r.can_delete));
    } catch (err) {
      $result.innerHTML = emptyState('🔍', err.status === 404 ? 'No report found' : 'Search failed', err.status === 404 ? 'Check the ID and try again.' : err.message);
    }
  });

  $list.addEventListener('click', (e) => {
    const c = e.target.closest('[data-id]'); if (!c) return;
    const r = mine.find((x) => String(x.id) === c.dataset.id); if (r) open(r, true, true);
  });

  poll(loadMine, 10000);
})();
