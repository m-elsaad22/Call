(function () {
  'use strict';
  var CFG = window.KayanTrackAdmin || {};
  var state = {
    tab: 'overview',
    preset: '7d',
    dateFrom: '',
    dateTo: '',
    charts: {},
    numbers: [],
    numbersAll: [],
    numbersSynced: false,
    numberDetail: null,
    placesByNumber: {},
    numbersPage: 1,
    numbersPerPage: 20,
    numbersSearch: '',
    numbersStatus: '',
    scanPct: 0,
    scanTimer: null,
    seoPage: 1,
    seoPerPage: 20,
    seoSearch: '',
    seoPostType: '',
    seoPostTypes: [],
    seoBulk: null
  };

  function $(sel, ctx) { return (ctx || document).querySelector(sel); }
  function $all(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }
  function isMobile() { return window.innerWidth <= 782; }
  function debounce(fn, delay) {
    var t;
    return function () {
      var ctx = this, args = arguments;
      clearTimeout(t);
      t = setTimeout(function () { fn.apply(ctx, args); }, delay);
    };
  }
  function clampPerPage(v) {
    v = parseInt(v, 10);
    if (isNaN(v) || v < 5) v = 5;
    if (v > 2000) v = 2000;
    return v;
  }
  function syncFilterBar() {
    var bar = $('#kt-filters');
    if (bar) bar.hidden = (state.tab === 'numbers' || state.tab === 'seo');
  }
  function perPageHtml(id, value) {
    return '<div class="kt-per-page">' +
      '<label for="' + id + '">عرض:</label>' +
      '<input type="number" id="' + id + '" class="kt-per-page-input" value="' + esc(value) + '" min="5" max="2000">' +
      '<div class="kt-presets" data-target="' + id + '">' +
      [10, 20, 50, 100, 500].map(function (n) {
        return '<button type="button" class="kt-preset' + (n === value ? ' active' : '') + '" data-val="' + n + '">' + n + '</button>';
      }).join('') +
      '</div></div>';
  }
  function paginationHtml(page, totalPages) {
    totalPages = Math.max(1, totalPages || 1);
    page = Math.max(1, page || 1);
    return '<div class="kt-pag">' +
      '<button type="button" class="kt-btn ghost kt-pag-prev"' + (page <= 1 ? ' disabled' : '') + '>السابق</button>' +
      '<span class="kt-pag-current">' + esc(page) + ' / ' + esc(totalPages) + '</span>' +
      '<button type="button" class="kt-btn ghost kt-pag-next"' + (page >= totalPages ? ' disabled' : '') + '>التالي</button>' +
      '</div>';
  }
  function bindPerPage(id, onChange) {
    var input = $('#' + id);
    if (!input) return;
    function apply() {
      var v = clampPerPage(input.value);
      input.value = v;
      $all('.kt-presets[data-target="' + id + '"] .kt-preset').forEach(function (btn) {
        btn.classList.toggle('active', parseInt(btn.getAttribute('data-val'), 10) === v);
      });
      onChange(v);
    }
    input.addEventListener('change', apply);
    input.addEventListener('keydown', function (e) { if (e.key === 'Enter') apply(); });
    $all('.kt-presets[data-target="' + id + '"] .kt-preset').forEach(function (btn) {
      btn.addEventListener('click', function () {
        input.value = btn.getAttribute('data-val');
        apply();
      });
    });
  }
  function bindPagination(root, page, totalPages, onPage) {
    var prev = $('.kt-pag-prev', root || document);
    var next = $('.kt-pag-next', root || document);
    if (prev) prev.addEventListener('click', function () { if (page > 1) onPage(page - 1); });
    if (next) next.addEventListener('click', function () { if (page < totalPages) onPage(page + 1); });
  }
  function startScanUi(label) {
    var box = $('#kt-scan-progress');
    var fill = $('#kt-scan-fill');
    var status = $('#kt-scan-status');
    if (box) box.hidden = false;
    if (status) status.textContent = label || 'جارٍ الفحص...';
    state.scanPct = 8;
    if (fill) fill.style.width = '8%';
    clearInterval(state.scanTimer);
    state.scanTimer = setInterval(function () {
      state.scanPct = Math.min(90, state.scanPct + (4 + Math.random() * 6));
      if (fill) fill.style.width = state.scanPct + '%';
      if (status) status.textContent = 'جارٍ فحص المقالات والإعدادات... (' + Math.round(state.scanPct) + '%)';
    }, 280);
  }
  function endScanUi(msg) {
    clearInterval(state.scanTimer);
    var fill = $('#kt-scan-fill');
    var status = $('#kt-scan-status');
    var box = $('#kt-scan-progress');
    if (fill) fill.style.width = '100%';
    if (status) status.textContent = msg || 'اكتمل الفحص';
    setTimeout(function () { if (box) box.hidden = true; }, 2200);
  }

  function toast(msg) {
    var el = $('#kt-toast');
    if (!el) return;
    el.textContent = msg;
    el.hidden = false;
    clearTimeout(el._t);
    el._t = setTimeout(function () { el.hidden = true; }, 2800);
  }

  function ajax(action, data) {
    var body = new FormData();
    body.append('action', action);
    body.append('nonce', CFG.nonce || '');
    Object.keys(data || {}).forEach(function (k) { body.append(k, data[k]); });
    body.append('preset', state.preset);
    if (state.preset === 'custom') {
      body.append('date_from', state.dateFrom);
      body.append('date_to', state.dateTo);
    }
    return fetch(CFG.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
      .then(function (r) { return r.json(); });
  }

  function setLoading(html) {
    var c = $('#kt-content');
    if (c) c.innerHTML = html || '<div class="kt-loading"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</div>';
  }

  function destroyCharts() {
    Object.keys(state.charts).forEach(function (k) {
      if (state.charts[k]) { state.charts[k].destroy(); delete state.charts[k]; }
    });
  }

  function bindFilters() {
    $all('.qf-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        $all('.qf-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        state.preset = btn.getAttribute('data-preset') || '7d';
        loadTab();
      });
    });
    var apply = $('#kt-apply-custom');
    if (apply) {
      apply.addEventListener('click', function () {
        state.preset = 'custom';
        state.dateFrom = ($('#kt-date-from') || {}).value || '';
        state.dateTo = ($('#kt-date-to') || {}).value || '';
        $all('.qf-btn').forEach(function (b) { b.classList.remove('active'); });
        loadTab();
      });
    }
  }

  function bindNav() {
    $all('.kt-nav-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        $all('.kt-nav-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        state.tab = btn.getAttribute('data-tab') || 'overview';
        if (state.tab !== 'numbers') state.numberDetail = null;
        loadTab();
      });
    });
  }

  function renderOverview(data) {
    var s = data.stats || {};
    destroyCharts();
    var html = '<div class="stat-row">' +
      statBox('الإجمالي', s.total, '') +
      statBox('مكالمات', s.calls, 'green') +
      statBox('واتساب', s.whatsapp, 'wa') +
      statBox('فريدة', s.unique_fp, '') +
      statBox('مشبوهة', s.suspicious, 'red') +
      '</div><div class="charts-row">' +
      '<div class="chart-card"><div class="section-title"><i class="fas fa-chart-line"></i> التحويلات اليومية</div><canvas id="kt-line"></canvas></div>' +
      '<div class="chart-card"><div class="section-title"><i class="fas fa-chart-pie"></i> واتساب vs اتصال</div><canvas id="kt-donut"></canvas></div>' +
      '</div><div class="charts-row">' +
      '<div class="chart-card"><div class="section-title">أفضل 5 صفحات</div>' + miniTable(data.top_pages, 'page_title', 'total') + '</div>' +
      '<div class="chart-card"><div class="section-title">أفضل 5 مدن</div>' + miniTable(data.top_cities, 'city', 'total') + '</div>' +
      '</div>';
    if (data.sus_ips && data.sus_ips.length) {
      html += '<div class="chart-card"><div class="section-title">عناوين IP مشبوهة</div><table class="kt-tbl"><thead><tr><th>IP</th><th>عدد</th><th></th></tr></thead><tbody>';
      data.sus_ips.forEach(function (row) {
        html += '<tr><td data-label="IP">' + esc(row.ip) + '</td><td data-label="عدد">' + esc(row.cnt) + '</td><td><button class="kt-btn sm danger kt-block-ip" data-ip="' + esc(row.ip) + '">حظر</button></td></tr>';
      });
      html += '</tbody></table></div>';
    }
    $('#kt-content').innerHTML = html;

    var daily = data.daily || [];
    var ctx1 = $('#kt-line');
    if (ctx1 && window.Chart) {
      state.charts.line = new Chart(ctx1, {
        type: 'line',
        data: {
          labels: daily.map(function (d) { return d.day; }),
          datasets: [
            { label: 'مكالمات', data: daily.map(function (d) { return d.calls; }), borderColor: '#0056b3', tension: .3 },
            { label: 'واتساب', data: daily.map(function (d) { return d.whatsapp; }), borderColor: '#25d366', tension: .3 }
          ]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
      });
    }
    var ctx2 = $('#kt-donut');
    if (ctx2 && window.Chart) {
      state.charts.donut = new Chart(ctx2, {
        type: 'doughnut',
        data: {
          labels: ['اتصال', 'واتساب'],
          datasets: [{ data: [s.calls || 0, s.whatsapp || 0], backgroundColor: ['#0056b3', '#25d366'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
      });
    }
    bindBlockIp();
  }

  function statBox(lbl, val, cls) {
    return '<div class="stat-box ' + (cls || '') + '"><div class="stat-val">' + esc(val || 0) + '</div><div class="stat-lbl">' + lbl + '</div></div>';
  }

  function miniTable(rows, labelKey, valKey) {
    if (!rows || !rows.length) return '<p class="kt-loading">لا توجد بيانات</p>';
    var h = '<table class="kt-tbl"><tbody>';
    rows.forEach(function (r) {
      h += '<tr><td>' + esc(r[labelKey] || '-') + '</td><td><strong>' + esc(r[valKey] || 0) + '</strong></td></tr>';
    });
    return h + '</tbody></table>';
  }

  function ipTone(ip, counts) {
    var n = parseInt((counts && counts[ip]) || 0, 10);
    if (n > 3) return 'ip-hot';
    if (n === 3) return 'ip-warm';
    return '';
  }

  function renderConversions(data, keep) {
    keep = keep || {};
    var rows = data.rows || [];
    var counts = data.ip_counts || {};
    var html = '<div class="kt-toolbar">' +
      '<input type="search" class="kt-search" id="kt-conv-search" placeholder="بحث IP / مدينة / صفحة">' +
      '<select id="kt-conv-type"><option value="">كل الأنواع</option><option value="call">اتصال</option><option value="whatsapp">واتساب</option><option value="form">نموذج</option></select>' +
      '<button class="kt-btn" id="kt-export-csv"><i class="fas fa-download"></i> تصدير CSV</button>' +
      '<button class="kt-btn ghost" id="kt-share-report"><i class="fas fa-share-alt"></i> مشاركة</button>' +
      '</div>' +
      '<p class="kt-note">عدد التحويلات في هذه الفترة: ' + esc(data.total || rows.length) +
      (data.shown && data.total > data.shown ? ' — المعروض ' + esc(data.shown) : '') +
      '. <span class="ip-warm">برتقالي</span> = نفس الآيبي ضغط 3 مرات في أي وقت. <span class="ip-hot">أحمر</span> = أكثر من 3.</p>' +
      '<div class="kt-scroll"><table class="kt-tbl kt-conv"><thead><tr>' +
      '<th>النوع</th><th>IP</th><th>المدينة</th><th>الجهاز</th><th>الصفحة</th><th>الوقت</th><th></th></tr></thead><tbody>';
    if (!rows.length) html += '<tr><td colspan="7">لا توجد تحويلات في هذه الفترة.</td></tr>';
    rows.forEach(function (r) {
      var tone = ipTone(r.ip, counts);
      html += '<tr class="' + (String(r.is_suspicious) === '1' ? 'suspicious' : '') + '">' +
        '<td>' + esc(r.click_type) + '</td>' +
        '<td class="' + tone + '" dir="ltr">' + esc(r.ip) + '</td>' +
        '<td>' + esc(r.city) + '</td>' +
        '<td>' + esc(r.device_type) + '</td>' +
        '<td>' + (r.page_url ? '<a href="' + esc(r.page_url) + '" target="_blank" rel="noopener">' + esc(r.page_title || r.page_url) + '</a>' : esc(r.page_title)) + '</td>' +
        '<td dir="ltr">' + esc(r.created_at) + '</td>' +
        '<td><button class="kt-btn sm danger kt-block-ip" data-ip="' + esc(r.ip) + '">حظر</button></td></tr>';
    });
    html += '</tbody></table></div>';
    $('#kt-content').innerHTML = html;
    bindBlockIp();
    var search = $('#kt-conv-search');
    var typeSel = $('#kt-conv-type');
    if (search && keep.search) search.value = keep.search;
    if (typeSel && keep.clickType) typeSel.value = keep.clickType;
    if (search) search.addEventListener('change', function () { loadConversions(search.value, typeSel ? typeSel.value : ''); });
    if (typeSel) typeSel.addEventListener('change', function () { loadConversions(search ? search.value : '', typeSel.value); });
    var exp = $('#kt-export-csv');
    if (exp) exp.addEventListener('click', function () {
      ajax('kayan_track_export_csv', {}).then(function (res) {
        if (!res.success) return toast('فشل التصدير');
        var blob = new Blob([res.data.csv], { type: 'text/csv;charset=utf-8' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'kayan-conversions.csv';
        a.click();
      });
    });
    var share = $('#kt-share-report');
    if (share) share.addEventListener('click', function () {
      ajax('kayan_track_generate_report', { title: 'تقرير التحويلات' }).then(function (res) {
        if (res.success && res.data.url) {
          prompt('رابط التقرير:', res.data.url);
        }
      });
    });
  }

  function loadConversions(search, clickType) {
    var typeEl = $('#kt-conv-type');
    var type = typeof clickType === 'string' ? clickType : (typeEl ? typeEl.value : '');
    var q = search || '';
    setLoading();
    ajax('kayan_track_conversions', {
      search: q,
      click_type: type
    }).then(function (res) {
      if (res.success) renderConversions(res.data, { search: q, clickType: type });
    });
  }

  function numberById(id) {
    return (state.numbersAll || state.numbers || []).filter(function (n) { return String(n.id) === String(id); })[0] || null;
  }

  function filteredNumbers() {
    var q = (state.numbersSearch || '').toLowerCase();
    var status = state.numbersStatus || '';
    return (state.numbersAll || []).filter(function (n) {
      if (status === 'active' && String(n.active) === '0') return false;
      if (status === 'inactive' && String(n.active) !== '0') return false;
      if (!q) return true;
      var hay = [n.label, n.phone, n.wa_number, n.display, n.description].join(' ').toLowerCase();
      return hay.indexOf(q) !== -1;
    });
  }

  function pagedNumbers() {
    var all = filteredNumbers();
    var per = clampPerPage(state.numbersPerPage);
    var pages = Math.max(1, Math.ceil(all.length / per) || 1);
    if (state.numbersPage > pages) state.numbersPage = pages;
    var start = (state.numbersPage - 1) * per;
    return { rows: all.slice(start, start + per), total: all.length, pages: pages, page: state.numbersPage };
  }

  function loadNumbers(forceSync) {
    var sync = !!forceSync || !state.numbersSynced;
    if (sync) startScanUi(state.numbersSynced ? 'بدء الفحص...' : 'جارٍ فحص الموقع...');
    else setLoading();
    ajax('kayan_track_numbers_list', { sync: sync ? '1' : '0' }).then(function (res) {
      state.numbersSynced = true;
      if (!res.success) {
        if (sync) endScanUi('تعذر الفحص.');
        toast('تعذر تحميل الأرقام');
        return;
      }
      var data = res.data || {};
      var syncInfo = data.sync || {};
      state.lastSync = syncInfo;
      state.numbersAll = data.numbers || [];
      state.numbers = state.numbersAll;
      if (sync) {
        var msg = syncInfo.added
          ? '✅ اكتمل الفحص — استيراد ' + syncInfo.added + ' رقم جديد'
          : 'اكتمل الفحص — لا أرقام جديدة';
        endScanUi(msg);
        if (syncInfo.merged) toast('تم دمج ' + syncInfo.merged + ' رقم مكرر');
        if (syncInfo.added) toast('تمت إضافة ' + syncInfo.added + ' رقم من الموقع');
      }
      renderNumbers(data);
    }).catch(function () {
      if (sync) endScanUi('انقطع الاتصال.');
      toast('تعذر تحميل الأرقام');
    });
  }

  function renderNumbers(data) {
    var page = pagedNumbers();
    var nums = page.rows;
    var sync = (data && data.sync) || state.lastSync || {};
    var html = '<div class="kt-toolbar kt-toolbar-numbers">' +
      '<input type="search" class="kt-search" id="kt-num-search" placeholder="بحث عن رقم أو اسم...">' +
      '<select id="kt-num-status" class="kt-search" style="flex:0 0 140px"><option value="">جميع الحالات</option><option value="active">نشط</option><option value="inactive">متوقف</option></select>' +
      perPageHtml('kt-num-per-page', state.numbersPerPage) +
      '<button class="kt-btn" id="kt-sync-numbers" title="فحص الموقع واستيراد الأرقام الجديدة">🔄 فحص وتحديث</button>' +
      '<button class="kt-btn ghost" id="kt-add-number"><i class="fas fa-plus"></i> إضافة رقم</button>' +
      (sync.ignored ? '<button class="kt-btn ghost" id="kt-restore-numbers">استعادة المحذوف (' + esc(sync.ignored) + ')</button>' : '') +
      '</div>' +
      '<p class="kt-note">تُستخرج أرقام الاتصال والواتساب تلقائياً. الحذف من القائمة يخفي الرقم فقط، والحذف النهائي يزيله من مقالات الموقع وروابط واتساب.</p>' +
      '<div class="num-grid">';
    if (!nums.length) {
      html += '<div class="kt-note">لا توجد أرقام ظاهرة. حدّث الاستخراج أو أضف رقماً يدوياً.</div>';
    }
    nums.forEach(function (n) {
      var st = n.stats || {};
      var name = n.label ? n.label : 'بدون اسم';
      html += '<div class="num-card kt-open-num" data-id="' + n.id + '" style="border-right-color:' + esc(n.color || '#0056b3') + '">' +
        '<strong>' + esc(name) + (String(n.active) === '0' ? ' <em class="num-off">متوقف</em>' : '') + '</strong>' +
        '<span class="num-phone" dir="ltr">' + esc(n.display || n.phone) + '</span>' +
        '<small>مكالمات: ' + esc(st.calls || 0) + ' | واتساب: ' + esc(st.whatsapp || 0) + '</small>' +
        (n.description ? '<small class="num-src">' + esc(n.description) + '</small>' : '') +
        '<div class="num-actions">' +
        '<button class="kt-btn sm ghost kt-edit-num" data-id="' + n.id + '">' + (n.label ? 'تعديل الاسم' : 'تسمية') + '</button>' +
        '<button class="kt-btn sm ghost kt-replace-num" data-id="' + n.id + '">استبدال</button>' +
        '<button class="kt-btn sm danger kt-del-num" data-id="' + n.id + '">حذف</button>' +
        '<button class="kt-btn sm danger kt-purge-num" data-id="' + n.id + '">حذف نهائي</button>' +
        '</div></div>';
    });
    html += '</div>' + (page.pages > 1 || page.total > 5 ? paginationHtml(page.page, page.pages) : '') + '<div id="kt-num-modal"></div>';
    $('#kt-content').innerHTML = html;
    var search = $('#kt-num-search');
    var status = $('#kt-num-status');
    if (search) {
      search.value = state.numbersSearch || '';
      search.addEventListener('input', debounce(function () {
        state.numbersSearch = search.value;
        state.numbersPage = 1;
        renderNumbers({ sync: sync });
      }, 300));
    }
    if (status) {
      status.value = state.numbersStatus || '';
      status.addEventListener('change', function () {
        state.numbersStatus = status.value;
        state.numbersPage = 1;
        renderNumbers({ sync: sync });
      });
    }
    bindPerPage('kt-num-per-page', function (v) {
      state.numbersPerPage = v;
      state.numbersPage = 1;
      renderNumbers({ sync: sync });
    });
    bindPagination(document, page.page, page.pages, function (p) {
      state.numbersPage = p;
      renderNumbers({ sync: sync });
    });
    var syncBtn = $('#kt-sync-numbers');
    if (syncBtn) syncBtn.addEventListener('click', function () { loadNumbers(true); });
    var add = $('#kt-add-number');
    if (add) add.addEventListener('click', function (e) { e.stopPropagation(); openNumberModal({}); });
    var restore = $('#kt-restore-numbers');
    if (restore) restore.addEventListener('click', function () {
      ajax('kayan_track_numbers_restore', {}).then(function (res) {
        if (res.success) { toast('تمت استعادة الأرقام المحذوفة'); state.numbersSynced = true; loadNumbers(true); }
      });
    });
    $all('.kt-open-num').forEach(function (card) {
      card.addEventListener('click', function () { openNumberDetail(card.getAttribute('data-id'), 1, ''); });
    });
    $all('.kt-edit-num').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        openNumberModal(numberById(btn.getAttribute('data-id')) || {}, false);
      });
    });
    $all('.kt-replace-num').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        openNumberModal(numberById(btn.getAttribute('data-id')) || {}, true);
      });
    });
    $all('.kt-del-num').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        if (!confirm('حذف هذا الرقم من القائمة فقط؟ لن يُحذف من مقالات الموقع، ويمكنك استعادته لاحقاً.')) return;
        ajax('kayan_track_number_delete', { id: btn.getAttribute('data-id') }).then(function () {
          toast('تم حذف الرقم من القائمة');
          state.numbersSynced = true;
          loadNumbers(false);
        });
      });
    });
    $all('.kt-purge-num').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        purgeNumber(btn.getAttribute('data-id'));
      });
    });
  }

  function purgeNumber(id) {
    if (!confirm('حذف الرقم نهائياً من كل مكان؟ سيُزال من مقالات الموقع وروابط واتساب وtel، مع الإبقاء على الأسعار والتواريخ.')) return;
    toast('جارٍ الحذف من المحتوى...');
    ajax('kayan_track_number_purge', { id: id }).then(function (res) {
      if (!res.success) { toast((res.data && res.data.message) || 'تعذر الحذف النهائي'); return; }
      var d = res.data || {};
      toast('حُذف من ' + (d.posts || 0) + ' مقالة');
      state.numberDetail = null;
      state.numbersSynced = true;
      loadNumbers(false);
    });
  }

  function openNumberDetail(id, page, type) {
    state.numberDetail = { id: id, page: page || 1, type: type || '' };
    setLoading();
    ajax('kayan_track_number_clicks', { id: id, page_num: page || 1, click_type: type || '' }).then(function (res) {
      if (!res.success) { state.numberDetail = null; toast('تعذر فتح تفاصيل الرقم'); loadTab(); return; }
      if (res.data.places) state.placesByNumber[id] = res.data.places;
      renderNumberDetail(res.data, type || '', state.placesByNumber[id] || null);
    });
  }

  function renderPlaces(places) {
    var rows = (places && places.rows) || [];
    var total = places ? (places.total || rows.length) : 0;
    var html = '<div class="num-places"><h4>أماكن ظهور الرقم' + (total ? ' (' + esc(total) + ')' : '') + '</h4>';
    if (!rows.length) {
      html += '<div class="num-place"><span>لا يظهر هذا الرقم في المقالات أو الإعدادات حالياً.</span></div>';
    }
    rows.forEach(function (place) {
      var title = place.url
        ? '<a href="' + esc(place.url) + '" target="_blank" rel="noopener">' + esc(place.title) + '</a>'
        : esc(place.title);
      html += '<div class="num-place"><div>' + title + '</div><span>' + esc(place.kind || '') + (place.spot ? ' · ' + esc(place.spot) : '') + '</span></div>';
    });
    if (places && places.truncated) html += '<div class="num-place"><span>هذه أول 120 صفحة. قد يكون الرقم في صفحات أخرى أيضاً.</span></div>';
    html += '</div>';
    return html;
  }

  function renderNumberDetail(data, type, places) {
    var n = data.number || {};
    var rows = data.rows || [];
    var name = n.label ? n.label : 'بدون اسم';
    var html = '<div class="kt-toolbar">' +
      '<button class="kt-btn ghost" id="kt-num-back">رجوع للأرقام</button>' +
      '<button class="kt-btn ghost" id="kt-detail-name">تسمية الرقم</button>' +
      '<button class="kt-btn ghost" id="kt-detail-replace">استبدال الرقم</button>' +
      '<button class="kt-btn danger" id="kt-detail-purge">🗑 حذف نهائياً من كل مكان</button>' +
      '</div>' +
      '<div class="num-detail-head">' +
      '<h3>' + esc(name) + '</h3>' +
      '<div class="num-phone" dir="ltr">' + esc(n.display || n.phone) + '</div>' +
      (n.description ? '<small class="num-src">' + esc(n.description) + '</small>' : '') +
      '</div>' +
      renderPlaces(places) +
      '<div class="section-title">ضغطات الرقم</div>' +
      '<div class="kt-tabs" id="kt-click-types">' +
      '<button class="kt-tab' + (type === '' ? ' active' : '') + '" data-ctype="">الكل' + (type === '' ? ' (' + esc(data.total || 0) + ')' : '') + '</button>' +
      '<button class="kt-tab' + (type === 'call' ? ' active' : '') + '" data-ctype="call">اتصال</button>' +
      '<button class="kt-tab' + (type === 'whatsapp' ? ' active' : '') + '" data-ctype="whatsapp">واتساب</button>' +
      '</div>' +
      '<table class="kt-tbl"><thead><tr><th>الوقت</th><th>النوع</th><th>الصفحة</th><th>المدينة</th><th>الجهاز</th><th>المصدر</th></tr></thead><tbody>';
    if (!rows.length) html += '<tr><td colspan="6">لا توجد ضغطات في الفترة المحددة.</td></tr>';
    rows.forEach(function (r) {
      var kind = r.click_type === 'whatsapp' ? 'واتساب' : 'اتصال';
      var src = r.utm_source || r.traffic_src || '';
      html += '<tr' + (String(r.is_suspicious) === '1' ? ' class="suspicious"' : '') + '>' +
        '<td data-label="الوقت">' + esc(r.created_at) + '</td>' +
        '<td data-label="النوع">' + kind + '</td>' +
        '<td data-label="الصفحة">' + (r.page_url ? '<a href="' + esc(r.page_url) + '" target="_blank" rel="noopener">' + esc(r.page_title || r.page_url) + '</a>' : esc(r.page_title || '')) + '</td>' +
        '<td data-label="المدينة">' + esc(r.city || r.country || '') + '</td>' +
        '<td data-label="الجهاز">' + esc(r.device_type || '') + (r.browser ? ' / ' + esc(r.browser) : '') + '</td>' +
        '<td data-label="المصدر">' + esc(src) + '</td></tr>';
    });
    html += '</tbody></table>';
    if ((data.pages || 1) > 1) {
      html += paginationHtml(data.page, data.pages);
    }
    html += '<div id="kt-num-modal"></div>';
    $('#kt-content').innerHTML = html;
    $('#kt-num-back').addEventListener('click', function () { state.numberDetail = null; state.numbersSynced = true; loadTab(); });
    $('#kt-detail-name').addEventListener('click', function () { openNumberModal(n, false); });
    $('#kt-detail-replace').addEventListener('click', function () { openNumberModal(n, true); });
    var purgeBtn = $('#kt-detail-purge');
    if (purgeBtn) purgeBtn.addEventListener('click', function () { purgeNumber(n.id); });
    $all('#kt-click-types .kt-tab').forEach(function (tab) {
      tab.addEventListener('click', function () { openNumberDetail(n.id, 1, tab.getAttribute('data-ctype') || ''); });
    });
    bindPagination(document, data.page || 1, data.pages || 1, function (p) {
      openNumberDetail(n.id, p, type);
    });
  }

  function openNumberModal(n, replacing) {
    var m = $('#kt-num-modal');
    if (!m) return;
    var title = n.id ? (replacing ? 'استبدال الرقم' : 'تسمية الرقم') : 'رقم جديد';
    m.innerHTML = '<div class="kt-modal"><div class="kt-modal-box"><h3>' + title + '</h3>' +
      '<div class="kt-form-row"><label>اسم الرقم</label><input id="nm-label" placeholder="مثال: واتساب التسربات دبي" value="' + esc(n.label || '') + '"></div>' +
      '<div class="kt-form-row"><label>الرقم</label><input id="nm-phone" dir="ltr" placeholder="9715xxxxxxxx" value="' + esc(n.phone || '') + '"' + (n.id && !replacing ? ' readonly' : '') + '></div>' +
      '<p class="kt-note">' + (replacing ? 'الاستبدال يبقي الاسم وسجل الضغطات على هذه البطاقة وينقل الضغطات القديمة للرقم السابق إليها.' : 'الاسم يظهر في البطاقة لتمييز الرقم. الرقم نفسه يُستبدل من زر استبدال.') + '</p>' +
      '<div class="kt-toolbar"><button class="kt-btn" id="nm-save">حفظ</button><button class="kt-btn ghost" id="nm-cancel">إلغاء</button></div></div></div>';
    $('#nm-cancel').addEventListener('click', function () { m.innerHTML = ''; });
    $('#nm-save').addEventListener('click', function () {
      ajax('kayan_track_number_save', {
        id: n.id || 0,
        label: $('#nm-label').value,
        phone: $('#nm-phone').value,
        wa_number: $('#nm-phone').value,
        color: n.color || '#0056b3',
        description: n.description || '',
        active: 1
      }).then(function (res) {
        if (!res.success) { toast((res.data && res.data.message) || 'تعذر الحفظ'); return; }
        m.innerHTML = '';
        toast(replacing ? 'تم استبدال الرقم' : 'تم حفظ الاسم');
        state.numbersSynced = true;
        if (n.id && $('#kt-num-back')) openNumberDetail(n.id, 1, '');
        else loadTab();
      });
    });
  }

  function renderPages(data) {
    var rows = data.rows || [];
    var html = '<div class="kt-toolbar"><button class="kt-btn" id="kt-share-pages">مشاركة التقرير</button></div>' +
      '<table class="kt-tbl"><thead><tr><th>الصفحة</th><th>اتصال</th><th>واتساب</th><th>الإجمالي</th><th></th></tr></thead><tbody>';
    rows.forEach(function (r) {
      html += '<tr><td data-label="الصفحة">' + esc(r.page_title) + '</td>' +
        '<td data-label="اتصال">' + esc(r.calls) + '</td>' +
        '<td data-label="واتساب">' + esc(r.whatsapp) + '</td>' +
        '<td data-label="الإجمالي">' + esc(r.total) + '</td>' +
        '<td><a href="' + esc(r.page_url) + '" target="_blank" rel="noopener">رابط</a></td></tr>';
    });
    html += '</tbody></table>';
    $('#kt-content').innerHTML = html;
    var btn = $('#kt-share-pages');
    if (btn) btn.addEventListener('click', function () {
      ajax('kayan_track_generate_report', { title: 'تقرير الصفحات' }).then(function (res) {
        if (res.success) prompt('رابط التقرير:', res.data.url);
      });
    });
  }

  function renderAnalytics(data) {
    destroyCharts();
    var html = '<div class="charts-row">' +
      '<div class="chart-card"><div class="section-title">أفضل الخدمات</div><canvas id="kt-svc"></canvas></div>' +
      '<div class="chart-card"><div class="section-title">أفضل المدن</div><canvas id="kt-cities"></canvas></div></div>' +
      '<div class="charts-row">' +
      '<div class="chart-card"><div class="section-title">الأجهزة</div><canvas id="kt-dev"></canvas></div>' +
      '<div class="chart-card"><div class="section-title">مصادر الزيارات</div><canvas id="kt-src"></canvas></div></div>';
    $('#kt-content').innerHTML = html;
    barChart('#kt-svc', data.services);
    barChart('#kt-cities', data.cities, 'city');
    barChart('#kt-dev', data.devices);
    barChart('#kt-src', data.sources);
  }

  function barChart(sel, rows, labelKey) {
    labelKey = labelKey || 'name';
    var el = $(sel);
    if (!el || !window.Chart || !rows) return;
    var key = sel.replace('#', '');
    state.charts[key] = new Chart(el, {
      type: 'bar',
      data: {
        labels: rows.map(function (r) { return r[labelKey] || r.name || '-'; }),
        datasets: [{ label: 'تحويلات', data: rows.map(function (r) { return r.total; }), backgroundColor: '#0056b3' }]
      },
      options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
    });
  }

  function renderVisitors(data, blacklist) {
    var vrows = (data && data.rows) || [];
    var brows = (blacklist && blacklist.rows) || [];
    var html = '<div class="kt-tabs"><button class="kt-tab active" data-vtab="visitors">الزوار</button><button class="kt-tab" data-vtab="blacklist">القائمة السوداء</button><button class="kt-tab" data-vtab="dni">DNI</button></div>' +
      '<div id="kt-vtab-content"></div>';
    $('#kt-content').innerHTML = html;
    function show(tab) {
      $all('.kt-tab').forEach(function (t) { t.classList.toggle('active', t.getAttribute('data-vtab') === tab); });
      var box = $('#kt-vtab-content');
      if (tab === 'visitors') {
        var h = '<table class="kt-tbl"><thead><tr><th>البصمة</th><th>الزيارات</th><th>الجهاز</th><th>آخر زيارة</th></tr></thead><tbody>';
        vrows.forEach(function (r) {
          h += '<tr><td data-label="البصمة">' + esc(r.fingerprint).slice(0, 12) + '…</td><td>' + esc(r.visit_count) + '</td><td>' + esc(r.device_type) + '</td><td>' + esc(r.last_visit) + '</td></tr>';
        });
        box.innerHTML = h + '</tbody></table>';
      } else if (tab === 'blacklist') {
        var h2 = '<table class="kt-tbl"><thead><tr><th>IP</th><th>السبب</th><th></th></tr></thead><tbody>';
        brows.forEach(function (r) {
          h2 += '<tr><td>' + esc(r.ip) + '</td><td>' + esc(r.reason) + '</td><td><button class="kt-btn sm ghost kt-unblock" data-ip="' + esc(r.ip) + '">رفع الحظر</button></td></tr>';
        });
        box.innerHTML = h2 + '</tbody></table>';
        bindUnblock();
      } else {
        loadDni(box);
      }
    }
    $all('.kt-tab').forEach(function (t) {
      t.addEventListener('click', function () { show(t.getAttribute('data-vtab')); });
    });
    show('visitors');
  }

  function loadDni(box) {
    box.innerHTML = '<div class="kt-loading">جاري التحميل...</div>';
    ajax('kayan_track_dni_list', {}).then(function (res) {
      if (!res.success) return;
      var rules = res.data.rules || [];
      var html = '<div class="kt-toolbar"><button class="kt-btn" id="kt-add-dni">قاعدة DNI</button></div><table class="kt-tbl"><thead><tr><th>المصدر</th><th>UTM</th><th>الرقم</th><th></th></tr></thead><tbody>';
      rules.forEach(function (r) {
        html += '<tr><td>' + esc(r.source_type) + '</td><td>' + esc(r.utm_source) + '</td><td>' + esc(r.number_label) + '</td>' +
          '<td><button class="kt-btn sm danger kt-del-dni" data-id="' + r.id + '">حذف</button></td></tr>';
      });
      html += '</tbody></table>';
      box.innerHTML = html;
      $all('.kt-del-dni').forEach(function (btn) {
        btn.addEventListener('click', function () {
          ajax('kayan_track_dni_delete', { id: btn.getAttribute('data-id') }).then(function () { loadDni(box); });
        });
      });
    });
  }

  function renderSettings(data) {
    var d = data || {};
    var sizes = d.table_sizes || {};
    $('#kt-content').innerHTML =
      '<form id="kt-settings-form" class="chart-card">' +
      '<div class="kt-form-row"><label>تعطيل التتبع</label><input type="checkbox" name="kayan_track_disable" ' + (d.kayan_track_disable ? 'checked' : '') + '></div>' +
      '<div class="kt-form-row"><label>الاحتفاظ (أيام)</label><select name="kayan_tracking_retention">' +
      [30,60,90,180,365].map(function (n) {
        return '<option value="' + n + '"' + (d.kayan_tracking_retention == n ? ' selected' : '') + '>' + n + ' يوم (~' + (sizes.conversions || 0) + ' MB conversions)</option>';
      }).join('') + '</select></div>' +
      '<div class="kt-form-row"><label>مهلة التكرار (دقائق)</label><input name="kayan_cooldown_mins" type="number" value="' + esc(d.kayan_cooldown_mins || 30) + '"></div>' +
      '<div class="kt-form-row"><label>حد الاشتباه / 24س</label><input name="kayan_fraud_threshold" type="number" value="' + esc(d.kayan_fraud_threshold || 3) + '"></div>' +
      '<div class="kt-form-row"><label>حظر تلقائي عند</label><input name="kayan_auto_blacklist" type="number" value="' + esc(d.kayan_auto_blacklist || 5) + '"></div>' +
      '<div class="kt-form-row"><label>تفعيل Telegram</label><input type="checkbox" name="kayan_telegram_on" ' + (d.kayan_telegram_on ? 'checked' : '') + '></div>' +
      '<div class="kt-form-row"><input name="kayan_telegram_token" placeholder="Bot Token" value="' + esc(d.kayan_telegram_token || '') + '"></div>' +
      '<div class="kt-form-row"><input name="kayan_telegram_chat" placeholder="Chat ID" value="' + esc(d.kayan_telegram_chat || '') + '"></div>' +
      '<div class="kt-form-row"><label>تفعيل DNI</label><input type="checkbox" name="kayan_dni_enabled" ' + (d.kayan_dni_enabled ? 'checked' : '') + '></div>' +
      '<button type="submit" class="kt-btn">حفظ الإعدادات</button></form>';
    $('#kt-settings-form').addEventListener('submit', function (e) {
      e.preventDefault();
      var fd = {};
      $all('#kt-settings-form [name]').forEach(function (el) {
        if (el.type === 'checkbox') fd[el.name] = el.checked ? '1' : '';
        else fd[el.name] = el.value;
      });
      ajax('kayan_track_settings_save', fd).then(function (res) {
        if (res.success) toast('تم حفظ الإعدادات');
      });
    });
  }

  function loadSeoPosts() {
    setLoading();
    ajax('kayan_track_seo_posts', {
      page: state.seoPage,
      per_page: state.seoPerPage,
      search: state.seoSearch || '',
      post_type: state.seoPostType || ''
    }).then(function (res) {
      if (!res.success) { toast('تعذر تحميل عناوين SEO'); return; }
      renderSeo(res.data || {});
    });
  }

  function seoActions(p) {
    return '<button type="button" class="kt-btn sm ghost kt-seo-edit" data-id="' + p.post_id + '">تعديل</button>' +
      '<button type="button" class="kt-btn sm kt-seo-save" data-id="' + p.post_id + '" style="display:none">حفظ</button>' +
      (p.has_backup ? '<button type="button" class="kt-btn sm ghost kt-seo-rollback" data-id="' + p.post_id + '">تراجع</button>' : '') +
      '<button type="button" class="kt-btn sm danger kt-seo-clear" data-id="' + p.post_id + '">مسح</button>';
  }

  function renderSeo(data) {
    var posts = data.posts || [];
    var types = data.post_types || state.seoPostTypes || [];
    state.seoPostTypes = types;
    var pages = Math.max(1, data.total_pages || 1);
    state.seoPage = data.current_page || state.seoPage;
    var typeOpts = '<option value="">جميع أنواع المحتوى</option>' + types.map(function (t) {
      return '<option value="' + esc(t.name) + '"' + (state.seoPostType === t.name ? ' selected' : '') + '>' + esc(t.label) + '</option>';
    }).join('');
    var notice = data.rank_math_active
      ? '<div class="kt-notice ok">✓ العناوين تُحفظ في حقل Rank Math الرسمي (<code>rank_math_title</code>) دون التأثير على إعدادات Rank Math الأخرى.</div>'
      : '<div class="kt-notice err"><strong>تحذير:</strong> إضافة Rank Math غير مفعلة. لا يمكن حفظ عناوين SEO حتى يتم تفعيلها.</div>';
    var html = '<h2 class="kt-page-title">🔎 إدارة عناوين SEO</h2>' + notice +
      '<div class="kt-toolbar kt-toolbar-numbers">' +
      '<input type="search" class="kt-search" id="kt-seo-search" placeholder="بحث عن مقالة...">' +
      '<select id="kt-seo-type" class="kt-search" style="flex:0 0 180px">' + typeOpts + '</select>' +
      perPageHtml('kt-seo-per-page', state.seoPerPage) +
      '<input type="text" id="kt-seo-bulk-template" class="kt-search" placeholder="قالب: {post_title} | {site_name}">' +
      '<button type="button" class="kt-btn" id="kt-seo-bulk-apply" disabled>تطبيق على المحددة</button>' +
      '</div>' +
      '<p class="kt-note">المتغيرات: {post_title} | {city} | {service} | {category} | {site_name}</p>' +
      '<div id="kt-seo-bulk-preview" class="chart-card" hidden></div>';

    if (!posts.length) {
      html += '<p class="kt-note">لا توجد مقالات.</p>';
    } else if (isMobile()) {
      html += '<div class="kt-seo-cards">';
      posts.forEach(function (p) {
        var seoDisplay = p.seo_title ? esc(p.seo_title) : '<em class="kt-muted">غير محدد</em>';
        html += '<div class="kt-seo-card" data-post-id="' + p.post_id + '">' +
          '<label class="kt-seo-check"><input type="checkbox" class="kt-seo-select" value="' + p.post_id + '"></label>' +
          '<div class="kt-seo-title"><a href="' + esc(p.edit_url || p.post_url || '#') + '" target="_blank" rel="noopener">' + esc(p.title) + '</a></div>' +
          '<div class="kt-muted">' + esc(p.post_type) + '</div>' +
          '<div class="kt-seo-cell"><span class="kt-seo-display">' + seoDisplay + '</span>' +
          '<input type="text" class="kt-seo-input" value="' + esc(p.seo_title || '') + '" style="display:none"></div>' +
          '<div class="num-actions">' + seoActions(p) + '</div></div>';
      });
      html += '</div>';
    } else {
      html += '<div class="kt-scroll"><table class="kt-tbl kt-seo-tbl"><thead><tr>' +
        '<th><input type="checkbox" id="kt-seo-select-all"></th>' +
        '<th>عنوان المقالة</th><th>عنوان SEO (Rank Math)</th><th>النوع</th><th>الإجراءات</th>' +
        '</tr></thead><tbody>';
      posts.forEach(function (p) {
        var seoDisplay = p.seo_title ? esc(p.seo_title) : '<em class="kt-muted">غير محدد (سيستخدم Rank Math الافتراضي)</em>';
        html += '<tr data-post-id="' + p.post_id + '">' +
          '<td><input type="checkbox" class="kt-seo-select" value="' + p.post_id + '"></td>' +
          '<td><a href="' + esc(p.edit_url || p.post_url || '#') + '" target="_blank" rel="noopener">' + esc(p.title) + '</a></td>' +
          '<td><div class="kt-seo-cell"><span class="kt-seo-display">' + seoDisplay + '</span>' +
          '<input type="text" class="kt-seo-input" value="' + esc(p.seo_title || '') + '" style="display:none"></div></td>' +
          '<td><small>' + esc(p.post_type) + '</small></td>' +
          '<td class="num-actions">' + seoActions(p) + '</td></tr>';
      });
      html += '</tbody></table></div>';
    }
    html += paginationHtml(state.seoPage, pages);
    $('#kt-content').innerHTML = html;
    var search = $('#kt-seo-search');
    var typeSel = $('#kt-seo-type');
    if (search) {
      search.value = state.seoSearch || '';
      search.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') e.preventDefault();
      });
      search.addEventListener('input', debounce(function () {
        state.seoSearch = search.value;
        state.seoPage = 1;
        loadSeoPosts();
      }, 400));
    }
    if (typeSel) typeSel.addEventListener('change', function () {
      state.seoPostType = typeSel.value;
      state.seoPage = 1;
      loadSeoPosts();
    });
    bindPerPage('kt-seo-per-page', function (v) {
      state.seoPerPage = v;
      state.seoPage = 1;
      loadSeoPosts();
    });
    bindPagination(document, state.seoPage, pages, function (p) {
      state.seoPage = p;
      loadSeoPosts();
    });
    bindSeoActions();
  }

  function selectedSeoIds() {
    return $all('.kt-seo-select:checked').map(function (el) { return el.value; });
  }

  function updateSeoBulkBtn() {
    var btn = $('#kt-seo-bulk-apply');
    if (btn) btn.disabled = !selectedSeoIds().length;
  }

  function bindSeoActions() {
    var all = $('#kt-seo-select-all');
    if (all) all.addEventListener('change', function () {
      $all('.kt-seo-select').forEach(function (el) { el.checked = all.checked; });
      updateSeoBulkBtn();
    });
    $all('.kt-seo-select').forEach(function (el) {
      el.addEventListener('change', updateSeoBulkBtn);
    });
    $all('.kt-seo-edit').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var row = btn.closest('tr, .kt-seo-card');
        if (!row) return;
        row.classList.add('is-editing');
        var display = row.querySelector('.kt-seo-display');
        var input = row.querySelector('.kt-seo-input');
        var save = row.querySelector('.kt-seo-save');
        if (display) display.style.display = 'none';
        if (input) {
          input.style.display = 'block';
          input.focus();
          if (typeof input.select === 'function') input.select();
        }
        btn.style.display = 'none';
        if (save) save.style.display = '';
      });
    });
    $all('.kt-seo-input').forEach(function (input) {
      input.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        var row = input.closest('tr, .kt-seo-card');
        var save = row ? row.querySelector('.kt-seo-save') : null;
        if (save) save.click();
      });
    });
    $all('.kt-seo-save').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var row = btn.closest('tr, .kt-seo-card');
        if (!row) return;
        var title = row.querySelector('.kt-seo-input').value;
        ajax('kayan_track_seo_save', { post_id: btn.getAttribute('data-id'), seo_title: title }).then(function (res) {
          if (res.success) { toast('تم الحفظ'); loadSeoPosts(); }
          else toast((res.data && res.data.message) || 'تعذر الحفظ');
        });
      });
    });
    $all('.kt-seo-rollback').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        if (!confirm('هل تريد التراجع إلى العنوان السابق؟')) return;
        ajax('kayan_track_seo_rollback', { post_id: btn.getAttribute('data-id') }).then(function (res) {
          if (res.success) { toast('تم التراجع'); loadSeoPosts(); }
          else toast((res.data && res.data.message) || 'لا توجد نسخة احتياطية');
        });
      });
    });
    $all('.kt-seo-clear').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        if (!confirm('هل تريد مسح عنوان SEO المخصص والعودة للعنوان الافتراضي لـ Rank Math؟')) return;
        ajax('kayan_track_seo_clear', { post_id: btn.getAttribute('data-id') }).then(function (res) {
          if (res.success) { toast('تم المسح'); loadSeoPosts(); }
          else toast((res.data && res.data.message) || 'تعذر المسح');
        });
      });
    });
    var bulk = $('#kt-seo-bulk-apply');
    if (bulk) bulk.addEventListener('click', function () {
      var ids = selectedSeoIds();
      var template = ($('#kt-seo-bulk-template') || {}).value || '';
      if (!template) { toast('أدخل قالب العنوان أولاً'); return; }
      ajax('kayan_track_seo_bulk_preview', { post_ids: JSON.stringify(ids), template: template }).then(function (res) {
        if (!res.success) { toast((res.data && res.data.message) || 'تعذر المعاينة'); return; }
        renderSeoBulk(res.data || [], ids, template);
      });
    });
    updateSeoBulkBtn();
  }

  function renderSeoBulk(previews, ids, template) {
    var box = $('#kt-seo-bulk-preview');
    if (!box) return;
    var rows = (previews || []).map(function (p) {
      var warnings = (p.warnings && p.warnings.length) ? '<br><small class="kt-warn">⚠ ' + esc(p.warnings.join(' | ')) + '</small>' : '';
      return '<tr><td>' + esc(p.post_title) + '</td><td>' + esc(p.current_seo || '') + '</td><td>' + esc(p.new_seo) + warnings + '</td></tr>';
    }).join('');
    box.hidden = false;
    box.innerHTML = '<h3>معاينة التطبيق الجماعي</h3>' +
      '<div class="kt-scroll"><table class="kt-tbl"><thead><tr><th>المقالة</th><th>الحالي</th><th>الجديد</th></tr></thead><tbody>' + rows + '</tbody></table></div>' +
      '<div class="kt-toolbar"><button class="kt-btn" id="kt-seo-bulk-confirm">✓ تأكيد التطبيق</button>' +
      '<button class="kt-btn ghost" id="kt-seo-bulk-cancel">إلغاء</button></div>';
    $('#kt-seo-bulk-confirm').addEventListener('click', function () {
      if (!confirm('تطبيق قالب SEO على المقالات المحددة؟')) return;
      ajax('kayan_track_seo_bulk_apply', { post_ids: JSON.stringify(ids), template: template }).then(function (res) {
        if (res.success) { toast('تم التطبيق: ' + (res.data.success || 0) + ' مقالة'); loadSeoPosts(); }
        else toast((res.data && res.data.message) || 'تعذر التطبيق');
      });
    });
    $('#kt-seo-bulk-cancel').addEventListener('click', function () { box.hidden = true; box.innerHTML = ''; });
  }

  function bindBlockIp() {
    $all('.kt-block-ip').forEach(function (btn) {
      btn.addEventListener('click', function () {
        ajax('kayan_track_block_ip', { ip: btn.getAttribute('data-ip') }).then(function (res) {
          if (res.success) toast('تم الحظر');
        });
      });
    });
  }

  function bindUnblock() {
    $all('.kt-unblock').forEach(function (btn) {
      btn.addEventListener('click', function () {
        ajax('kayan_track_unblock_ip', { ip: btn.getAttribute('data-ip') }).then(function () { loadTab(); });
      });
    });
  }

  function esc(s) {
    return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/"/g,'&quot;');
  }

  function loadTab() {
    destroyCharts();
    syncFilterBar();
    var tab = state.tab;
    if (tab !== 'numbers') {
      var scan = $('#kt-scan-progress');
      if (scan) scan.hidden = true;
    }
    if (tab === 'overview') {
      setLoading();
      ajax('kayan_track_overview', {}).then(function (res) { if (res.success) renderOverview(res.data); });
    } else if (tab === 'conversions') {
      loadConversions(1);
    } else if (tab === 'numbers') {
      if (state.numberDetail) {
        openNumberDetail(state.numberDetail.id, state.numberDetail.page || 1, state.numberDetail.type || '');
        return;
      }
      loadNumbers(false);
    } else if (tab === 'seo') {
      loadSeoPosts();
    } else if (tab === 'pages') {
      setLoading();
      ajax('kayan_track_pages', {}).then(function (res) { if (res.success) renderPages(res.data); });
    } else if (tab === 'analytics') {
      setLoading();
      ajax('kayan_track_analytics', {}).then(function (res) { if (res.success) renderAnalytics(res.data); });
    } else if (tab === 'visitors') {
      setLoading();
      Promise.all([
        ajax('kayan_track_visitors', {}),
        ajax('kayan_track_blacklist', {})
      ]).then(function (arr) {
        renderVisitors(arr[0].success ? arr[0].data : {}, arr[1].success ? arr[1].data : {});
      });
    } else if (tab === 'settings') {
      setLoading();
      ajax('kayan_track_settings_get', {}).then(function (res) { if (res.success) renderSettings(res.data); });
    }
  }

  bindFilters();
  bindNav();
  var lastMobileLayout = isMobile();
  window.addEventListener('resize', debounce(function () {
    var nowMobile = isMobile();
    if (nowMobile === lastMobileLayout) return;
    lastMobileLayout = nowMobile;
    if (document.querySelector('.is-editing, .kt-seo-input:focus')) return;
    if (state.tab === 'seo') loadSeoPosts();
    else if (state.tab === 'numbers' && !state.numberDetail) renderNumbers({ sync: { ignored: 0 } });
  }, 400));
  loadTab();
})();
