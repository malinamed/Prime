<template>
    <div class="app-shell">
        <!-- Topbar -->
        <header class="topbar">
            <a href="/" class="topbar__brand">
                <div class="topbar__logo-mark">PI</div>
                <span class="topbar__brand-name">Prime<span>Investor</span></span>
            </a>

            <div class="topbar__right">
                <nav class="topbar__nav">
                    <a href="#" class="topbar__nav-link active">Dashboard</a>
                    <a href="#" class="topbar__nav-link">Portfolio</a>
                    <a href="#" class="topbar__nav-link">Reports</a>
                    <a href="#" class="topbar__nav-link">Settings</a>
                </nav>

                <div class="topbar__client">
                    <div class="topbar__avatar">AC</div>
                    <div class="topbar__client-info">
                        <span class="topbar__client-label">Welcome</span>
                        <span class="topbar__client-name">Alex Constantin</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="layout">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar__section-label">Overview</div>
                <a href="#" class="sidebar__item active">
                    <svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                    </svg>
                    Dashboard
                </a>
                <a href="#" class="sidebar__item">
                    <svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>
                    </svg>
                    Market Data
                </a>
                <a href="#" class="sidebar__item">
                    <svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/>
                    </svg>
                    History
                </a>

                <div class="sidebar__section-label">Assets</div>
                <a href="#" class="sidebar__item">
                    <svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/>
                    </svg>
                    Indices
                </a>
                <a href="#" class="sidebar__item">
                    <svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>
                    </svg>
                    Crypto
                </a>
                <a href="#" class="sidebar__item">
                    <svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                    Currency
                </a>

                <div class="sidebar__section-label">System</div>
                <a href="#" class="sidebar__item">
                    <svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>
                    </svg>
                    API Status
                </a>
            </aside>

            <!-- Main -->
            <main class="main-content">
                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-header__title">Market Watch Dashboard</h1>
                    <p class="page-header__subtitle">
                        Real-time financial data aggregated from global market sources.
                    </p>
                    <div class="page-header__meta">
                        <span v-if="hasData" class="badge badge--live">
                            <span class="live-dot"></span>
                            Live Cache
                        </span>
                        <span v-else class="badge badge--pending">Cache Pending</span>

                        <span v-if="hasMockIndices" class="badge badge--mock">
                            Indices: Demo Data
                        </span>

                        <span v-if="syncedAt" class="page-header__subtitle" style="margin-top:0;">
                            Last sync: {{ formattedSyncTime }}
                        </span>
                    </div>
                </div>

                <!-- No data state -->
                <div v-if="!hasData" class="card" style="max-width:520px;">
                    <div class="card__body">
                        <div class="empty-state">
                            <div class="empty-state__icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                            </div>
                            <div class="empty-state__title">Data Sync Pending</div>
                            <p class="empty-state__desc">
                                The Redis cache has not been populated yet. Run the initial sync command inside the application container:
                            </p>
                            <code class="empty-state__cmd">php artisan market:sync</code>
                            <p class="empty-state__desc" style="margin-top:0;">
                                The scheduler will then refresh data every 15 minutes automatically.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Cards -->
                <div v-else class="dashboard-grid">

                    <!-- Card 1: Market Indicators -->
                    <div class="card">
                        <div class="card__header">
                            <div class="card__header-left">
                                <div class="card__icon card__icon--blue">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="card__title">Market Indicators</div>
                                    <div class="card__subtitle">S&P 500 and Nasdaq-100 proxies (ETFs)</div>
                                </div>
                            </div>
                            <span v-if="hasMockIndices" class="badge badge--mock">Demo</span>
                            <span v-else class="badge badge--live">
                                <span class="live-dot"></span> Live
                            </span>
                        </div>
                        <div class="card__body">
                            <div
                                v-for="(index, symbol) in indices"
                                :key="symbol"
                                class="data-row"
                            >
                                <div class="data-row__left">
                                    <span class="data-row__label">{{ index.label }}</span>
                                    <span class="data-row__sublabel">{{ index.symbol }}</span>
                                </div>
                                <div class="data-row__right">
                                    <span class="data-row__value data-row__value--lg">
                                        ${{ formatPrice(index.price) }}
                                    </span>
                                    <span
                                        class="data-row__change"
                                        :class="changeClass(index.change)"
                                    >
                                        {{ index.change >= 0 ? '+' : '' }}{{ index.change.toFixed(2) }}
                                        ({{ index.change_percent >= 0 ? '+' : '' }}{{ index.change_percent.toFixed(2) }}%)
                                    </span>
                                </div>
                            </div>

                            <div v-if="!indicesAvailable" class="empty-state" style="padding:1.5rem 0;">
                                <p class="empty-state__desc">No index data available.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Currency Watch -->
                    <div class="card">
                        <div class="card__header">
                            <div class="card__header-left">
                                <div class="card__icon card__icon--gold">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="card__title">Currency Watch (RON)</div>
                                    <div class="card__subtitle">Major currencies vs Romanian Leu</div>
                                </div>
                            </div>
                            <span class="badge badge--live">
                                <span class="live-dot"></span> Live
                            </span>
                        </div>
                        <div class="card__body">
                            <div
                                v-for="(entry, code) in currencies"
                                :key="code"
                                class="data-row"
                            >
                                <div class="data-row__left">
                                    <span class="data-row__label">{{ entry.label }}</span>
                                    <div class="currency-pair">
                                        <span>{{ entry.from }}</span>
                                        <span class="currency-pair__arrow">to</span>
                                        <span>{{ entry.to }}</span>
                                    </div>
                                </div>
                                <div class="data-row__right">
                                    <span class="data-row__value data-row__value--lg">
                                        {{ entry.rate.toFixed(4) }}
                                    </span>
                                    <span class="data-row__sublabel">RON per 1 {{ entry.from }}</span>
                                </div>
                            </div>

                            <div v-if="!currenciesAvailable" class="empty-state" style="padding:1.5rem 0;">
                                <p class="empty-state__desc">No currency data available.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Crypto Assets -->
                    <div class="card">
                        <div class="card__header">
                            <div class="card__header-left">
                                <div class="card__icon card__icon--purple">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.46 2.5 2.5 0 0 1 .46-4.96zm0 0"/>
                                        <path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.46 2.5 2.5 0 0 0-.46-4.96z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="card__title">Crypto Assets</div>
                                    <div class="card__subtitle">Bitcoin and Ethereum spot prices</div>
                                </div>
                            </div>
                            <span class="badge badge--live">
                                <span class="live-dot"></span> Live
                            </span>
                        </div>
                        <div class="card__body">
                            <div
                                v-for="(asset, symbol) in crypto"
                                :key="symbol"
                                class="data-row"
                            >
                                <div class="data-row__left">
                                    <span class="data-row__label">{{ asset.name }}</span>
                                    <span class="data-row__sublabel">{{ asset.symbol }}</span>
                                </div>
                                <div class="data-row__right">
                                    <span class="data-row__value data-row__value--lg">
                                        ${{ formatPrice(asset.price_usd) }}
                                    </span>
                                    <span
                                        class="data-row__change"
                                        :class="changeClass(asset.change_24h)"
                                    >
                                        {{ asset.change_24h >= 0 ? '+' : '' }}{{ asset.change_24h.toFixed(2) }}% (24h)
                                    </span>
                                </div>
                            </div>

                            <div v-if="!cryptoAvailable" class="empty-state" style="padding:1.5rem 0;">
                                <p class="empty-state__desc">No crypto data available.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="dashboard-footer" v-if="hasData">
                    <div class="footer__sources">
                        <span class="footer__source-tag">Frankfurter API</span>
                        <span class="footer__source-tag">CoinGecko API</span>
                        <span class="footer__source-tag">Alpha Vantage API</span>
                        <span class="footer__source-tag">Redis Cache</span>
                    </div>
                    <span>Data refreshes every 15 minutes via background scheduler</span>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    marketData: {
        type: Object,
        default: null,
    },
});

