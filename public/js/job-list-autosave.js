/**
 * Auto-save for job list Priority / Staff / Checker / Status selects.
 * Vanilla JS only — does not depend on jQuery ready timing.
 * Prompts for Units before moving status to For Email Confirmation.
 */
(function (w, d) {
  'use strict';

  var saving = false;
  var FEC_MODAL_ID = 'luntian-fec-units-modal';

  function csrfToken() {
    var meta = d.querySelector('meta[name="csrf-token"]');
    return meta ? String(meta.getAttribute('content') || '') : '';
  }

  function toast(msg) {
    if (typeof w.showSuccessToast === 'function') w.showSuccessToast(msg);
    else w.alert(msg);
  }

  function normalizeUrl(raw) {
    var url = String(raw || '').trim();
    if (!url) return '';
    try {
      if (/^https?:\/\//i.test(url)) {
        var u = new URL(url, w.location.href);
        return u.pathname + u.search;
      }
    } catch (e) {}
    return url;
  }

  function rowOf(el) {
    return el.closest('tr.lbs-data-row, tr.efficient_living-data-row, tr.luntian-data-row');
  }

  function updateUrlFor(el) {
    var row = rowOf(el);
    if (!row) return '';
    return normalizeUrl(row.getAttribute('data-update-url') || '');
  }

  function isForEmailConfirmation(val) {
    return String(val || '').toLowerCase().trim() === 'for email confirmation';
  }

  function parseCurrentUnits(v) {
    var n = parseInt(v, 10);
    return isNaN(n) || n < 0 ? 0 : n;
  }

  function ensureFecModalApi() {
    if (w.LuntianFecUnitsModal && typeof w.LuntianFecUnitsModal.promptIfNeeded === 'function') {
      return w.LuntianFecUnitsModal;
    }

    function ensureModal() {
      var el = d.getElementById(FEC_MODAL_ID);
      if (el) return el;
      el = d.createElement('div');
      el.id = FEC_MODAL_ID;
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
      d.body.appendChild(el);
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
      promptIfNeeded: function (opts) {
        return new Promise(function (resolve, reject) {
          var sv = opts && opts.statusValue;
          var cu = parseCurrentUnits(opts && opts.currentUnits);
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
            d.removeEventListener('keydown', onEsc);
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
          modal.addEventListener('click', onBackdrop);
          d.addEventListener('keydown', onEsc);
        });
      },
    };

    return w.LuntianFecUnitsModal;
  }

  function postUpdate(url, fields) {
    var token = csrfToken();
    var body = new URLSearchParams();
    body.set('_token', token);
    body.set('_method', 'PUT');
    Object.keys(fields || {}).forEach(function (k) {
      body.set(k, fields[k] == null ? '' : String(fields[k]));
    });

    return fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        Accept: 'application/json',
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: body.toString(),
    }).then(function (res) {
      return res
        .json()
        .catch(function () {
          return {};
        })
        .then(function (data) {
          return { ok: res.ok, status: res.status, data: data };
        });
    });
  }

  function applyPriorityColor(select, value) {
    if (!select) return;
    var color = '';
    var opts = select.options || [];
    for (var i = 0; i < opts.length; i++) {
      if (String(opts[i].value) === String(value)) {
        color = String(opts[i].getAttribute('data-color') || '').trim();
        break;
      }
    }
    if (!color) {
      try {
        var map = JSON.parse(select.getAttribute('data-priority-colors') || '{}');
        if (map && typeof map === 'object' && map[value]) color = String(map[value]).trim();
      } catch (e) {}
    }
    if (color) select.style.backgroundColor = color;
    else select.style.removeProperty('background-color');
  }

  function commitUpdate(select, url, fields, val, prev, okMsg, isPriority) {
    saving = true;
    select.disabled = true;

    postUpdate(url, fields)
      .then(function (result) {
        if (!result.ok) {
          var err =
            (result.data && (result.data.message || result.data.error)) ||
            'Failed to save (' + result.status + ').';
          toast(err);
          select.value = prev;
          if (isPriority) applyPriorityColor(select, prev);
          return;
        }
        select.setAttribute('data-prev', val);
        if (fields.units != null) {
          var row = rowOf(select);
          if (row) row.setAttribute('data-job-units', String(fields.units));
        }
        toast((result.data && result.data.message) || okMsg);
        setTimeout(function () {
          w.location.reload();
        }, 700);
      })
      .catch(function () {
        toast('Failed to save. Check your connection.');
        select.value = prev;
        if (isPriority) applyPriorityColor(select, prev);
      })
      .finally(function () {
        saving = false;
        select.disabled = false;
      });
  }

  function handleSelectChange(select) {
    if (!select || saving) return;

    var isStaff = select.hasAttribute('data-initials-select');
    var isPriority = select.hasAttribute('data-priority-select');
    var isStatus = select.hasAttribute('data-status-select');
    if (!isStaff && !isPriority && !isStatus) return;

    var val = String(select.value || '');
    var prev = String(select.getAttribute('data-prev') || '');
    if (val === prev) return;

    if (isPriority) applyPriorityColor(select, val);

    var url = updateUrlFor(select);
    var token = csrfToken();
    if (!url) {
      toast('Missing update URL for this row.');
      select.value = prev;
      if (isPriority) applyPriorityColor(select, prev);
      return;
    }
    if (!token) {
      toast('Missing CSRF token. Reload the page.');
      select.value = prev;
      if (isPriority) applyPriorityColor(select, prev);
      return;
    }

    var fields = {};
    var okMsg = 'Updated successfully.';
    if (isStaff) {
      var role = String(select.getAttribute('data-role') || '').toLowerCase();
      if (role === 'staff') fields.staff_id = val;
      else if (role === 'stage') fields.stage = val;
      else fields.checker_id = val;
      okMsg = 'Staff/Checker updated successfully.';
    } else if (isPriority) {
      fields.priority = val;
      okMsg = 'Priority updated successfully.';
    } else if (isStatus) {
      fields.job_status = val;
      okMsg = 'Status updated to ' + val + '.';
    }

    if (isStatus && isForEmailConfirmation(val)) {
      var row = rowOf(select);
      var currentUnits = row ? parseCurrentUnits(row.getAttribute('data-job-units')) : 0;
      var fecApi = ensureFecModalApi();
      fecApi
        .promptIfNeeded({ currentUnits: currentUnits, statusValue: val })
        .then(function (fecResult) {
          if (fecResult && fecResult.unitsToSend != null) {
            fields.units = String(fecResult.unitsToSend);
          }
          commitUpdate(select, url, fields, val, prev, okMsg, false);
        })
        .catch(function () {
          select.value = prev;
        });
      return;
    }

    commitUpdate(select, url, fields, val, prev, okMsg, isPriority);
  }

  d.addEventListener(
    'change',
    function (e) {
      var t = e.target;
      if (!t || !t.closest) return;
      if (
        t.matches &&
        t.matches('[data-initials-select], [data-priority-select], [data-status-select]')
      ) {
        e.stopPropagation();
        handleSelectChange(t);
      }
    },
    true
  );
})(window, document);
