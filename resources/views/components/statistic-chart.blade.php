{{--
    Komponen untuk menampilkan grafik statistik bulanan (Line Chart).
    Menerima properti $chartData yang berisi koleksi data bulanan.
--}}
@props(['chartData'])

<div class="h-80">
    <canvas id="statisticChart"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartData = @json($chartData);

    const labels = chartData.map(item => item.month);
    const incomeData = chartData.map(item => item.income);
    const expenseData = chartData.map(item => item.expense);

    const ctx = document.getElementById('statisticChart').getContext('2d');

    const incomeGradient = ctx.createLinearGradient(0, 0, 0, 300);
    incomeGradient.addColorStop(0, 'rgba(75, 192, 192, 0.5)');
    incomeGradient.addColorStop(1, 'rgba(75, 192, 192, 0)');

    const expenseGradient = ctx.createLinearGradient(0, 0, 0, 300);
    expenseGradient.addColorStop(0, 'rgba(255, 99, 132, 0.5)');
    expenseGradient.addColorStop(1, 'rgba(255, 99, 132, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Earnings',
                    data: incomeData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: incomeGradient,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(75, 192, 192, 1)',
                },
                {
                    label: 'Expenses',
                    data: expenseData,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: expenseGradient,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(255, 99, 132, 1)',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)',
                        callback: function(value) {
                            // Format angka menjadi lebih singkat (e.g., 1000 -> 1k)
                            if (value >= 1000000) return (value / 1000000) + 'M';
                            if (value >= 1000) return (value / 1000) + 'k';
                            return value;
                        }
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: 'rgba(255, 255, 255, 0.7)'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: 'rgba(255, 255, 255, 0.9)',
                        font: {
                            size: 14
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { size: 16 },
                    bodyFont: { size: 14 },
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>
