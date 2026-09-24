/**
 * Drag column borders to resize dashboard tables.
 * Works with tables that have a <colgroup>, or creates one from header cells.
 * Persists widths in localStorage when the table has an id.
 */
(function (w, d) {
  'use strict';

  var MIN_WIDTH = 56;
  var HANDLE_CLASS = 'table-col-resize-handle';
  var STORAGE_PREFIX = 'luntian.table-col-widths:';
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
      if (col.style.width) return;
      var rect = th.getBoundingClientRect();
      var wpx = Math.max(MIN_WIDTH, Math.round(rect.width || th.offsetWidth || MIN_WIDTH));
      col.style.width = wpx + 'px';
    });

    return colgroup;
  }

  function storageKey(table) {
    var id = table.getAttribute('id') || table.getAttribute('data-col-resize-key');
    if (!id) return null;
    return STORAGE_PREFIX + id;
  }

  function loadWidths(table, cols) {
    var key = storageKey(table);
    if (!key) return;
    try {
      var raw = w.localStorage.getItem(key);
      if (!raw) return;
      var saved = JSON.parse(raw);
      if (!Array.isArray(saved)) return;
      Array.prototype.forEach.call(cols, function (col, i) {
        var px = parseInt(saved[i], 10);
        if (px >= MIN_WIDTH) col.style.width = px + 'px';
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

    loadWidths(table, cols);
    syncTableMinWidth(table, cols);

    Array.prototype.forEach.call(headerCells, function (th, index) {
      if (index >= cols.length - 0 && index === headerCells.length - 1) {
        // still allow resize on last column by growing table
      }
      if (th.querySelector('.' + HANDLE_CLASS)) return;
      if (index >= cols.length) return;

      th.classList.add('table-col-resize-th');
      var handle = d.createElement('span');
      handle.className = HANDLE_CLASS;
      handle.setAttribute('aria-hidden', 'true');
      handle.title = 'Drag to resize column';
      th.appendChild(handle);

      handle.addEventListener('mousedown', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var startX = e.clientX;
        var startWidth = parseInt(cols[index].style.width, 10) || th.offsetWidth || MIN_WIDTH;

        d.body.classList.add('table-col-resizing');

        function onMove(ev) {
          var delta = ev.clientX - startX;
          var newWidth = Math.max(MIN_WIDTH, startWidth + delta);
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
        var natural = Math.max(MIN_WIDTH, Math.round(th.scrollWidth + 24));
        cols[index].style.width = natural + 'px';
        syncTableMinWidth(table, cols);
        saveWidths(table, cols);
      });
    });
  }

  function initAll() {
    d.querySelectorAll(TABLE_SEL).forEach(attachHandles);
  }

  w.initTableColumnResize = initAll;
  w.refreshTableColumnResize = function (root) {
    var scope = root && root.querySelectorAll ? root : d;
    scope.querySelectorAll(TABLE_SEL).forEach(attachHandles);
  };

  if (d.readyState === 'loading') {
    d.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})(window, document);
