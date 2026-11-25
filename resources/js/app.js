// Debounce helper
function debounce(fn, delay = 300) {
    let timer = null;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

// Close any open <details> dropdown (nav menus, mobile menu) when clicking outside
document.addEventListener('click', (event) => {
    document.querySelectorAll('details[open]').forEach((details) => {
        if (!details.contains(event.target)) {
            details.open = false;
        }
    });
});

// Searchable combobox for document pickers
function initSearchableSelects(root = document) {
    root.querySelectorAll('[data-searchable-select]').forEach((container) => {
        if (container.dataset.searchableInit) return;
        container.dataset.searchableInit = '1';

        const hidden = container.querySelector('[data-search-value]');
        const input = container.querySelector('[data-search-input]');
        const optionsBox = container.querySelector('[data-search-options]');
        const options = [...optionsBox.querySelectorAll('[data-value]')];

        const showOptions = () => {
            const term = input.value.trim().toLowerCase();
            let anyVisible = false;
            options.forEach((option) => {
                const match = !term || option.dataset.label.toLowerCase().includes(term);
                option.classList.toggle('hidden', !match);
                if (match) anyVisible = true;
            });
            optionsBox.classList.toggle('hidden', !anyVisible);
        };

        input.addEventListener('focus', showOptions);
        input.addEventListener('input', showOptions);

        options.forEach((option) => {
            option.addEventListener('click', () => {
                hidden.value = option.dataset.value;
                input.value = option.dataset.value ? option.dataset.label : '';
                optionsBox.classList.add('hidden');
            });
        });

        document.addEventListener('click', (event) => {
            if (!container.contains(event.target)) {
                optionsBox.classList.add('hidden');
            }
        });
    });
}

// Show/hide revocation field based on status
function initRevocationToggle() {
    const statusSelect = document.querySelector('[data-status-select]');
    const dicabutField = document.querySelector('[data-dicabut-dengan-field]');
    if (!statusSelect || !dicabutField) return;

    const sync = () => {
        const isActive = statusSelect.value === 'Berlaku';
        dicabutField.classList.toggle('hidden', isActive);
        if (isActive) {
            const hidden = dicabutField.querySelector('[data-search-value]');
            const input = dicabutField.querySelector('[data-search-input]');
            if (hidden) hidden.value = '';
            if (input) input.value = '';
        }
    };

    statusSelect.addEventListener('change', sync);
    sync();
}

// --- UNIVERSAL LIVE TABLE SEARCH ---
function initLiveTableSearch() {
    document.querySelectorAll('[data-live-search-form]').forEach((form) => {
        if (form.dataset.liveSearchInit) return;
        form.dataset.liveSearchInit = '1';

        const targetSelector = form.dataset.target;
        const targetContainer = targetSelector ? document.querySelector(targetSelector) : null;
        const input = form.querySelector('[data-live-search-input]');
        const clearBtn = form.querySelector('[data-live-search-clear]');
        const spinner = form.querySelector('[data-live-search-spinner]');
        let currentController = null;

        const updateClearBtn = () => {
            if (!clearBtn || !input) return;
            if (input.value.trim().length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        };

        const executeSearch = (customUrl = null) => {
            updateClearBtn();
            if (!targetContainer) return;

            if (currentController) {
                currentController.abort();
            }
            currentController = new AbortController();

            if (spinner) spinner.classList.remove('hidden');
            targetContainer.classList.add('opacity-40', 'pointer-events-none', 'transition-opacity', 'duration-150');

            let url;
            if (customUrl) {
                url = new URL(customUrl, window.location.origin);
            } else {
                const formData = new FormData(form);
                const params = new URLSearchParams();
                for (const [key, value] of formData.entries()) {
                    if (value !== '' && value !== null) {
                        params.append(key, value);
                    }
                }
                const action = form.getAttribute('action') || window.location.pathname;
                url = new URL(action, window.location.origin);
                url.search = params.toString();
            }

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: currentController.signal,
            })
            .then((response) => {
                if (!response.ok) throw new Error('Search failed');
                return response.text();
            })
            .then((html) => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector(targetSelector);
                if (newContent) {
                    targetContainer.innerHTML = newContent.innerHTML;
                }
                window.history.replaceState({}, '', url.toString());
                bindPaginationClicks();
            })
            .catch((err) => {
                if (err.name !== 'AbortError') {
                    console.error('Live search error:', err);
                }
            })
            .finally(() => {
                if (spinner) spinner.classList.add('hidden');
                targetContainer.classList.remove('opacity-40', 'pointer-events-none');
            });
        };

        const debouncedSearch = debounce(() => executeSearch(), 300);

        if (input) {
            input.addEventListener('input', () => {
                updateClearBtn();
                debouncedSearch();
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    executeSearch();
                }
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                if (input) {
                    input.value = '';
                    input.focus();
                }
                updateClearBtn();
                executeSearch();
            });
        }

        form.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', () => executeSearch());
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            executeSearch();
        });

        const bindPaginationClicks = () => {
            if (!targetContainer) return;
            targetContainer.querySelectorAll('a[href]').forEach((link) => {
                if (link.closest('nav[role="navigation"]') || link.href.includes('page=')) {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        executeSearch(link.href);
                    });
                }
            });
        };

        bindPaginationClicks();
        updateClearBtn();
    });
}

