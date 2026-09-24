/**
 * Drag column borders to resize dashboard tables.
 * Works with tables that have a <colgroup>, or creates one from header cells.
 * Persists widths in localStorage when the table has an id.
 */
(function (w, d) {
  'use strict';

  var MIN_WIDTH = 56;
  var MIN_ACTION_WIDTH = 120;
  var MIN_INTERACTIVE_WIDTH = 88;
  var HANDLE_CLASS = 'table-col-resize-handle';
  var STORAGE_PREFIX = 'luntian.table-col-widths:v2:';
  var TABLE_SEL = [
    'table.lbs-table',
    'table.efficient_living-table',
    'table.luntian-table',
    'table.reports-table',
    'table.fyrs-list-table',
    'table[data-table-sort]',
    'table[data-col-resize]',
    'table#lbsTable',
    'table#efficient_livingTable',
    'table#luntianTable',
    'table#reportsTable',
    'table#jobRequestTable',
    'table#acClientTable',
    'table[id$="MailboxTable"]',
  ].join(',');

  var prepared = typeof w.WeakSet === 'function' ? new w.WeakSet() : null;

  function headerLabel(th) {
    return String(th.textContent || '')
      .replace(/\s+/g, ' ')
      .trim()
      .toLowerCase();
  }

  function minWidthForHeader(th) {
    if (!th) return MIN_WIDTH;
    if (th.classList.contains('lbs-th-action')) return MIN_ACTION_WIDTH;
    var label = headerLabel(th);
    if (label.indexOf('action') === 0) return MIN_ACTION_WIDTH;
    if (label.indexOf('complexity') === 0) return 120;
    if (
      label === 'staff' ||
      label === 'checker' ||
      label === 'status' ||
      label === 'priority' ||
      label === 'assigned to' ||
      label === 'checked by' ||
      label === 'urgent'
    ) {
      return MIN_INTERACTIVE_WIDTH;
    }
    return MIN_WIDTH;
  }

  function ensureColgroup(table) {
    var colgroup = table.querySelector('colgroup');
    var headerCells = table.querySelectorAll('thead tr:first-child > th, thead tr:first-child > td');
    if (!headerCells.length) return null;

    if (!colgroup) {
      colgroup = d.createElement('colgroup');
      table.insertBefore(colgroup, table.firstChild);
    }

    var cols = colgroup.querySelectorAll('col');
    while (cols.length < headerCells.length) {
      colgroup.appendChild(d.createElement('col'));
      cols = colgroup.querySelectorAll('col');
    }

    Array.prototype.forEach.call(headerCells, function (th, i) {
      var col = cols[i];
      if (!col) return;
      var minW = minWidthForHeader(th);
      if (col.style.width) {
        var existing = parseInt(col.style.width, 10);
        if (existing >= minW) return;
      }
      var rect = th.getBoundingClientRect();
      var wpx = Math.max(minW, Math.round(rect.width || th.offsetWidth || minW));
      col.style.width = wpx + 'px';
    });

    return colgroup;
  }

  function storageKey(table) {
    var id = table.getAttribute('id') || table.getAttribute('data-col-resize-key');
    if (!id) return null;
    return STORAGE_PREFIX + id;
  }

  function loadWidths(table, cols, headerCells) {
    var key = storageKey(table);
    if (!key) return;
    try {
      var raw = w.localStorage.getItem(key);
      if (!raw) return;
      var saved = JSON.parse(raw);
      if (!Array.isArray(saved)) return;
      Array.prototype.forEach.call(cols, function (col, i) {
        var px = parseInt(saved[i], 10);
        var minW = minWidthForHeader(headerCells[i]);
        if (px >= minW) col.style.width = px + 'px';
        else if (px > 0) col.style.width = minW + 'px';
      });
    } catch (e) {}
  }

  function saveWidths(table, cols) {
    var key = storageKey(table);
    if (!key) return;
    try {
      var widths = Array.prototype.map.call(cols, function (col) {
        return parseInt(col.style.width, 10) || MIN_WIDTH;
      });
      w.localStorage.setItem(key, JSON.stringify(widths));
    } catch (e) {}
  }

  function syncTableMinWidth(table, cols) {
    var total = 0;
    Array.prototype.forEach.call(cols, function (col) {
      total += parseInt(col.style.width, 10) || MIN_WIDTH;
    });
    table.style.minWidth = total + 'px';
  }

  function attachHandles(table) {
    if (prepared && prepared.has(table)) return;
    if (prepared) prepared.add(table);

    if (w.matchMedia && w.matchMedia('(max-width: 768px)').matches) return;

    var colgroup = ensureColgroup(table);
    if (!colgroup) return;
    var cols = colgroup.querySelectorAll('col');
    var headerCells = table.querySelectorAll('thead tr:first-child > th, thead tr:first-child > td');
    if (!headerCells.length) return;

    table.classList.add('table-col-resizable');
    if (!table.classList.contains('table-fixed') && !/\btable-fixed\b/.test(table.getAttribute('class') || '')) {
      table.style.tableLayout = 'fixed';
    }

    loadWidths(table, cols, headerCells);
    syncTableMinWidth(table, cols);

    Array.prototype.forEach.call(headerCells, function (th, index) {
      if (th.querySelector('.' + HANDLE_CLASS)) return;
      if (index >= cols.length) return;

      // Keep Action icons usable — don't attach a drag handle that can steal clicks.
      if (th.classList.contains('lbs-th-action') || headerLabel(th).indexOf('action') === 0) {
        var actionMin = MIN_ACTION_WIDTH;
        var actionWidth = parseInt(cols[index].style.width, 10) || 0;
        if (actionWidth < actionMin) cols[index].style.width = actionMin + 'px';
        return;
      }

      th.classList.add('table-col-resize-th');
      var handle = d.createElement('span');
      handle.className = HANDLE_CLASS;
      handle.setAttribute('aria-hidden', 'true');
      handle.title = 'Drag to resize column';
      th.appendChild(handle);

      var colMin = minWidthForHeader(th);

      handle.addEventListener('mousedown', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var startX = e.clientX;
        var startWidth = parseInt(cols[index].style.width, 10) || th.offsetWidth || colMin;

        d.body.classList.add('table-col-resizing');

        function onMove(ev) {
          var delta = ev.clientX - startX;
          var newWidth = Math.max(colMin, startWidth + delta);
          cols[index].style.width = newWidth + 'px';
          syncTableMinWidth(table, cols);
        }

        function onUp() {
          d.removeEventListener('mousemove', onMove);
          d.removeEventListener('mouseup', onUp);
          d.body.classList.remove('table-col-resizing');
          saveWidths(table, cols);
        }

        d.addEventListener('mousemove', onMove);
        d.addEventListener('mouseup', onUp);
      });

      handle.addEventListener('dblclick', function (e) {
        e.preventDefault();
        e.stopPropagation();
        cols[index].style.width = '';
        var natural = Math.max(colMin, Math.round(th.scrollWidth + 24));
        cols[index].style.width = natural + 'px';
        syncTableMinWidth(table, cols);
        saveWidths(table, cols);
      });
    });

    syncTableMinWidth(table, cols);
  }

  function initAll() {
    d.querySelectorAll(TABLE_SEL).forEach(attachHandles);
  }

  w.initTableColumnResize = initAll;
  w.refreshTableColumnResize = function (root) {
    var scope = root && root.querySelectorAll ? root : d;
    // Allow re-init after AJAX refresh replaces the table node.
    if (prepared && root && root.querySelectorAll) {
      scope.querySelectorAll(TABLE_SEL).forEach(function (table) {
        prepared.delete(table);
      });
    }
    scope.querySelectorAll(TABLE_SEL).forEach(attachHandles);
  };

  if (d.readyState === 'loading') {
    d.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})(window, document);
