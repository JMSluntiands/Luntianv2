/**
 * Auto-save for job list Priority / Staff / Checker / Status selects.
 * Vanilla JS only — does not depend on jQuery ready timing.
 */
(function (w, d) {
  'use strict';

  var saving = false;

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

  function handleSelectChange(select) {
    if (!select || saving) return;

    var isStaff = select.hasAttribute('data-initials-select');
    var isPriority = select.hasAttribute('data-priority-select');
    var isStatus = select.hasAttribute('data-status-select');
    if (!isStaff && !isPriority && !isStatus) return;

    var val = String(select.value || '');
    var prev = String(select.getAttribute('data-prev') || '');
    if (val === prev) return;

    var url = updateUrlFor(select);
    var token = csrfToken();
    if (!url) {
      toast('Missing update URL for this row.');
      select.value = prev;
      return;
    }
    if (!token) {
      toast('Missing CSRF token. Reload the page.');
      select.value = prev;
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
          return;
        }
        select.setAttribute('data-prev', val);
        toast((result.data && result.data.message) || okMsg);
        setTimeout(function () {
          w.location.reload();
        }, 700);
      })
      .catch(function () {
        toast('Failed to save. Check your connection.');
        select.value = prev;
      })
      .finally(function () {
        saving = false;
        select.disabled = false;
      });
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
