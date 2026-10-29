// Close any open <details> dropdown (nav menus, mobile menu) when the user
// clicks outside of it -- native <details> has no built-in "click outside to
// close" behavior.
document.addEventListener('click', (event) => {
    document.querySelectorAll('details[open]').forEach((details) => {
        if (!details.contains(event.target)) {
            details.open = false;
        }
    });
});

// Searchable combobox: a text input + hidden id input + a filtered list of
// buttons. Used for the "mencabut" / "dicabut dengan" document pickers in
// the admin document form, where the plain <select> becomes unusable once
// there are dozens of documents to scroll through.
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

// Show/hide "dicabut dengan" based on selected status: an active ("Berlaku")
// document can't already be revoked-by another one, so that field only
// makes sense once status is "Tidak Berlaku". "Mencabut" stays available
// either way (an active document can still supersede an older one).
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

document.addEventListener('DOMContentLoaded', () => {
    initSearchableSelects();
    initRevocationToggle();
});
