<script>
(function () {
  function peerTrendChartCommonOpts() {
    return {
      responsive: true,
      maintainAspectRatio: false,
      animation: { duration: 520, easing: 'easeOutQuart' },
      layout: { padding: { top: 6, right: 4, bottom: 2, left: 2 } },
      interaction: { intersect: false, mode: 'index' },
      plugins: {
        legend: {
          display: true,
          position: 'bottom',
          align: 'center',
          labels: {
            boxWidth: 8,
            boxHeight: 8,
            padding: 10,
            usePointStyle: true,
            pointStyle: 'circle',
            font: { size: 9, weight: '500', family: 'system-ui, -apple-system, sans-serif' },
            color: '#475569',
          },
        },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, 0.92)',
          padding: 10,
          cornerRadius: 8,
          titleFont: { size: 10 },
          bodyFont: { size: 11 },
          callbacks: {
            label: function (ctx) {
              var v = ctx.parsed && ctx.parsed.y != null ? ctx.parsed.y : ctx.raw;
              return (ctx.dataset.label || '') + ': ' + (typeof v === 'number' ? v.toFixed(2) : v);
            },
          },
        },
      },
      scales: {
        x: {
          grid: { display: false, drawBorder: false },
          ticks: { font: { size: 9 }, maxRotation: 0, color: '#64748b' },
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(148, 163, 184, 0.22)', lineWidth: 1, drawBorder: false },
          border: { display: false },
          ticks: { font: { size: 9 }, color: '#64748b', padding: 6 },
        },
      },
    };
  }

  function initPeerTrendPanelsCharts() {
    if (typeof Chart === 'undefined') return;
    var dataEl = document.getElementById('peer-trend-panels-chart-data');
    if (!dataEl) return;
    var payload;
    try {
      payload = JSON.parse(dataEl.textContent);
    } catch (e) {
      return;
    }
    var weeks = payload.weeks || ['L4W', 'W13', 'W14', 'W15'];
    var col = payload.colors || {};

    function lineDs(label, data, color) {
      return {
        label: label,
        data: data,
        borderColor: color,
        backgroundColor: color,
        borderWidth: 2.25,
        pointRadius: 3.5,
        pointHoverRadius: 6,
        pointBackgroundColor: '#fff',
        pointBorderColor: color,
        pointBorderWidth: 2,
        tension: 0.4,
        fill: false,
      };
    }

    var el;

    el = document.getElementById('peer-chart-line-incident-main');
    if (el && payload.incidentMain) {
      var im = payload.incidentMain;
      new Chart(el.getContext('2d'), {
        type: 'line',
        data: {
          labels: weeks,
          datasets: [
            lineDs('Accident', im[0], col.accident || '#E67E22'),
            lineDs('Nearmiss', im[1], col.nearmiss || '#BDC3C7'),
          ],
        },
        options: peerTrendChartCommonOpts(),
      });
    }

    el = document.getElementById('peer-chart-line-incident-hipo');
    if (el && payload.incidentHipo) {
      var ih = payload.incidentHipo;
      new Chart(el.getContext('2d'), {
        type: 'line',
        data: {
          labels: weeks,
          datasets: [
            lineDs('HIPO GR', ih[0], col.hipo_gr || '#E74C3C'),
            lineDs('HIPO NON GR', ih[1], col.hipo_non || '#F1C40F'),
            lineDs('NON HIPO', ih[2], col.non_hipo || '#27AE60'),
          ],
        },
        options: peerTrendChartCommonOpts(),
      });
    }

    el = document.getElementById('peer-chart-line-accident-main');
    if (el && payload.accidentMain) {
      var am = payload.accidentMain;
      new Chart(el.getContext('2d'), {
        type: 'line',
        data: {
          labels: weeks,
          datasets: [
            lineDs('Fire Case', am[0], col.fire || '#2980B9'),
            lineDs('Injury', am[1], col.injury || '#E74C3C'),
            lineDs('Property Damage', am[2], col.property || '#F1C40F'),
          ],
        },
        options: peerTrendChartCommonOpts(),
      });
    }

    el = document.getElementById('peer-chart-line-accident-hipo');
    if (el && payload.accidentHipo) {
      var ah = payload.accidentHipo;
      new Chart(el.getContext('2d'), {
        type: 'line',
        data: {
          labels: weeks,
          datasets: [
            lineDs('HIPO GR', ah[0], col.hipo_gr || '#E74C3C'),
            lineDs('HIPO NON GR', ah[1], col.hipo_non || '#F1C40F'),
            lineDs('NON HIPO', ah[2], col.non_hipo || '#27AE60'),
          ],
        },
        options: peerTrendChartCommonOpts(),
      });
    }

    var rateCanvases = [
      'peer-chart-line-rates-0',
      'peer-chart-line-rates-1',
      'peer-chart-line-rates-2',
      'peer-chart-line-rates-3',
    ];
    var rateColors = [col.ifr || '#7FB3B3', col.afr || '#1e3a5f', col.lti_fr || '#8B7355', col.lti_sr || '#C0392B'];
    if (payload.rates && Array.isArray(payload.rates)) {
      payload.rates.forEach(function (pts, idx) {
        var c = document.getElementById(rateCanvases[idx]);
        if (!c || !pts || !pts.length) return;
        var labels = weeks.length >= pts.length ? weeks.slice(0, pts.length) : weeks;
        if (pts.length !== labels.length && pts.length === 4) {
          labels = ['L4W', 'W13', 'W14', 'W15'];
        }
        new Chart(c.getContext('2d'), {
          type: 'line',
          data: {
            labels: labels,
            datasets: [
              {
                label: c.getAttribute('data-metric') || 'Rate',
                data: pts,
                borderColor: rateColors[idx] || '#64748b',
                backgroundColor: rateColors[idx] || '#64748b',
                borderWidth: 2.25,
                pointRadius: 3.5,
                pointHoverRadius: 6,
                pointBackgroundColor: '#fff',
                pointBorderColor: rateColors[idx] || '#64748b',
                pointBorderWidth: 2,
                tension: 0.38,
                fill: false,
              },
            ],
          },
          options: (function () {
            var o = peerTrendChartCommonOpts();
            o.plugins.legend = { display: false };
            var maxV = Math.max.apply(
              null,
              pts.map(function (x) {
                return Number(x) || 0;
              })
            );
            if (maxV > 500) {
              o.scales.y.ticks.callback = function (v) {
                return v;
              };
            }
            return o;
          })(),
        });
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPeerTrendPanelsCharts);
  } else {
    initPeerTrendPanelsCharts();
  }
})();
</script>
