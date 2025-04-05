@extends('layouts.layout')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.statistics.profit') }}" class="btn btn-secondary">
        ⬅️ Quay về thống kê tổng quan
    </a>
</div>
<canvas id="monthlyProfitChart" height="100"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    async function loadMonthlyProfitChart() {
        const res = await fetch('{{ route('admin.statistics.monthlyProfitChart') }}');
        const data = await res.json();

        const labels = data.map(item => item.month);
        const profits = data.map(item => item.profit);

        const ctx = document.getElementById('monthlyProfitChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Lợi nhuận theo tháng',
                    data: profits,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', loadMonthlyProfitChart);
</script>
@endsection