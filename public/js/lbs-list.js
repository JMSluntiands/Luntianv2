(function (w) {
  'use strict';
  var MODAL_ID = 'luntian-fec-units-modal';

  function isForEmailConfirmation(val) {
    return String(val || '').toLowerCase().trim() === 'for email confirmation';
  }

  function parseCurrentUnits(v) {
    var n = parseInt(v, 10);
    return isNaN(n) || n < 0 ? 0 : n;
  }

  function ensureModal() {
    var el = document.getElementById(MODAL_ID);
    if (el) return el;
    el = document.createElement('div');
    el.id = MODAL_ID;
    el.className =
      'fixed inset-0 z-[10050] flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200';
    el.setAttribute('role', 'dialog');
    el.setAttribute('aria-labelledby', 'luntian-fec-units-title');
    el.setAttribute('aria-modal', 'true');
    el.innerHTML =
      '<div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-600 dark:bg-[#2D3748]">' +
      '<div class="border-b border-slate-200 px-5 py-4 dark:border-slate-600">' +
      '<h2 id="luntian-fec-units-title" class="m-0 text-lg font-bold text-slate-800 dark:text-white">Units required</h2>' +
      '<p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Maglagay muna ng bilang ng units bago ilipat ang status sa <strong>For Email Confirmation</strong>.</p>' +
      '</div>' +
      '<div class="px-5 py-4">' +
      '<label for="luntian-fec-units-input" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Units</label>' +
      '<input id="luntian-fec-units-input" type="number" min="1" max="9999" step="1" data-fec-units-input class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" placeholder="e.g. 1" />' +
      '<p class="mt-2 min-h-[1.25rem] text-sm text-red-600 dark:text-red-400" data-fec-units-error></p>' +
      '</div>' +
      '<div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-600">' +
      '<button type="button" data-fec-units-cancel class="cursor-pointer rounded-lg bg-slate-200 px-4 py-2 text-sm font-medium text-slate-800 hover:bg-slate-300 dark:bg-slate-600 dark:text-white dark:hover:bg-slate-500">Cancel</button>' +
      '<button type="button" data-fec-units-confirm class="cursor-pointer rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">Save &amp; continue</button>' +
      '</div></div>';
    document.body.appendChild(el);
    return el;
  }

  function showModal(el) {
    el.classList.remove('opacity-0', 'pointer-events-none');
    el.classList.add('opacity-100', 'pointer-events-auto');
  }

  function hideModal(el) {
    el.classList.add('opacity-0', 'pointer-events-none');
    el.classList.remove('opacity-100', 'pointer-events-auto');
  }

  w.LuntianFecUnitsModal = {
    isForEmailConfirmation: isForEmailConfirmation,
    /**
     * @param {{ currentUnits: number, statusValue: string }} opts
     * @returns {Promise<{ unitsToSend: number|null }>}
     */
    promptIfNeeded: function (opts) {
      return new Promise(function (resolve, reject) {
        var sv = opts.statusValue;
        var cu = parseCurrentUnits(opts.currentUnits);
        if (!isForEmailConfirmation(sv)) {
          resolve({ unitsToSend: null });
          return;
        }
        if (cu >= 1) {
          resolve({ unitsToSend: null });
          return;
        }
        var modal = ensureModal();
        var input = modal.querySelector('[data-fec-units-input]');
        var errEl = modal.querySelector('[data-fec-units-error]');
        var btnOk = modal.querySelector('[data-fec-units-confirm]');
        var btnCancel = modal.querySelector('[data-fec-units-cancel]');
        if (input) {
          input.value = '';
          setTimeout(function () {
            input.focus();
          }, 100);
        }
        if (errEl) errEl.textContent = '';
        showModal(modal);

        function cleanup() {
          hideModal(modal);
          document.removeEventListener('keydown', onEsc);
          if (btnOk) btnOk.removeEventListener('click', onConfirmClick);
          if (btnCancel) btnCancel.removeEventListener('click', onCancelClick);
          modal.removeEventListener('click', onBackdrop);
        }

        function onCancel() {
          cleanup();
          reject(new Error('cancel'));
        }

        function onConfirmClick() {
          var raw = input ? String(input.value || '').trim() : '';
          var num = parseInt(raw, 10);
          if (!raw || isNaN(num) || num < 1) {
            if (errEl) errEl.textContent = 'Maglagay ng units (minimum 1).';
            return;
          }
          if (num > 9999) {
            if (errEl) errEl.textContent = 'Maximum 9999 units.';
            return;
          }
          cleanup();
          resolve({ unitsToSend: num });
        }

        function onCancelClick() {
          onCancel();
        }

        function onEsc(e) {
          if (e.key === 'Escape') onCancel();
        }

        function onBackdrop(e) {
          if (e.target === modal) onCancel();
        }

        if (btnOk) btnOk.addEventListener('click', onConfirmClick);
        if (btnCancel) btnCancel.addEventListener('click', onCancelClick);
        document.addEventListener('keydown', onEsc);
        modal.addEventListener('click', onBackdrop);
      });
    }
  };
})(window);

