/**
 * Shared client-side table sort (asc / desc) for dashboard list tables.
 * Prefer td[data-sort] for cell values; use data-sort-dir on th for asc/desc toggle.
 */
(function (w, d) {
  'use strict';

  var DETAIL_SEL =
    '.lbs-row-detail, .efficient_living-row-detail, .luntian-row-detail, tr[data-row-detail]';
  var SKIP_TH =
    '.lbs-th-action, .efficient_living-th-action, .luntian-th-action, .reports-th-action';
  var TABLE_SEL = [
    'table#lbsTable',
    'table#efficient_livingTable',
    'table#luntianTable',
    'table#reportsTable',
    'table[id$="MailboxTable"]',
    'table[data-table-sort]',
    'table.lbs-table',
    'table.efficient_living-table',
    'table.luntian-table',
    'table.reports-table',
  ].join(',');

  var ARROW_BOTH = '\u2195';
  var ARROW_UP = '\u2191';
  var ARROW_DOWN = '\u2193';
  var prepared = typeof w.WeakSet === 'function' ? new w.WeakSet() : null;

  function cellSortValue(cell) {
    if (!cell) return '';
    var attr = cell.getAttribute('data-sort');
    if (attr != null && String(attr).trim() !== '') {
      return String(attr).trim();
    }

    var clone = cell.cloneNode(true);
    clone
      .querySelectorAll(
        'select, option, .lbs-status-menu, .lbs-initials-menu, [data-status-menu], [hidden], script, style'
      )
      .forEach(function (el) {
        el.remove();
      });

    var trigger =
      clone.querySelector(
        '[data-status-trigger], [data-initials-trigger], .lbs-status-btn, .lbs-initials-btn'
      ) || clone.querySelector('button, a');
    if (trigger) {
      return String(trigger.textContent || '')
        .replace(/\s+/g, ' ')
        .trim();
    }

    return String(clone.textContent || '')
      .replace(/\s+/g, ' ')
      .trim();
  }

  function compareValues(aVal, bVal, dir) {
    var aEmpty = aVal === '' || aVal == null;
    var bEmpty = bVal === '' || bVal == null;
    if (aEmpty && bEmpty) return 0;
    if (aEmpty) return 1;
    if (bEmpty) return -1;

    var aStr = String(aVal).trim();
    var bStr = String(bVal).trim();

    var aTime = Date.parse(aStr.replace(' ', 'T'));
    var bTime = Date.parse(bStr.replace(' ', 'T'));
    var bothDates =
      !isNaN(aTime) &&
      !isNaN(bTime) &&
      (/^\d{4}-\d{2}-\d{2}/.test(aStr) || /\d+[\/\-]\d+/.test(aStr)) &&
      (/^\d{4}-\d{2}-\d{2}/.test(bStr) || /\d+[\/\-]\d+/.test(bStr));
    if (bothDates) {
      return dir === 'asc' ? aTime - bTime : bTime - aTime;
    }

    var aNum = parseFloat(aStr);
    var bNum = parseFloat(bStr);
    var bothNumeric =
      !isNaN(aNum) &&
      !isNaN(bNum) &&
      /^-?\d+(\.\d+)?$/.test(aStr) &&
      /^-?\d+(\.\d+)?$/.test(bStr);

    if (bothNumeric) {
      return dir === 'asc' ? aNum - bNum : bNum - aNum;
    }

    var cmp = aStr.localeCompare(bStr, undefined, {
      numeric: true,
      sensitivity: 'base',
    });
    return dir === 'asc' ? cmp : -cmp;
  }

  function ensureSortIcon(th) {
    if (
      th.querySelector(
        '.lbs-sort-icon, .efficient_living-sort-icon, .luntian-sort-icon, .reports-sort-icon, .bph-sort-icon'
      )
    ) {
      return;
    }
    var icon = d.createElement('span');
    icon.className = 'lbs-sort-icon ml-1 text-xs opacity-60';
    icon.setAttribute('aria-hidden', 'true');
    icon.setAttribute('data-sort-icon', '1');
    icon.textContent = ARROW_BOTH;
    th.appendChild(icon);
  }

  function updateSortIcon(th, dir) {
    var icon = th.querySelector(
      '.lbs-sort-icon, .efficient_living-sort-icon, .luntian-sort-icon, .reports-sort-icon, .bph-sort-icon'
    );
    if (!icon) return;
    if (dir === 'asc') icon.textContent = ARROW_UP;
    else if (dir === 'desc') icon.textContent = ARROW_DOWN;
    else icon.textContent = ARROW_BOTH;
  }

  function headerLabel(th) {
    var clone = th.cloneNode(true);
    clone
      .querySelectorAll(
        '.lbs-sort-icon, .efficient_living-sort-icon, .luntian-sort-icon, .reports-sort-icon, .bph-sort-icon, [data-sort-icon]'
      )
      .forEach(function (el) {
        el.remove();
      });
    return String(clone.textContent || '')
      .replace(/\s+/g, ' ')
      .trim()
      .toLowerCase();
  }

  function isSkipHeader(th) {
    if (!th) return true;
    if (th.getAttribute('data-sortable') === '0') return true;
    if (th.hasAttribute('data-no-sort')) return true;
    try {
      if (th.matches(SKIP_TH)) return true;
    } catch (e) {}
    var label = headerLabel(th);
    return label === 'action' || label === 'actions';
  }

  function isSortableTable(table) {
    if (!table || table.tagName !== 'TABLE') return false;
    try {
      if (table.matches(TABLE_SEL)) return true;
    } catch (e) {}
    return !!table.querySelector(
      'thead th[data-sort], thead th[data-sort-dir], thead th .lbs-sort-icon, thead th .reports-sort-icon, thead th .efficient_living-sort-icon, thead th .luntian-sort-icon, thead th .bph-sort-icon'
    );
  }

  function collectRowGroups(tbody) {
    var groups = [];
    var children = Array.prototype.slice.call(tbody.children);
    for (var i = 0; i < children.length; i++) {
      var row = children[i];
      if (!row || row.tagName !== 'TR') continue;
      if (row.matches(DETAIL_SEL)) continue;
      var detail = null;
      var next = children[i + 1];
      if (next && next.tagName === 'TR' && next.matches(DETAIL_SEL)) {
        detail = next;
        i += 1;
      }
      groups.push({ row: row, detail: detail });
    }
    return groups;
  }

  function headerIndex(table, th) {
    var thead = table.tHead || table.querySelector('thead');
    if (!thead) return typeof th.cellIndex === 'number' ? th.cellIndex : -1;
    var row = th.parentElement;
    if (!row || row.tagName !== 'TR') {
      return typeof th.cellIndex === 'number' ? th.cellIndex : -1;
    }
    var headers = Array.prototype.slice.call(row.children).filter(function (el) {
      return el.tagName === 'TH';
    });
    var idx = headers.indexOf(th);
    if (idx >= 0) return idx;
    return typeof th.cellIndex === 'number' ? th.cellIndex : -1;
  }

  function currentDir(th) {
    var dir = (th.getAttribute('data-sort-dir') || th.getAttribute('data-sort') || '')
      .trim()
      .toLowerCase();
    return dir === 'asc' || dir === 'desc' ? dir : '';
  }

  function setHeaderDir(th, dir) {
    if (dir === 'asc' || dir === 'desc') {
      th.setAttribute('data-sort-dir', dir);
      th.setAttribute('data-sort', dir); // keep CSS hooks working
    } else {
      th.removeAttribute('data-sort-dir');
      th.setAttribute('data-sort', '');
    }
    updateSortIcon(th, dir || '');
  }

  function sortTable(table, th, dir) {
    var tbody = table.tBodies[0];
    if (!tbody) return;

    var colIndex = headerIndex(table, th);
    if (colIndex < 0) return;

    var thead = table.tHead || table.querySelector('thead');
    if (thead) {
      Array.prototype.forEach.call(thead.querySelectorAll('th'), function (h) {
        if (h !== th) setHeaderDir(h, '');
      });
    }
    setHeaderDir(th, dir);

    var groups = collectRowGroups(tbody);
    groups.sort(function (ga, gb) {
      return compareValues(
        cellSortValue(ga.row.children[colIndex]),
        cellSortValue(gb.row.children[colIndex]),
        dir
      );
    });

    var frag = d.createDocumentFragment();
    groups.forEach(function (g) {
      frag.appendChild(g.row);
      if (g.detail) frag.appendChild(g.detail);
    });
    tbody.appendChild(frag);
  }

  function prepareTable(table) {
    if (!isSortableTable(table)) return;
    var thead = table.tHead || table.querySelector('thead');
    if (!thead) return;

    Array.prototype.forEach.call(thead.querySelectorAll('th'), function (th) {
      if (isSkipHeader(th)) {
        th.classList.add('cursor-default');
        th.setAttribute('data-no-sort', '');
        th.setAttribute('data-sortable', '0');
        return;
      }
      th.setAttribute('data-sortable', '1');
      th.removeAttribute('data-no-sort');
      if (!th.hasAttribute('data-sort')) th.setAttribute('data-sort', '');
      th.classList.add('cursor-pointer', 'select-none');
      th.style.cursor = 'pointer';
      ensureSortIcon(th);
    });

    if (prepared) prepared.add(table);
    table.setAttribute('data-sort-ready', '1');
  }

  function onDocClick(e) {
    var th = e.target && e.target.closest ? e.target.closest('thead th') : null;
    if (!th) return;
    if (th.getAttribute('data-sortable') === '0' || th.hasAttribute('data-no-sort')) return;
    try {
      if (th.matches(SKIP_TH)) return;
    } catch (err) {}
    if (e.target.closest('a, button, input, select, label')) return;

    var table = th.closest('table');
    if (!table || !isSortableTable(table)) return;

    if (!table.hasAttribute('data-sort-ready') && !(prepared && prepared.has(table))) {
      prepareTable(table);
    }
    if (th.getAttribute('data-sortable') === '0' || th.hasAttribute('data-no-sort')) return;

    var current = currentDir(th);
    var next = current === 'asc' ? 'desc' : 'asc';
    sortTable(table, th, next);
  }

  function initTableSort(tableOrSelector) {
    if (!tableOrSelector) {
      initAllTableSorts();
      return;
    }
    if (typeof tableOrSelector === 'string') {
      d.querySelectorAll(tableOrSelector).forEach(prepareTable);
      return;
    }
    if (tableOrSelector.jquery) {
      tableOrSelector.each(function () {
        prepareTable(this);
      });
      return;
    }
    if (tableOrSelector.nodeType === 1) {
      prepareTable(tableOrSelector);
    }
  }

  function initAllTableSorts(root) {
    var scope = root && root.querySelectorAll ? root : d;
    scope.querySelectorAll('table').forEach(prepareTable);
  }

  w.initTableSort = initTableSort;
  w.initAllTableSorts = initAllTableSorts;

  d.addEventListener('click', onDocClick, true);

  function boot() {
    initAllTableSorts();
  }

  if (d.readyState === 'loading') {
    d.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})(window, document);
