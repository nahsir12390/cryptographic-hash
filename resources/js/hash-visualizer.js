const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

async function postJson(url, body) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken ?? '',
        },
        body: JSON.stringify(body),
    });

    const data = await response.json();

    if (!response.ok) {
        throw new Error(data.message ?? 'Request failed');
    }

    return data;
}

function el(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
}

function renderError(container, message) {
    container.replaceChildren();
    container.append(el('p', 'animate-fade-in-up text-sm text-red-400', message));
}

/** Toggles a button into/out of a disabled, spinning "busy" state. */
function setBusy(button, busy) {
    if (!button) return;
    button.disabled = busy;
    button.classList.toggle('opacity-60', busy);

    const spinnerClass = 'inline-block h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white';
    let spinner = button.querySelector('[data-spinner]');

    if (busy && !spinner) {
        spinner = el('span', spinnerClass);
        spinner.dataset.spinner = '1';
        button.prepend(spinner);
    } else if (!busy && spinner) {
        spinner.remove();
    }
}

function addCopyButton(row, text) {
    const button = el('button', 'ml-auto shrink-0 rounded-md border border-white/10 px-2 py-1 text-xs text-[#a1a09a] transition-colors duration-150 hover:border-white/25 hover:text-[#EDEDEC]', 'Copy');
    button.type = 'button';
    button.addEventListener('click', async () => {
        await navigator.clipboard.writeText(text);
        button.textContent = 'Copied!';
        button.classList.add('text-emerald-400');
        setTimeout(() => {
            button.textContent = 'Copy';
            button.classList.remove('text-emerald-400');
        }, 1500);
    });
    row.append(button);
}

function initGenerator() {
    const textInput = document.getElementById('generator-text');
    const button = document.getElementById('generate-btn');
    const results = document.getElementById('generator-results');

    button?.addEventListener('click', async () => {
        const algorithms = Array.from(document.querySelectorAll('.generator-algorithm:checked')).map((c) => c.value);

        if (algorithms.length === 0) {
            renderError(results, 'Select at least one algorithm.');
            return;
        }

        setBusy(button, true);
        try {
            const data = await postJson('/visualizer/generate', {
                text: textInput.value,
                algorithms,
            });

            results.replaceChildren();
            Object.values(data.results).forEach((result, index) => {
                const row = el('div', 'animate-fade-in-up flex items-center gap-3 rounded-lg border border-white/5 bg-black/20 p-3 text-sm font-mono break-all');
                row.style.animationDelay = `${index * 60}ms`;
                row.append(el('span', 'shrink-0 text-[#a1a09a]', `${result.label}:`));
                row.append(el('span', 'truncate', result.digest));
                addCopyButton(row, result.digest);
                results.append(row);
            });
        } catch (error) {
            renderError(results, error.message);
        } finally {
            setBusy(button, false);
        }
    });
}

function renderBitGrid(diff) {
    const grid = el('div', 'flex flex-wrap gap-[3px]');
    diff.bits.forEach((bit, index) => {
        const cell = el('span', `animate-pop-in h-3 w-3 rounded-sm transition-transform duration-150 hover:scale-150 ${bit.same ? 'bg-emerald-500/70 shadow-sm shadow-emerald-500/40' : 'bg-red-500 shadow-sm shadow-red-500/50'}`);
        cell.style.animationDelay = `${Math.min(index * 4, 600)}ms`;
        cell.title = `Bit ${index}: A=${bit.a} B=${bit.b}`;
        grid.append(cell);
    });
    return grid;
}

function renderPercentageBar(percentage) {
    const wrapper = el('div', 'space-y-1');
    const track = el('div', 'h-2 w-full overflow-hidden rounded-full bg-white/5');
    const fill = el('div', 'h-full w-0 rounded-full bg-gradient-to-r from-indigo-500 to-fuchsia-500 transition-[width] duration-700 ease-out');
    track.append(fill);
    wrapper.append(track);

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            fill.style.width = `${percentage}%`;
        });
    });

    return wrapper;
}

function initCompare() {
    const button = document.getElementById('compare-btn');
    const results = document.getElementById('compare-results');

    button?.addEventListener('click', async () => {
        setBusy(button, true);
        try {
            const data = await postJson('/visualizer/compare', {
                text_a: document.getElementById('compare-a').value,
                text_b: document.getElementById('compare-b').value,
                algorithm: document.getElementById('compare-algorithm').value,
            });

            results.replaceChildren();

            const hashRow = el('div', 'animate-fade-in-up space-y-1 text-sm font-mono break-all');
            hashRow.append(el('p', '', `A: ${data.hash_a}`));
            hashRow.append(el('p', '', `B: ${data.hash_b}`));
            results.append(hashRow);

            const summary = el(
                'p',
                'animate-fade-in-up text-sm text-[#a1a09a]',
                `${data.diff.differing_bits} of ${data.diff.total_bits} bits differ (${data.diff.differing_percentage}%). A secure hash should sit close to 50%.`
            );
            summary.style.animationDelay = '60ms';
            results.append(summary);
            results.append(renderPercentageBar(data.diff.differing_percentage));
            results.append(renderBitGrid(data.diff));
        } catch (error) {
            renderError(results, error.message);
        } finally {
            setBusy(button, false);
        }
    });
}

function initCollision() {
    const button = document.getElementById('collision-btn');
    const results = document.getElementById('collision-results');

    button?.addEventListener('click', async () => {
        setBusy(button, true);
        try {
            const data = await postJson('/visualizer/compare', {
                text_a: document.getElementById('collision-a').value,
                text_b: document.getElementById('collision-b').value,
                algorithm: document.getElementById('collision-algorithm').value,
            });

            results.replaceChildren();

            const hashRow = el('div', 'animate-fade-in-up space-y-1 text-sm font-mono break-all mb-3');
            hashRow.append(el('p', '', `A: ${data.hash_a}`));
            hashRow.append(el('p', '', `B: ${data.hash_b}`));
            results.append(hashRow);

            const banner = data.is_collision
                ? el(
                      'p',
                      'animate-shake animate-pulse-ring rounded-lg border border-amber-400/40 bg-amber-500/10 px-4 py-3 text-sm font-semibold text-amber-300',
                      'Collision found! Both inputs produced the same digest, even though the text is different.'
                  )
                : el(
                      'p',
                      'animate-fade-in-up rounded-lg border border-white/10 bg-black/20 px-4 py-3 text-sm text-[#a1a09a]',
                      'No collision here: the two digests are different.'
                  );
            results.append(banner);
        } catch (error) {
            renderError(results, error.message);
        } finally {
            setBusy(button, false);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initGenerator();
    initCompare();
    initCollision();
});