$(function () {
  function getLbsTable() {
    return $('#lbsTable');
  }

  var $search = $('#lbsSearch');
  var $filterDate = $('#lbsFilterDate');
  var $filterBuilder = $('#lbsFilterBuilder');
  var $filterPriority = $('#lbsFilterPriority');
  var $filterReset = $('#lbsFilterReset');
  var $lbsListRefreshBtn = $('#lbsListRefreshBtn');
  var $lbsListTablesInner = $('#lbs-list-tables-inner');
  var $lbsListTablesRoot = $('#lbs-list-tables-refresh-root');
  function initFilterSelect2() {
    if (!$.fn || !$.fn.select2) return;
    var commonOpts = {
      width: '100%',
      minimumResultsForSearch: 8
    };
    if ($filterBuilder.length && !$filterBuilder.data('select2')) {
      $filterBuilder.select2(commonOpts);
    }
    if ($filterPriority.length && !$filterPriority.data('select2')) {
      $filterPriority.select2(commonOpts);
    }
  }

  initFilterSelect2();

  var csrfToken =
    document.querySelector('meta[name="csrf-token"]') &&
    document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function getCsrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');
    return (meta && meta.getAttribute('content')) || csrfToken || '';
  }

  function normalizeUpdateUrl(raw) {
    var url = String(raw || '').trim();
    if (!url) return '';
    try {
      // APP_URL host can differ from the browser host (e.g. jms.luntian.local vs 127.0.0.1).
      // Always post to the current origin using path only.
      if (/^https?:\/\//i.test(url)) {
        var parsed = new URL(url, window.location.href);
        return parsed.pathname + parsed.search;
      }
    } catch (e) {}
    return url;
  }

  function getRowUpdateUrl($row) {
    if (!$row || !$row.length) return '';
    return normalizeUpdateUrl($row.attr('data-update-url') || $row.data('updateUrl') || '');
  }

  function postJobUpdate(updateUrl, fields) {
    var token = getCsrfToken();
    var data = Object.assign({ _token: token, _method: 'PUT' }, fields || {});
    return $.ajax({
      url: normalizeUpdateUrl(updateUrl),
      method: 'POST',
      data: data,
      headers: {
        'X-CSRF-TOKEN': token,
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
  }

  function toastMsg(msg) {
    if (window.showSuccessToast) window.showSuccessToast(msg);
    else window.alert(msg);
  }

  function recalcStatusSummary() {
    var $table = getLbsTable();
    if (!$table.length) return;
    var $rows = $table
      .find('tbody tr')
      .not('.lbs-row-detail')
      .filter(function () {
        return $(this).css('display') !== 'none';
      });
    var total = $rows.length;
    var allocated = 0;
    var forReview = 0;
    var overdue = 0;
    $rows.each(function () {
      var txt = $.trim($(this).find('td[data-label="Status"]').text()).toLowerCase();
      if (txt === 'allocated') allocated++;
      if (txt === 'for review') forReview++;
      var overdueAttr = $(this).find('.lbs-td-due').attr('data-overdue');
      if (overdueAttr === '1') overdue++;
    });
    $('[data-lbs-count="total"]').text(total);
    $('[data-lbs-count="allocated"]').text(allocated);
    $('[data-lbs-count="for-review"]').text(forReview);
    $('[data-lbs-count="overdue"]').text(overdue);
  }

  function applyTableFilters() {
    var $table = getLbsTable();
    if (!$table.length) return;
    var $tbody = $table.find('tbody');
    if (!$tbody.length) return;

    var q = ($search.length ? $.trim($search.val()) : '' || '').toLowerCase();
    var filterDate = ($filterDate.length ? String($filterDate.val() || '').trim() : '');
    var filterBuilder = ($filterBuilder.length ? String($filterBuilder.val() || '').trim().toLowerCase() : '');
    var filterPriority = ($filterPriority.length ? String($filterPriority.val() || '').trim().toLowerCase() : '');

    var $rows = $tbody.find('tr.lbs-data-row').not('.lbs-filter-empty');
    var visible = 0;
    $rows.each(function () {
      var $row = $(this);
      var text = ($row.text() || '').toLowerCase();
      var rowDate = String($row.attr('data-log-date-key') || '').trim();
      var rowBuilder = String($row.attr('data-builder') || '').trim().toLowerCase();
      var rowPriority = String($row.attr('data-priority') || '').trim().toLowerCase();

      var matchSearch = !q || text.indexOf(q) !== -1;
      var matchDate = !filterDate || rowDate === filterDate;
      var matchBuilder = !filterBuilder || rowBuilder === filterBuilder;
      var matchPriority = !filterPriority || rowPriority === filterPriority;
      var match = matchSearch && matchDate && matchBuilder && matchPriority;

      $row.attr('data-job-filter-match', match ? '1' : '0');
      if (match) visible++;
      var $next = $row.next('.lbs-row-detail');
      if ($next.length && !match) {
        $next.hide();
      }
    });

    $table.attr('data-job-page', '1');
    if (window.JobListPagination && typeof window.JobListPagination.refresh === 'function') {
      window.JobListPagination.refresh($table[0]);
    } else {
      $rows.each(function () {
        var $row = $(this);
        var match = $row.attr('data-job-filter-match') !== '0';
        $row.toggle(match);
      });
    }

    var colCount = $table.find('thead th').length || 13;
    var $empty = $tbody.find('.lbs-filter-empty');
    if ($rows.length && visible === 0) {
      if (!$empty.length) {
        $empty = $(
          '<tr class="lbs-filter-empty"><td class="border-b border-slate-200 px-4 py-6 text-center text-slate-400 dark:border-slate-700 dark:text-slate-400" colspan="' +
            colCount +
            '">No jobs match your search or filters.</td></tr>'
        );
        $tbody.append($empty);
      } else {
        $empty.find('td[colspan]').attr('colspan', colCount);
      }
      $empty.show();
    } else if ($empty.length) {
      $empty.hide();
    }

    recalcStatusSummary();
  }

  function resetFilterSelect($el) {
    if (!$el || !$el.length) return;
    $el.val('');
    if ($el.data('select2')) {
      $el.trigger('change.select2');
    }
  }

  function bindTableFilters() {
    if (!getLbsTable().length) return;

    if ($search.length) {
      $search.off('.lbsFilter').on('input.lbsFilter keyup.lbsFilter search.lbsFilter', applyTableFilters);
    }
    if ($filterDate.length) {
      $filterDate.off('.lbsFilter').on('change.lbsFilter input.lbsFilter', applyTableFilters);
    }

    var selectEvents = 'change.lbsFilter select2:select.lbsFilter select2:clear.lbsFilter select2:unselect.lbsFilter';
    if ($filterBuilder.length) {
      $filterBuilder.off('.lbsFilter').on(selectEvents, applyTableFilters);
    }
    if ($filterPriority.length) {
      $filterPriority.off('.lbsFilter').on(selectEvents, applyTableFilters);
    }
    if ($filterReset.length) {
      $filterReset.off('.lbsFilter').on('click.lbsFilter', function () {
        if ($search.length) $search.val('');
        if ($filterDate.length) $filterDate.val('');
        resetFilterSelect($filterBuilder);
        resetFilterSelect($filterPriority);
        applyTableFilters();
      });
    }

    applyTableFilters();
  }

  initFilterSelect2();
  bindTableFilters();

  function bindLbsMainTableInteractions() {
    var $table = getLbsTable();
    if (!$table.length) return;

    $table
      .find('[data-expand-row]')
      .off('click.lbsMainExpand')
      .on('click.lbsMainExpand', function (e) {
        e.stopPropagation();
        var $row = $(this).closest('tr');
        var $next = $row.next('.lbs-row-detail');
        if (!$next.length) return;
        var open = $next.prop('hidden');
        $next.prop('hidden', !open);
        $(this)
          .attr('aria-expanded', open)
          .attr('title', open ? 'Hide details' : 'View full row details below');
      });

    if (typeof w.initTableSort === 'function') {
      w.initTableSort($table);
    } else if (typeof w.initAllTableSorts === 'function') {
      w.initAllTableSorts();
    }
  }

  bindLbsMainTableInteractions();

  if ($lbsListRefreshBtn.length && $lbsListTablesInner.length && $lbsListTablesRoot.length) {
    var refreshUrl = String($lbsListTablesRoot.data('refresh-url') || '').trim();
    if (refreshUrl) {
      $lbsListRefreshBtn.on('click', function () {
        if ($lbsListRefreshBtn.prop('disabled')) return;
        var icon = $lbsListRefreshBtn.find('.lbs-list-refresh-icon');
        $lbsListRefreshBtn.prop('disabled', true).attr('aria-busy', 'true');
        if (icon.length) icon.addClass('animate-spin');

        fetch(refreshUrl, {
          credentials: 'same-origin',
          headers: {
            Accept: 'text/html',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
          .then(function (res) {
            if (!res.ok) throw new Error('refresh failed');
            return res.text();
          })
          .then(function (html) {
            $lbsListTablesInner.html(html);
            bindLbsMainTableInteractions();
            if (window.JobListPagination && typeof window.JobListPagination.refreshAll === 'function') {
              window.JobListPagination.refreshAll($lbsListTablesInner[0]);
            }
            applyTableFilters();
          })
          .catch(function () {
            var msg = 'Could not refresh the table. Try again or reload the page.';
            if (window.showSuccessToast) window.showSuccessToast(msg);
            else window.alert(msg);
          })
          .finally(function () {
            $lbsListRefreshBtn.prop('disabled', false).attr('aria-busy', 'false');
            if (icon.length) icon.removeClass('animate-spin');
          });
      });
    }
  }

  function closeAllStatusMenus() {
    $('.lbs-status-menu').prop('hidden', true);
    $('[data-status-trigger]').attr('aria-expanded', 'false');
  }

  function submitAssignmentChange($select, role, val, prevVal) {
    var $wrap = $select.closest('[data-initials-wrap]');
    var $row = $wrap.closest('tr.lbs-data-row');
    if (!$row.length) $row = $select.closest('tr.lbs-data-row');
    var updateUrl = getRowUpdateUrl($row);
    var token = getCsrfToken();
    var $detail = $row.next('.lbs-row-detail');
    var selector = role === 'staff' ? '.lbs-detail-staff-badge' : (role === 'checker' ? '.lbs-detail-checker-badge' : null);

    if ($detail.length && selector) {
      $detail.find(selector).text(val || '--');
    }

    if (!updateUrl) {
      toastMsg('Missing update URL for this row.');
      $select.val(prevVal);
      return;
    }
    if (!token) {
      toastMsg('Missing CSRF token. Reload the page.');
      $select.val(prevVal);
      return;
    }

    $select.prop('disabled', true);

    var fields = {};
    if (role === 'staff') fields.staff_id = val;
    else if (role === 'stage') fields.stage = val;
    else fields.checker_id = val;

    postJobUpdate(updateUrl, fields)
      .done(function (res) {
        $select.attr('data-prev', val);
        var msg = (res && res.message) || (role === 'stage' ? 'Stage updated successfully.' : 'Staff/Checker updated successfully.');
        toastMsg(msg);
        setTimeout(function () {
          window.location.reload();
        }, 800);
      })
      .fail(function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to update.';
        toastMsg(msg);
        $select.val(prevVal);
        if ($detail.length && selector) {
          $detail.find(selector).text(prevVal || '--');
        }
      })
      .always(function () {
        $select.prop('disabled', false);
      });
  }

  // Native <select> for Staff / Checker (reliable in overflow table cells).
  // NOTE: primary auto-save is handled by job-list-autosave.js (capture-phase).
  // These jQuery handlers remain as a secondary path for older pages.

  $(document).on('change.lbsInitialsSelect', '[data-initials-select]', function () {
    // no-op if vanilla autosave already handled it via data-prev update mid-flight
  });

  $(document).on('change.lbsPrioritySelect', '[data-priority-select]', function () {
    // handled by job-list-autosave.js
  });

  function closeAllInitialsMenus() {
    // Kept for status-menu coordination / legacy button menus.
    $('.lbs-initials-menu').prop('hidden', true).attr('hidden', 'hidden').css({ display: 'none' });
    $('[data-initials-trigger]').attr('aria-expanded', 'false');
  }

  $(document).on('click', '#lbsTable [data-status-trigger], #efficient_livingTable [data-status-trigger], #luntianTable [data-status-trigger]', function (e) {
    e.stopPropagation();
    e.preventDefault();
    var $trigger = $(this);
    var $wrap = $trigger.closest('[data-status-wrap]');
    var $menu = $wrap.find('.lbs-status-menu');
    if (!$menu.length) return;

    if (!$menu.prop('hidden')) {
      $menu.prop('hidden', true);
      $trigger.attr('aria-expanded', 'false');
      return;
    }
    closeAllStatusMenus();
    closeAllInitialsMenus();
    var rect = this.getBoundingClientRect();
    $menu.css({
      position: 'fixed',
      top: rect.bottom + 4 + 'px',
      left: rect.left + 'px',
      minWidth: Math.max(rect.width, 90) + 'px',
      display: 'flex',
      visibility: 'visible'
    });
    $menu.prop('hidden', false).removeAttr('hidden');
    $trigger.attr('aria-expanded', 'true');
  });

  var statusTableSelector = '#lbsTable .lbs-status-option, #efficient_livingTable .lbs-status-option, #luntianTable .lbs-status-option';

  function resolveAppBasePath() {
    var path = String(window.location.pathname || '');
    var idx = path.indexOf('/dashboard/');
    return idx >= 0 ? path.slice(0, idx) : '';
  }

  function resolveStatusRedirectUrl(statusValue) {
    var normalized = String(statusValue || '').toLowerCase().trim();
    var pathname = String(window.location.pathname || '').toLowerCase();
    var basePath = resolveAppBasePath();
    var modulePrefix = pathname.indexOf('/dashboard/efficient-living/') !== -1
      ? '/dashboard/efficient-living'
      : '/dashboard/lbs';

    if (normalized === 'completed') {
      return basePath + modulePrefix + '/completed';
    }
    if (normalized === 'for review') {
      return basePath + modulePrefix + '/review';
    }
    if (normalized === 'for email confirmation') {
      return basePath + modulePrefix + '/mailbox';
    }
    if (normalized === 'archived') {
      return basePath + modulePrefix + '/trash';
    }

    return null;
  }

  function submitStatusChange($el, val, prevText, updateUrl, $row) {
    var currentUnits = 0;
    if ($row.length && $row.data('job-units') !== undefined) {
      currentUnits = parseInt($row.data('job-units'), 10);
      if (isNaN(currentUnits) || currentUnits < 0) currentUnits = 0;
    }

    if (!window.LuntianFecUnitsModal || !window.LuntianFecUnitsModal.promptIfNeeded) {
      if (window.showSuccessToast) window.showSuccessToast('Status UI error: reload the page.');
      if ($el.is('select')) $el.val(prevText);
      return;
    }

    var $detail = $row.next('.lbs-row-detail');
    var $badge = $detail.find('.lbs-detail-status-badge');
    var isSelect = $el.is('select');

    window.LuntianFecUnitsModal.promptIfNeeded({
      currentUnits: currentUnits,
      statusValue: val
    })
      .then(function (fecResult) {
        var unitsToSend = fecResult && fecResult.unitsToSend != null ? fecResult.unitsToSend : null;
        if (isSelect) $el.prop('disabled', true);
        else $el.addClass('lbs-status-updating');

        if ($detail.length && $badge.length) {
          $badge.text(val).removeAttr('style');
        }
        recalcStatusSummary();

        if (!updateUrl) {
          if (window.showSuccessToast) window.showSuccessToast('Missing update URL for this row.');
          if (isSelect) $el.val(prevText).prop('disabled', false);
          else $el.removeClass('lbs-status-updating').text(prevText);
          return;
        }
        if (!getCsrfToken()) {
          if (window.showSuccessToast) window.showSuccessToast('Missing CSRF token. Reload the page.');
          if (isSelect) $el.val(prevText).prop('disabled', false);
          else $el.removeClass('lbs-status-updating').text(prevText);
          return;
        }

        var fields = { job_status: val };
        if (unitsToSend !== null) fields.units = String(unitsToSend);

        postJobUpdate(updateUrl, fields)
          .done(function (res) {
            if (isSelect) $el.attr('data-prev', val);
            else $el.removeClass('lbs-status-updating').addClass('lbs-status-success').text(val);
            var msg = (res && res.message) || 'Status updated to ' + val + '.';
            if (window.showSuccessToast) window.showSuccessToast(msg);
            var redirectUrl = resolveStatusRedirectUrl(val);
            setTimeout(function () {
              if (!isSelect) $el.removeClass('lbs-status-success');
              if (redirectUrl) {
                window.location.href = redirectUrl;
                return;
              }
              window.location.reload();
            }, 1500);
          })
          .fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to update status.';
            if (window.showSuccessToast) window.showSuccessToast(msg);
            if (isSelect) $el.val(prevText);
            else $el.removeClass('lbs-status-updating').text(prevText);
            if ($detail.length && $badge.length) {
              $badge.text(prevText);
            }
            recalcStatusSummary();
          })
          .always(function () {
            if (isSelect) $el.prop('disabled', false);
          });
      })
      .catch(function () {
        if (isSelect) $el.val(prevText);
      });
  }

  $(document).on('change.lbsStatusSelect', '[data-status-select]', function () {
    // handled by job-list-autosave.js (capture phase)
  });

  $(document).on('click', statusTableSelector, function (e) {
    e.stopPropagation();
    var $option = $(this);
    var $wrap = $option.closest('[data-status-wrap]');
    var $trigger = $wrap.find('[data-status-trigger]');
    var $menu = $wrap.find('.lbs-status-menu');
    var val = $option.data('status-value');
    var $row = $wrap.closest('tr.lbs-data-row');
    var updateUrl = getRowUpdateUrl($row);
    var prevText = $trigger.text();
    $menu.prop('hidden', true);
    $trigger.attr('aria-expanded', 'false');
    submitStatusChange($trigger, val, prevText, updateUrl, $row);
  });

  $(document).on('click', function (e) {
    if ($(e.target).closest('[data-status-trigger], .lbs-status-menu, [data-initials-trigger], .lbs-initials-menu, [data-initials-select], [data-priority-select], [data-status-select]').length) return;
    closeAllStatusMenus();
    closeAllInitialsMenus();
  });

  applyTableFilters();
});
