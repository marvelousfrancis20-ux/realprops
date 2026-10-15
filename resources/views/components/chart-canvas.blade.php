@props(['id', 'type' => 'bar', 'labels' => [], 'values' => [], 'label' => 'Value', 'color' => '#0F766E', 'height' => 260, 'currency' => false, 'horizontal' => false])

@php
    $isCircle = in_array($type, ['doughnut', 'pie'], true);
@endphp

<div class="relative w-full" style="height: {{ (int) $height }}px">
    <canvas id="{{ $id }}" role="img" aria-label="{{ $label }} chart"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var canvas = document.getElementById(@json($id));
        if (!canvas || !window.Chart) return;

        var isDark = document.documentElement.classList.contains('dark');
        var grid = isDark ? 'rgba(148,163,184,0.15)' : 'rgba(15,23,42,0.08)';
        var text = isDark ? '#cbd5e1' : '#64748b';
        var isCircle = @json($isCircle);
        var isCurrency = @json($currency);

        var config = {
            type: @json($type),
            data: {
                labels: @json($labels),
                datasets: [{
                    label: @json($label),
                    data: @json($values),
                    backgroundColor: isCircle
                        ? ['#0F766E', '#F59E0B', '#0EA5E9', '#8B5CF6', '#EF4444', '#10B981']
                        : (@json($type) === 'line' ? 'rgba(15,118,110,0.15)' : @json($color)),
                    borderColor: isCircle ? '#ffffff' : @json($color),
                    borderWidth: isCircle ? 2 : 2,
                    fill: @json($type) === 'line',
                    tension: 0.35,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: isCircle ? '62%' : undefined,
                plugins: {
                    legend: isCircle ? {
                        position: 'bottom',
                        labels: { color: text, boxWidth: 12, font: { size: 11 } }
                    } : { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                var v = isCircle ? ctx.parsed : (ctx.parsed.y !== undefined ? ctx.parsed.y : ctx.parsed.x);
                                v = Number(v);
                                return isCurrency ? '₦' + v.toLocaleString() : v.toLocaleString();
                            }
                        }
                    }
                },
                scales: isCircle ? {} : {
                    x: {
                        ticks: { color: text },
                        grid: { display: false },
                        beginAtZero: true
                    },
                    y: { beginAtZero: true, ticks: { color: text }, grid: { color: grid } }
                },
                indexAxis: @json($horizontal) ? 'y' : 'x'
            }
        };

        new Chart(canvas, config);
    });
</script>
