/**
 * Shared client-side table sort (asc / desc) for dashboard list tables.
 * Prefer td[data-sort]; keep Action columns non-sortable; preserve detail-row pairs.
 */
(function (w, d) {
  'use strict';

  var DETAIL_SEL = '.lbs-row-detail, .efficient_living-row-detail, .luntian-row-detail, tr[data-row-detail]';
  var SKIP_TH =
    '.lbs-th-action, .efficient_living-th-action, .luntian-th-action, .reports-th-action, [data-no-sort]';
  var TABLE_SEL = [
    'table#lbsTable',
    'table#efficient_livingTable',
    'table#luntianTable',
    'table#reportsTable',
    'table[id$="MailboxTable"]',
    'table[data-table-sort]',
    'table.lbs-table',
    'table.reports-table',
  ].join(',');

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
      clone.querySelector('[data-status-trigger], [data-initials-trigger], .lbs-status-btn, .lbs-initials-btn') ||
      clone.querySelector('button, a');
    if (trigger) {
      return String(trigger.textContent || '').replace(/\s+/g, ' ').trim();
    }

    return String(clone.textContent || '').replace(/\s+/g, ' ').trim();
  }

  function compareValues(aVal, bVal, dir) {
    var aEmpty = aVal === '' || aVal == null;
    var bEmpty = bVal === '' || bVal == null;
    if (aEmpty && bEmpty) return 0;
    if (aEmpty) return 1;
    if (bEmpty) return -1;

    var aStr = String(aVal).trim();
    var bStr = String(bVal).trim();
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
    if (th.querySelector('.lbs-sort-icon, .efficient_living-sort-icon, .luntian-sort-icon, .reports-sort-icon, .bph-sort-icon')) {
      return;
    }
    var icon = d.createElement('span');
    icon.className = 'lbs-sort-icon ml-1 text-xs opacity-60';
    icon.setAttribute('aria-hidden', 'true');
    icon.textContent = '↕';
    th.appendChild(icon);
  }

  function isSkipHeader(th) {
    if (!th) return true;
    if (th.matches(SKIP_TH)) return true;
    if (th.hasAttribute('data-no-sort')) return true;
    var label = String(th.textContent || '')
      .replace(/\s+/g, ' ')
      .trim()
      .toLowerCase()
      .replace(/[↕↑↓]/g, '')
      .trim();
    if (label === 'action' || label === 'actions') return true;
    return false;
  }

  function isSortableTable(table) {
    if (!table || table.tagName !== 'TABLE') return false;
    if (table.matches(TABLE_SEL)) return true;
    return !!table.querySelector(
      'thead th[data-sort], thead th .lbs-sort-icon, thead th .reports-sort-icon, thead th .efficient_living-sort-icon, thead th .luntian-sort-icon, thead th .bph-sort-icon'
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

  function sortTable(table, th, dir) {
    var tbody = table.tBodies[0];
    if (!tbody) return;

    var headers = Array.prototype.slice.call(table.querySelectorAll('thead th'));
    var colIndex = headers.indexOf(th);
    if (colIndex < 0) return;

    headers.forEach(function (h) {
      if (h !== th) h.setAttribute('data-sort', '');
    });
    th.setAttribute('data-sort', dir);

    var groups = collectRowGroups(tbody);
    groups.sort(function (ga, gb) {
      var aCell = ga.row.children[colIndex];
      var bCell = gb.row.children[colIndex];
      return compareValues(cellSortValue(aCell), cellSortValue(bCell), dir);
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
        return;
      }
      if (!th.hasAttribute('data-sort')) th.setAttribute('data-sort', '');
      th.classList.add('cursor-pointer', 'select-none');
      ensureSortIcon(th);
    });
  }

  function onDocClick(e) {
    var th = e.target.closest('thead th');
    if (!th || isSkipHeader(th)) return;
    if (e.target.closest('a, button, input, select, label')) return;

    var table = th.closest('table');
    if (!table || !isSortableTable(table)) return;

    prepareTable(table);

    var current = th.getAttribute('data-sort') || '';
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

  d.addEventListener('click', onDocClick);

  if (d.readyState === 'loading') {
    d.addEventListener('DOMContentLoaded', function () {
      initAllTableSorts();
    });
  } else {
    initAllTableSorts();
  }
})(window, document);
