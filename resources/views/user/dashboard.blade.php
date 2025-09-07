@extends('layouts.app')

@section('content')
{{--
    The Alpine.js manager for the "Add Transaction" modal functionality is retained
    even though we are overhauling the display.
--}}
<div x-data="transactionsManager()">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <h1 class="text-3xl font-bold text-white mb-4 sm:mb-0">My Dashboard</h1>
        {{-- This button still calls the Alpine.js function to open the modal --}}
        <x-button @click="openAddModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add Transaction
        </x-button>
    </div>

    <!-- Notifications (Retained) -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 5000)"
            class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg relative mb-6">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 5000)"
            class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg relative mb-6">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- New Main Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- === LEFT COLUMN (Main Content) === -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Balance Summary Card -->
            <x-card-glass>
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-white/10">
                    <div class="p-4 text-center">
                        <p class="text-sm text-gray-400">Total Balance</p>
                        <p class="text-2xl font-bold text-white mt-1">${{ number_format($totalBalance, 2) }}</p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-sm text-gray-400">Earnings (This Month)</p>
                        <p class="text-2xl font-bold text-green-400 mt-1">${{ number_format($totalIncome, 2) }}</p>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-sm text-gray-400">Expenses (This Month)</p>
                        <p class="text-2xl font-bold text-red-400 mt-1">${{ number_format($totalExpense, 2) }}</p>
                    </div>
                </div>
            </x-card-glass>

            <!-- Statistics Card -->
            <x-card-glass class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-white">Statistics</h3>
                    <a href="{{-- route('reports.index') --}}" class="text-sm text-gray-400 hover:text-white">View All &rarr;</a>
                </div>
                <!-- [FIX] Menambahkan div pembungkus dengan tinggi tetap untuk chart -->
                <div class="relative h-64 md:h-80">
                    <canvas id="statisticsChart"></canvas>
                </div>
            </x-card-glass>

            <!-- [DIUBAH] Financial Insights Card -->
            <x-card-glass class="p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Financial Insights</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">

                    <!-- Budget Progress Section -->
                    <div class="lg:col-span-2">
                        <h4 class="font-semibold text-gray-300 mb-3">Monthly Budget Progress</h4>
                        <div class="space-y-4">
                            @forelse ($budgetProgress as $budget)
                                @php
                                    // Logika untuk warna progress bar
                                    $progressColor = 'bg-blue-500'; // Default
                                    if ($budget['percentage'] > 75 && $budget['percentage'] <= 90) {
                                        $progressColor = 'bg-yellow-500';
                                    } elseif ($budget['percentage'] > 90) {
                                        $progressColor = 'bg-red-500';
                                    }
                                @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-300">{{ $budget['category_name'] }}</span>
                                        <span class="text-white font-medium">${{ number_format($budget['spent_amount'], 0) }} / ${{ number_format($budget['budget_amount'], 0) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-700/50 rounded-full h-2.5">
                                        <div class="{{ $progressColor }} h-2.5 rounded-full" style="width: {{ $budget['percentage'] }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-sm text-gray-500">You haven't set any budgets for this month.</p>
                                    <a href="{{-- route('budgets.index') --}}" class="text-blue-400 hover:underline text-sm">Create a Budget</a>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Financial Health Section -->
                    <div class="flex flex-col items-center justify-center text-center">
                         <h4 class="font-semibold text-gray-300 mb-3 md:hidden">Financial Health</h4> <!-- Hanya tampil di mobile -->
                        <div x-data="savingsRateChart({{ $financialHealthMetrics['savings_rate'] }})" x-init="renderChart()" class="relative w-32 h-32">
                            <canvas id="savingsRateCanvas"></canvas>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">{{ $financialHealthMetrics['savings_rate'] }}%</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-400 mt-2">Savings Rate</p>
                        <p class="text-xs text-gray-500">(This Month)</p>
                    </div>
                </div>
            </x-card-glass>

        </div>

        <!-- === RIGHT COLUMN (Side Info) === -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Profile & Credit Card -->
            <x-card-glass class="p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white">Profile</h3>
                    <a href="{{ route('profile.edit') }}" class="text-gray-400 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
                <div class="text-center mt-4">
                    <img class="w-24 h-24 rounded-full mx-auto border-2 border-white/20 object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                    <h4 class="text-lg font-semibold text-white mt-3">{{ Auth::user()->name }}</h4>
                    <p class="text-sm text-gray-400">Exclusive Card</p>
                </div>

                @if ($defaultCard)
                    <div class="mt-6 p-4 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 text-white shadow-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs">Expires</p>
                                <p class="font-medium">{{ $defaultCard->expiry_date }}</p>
                            </div>
                            <p class="text-2xl font-bold italic">{{ $defaultCard->card_type }}</p>
                        </div>
                        <div class="mt-8 text-right">
                            <p class="text-xs">Card Number</p>
                            <p class="text-xl font-semibold font-mono tracking-widest">•••• {{ $defaultCard->card_number }}</p>
                        </div>
                    </div>
                @else
                    <div class="mt-6 p-4 rounded-xl bg-gray-700/50 text-center text-gray-400">
                        <p>No card added yet.</p>
                        <a href="{{-- route('cards.index') --}}" class="text-sm text-blue-400 hover:underline">Add a card now</a>
                    </div>
                @endif
            </x-card-glass>

            <!-- Monthly Transaction Card -->
            <x-card-glass class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-white">Recent Transactions</h3>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-gray-400 hover:text-white">See All &rarr;</a>
                </div>
                <div class="space-y-4">
                    @forelse ($recentTransactions as $transaction)
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-white">{{ Str::limit($transaction->description, 20) }}</p>
                                <p class="text-sm text-gray-400">{{ $transaction->transaction_date->format('d M, H:i') }}</p>
                            </div>
                            <p class="font-bold {{ $transaction->type == 'income' ? 'text-green-400' : 'text-red-400' }}">
                                {{ $transaction->type == 'income' ? '+$' : '-$' }}{{ number_format($transaction->amount, 2) }}
                            </p>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center py-4">No transactions this month.</p>
                    @endforelse
                </div>
            </x-card-glass>
        </div>
    </div>

    {{-- Modal section for transactions (Retained) --}}
    @php
        // Pastikan variabel ini ada, bahkan jika kosong
        $accounts = $accounts ?? collect();
        $categories = $categories ?? collect();
        $firstAccountId = $accounts->first()->id ?? '';
    @endphp
    <x-modal show="showFormModal" title="">
        <x-slot name="title">
            <span x-text="formState.id ? 'Edit Transaction' : 'Add New Transaction'"></span>
        </x-slot>
        @include('user.transactions.partials.form-transaction', [
            'accounts' => $accounts,
            'categories' => $categories,
            'goals' => $goals,
        ])
    </x-modal>
</div>

@endsection

{{-- Alpine.js and Chart.js scripts --}}
@push('scripts')
<script>
// --- Chart untuk Statistik Bulanan ---
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('statisticsChart');
    if (ctx) {
        const chartData = @json($monthlySummary);
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.map(d => d.month),
                datasets: [{
                    label: 'Income',
                    data: chartData.map(d => d.income),
                    backgroundColor: 'rgba(74, 222, 128, 0.5)',
                    borderColor: 'rgba(74, 222, 128, 1)',
                    borderWidth: 1
                }, {
                    label: 'Expense',
                    data: chartData.map(d => d.expense),
                    backgroundColor: 'rgba(248, 113, 113, 0.5)',
                    borderColor: 'rgba(248, 113, 113, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#9ca3af' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    },
                    x: {
                        ticks: { color: '#9ca3af' },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: '#d1d5db' }
                    }
                }
            }
        });
    }
});


