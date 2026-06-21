import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('premiumImagePanel', (initialState = 'locked') => ({
        uiState: initialState,
        status: 'none',
        errorMessage: null,
        assetUrl: null,
        houseDescription: null,
        shareText: null,
        copied: false,
        loading: false,
        pollTimer: null,

        init() {
            if (this.uiState === 'generating' || new URLSearchParams(window.location.search).get('generating')) {
                this.uiState = 'generating';
                this.startPolling();
            }
        },

        async generate() {
            this.loading = true;
            try {
                const response = await this.request('POST', window.premiumRoutes.generate);
                const data = await response.json();
                this.applyStatus(data);
                if (this.uiState === 'generating') {
                    this.startPolling();
                }
            } catch (error) {
                this.errorMessage = 'Não foi possível iniciar a geração. Tente novamente.';
                this.uiState = 'failed';
            } finally {
                this.loading = false;
            }
        },

        async retry() {
            this.loading = true;
            try {
                const response = await this.request('POST', window.premiumRoutes.retry);
                const data = await response.json();
                this.applyStatus(data);
                this.startPolling();
            } catch (error) {
                this.errorMessage = 'Não foi possível tentar novamente.';
            } finally {
                this.loading = false;
            }
        },

        startPolling() {
            this.stopPolling();
            this.pollTimer = setInterval(() => this.fetchStatus(), 3000);
            this.fetchStatus();
        },

        stopPolling() {
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
                this.pollTimer = null;
            }
        },

        async fetchStatus() {
            try {
                const response = await fetch(window.premiumRoutes.status, {
                    headers: { Accept: 'application/json' },
                });
                const data = await response.json();
                this.applyStatus(data);
                if (['completed', 'failed', 'none'].includes(this.uiState) && this.uiState !== 'generating') {
                    this.stopPolling();
                }
            } catch (error) {
                // mantém polling
            }
        },

        applyStatus(data) {
            this.status = data.status ?? 'none';
            this.errorMessage = data.error_message ?? null;
            this.assetUrl = data.asset?.url ?? null;
            this.houseDescription = data.house_description ?? null;
            this.shareText = data.share_text ?? null;

            if (this.status === 'completed') {
                this.uiState = 'completed';
                this.stopPolling();
            } else if (this.status === 'failed') {
                this.uiState = 'failed';
                this.stopPolling();
            } else if (['pending', 'processing'].includes(this.status)) {
                this.uiState = 'generating';
            }
        },

        async request(method, url) {
            const response = await fetch(url, {
                method,
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
            });

            if (!response.ok) {
                throw new Error('request failed');
            }

            return response;
        },

        async copyShare() {
            if (!this.shareText) {
                return;
            }

            await navigator.clipboard.writeText(this.shareText);
            this.copied = true;
            setTimeout(() => { this.copied = false; }, 2000);
        },
    }));
});

Alpine.start();