// --- HERO LIVE SEARCH DROPDOWN (Public Portal) ---
function initPublicLiveSearchDropdown() {
    const container = document.querySelector('[data-public-livesearch]');
    if (!container || container.dataset.publicSearchInit) return;
    container.dataset.publicSearchInit = '1';

    const input = container.querySelector('[data-public-search-input]');
    const dropdown = container.querySelector('[data-public-search-dropdown]');
    const resultsBox = container.querySelector('[data-public-search-results]');
    const spinner = container.querySelector('[data-public-search-spinner]');
    const clearBtn = container.querySelector('[data-public-search-clear]');
    const form = input?.closest('form');

    let currentController = null;
    let selectedIndex = -1;

    const hideDropdown = () => {
        if (dropdown) dropdown.classList.add('hidden');
        selectedIndex = -1;
    };

    const showDropdown = () => {
        if (dropdown && input && input.value.trim().length > 0) {
            dropdown.classList.remove('hidden');
        }
    };

    const updateClearBtn = () => {
        if (!clearBtn || !input) return;
        if (input.value.trim().length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    };

    const highlightMatch = (text, query) => {
        if (!query) return escapeHtml(text);
        const safeQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${safeQuery})`, 'gi');
        return escapeHtml(text).replace(regex, '<mark class="bg-amber-100 text-amber-900 font-semibold px-0.5 rounded">$1</mark>');
    };

    const escapeHtml = (str) => {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    const fetchSuggestions = () => {
        const query = input.value.trim();
        updateClearBtn();

        if (query.length === 0) {
            hideDropdown();
            return;
        }

        if (currentController) currentController.abort();
        currentController = new AbortController();

        showDropdown();
        if (spinner) spinner.classList.remove('hidden');

        const jenis = form?.querySelector('select[name="jenis"]')?.value || '';
        const tahun = form?.querySelector('select[name="tahun"]')?.value || '';

        const params = new URLSearchParams({
            format: 'json',
            q: query,
        });
        if (jenis) params.append('jenis', jenis);
        if (tahun) params.append('tahun', tahun);

        fetch(`/?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: currentController.signal,
        })
        .then((res) => res.json())
        .then((data) => {
            renderResults(data, query);
        })
        .catch((err) => {
            if (err.name !== 'AbortError') {
                console.error('Suggestion error:', err);
            }
        })
        .finally(() => {
            if (spinner) spinner.classList.add('hidden');
        });
    };

    const renderResults = (data, query) => {
        selectedIndex = -1;
        const results = data.results || [];
        const total = data.total || 0;

        if (results.length === 0) {
            resultsBox.innerHTML = `
                <div class="px-6 py-8 text-center text-slate-500">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-800">Tidak ada dokumen yang cocok</p>
                    <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci lain seperti nomor, perihal, atau jenis dokumen</p>
                </div>
            `;
            return;
        }

        let html = '<div class="py-1 divide-y divide-slate-100 max-h-[380px] overflow-y-auto overflow-x-hidden" role="listbox">';
        results.forEach((doc, idx) => {
            const isBerlaku = doc.status === 'Berlaku';
            const statusBadge = isBerlaku
                ? '<span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Berlaku</span>'
                : '<span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-[11px] font-semibold text-rose-700 ring-1 ring-inset ring-rose-600/20"><span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>Tidak Berlaku</span>';

            html += `
                <a href="${doc.url}" data-result-item data-index="${idx}"
                    class="group flex items-center justify-between gap-3.5 px-4 py-3.5 hover:bg-blue-50/80 transition-colors cursor-pointer text-left focus:outline-none focus:bg-blue-50">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-0.5 text-[11px] font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/15">
                                ${escapeHtml(doc.tipe_dokumen)}
                            </span>
                            ${statusBadge}
                            <span class="text-xs text-slate-400 font-medium">· Nomor ${escapeHtml(doc.nomor)} (${doc.tahun})</span>
                        </div>
                        <h4 class="text-sm font-semibold text-slate-900 group-hover:text-blue-700 leading-snug break-words">
                            ${highlightMatch(doc.judul, query)}
                        </h4>
                    </div>
                    <div class="shrink-0 flex items-center pl-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-700 transition cursor-pointer">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </a>
            `;
        });
        html += '</div>';

        html += `
            <div class="border-t border-slate-100 bg-slate-50/90 px-4 py-3 flex items-center justify-between text-xs text-slate-600">
                <span>Ditemukan <strong class="text-slate-800 font-semibold">${total}</strong> dokumen</span>
                <button type="submit" class="font-semibold text-blue-700 hover:text-blue-900 hover:underline inline-flex items-center gap-1 cursor-pointer">
                    Lihat semua hasil di katalog →
                </button>
            </div>
        `;

        resultsBox.innerHTML = html;
    };

    const debouncedFetch = debounce(fetchSuggestions, 250);

    input?.addEventListener('input', debouncedFetch);
    input?.addEventListener('focus', () => {
        if (input.value.trim().length > 0) {
            showDropdown();
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            input.value = '';
            updateClearBtn();
            hideDropdown();
            input.focus();
        });
    }

    // Keyboard navigation
    input?.addEventListener('keydown', (e) => {
        const items = resultsBox?.querySelectorAll('[data-result-item]') || [];
        if (dropdown?.classList.contains('hidden') || items.length === 0) {
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            updateSelection(items);
        } else if (e.key === 'Enter') {
            if (selectedIndex >= 0 && items[selectedIndex]) {
                e.preventDefault();
                items[selectedIndex].click();
            }
        } else if (e.key === 'Escape') {
            hideDropdown();
        }
    });

    const updateSelection = (items) => {
        items.forEach((item, idx) => {
            if (idx === selectedIndex) {
                item.classList.add('bg-blue-50', 'ring-1', 'ring-inset', 'ring-blue-400');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('bg-blue-50', 'ring-1', 'ring-inset', 'ring-blue-400');
            }
        });
    };

    document.addEventListener('click', (e) => {
        if (!container.contains(e.target)) {
            hideDropdown();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initSearchableSelects();
    initRevocationToggle();
    initLiveTableSearch();
    initPublicLiveSearchDropdown();
});
