$(function () {
  var $table = $('#lbsTable');
  var $search = $('#lbsSearch');
  var csrfToken = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function recalcStatusSummary() {
    if (!$table.length) return;
    var $rows = $table.find('tbody tr').not('.lbs-row-detail').filter(function () {
      return $(this).css('display') !== 'none';
    });
    var total = $rows.length;
    var allocated = 0;
    var forReview = 0;
    var overdue = 0;
    $rows.each(function () {
      var txt = $.trim($(this).find('.lbs-status-trigger').text()).toLowerCase();
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

  if ($search.length && $table.length) {
    var $tbody = $table.find('tbody');
    $search.on('input', function () {
      var q = ($.trim($(this).val()) || '').toLowerCase();
      if (!$tbody.length) return;
      var $rows = $tbody.find('tr').not('.lbs-row-detail');
      $rows.each(function () {
        var $row = $(this);
        var text = ($row.text() || '').toLowerCase();
        var match = !q || text.indexOf(q) !== -1;
        $row.toggle(match);
        var $next = $row.next('.lbs-row-detail');
        if ($next.length) $next.toggle(match);
      });
      recalcStatusSummary();
    });
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
      if (role === 'staff') payload.append('staff_id', val); else payload.append('checker_id', val);

      $.ajax({
        url: updateUrl,
        method: 'PUT',
        data: payload.toString(),
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' }
      }).done(function (res) {
        var msg = (res && res.message) || 'Staff/Checker updated successfully.';
        if (window.showSuccessToast) window.showSuccessToast(msg);
        setTimeout(function () { window.location.reload(); }, 800);
      }).fail(function (xhr) {
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

  $(document).on('click', '[data-status-trigger]', function (e) {
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
      top: (rect.bottom + 4) + 'px',
      left: rect.left + 'px',
      minWidth: Math.max(rect.width, 90) + 'px',
      display: 'flex',
      visibility: 'visible'
    });
    $menu.prop('hidden', false).removeAttr('hidden');
    $trigger.attr('aria-expanded', 'true');
  });

  $(document).on('click', '[data-status-wrap] .lbs-status-option', function (e) {
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

    $.ajax({
      url: updateUrl,
      method: 'PUT',
      data: payload.toString(),
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' }
    }).done(function (res) {
      var msg = (res && res.message) || 'Status updated to ' + val + '.';
      if (window.showSuccessToast) window.showSuccessToast(msg);
      setTimeout(function () { window.location.reload(); }, 1500);
    }).fail(function (xhr) {
      var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to update status.';
      if (window.showSuccessToast) window.showSuccessToast(msg);
      $trigger.removeClass(allClasses.join(' ')).addClass(prevClass).text(prevText);
      if ($detail.length && $badge.length) {
        $badge.removeClass(allClasses.join(' ')).addClass(prevClass).text(prevText);
      }
      recalcStatusSummary();
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
      var rows = $tbody.find('tr').not('.lbs-row-detail').get();
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

  recalcStatusSummary();
});

