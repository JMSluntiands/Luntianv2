/**
 * Shared row numbers + pagination for job list tables.
 * Works with tr.lbs-data-row and optional detail rows.
 */
(function (w, d) {
  'use strict';

  var PER_PAGE_DEFAULT = 25;
  var DETAIL_SEL =
    '.lbs-row-detail, .efficient_living-row-detail, .luntian-row-detail, tr[data-row-detail]';
  var TABLE_SEL = [
    'table.lbs-table',
    'table.efficient_living-table',
    'table.luntian-table',
    'table#lbsTable',
    'table#efficient_livingTable',
    'table#luntianTable',
    'table[data-job-list-paginate]',
  ].join(',');

  function perPageFor(table) {
    var n = parseInt(table.getAttribute('data-per-page') || '', 10);
    return !isNaN(n) && n > 0 ? n : PER_PAGE_DEFAULT;
  }

  function isDetailRow(tr) {
    if (!tr || tr.tagName !== 'TR') return false;
    try {
      return tr.matches(DETAIL_SEL);
    } catch (e) {
      return false;
    }
  }

  function dataRows(table) {
    return Array.prototype.slice.call(
      table.querySelectorAll('tbody tr.lbs-data-row')
    );
  }

  function matchedRows(table) {
    return dataRows(table).filter(function (row) {
      return row.getAttribute('data-job-filter-match') !== '0';
    });
  }

  function ensureRowNumColumn(table) {
    if (table.getAttribute('data-job-rownum-ready') === '1') return;
    var theadRow = table.querySelector('thead tr');
    if (!theadRow) return;

    if (!theadRow.querySelector('th[data-row-num]')) {
      var th = d.createElement('th');
      th.setAttribute('data-row-num', '1');
      th.setAttribute('data-sortable', '0');
      th.setAttribute('data-no-sort', '1');
      th.className =
        'lbs-th lbs-th-rownum cursor-default border-b border-slate-200 bg-slate-100 px-3 py-3 text-center align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400';
      th.innerHTML = '<span>#</span>';
      theadRow.insertBefore(th, theadRow.firstChild);

      var cg = table.querySelector('colgroup');
      if (cg) {
        var col = d.createElement('col');
        col.style.width = '52px';
        cg.insertBefore(col, cg.firstChild);
      }
    }

    dataRows(table).forEach(function (tr) {
      if (tr.querySelector('[data-row-num-cell]')) return;
      var td = d.createElement('td');
      td.setAttribute('data-label', '#');
      td.setAttribute('data-row-num-cell', '1');
      td.className =
        'lbs-td lbs-td-rownum border-b border-slate-200 px-3 py-3 text-center align-middle text-xs font-semibold text-slate-500 dark:border-slate-700 dark:text-slate-400';
      td.textContent = '';
      tr.insertBefore(td, tr.firstChild);
    });

    table.querySelectorAll('tbody tr').forEach(function (tr) {
      if (tr.classList.contains('lbs-data-row')) return;
      var cell = tr.querySelector('td[colspan]');
      if (!cell) return;
      var span = parseInt(cell.getAttribute('colspan') || '0', 10);
      if (!isNaN(span) && span > 0 && !cell.getAttribute('data-rownum-colspan-fixed')) {
        cell.setAttribute('colspan', String(span + 1));
        cell.setAttribute('data-rownum-colspan-fixed', '1');
      }
    });

    table.setAttribute('data-job-rownum-ready', '1');
  }

  function ensurePager(table) {
    var host =
      table.closest('.max-w-full.overflow-hidden, .lbs-table-card, .efficient_living-table-card, .luntian-table-card, .bph-table-card') ||
      table.parentElement;
    if (!host) return null;

    var existing = null;
    for (var i = 0; i < host.children.length; i++) {
      if (host.children[i].classList && host.children[i].classList.contains('job-list-pagination')) {
        existing = host.children[i];
        break;
      }
    }
    if (existing) return existing;

    var pager = d.createElement('div');
    pager.className =
      'job-list-pagination flex flex-col gap-3 border-t border-slate-200 bg-slate-50/80 px-4 py-3 dark:border-slate-700 dark:bg-slate-800/40 sm:flex-row sm:items-center sm:justify-between';
    pager.innerHTML =
      '<p class="job-list-pagination-info m-0 text-sm text-slate-600 dark:text-slate-300"></p>' +
      '<div class="job-list-pagination-controls flex flex-wrap items-center gap-1.5"></div>';
    host.appendChild(pager);
    return pager;
  }

  function renderPager(table, page, pages, total) {
    var pager = ensurePager(table);
    if (!pager) return;
    var perPage = perPageFor(table);
    var info = pager.querySelector('.job-list-pagination-info');
    var controls = pager.querySelector('.job-list-pagination-controls');
    if (!info || !controls) return;

    if (total === 0) {
      info.textContent = 'No jobs to show';
      controls.innerHTML = '';
      pager.hidden = false;
      return;
    }

    var start = (page - 1) * perPage + 1;
    var end = Math.min(page * perPage, total);
    info.textContent = 'Showing ' + start + '–' + end + ' of ' + total;

    function btn(label, targetPage, disabled, current) {
      var b = d.createElement('button');
      b.type = 'button';
      b.textContent = label;
      b.className =
        'job-list-page-btn inline-flex min-w-[2rem] items-center justify-center rounded-md border px-2.5 py-1.5 text-xs font-semibold transition-colors ' +
        (current
          ? 'border-emerald-600 bg-emerald-600 text-white'
          : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700');
      if (disabled) {
        b.disabled = true;
        b.className += ' cursor-not-allowed opacity-40';
      } else {
        b.addEventListener('click', function () {
          table.setAttribute('data-job-page', String(targetPage));
          refresh(table);
        });
      }
      return b;
    }

    controls.innerHTML = '';
    controls.appendChild(btn('Prev', Math.max(1, page - 1), page <= 1, false));

    var windowSize = 5;
    var startPage = Math.max(1, page - Math.floor(windowSize / 2));
    var endPage = Math.min(pages, startPage + windowSize - 1);
    startPage = Math.max(1, endPage - windowSize + 1);

    if (startPage > 1) {
      controls.appendChild(btn('1', 1, false, page === 1));
      if (startPage > 2) {
        var dots = d.createElement('span');
        dots.className = 'px-1 text-xs text-slate-400';
        dots.textContent = '…';
        controls.appendChild(dots);
      }
    }

    for (var p = startPage; p <= endPage; p++) {
      controls.appendChild(btn(String(p), p, false, p === page));
    }

    if (endPage < pages) {
      if (endPage < pages - 1) {
        var dots2 = d.createElement('span');
        dots2.className = 'px-1 text-xs text-slate-400';
        dots2.textContent = '…';
        controls.appendChild(dots2);
      }
      controls.appendChild(btn(String(pages), pages, false, page === pages));
    }

    controls.appendChild(btn('Next', Math.min(pages, page + 1), page >= pages, false));
    pager.hidden = false;
  }

  function refresh(table) {
    if (!table) return;
    ensureRowNumColumn(table);

    var all = dataRows(table);
    all.forEach(function (row) {
      if (!row.hasAttribute('data-job-filter-match')) {
        row.setAttribute('data-job-filter-match', '1');
      }
    });

    var rows = matchedRows(table);
    var perPage = perPageFor(table);
    var pages = Math.max(1, Math.ceil(rows.length / perPage) || 1);
    var page = parseInt(table.getAttribute('data-job-page') || '1', 10);
    if (isNaN(page) || page < 1) page = 1;
    if (page > pages) page = pages;
    table.setAttribute('data-job-page', String(page));

    all.forEach(function (row) {
      row.style.display = 'none';
      var next = row.nextElementSibling;
      if (isDetailRow(next)) {
        next.style.display = 'none';
      }
    });

    var start = (page - 1) * perPage;
    rows.slice(start, start + perPage).forEach(function (row, i) {
      row.style.display = '';
      var cell = row.querySelector('[data-row-num-cell]');
      if (cell) cell.textContent = String(start + i + 1);
      var next = row.nextElementSibling;
      if (isDetailRow(next) && !next.hidden && !next.hasAttribute('hidden')) {
        next.style.display = '';
      }
    });

    renderPager(table, page, pages, rows.length);
  }

  function refreshAll(root) {
    var scope = root && root.querySelectorAll ? root : d;
    var list = scope.querySelectorAll
      ? scope.querySelectorAll(TABLE_SEL)
      : d.querySelectorAll(TABLE_SEL);
    Array.prototype.forEach.call(list, function (table) {
      if (!table.querySelector('tbody tr.lbs-data-row')) return;
      refresh(table);
    });
  }

  function init() {
    refreshAll(d);
  }

  d.addEventListener('click', function (e) {
    var th = e.target && e.target.closest ? e.target.closest('thead th') : null;
    if (!th) return;
    var table = th.closest(TABLE_SEL);
    if (!table || !table.querySelector('tbody tr.lbs-data-row')) return;
    setTimeout(function () {
      refresh(table);
    }, 0);
  });

  // Expand chevron: show off-screen columns under the row (works with pagination display:none).
  d.addEventListener('click', function (e) {
    var btn = e.target && e.target.closest ? e.target.closest('[data-expand-row]') : null;
    if (!btn) return;
    var row = btn.closest('tr');
    if (!row) return;
    var next = row.nextElementSibling;
    if (!isDetailRow(next)) return;
    e.preventDefault();
    e.stopPropagation();

    var open = next.hidden || next.hasAttribute('hidden') || next.style.display === 'none';
    if (open) {
      next.hidden = false;
      next.removeAttribute('hidden');
      next.style.display = '';
    } else {
      next.hidden = true;
      next.setAttribute('hidden', '');
      next.style.display = 'none';
    }
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    btn.setAttribute('title', open ? 'Hide columns below' : 'Show columns below (no horizontal scroll)');
    btn.classList.toggle('is-expanded', open);
  });

  w.JobListPagination = {
    init: init,
    refresh: refresh,
    refreshAll: refreshAll,
    perPage: PER_PAGE_DEFAULT,
  };

  if (d.readyState === 'loading') {
    d.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})(window, document);
