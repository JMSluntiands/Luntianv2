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
  var $table = $('#lbsTable');
  var $search = $('#lbsSearch');
  var $filterDate = $('#lbsFilterDate');
  var $filterBuilder = $('#lbsFilterBuilder');
  var $filterPriority = $('#lbsFilterPriority');
  var $filterReset = $('#lbsFilterReset');
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

  function recalcStatusSummary() {
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
    if (!$table.length) return;
    var $tbody = $table.find('tbody');
    if (!$tbody.length) return;

    var q = ($search.length ? $.trim($search.val()) : '' || '').toLowerCase();
    var filterDate = ($filterDate.length ? String($filterDate.val() || '').trim() : '');
    var filterBuilder = ($filterBuilder.length ? String($filterBuilder.val() || '').trim().toLowerCase() : '');
    var filterPriority = ($filterPriority.length ? String($filterPriority.val() || '').trim().toLowerCase() : '');

    var $rows = $tbody.find('tr').not('.lbs-row-detail');
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

      $row.toggle(match);
      var $next = $row.next('.lbs-row-detail');
      if ($next.length) {
        if (match && !$next.prop('hidden')) {
          $next.show();
        } else if (!match) {
          $next.hide();
        }
      }
    });
    recalcStatusSummary();
  }

  if ($table.length) {
    if ($search.length) $search.on('input', applyTableFilters);
    if ($filterDate.length) $filterDate.on('change', applyTableFilters);
    if ($filterBuilder.length) $filterBuilder.on('change', applyTableFilters);
    if ($filterPriority.length) $filterPriority.on('change', applyTableFilters);
    if ($filterReset.length) {
      $filterReset.on('click', function () {
        if ($search.length) $search.val('');
        if ($filterDate.length) $filterDate.val('');
        if ($filterBuilder.length) $filterBuilder.val('');
        if ($filterPriority.length) $filterPriority.val('');
        applyTableFilters();
      });
    }
  }

  $table.find('[data-expand-row]').on('click', function (e) {
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

  function closeAllStatusMenus() {
    $('.lbs-status-menu').prop('hidden', true);
    $('[data-status-trigger]').attr('aria-expanded', 'false');
  }

  function closeAllInitialsMenus() {
    $('.lbs-initials-menu').prop('hidden', true);
    $('[data-initials-trigger]').attr('aria-expanded', 'false');
  }

  $('[data-initials-wrap]').each(function () {
    var $wrap = $(this);
    var $trigger = $wrap.find('[data-initials-trigger]');
    var $menu = $wrap.find('.lbs-initials-menu');
    var role = $wrap.data('role');
    if (!$trigger.length || !$menu.length) return;

    $trigger.on('click', function (e) {
      e.stopPropagation();
      if (!$menu.prop('hidden')) {
        $menu.prop('hidden', true);
        $trigger.attr('aria-expanded', 'false');
        return;
      }
      closeAllStatusMenus();
      closeAllInitialsMenus();
      var rect = this.getBoundingClientRect();
      $menu.css({
        top: rect.bottom + 4,
        left: rect.left,
        minWidth: Math.max(rect.width, 70)
      });
      $menu.prop('hidden', false);
      $trigger.attr('aria-expanded', 'true');
    });

    $menu.find('.lbs-initials-option').on('click', function (e) {
      e.stopPropagation();
      var val = $(this).data('value');
      var $row = $wrap.closest('tr.lbs-data-row');
      var updateUrl = $row.length && $row.data('update-url');
      var prevVal = $trigger.text();
      $menu.prop('hidden', true);
      $trigger.attr('aria-expanded', 'false');

      if (!updateUrl || !csrfToken) {
        $trigger.text(val);
        var $detail = $row.next('.lbs-row-detail');
        if ($detail.length) {
          var selector = role === 'staff' ? '.lbs-detail-staff-badge' : '.lbs-detail-checker-badge';
          $detail.find(selector).text(val);
        }
        return;
      }

      $trigger.text(val);
      var $detail = $row.next('.lbs-row-detail');
      if ($detail.length) {
        var selector = role === 'staff' ? '.lbs-detail-staff-badge' : '.lbs-detail-checker-badge';
        $detail.find(selector).text(val);
      }

      var payload = new URLSearchParams();
      payload.append('_token', csrfToken);
      if (role === 'staff') payload.append('staff_id', val);
      else payload.append('checker_id', val);

      $.ajax({
        url: updateUrl,
        method: 'PUT',
        data: payload.toString(),
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          Accept: 'application/json',
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      })
        .done(function (res) {
          var msg = (res && res.message) || 'Staff/Checker updated successfully.';
          if (window.showSuccessToast) window.showSuccessToast(msg);
          setTimeout(function () {
            window.location.reload();
          }, 800);
        })
        .fail(function (xhr) {
          var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to update.';
          if (window.showSuccessToast) window.showSuccessToast(msg);
          $trigger.text(prevVal);
          if ($detail.length) {
            var sel = role === 'staff' ? '.lbs-detail-staff-badge' : '.lbs-detail-checker-badge';
            $detail.find(sel).text(prevVal);
          }
        });
    });
  });

  $(document).on('click', '#lbsTable [data-status-trigger], #efficient_livingTable [data-status-trigger]', function (e) {
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

  var statusTableSelector = '#lbsTable .lbs-status-option, #efficient_livingTable .lbs-status-option';

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

  $(document).on('click', statusTableSelector, function (e) {
    e.stopPropagation();
    var $option = $(this);
    var $wrap = $option.closest('[data-status-wrap]');
    var $trigger = $wrap.find('[data-status-trigger]');
    var $menu = $wrap.find('.lbs-status-menu');
    var val = $option.data('status-value');
    var $row = $wrap.closest('tr.lbs-data-row');
    var updateUrl = $row.length && $row.data('update-url');
    var prevText = $trigger.text();
    var prevClass = 'lbs-badge-' + String(prevText).toLowerCase().replace(/\s+/g, '-');
    $menu.prop('hidden', true);
    $trigger.attr('aria-expanded', 'false');

    var currentUnits = 0;
    if ($row.length && $row.data('job-units') !== undefined) {
      currentUnits = parseInt($row.data('job-units'), 10);
      if (isNaN(currentUnits) || currentUnits < 0) currentUnits = 0;
    }

    if (!window.LuntianFecUnitsModal || !window.LuntianFecUnitsModal.promptIfNeeded) {
      if (window.showSuccessToast) window.showSuccessToast('Status UI error: reload the page.');
      return;
    }

    window.LuntianFecUnitsModal.promptIfNeeded({
      currentUnits: currentUnits,
      statusValue: val
    })
      .then(function (fecResult) {
        var unitsToSend = fecResult && fecResult.unitsToSend != null ? fecResult.unitsToSend : null;

        $trigger.addClass('lbs-status-updating');

        var badgeClass = 'lbs-badge-' + String(val).toLowerCase().replace(/\s+/g, '-');
        var allClasses = [
          'lbs-badge-pending',
          'lbs-badge-accepted',
          'lbs-badge-allocated',
          'lbs-badge-awaiting-further-information',
          'lbs-badge-completed',
          'lbs-badge-for-email-confirmation',
          'lbs-badge-cancelled',
          'lbs-badge-for-review',
          'lbs-badge-processing',
          'lbs-badge-for-checking',
          'lbs-badge-revised'
        ];
        $trigger.removeClass(allClasses.join(' ')).addClass(badgeClass).text(val).removeAttr('style');
        var $detail = $row.next('.lbs-row-detail');
        var $badge = $detail.find('.lbs-detail-status-badge');
        if ($detail.length && $badge.length) {
          $badge.removeClass(allClasses.join(' ')).addClass(badgeClass).text(val).removeAttr('style');
        }
        recalcStatusSummary();

        if (!updateUrl || !csrfToken) return;

        var payload = new URLSearchParams();
        payload.append('_token', csrfToken);
        payload.append('job_status', val);
        if (unitsToSend !== null) payload.append('units', String(unitsToSend));

        $.ajax({
          url: updateUrl,
          method: 'PUT',
          data: payload.toString(),
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            Accept: 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        })
          .done(function (res) {
            $trigger.removeClass('lbs-status-updating').addClass('lbs-status-success');
            var msg = (res && res.message) || 'Status updated to ' + val + '.';
            if (window.showSuccessToast) window.showSuccessToast(msg);
            var redirectUrl = resolveStatusRedirectUrl(val);
            setTimeout(function () {
              $trigger.removeClass('lbs-status-success');
              if (redirectUrl) {
                window.location.href = redirectUrl;
                return;
              }
              window.location.reload();
            }, 1500);
          })
          .fail(function (xhr) {
            $trigger.removeClass('lbs-status-updating');
            var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to update status.';
            if (window.showSuccessToast) window.showSuccessToast(msg);
            $trigger.removeClass(allClasses.join(' ')).addClass(prevClass).text(prevText);
            if ($detail.length && $badge.length) {
              $badge.removeClass(allClasses.join(' ')).addClass(prevClass).text(prevText);
            }
            recalcStatusSummary();
          });
      })
      .catch(function () {
        /* user cancelled FEC units modal — keep previous status */
      });
  });

  $(document).on('click', function (e) {
    if ($(e.target).closest('[data-status-trigger], .lbs-status-menu').length) return;
    closeAllStatusMenus();
    closeAllInitialsMenus();
  });

  if ($table.length) {
    var $thead = $table.find('thead');
    $thead.on('click', 'th', function (e) {
      var $th = $(e.target).closest('th');
      if (!$th.length || $th.hasClass('lbs-th-action')) return;
      var current = $th.attr('data-sort') || '';
      var next = current === 'asc' ? 'desc' : 'asc';
      $thead.find('th').attr('data-sort', '');
      $th.attr('data-sort', next);
      var colIndex = $th.index();
      var $tbody = $table.find('tbody');
      var rows = $tbody
        .find('tr')
        .not('.lbs-row-detail')
        .get();
      rows.sort(function (a, b) {
        var aCell = a.children[colIndex];
        var bCell = b.children[colIndex];
        var aVal = (aCell && (aCell.getAttribute('data-sort') || aCell.textContent)) || '';
        var bVal = (bCell && (bCell.getAttribute('data-sort') || bCell.textContent)) || '';
        var aNum = parseFloat(aVal);
        var bNum = parseFloat(bVal);
        if (!isNaN(aNum) && !isNaN(bNum)) {
          return next === 'asc' ? aNum - bNum : bNum - aNum;
        }
        if (next === 'asc') {
          return String(aVal).localeCompare(String(bVal), undefined, { numeric: true });
        }
        return String(bVal).localeCompare(String(aVal), undefined, { numeric: true });
      });
      rows.forEach(function (row) {
        var $row = $(row);
        $tbody.append($row);
        var $detail = $row.next('.lbs-row-detail');
        if ($detail.length) $tbody.append($detail);
      });
    });
  }

  applyTableFilters();
});
