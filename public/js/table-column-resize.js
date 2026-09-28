/**
 * Drag column borders to resize dashboard tables.
 * Default: fit all columns into the visible container (no horizontal scroll).
 * Users can still drag to resize; widths persist in localStorage per table id.
 */
(function (w, d) {
  'use strict';

  var MIN_WIDTH = 56;
  var MIN_ACTION_WIDTH = 110;
  var MIN_INTERACTIVE_WIDTH = 72;
  var HANDLE_CLASS = 'table-col-resize-handle';
  // v3: fit-to-container defaults (invalidates older oversized saved widths).
  var STORAGE_PREFIX = 'luntian.table-col-widths:v3:';
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
    if (label.indexOf('complexity') === 0) return 100;
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

  function containerWidth(table) {
    var wrap =
      table.closest('.max-w-full.overflow-x-auto, .overflow-x-auto, .lbs-table-wrap, .efficient_living-table-wrap, .luntian-table-wrap') ||
      table.parentElement;
    var wpx = wrap ? wrap.clientWidth : 0;
    if (wpx < 80 && table.parentElement) wpx = table.parentElement.clientWidth;
    return Math.max(0, wpx);
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
    if (!key) return false;
    try {
      var raw = w.localStorage.getItem(key);
      if (!raw) return false;
      var saved = JSON.parse(raw);
      if (!Array.isArray(saved) || !saved.length) return false;
      Array.prototype.forEach.call(cols, function (col, i) {
        var px = parseInt(saved[i], 10);
        var minW = minWidthForHeader(headerCells[i]);
        if (px >= minW) col.style.width = px + 'px';
        else if (px > 0) col.style.width = minW + 'px';
      });
      return true;
    } catch (e) {
      return false;
    }
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

  function sumWidths(cols) {
    var total = 0;
    Array.prototype.forEach.call(cols, function (col) {
      total += parseInt(col.style.width, 10) || MIN_WIDTH;
    });
    return total;
  }

  function syncTableMinWidth(table, cols) {
    var total = sumWidths(cols);
    var avail = containerWidth(table);
    if (avail > 0 && total <= avail + 1) {
      // Fit inside the viewport — no forced horizontal scroll.
      table.style.minWidth = '100%';
      table.style.width = '100%';
    } else {
      table.style.minWidth = total + 'px';
      table.style.width = total + 'px';
    }
  }

  /**
   * Scale all columns so their widths fill (or shrink into) the container.
   * Respects per-column minimums; if mins alone exceed the container, scroll is unavoidable.
   */
  function fitColumnsToContainer(table, cols, headerCells) {
    var avail = containerWidth(table);
    if (avail < 80 || !cols.length) return;

    var mins = [];
    var weights = [];
    var minSum = 0;
    var weightSum = 0;

    Array.prototype.forEach.call(cols, function (col, i) {
      var minW = minWidthForHeader(headerCells[i]);
      var cur = parseInt(col.style.width, 10) || minW;
      if (cur < minW) cur = minW;
      mins.push(minW);
      weights.push(cur);
      minSum += minW;
      weightSum += cur;
    });

    var target = avail;
    if (minSum >= avail) {
      // Can't fit — use minimums and allow scroll.
      Array.prototype.forEach.call(cols, function (col, i) {
        col.style.width = mins[i] + 'px';
      });
      syncTableMinWidth(table, cols);
      return;
    }

    // Distribute target width proportional to current weights, then clamp to mins.
    var widths = weights.map(function (wt, i) {
      return Math.max(mins[i], Math.floor((wt / weightSum) * target));
    });

    var assigned = widths.reduce(function (a, b) {
      return a + b;
    }, 0);
    var leftover = target - assigned;

    // Give leftover pixels to the largest flexible columns (not Action / tiny mins).
    var order = widths
      .map(function (wpx, i) {
        return { i: i, flex: wpx - mins[i] };
      })
      .sort(function (a, b) {
        return b.flex - a.flex;
      });

    var guard = 0;
    while (leftover !== 0 && guard < 500) {
      guard += 1;
      var step = leftover > 0 ? 1 : -1;
      var progressed = false;
      for (var o = 0; o < order.length && leftover !== 0; o++) {
        var idx = order[o].i;
        var next = widths[idx] + step;
        if (next < mins[idx]) continue;
        widths[idx] = next;
        leftover -= step;
        progressed = true;
      }
      if (!progressed) break;
    }

    Array.prototype.forEach.call(cols, function (col, i) {
      col.style.width = widths[i] + 'px';
    });
    syncTableMinWidth(table, cols);
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
    // Always start with every column visible in the container.
    fitColumnsToContainer(table, cols, headerCells);

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
        // Reset all columns to fit the container (show everything again).
        fitColumnsToContainer(table, cols, headerCells);
        saveWidths(table, cols);
      });
    });
  }

  function initAll() {
    d.querySelectorAll(TABLE_SEL).forEach(attachHandles);
  }

  var resizeTimer = null;
  function onWindowResize() {
    if (resizeTimer) w.clearTimeout(resizeTimer);
    resizeTimer = w.setTimeout(function () {
      d.querySelectorAll(TABLE_SEL + '.table-col-resizable').forEach(function (table) {
        var colgroup = table.querySelector('colgroup');
        if (!colgroup) return;
        var cols = colgroup.querySelectorAll('col');
        var headerCells = table.querySelectorAll('thead tr:first-child > th, thead tr:first-child > td');
        if (!cols.length || !headerCells.length) return;
        fitColumnsToContainer(table, cols, headerCells);
      });
    }, 120);
  }
  w.addEventListener('resize', onWindowResize);

  w.initTableColumnResize = initAll;
  w.refreshTableColumnResize = function (root) {
    var scope = root && root.querySelectorAll ? root : d;
    if (prepared && root && root.querySelectorAll) {
      scope.querySelectorAll(TABLE_SEL).forEach(function (table) {
        prepared.delete(table);
      });
    }
    scope.querySelectorAll(TABLE_SEL).forEach(attachHandles);
  };
  w.fitTableColumnsToContainer = function (table) {
    if (!table) return;
    var colgroup = table.querySelector('colgroup');
    if (!colgroup) return;
    var cols = colgroup.querySelectorAll('col');
    var headerCells = table.querySelectorAll('thead tr:first-child > th, thead tr:first-child > td');
    fitColumnsToContainer(table, cols, headerCells);
  };

  if (d.readyState === 'loading') {
    d.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})(window, document);