// --- Alpine.js Component untuk Savings Rate Donut Chart ---
function savingsRateChart(rate) {
    return {
        rate: rate,
        renderChart() {
            const canvas = document.getElementById('savingsRateCanvas');
            if (!canvas) return;

            // Pastikan rate berada dalam rentang 0-100
            const displayRate = Math.max(0, Math.min(100, rate));
            const remaining = 100 - displayRate;

            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [displayRate, remaining],
                        backgroundColor: [
                            '#22c55e', // green-500
                            'rgba(255, 255, 255, 0.1)'
                        ],
                        borderColor: 'transparent',
                        borderWidth: 0,
                        cutout: '80%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });
        }
    }
}

// --- Alpine.js Component untuk Modal Transaksi ---
function transactionsManager() {
    const oldInput = @json(session()->getOldInput());
    const formErrors = {{ $errors->any() ? 'true' : 'false' }};
    const sessionFormType = '{{ session('form_type') ?? '' }}';
    const defaultAccountId = '{{ $firstAccountId }}';

    return {
        showFormModal: formErrors && sessionFormType === 'add_transaction',
        formState: {
            id: null, type: 'expense', account_id: defaultAccountId, category_id: '',
            amount: '', description: '', transaction_date: new Date().toISOString().slice(0, 16),
            transfer_to_account_id: null, goal_id: '',
        },
        openAddModal() {
            this.formState = {
                id: null, type: 'expense', account_id: defaultAccountId, category_id: '',
                amount: '', description: '', transaction_date: new Date().toISOString().slice(0, 16),
                transfer_to_account_id: null, goal_id: '',
            };
            if (formErrors && sessionFormType === 'add_transaction') {
                this.formState = { ...this.formState, ...oldInput };
            }
            this.showFormModal = true;
        },
        closeModals() {
            this.showFormModal = false;
        }
    };
}
</script>
@endpush
