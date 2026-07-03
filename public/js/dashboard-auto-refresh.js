(function () {
  var REFRESH_SEC = 5;
  var countEl = document.getElementById('dashboard-refresh-count');
  var widget = document.getElementById('dashboard-refresh-widget');
  var labelEl = widget && widget.querySelector('[data-refresh-label]');
  var root = document.getElementById('dashboard-root');

  if (!countEl || !widget || !root) {
    return;
  }

  function resolveApiUrl(raw, fallback) {
    var fb = fallback || '/dashboard/stats';
    var value = String(raw || fb).trim() || fb;
    try {
      if (value.charAt(0) === '/') {
        return value.replace(/\/$/, '') || fb;
      }
      var parsed = new URL(value, window.location.origin);
      return (parsed.pathname + parsed.search).replace(/\/$/, '') || fb;
    } catch (e) {
      return fb;
    }
  }

  var statsUrl = resolveApiUrl(root.dataset.statsApiBase, '/dashboard/stats');
  var branchFilter = (root.dataset.dashboardBranchFilter || '').trim();
  var secondsLeft = REFRESH_SEC;
  var fetching = false;

  function normalizeBranchKey(s) {
    return String(s || '').trim().toLowerCase().replace(/\s+/g, ' ');
  }

  function branchMatches(label, filter) {
    var a = normalizeBranchKey(label);
    var b = normalizeBranchKey(filter);
    if (!b) {
      return true;
    }
    if (a === b) {
      return true;
    }
    var aCompact = a.replace(/\s+/g, '');
    var bCompact = b.replace(/\s+/g, '');
    if (aCompact === bCompact) {
      return true;
    }
    return a.indexOf(b) !== -1 || b.indexOf(a) !== -1;
  }

  function bucketView(bucket) {
    var rows = bucket || {};
    if (!branchFilter) {
      var sum = 0;
      Object.keys(rows).forEach(function (branch) {
        sum += Number(rows[branch]) || 0;
      });
      return { sum: sum, rows: rows };
    }

    var hitKey = null;
    Object.keys(rows).forEach(function (branch) {
      if (branchMatches(branch, branchFilter)) {
        hitKey = branch;
      }
    });

    if (hitKey) {
      return { sum: Number(rows[hitKey]) || 0, rows: {} };
    }

    return { sum: 0, rows: {} };
  }

  function pulseCount() {
    countEl.classList.remove('dashboard-refresh-widget__count--tick');
    void countEl.offsetWidth;
    countEl.classList.add('dashboard-refresh-widget__count--tick');
  }

  function setCount(n) {
    countEl.textContent = String(n);
    pulseCount();
  }

  function setRefreshing(active) {
    widget.classList.toggle('dashboard-refresh-widget--active', active);
    if (labelEl) {
      labelEl.textContent = active ? 'Refreshing…' : 'Refresh in';
    }
  }

  function updateDom(stats) {
    if (!stats || typeof stats !== 'object') {
      return;
    }

    ['total', 'completed', 'processing', 'pending'].forEach(function (key) {
      var view = bucketView(stats[key]);
      var sumEl = document.querySelector('[data-dashboard-stat="' + key + '"][data-dashboard-scope="sum"]');
      if (sumEl) {
        sumEl.textContent = String(view.sum);
      }

      Object.keys(view.rows).forEach(function (branch) {
        var rowEl = document.querySelector(
          '[data-dashboard-stat="' + key + '"][data-dashboard-branch="' + branch + '"]'
        );
        if (rowEl) {
          rowEl.textContent = String(view.rows[branch]);
        }
      });

      if (branchFilter) {
        document.querySelectorAll('[data-dashboard-stat="' + key + '"][data-dashboard-branch]').forEach(function (el) {
          var branch = el.getAttribute('data-dashboard-branch') || '';
          if (branchMatches(branch, branchFilter)) {
            el.textContent = String(view.sum);
          }
        });
      }
    });

    var jsonEl = document.getElementById('dashboard-stats-json');
    if (jsonEl) {
      jsonEl.textContent = JSON.stringify(stats);
    }

    document.dispatchEvent(new CustomEvent('dashboard:statsUpdated', { detail: stats }));
  }

  function fetchStats() {
    if (fetching) {
      return;
    }

    fetching = true;
    setRefreshing(true);
    countEl.textContent = '…';

    fetch(statsUrl, {
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })
      .then(function (res) {
        var ct = res.headers.get('content-type') || '';
        if (!res.ok || ct.indexOf('application/json') === -1) {
          throw new Error('stats fetch failed');
        }
        return res.json();
      })
      .then(function (data) {
        updateDom(data);
      })
      .catch(function () {
        /* keep last values */
      })
      .finally(function () {
        fetching = false;
        setRefreshing(false);
        secondsLeft = REFRESH_SEC;
        setCount(secondsLeft);
      });
  }

  setCount(secondsLeft);

  window.setInterval(function () {
    if (fetching) {
      return;
    }

    secondsLeft -= 1;
    if (secondsLeft <= 0) {
      fetchStats();
      return;
    }

    setCount(secondsLeft);
  }, 1000);

  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible' && !fetching) {
      fetchStats();
    }
  });
})();