const hasData        = computed(() => props.marketData !== null);
const indices        = computed(() => props.marketData?.indices    ?? {});
const currencies     = computed(() => props.marketData?.currencies ?? {});
const crypto         = computed(() => props.marketData?.crypto     ?? {});
const syncedAt       = computed(() => props.marketData?.synced_at  ?? null);

const indicesAvailable    = computed(() => Object.keys(indices.value).length > 0);
const currenciesAvailable = computed(() => Object.keys(currencies.value).length > 0);
const cryptoAvailable     = computed(() => Object.keys(crypto.value).length > 0);

const hasMockIndices = computed(() => {
    return Object.values(indices.value).some(i => i.is_mock === true);
});

const formattedSyncTime = computed(() => {
    if (!syncedAt.value) return '';
    try {
        return new Date(syncedAt.value).toLocaleString('en-GB', {
            day:    '2-digit',
            month:  'short',
            year:   'numeric',
            hour:   '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    } catch {
        return syncedAt.value;
    }
});

function formatPrice(value) {
    if (value === null || value === undefined) return '—';
    const num = parseFloat(value);
    if (num >= 1000) {
        return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    return num.toFixed(2);
}

function changeClass(value) {
    const v = parseFloat(value);
    if (v > 0) return 'data-row__change--positive';
    if (v < 0) return 'data-row__change--negative';
    return 'data-row__change--neutral';
}
</script>
