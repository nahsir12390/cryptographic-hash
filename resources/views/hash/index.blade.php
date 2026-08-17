<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cryptographic Hash Function Visualizer &amp; Collision Explainer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden text-[var(--ink)]">
    <div class="app-shell">
        <header class="shell-card hero-shell animate-fade-up">
            <div class="hero-inner">
                <div>
                    <span class="soft-label">
                        <span class="pulse-dot"></span>
                        Security Lab
                    </span>

                    <h1 class="hero-title">
                        <span>Hash</span>
                        <span>Explorer</span>
                    </h1>

                    <p class="hero-copy">
                        Observe how even a tiny character change ripples through a hash, compare algorithm strength, and understand why collision resistance remains central to digital trust.
                    </p>

                    <div class="metric-bar">
                        <span class="soft-label" style="padding:0.3rem 0.7rem; letter-spacing:0.08em;">
                            Live
                        </span>
                        <span>MD5 • SHA-1 • SHA-256</span>
                        <div class="track"><div class="fill"></div></div>
                    </div>
                </div>

                <div class="hero-side">
                    <div class="stat-card brand-panel">
                        <div class="mini-visual">
                            <div class="mini-header">
                                <span>Digest</span>
                                <span class="mini-pill">Secure</span>
                            </div>
                            <div class="mini-code">8f7c2d...9ae1</div>
                            <div class="mini-grid" aria-hidden="true">
                                <span class="mini-bit active"></span>
                                <span class="mini-bit active"></span>
                                <span class="mini-bit"></span>
                                <span class="mini-bit active"></span>
                                <span class="mini-bit"></span>
                                <span class="mini-bit active"></span>
                                <span class="mini-bit active"></span>
                                <span class="mini-bit"></span>
                                <span class="mini-bit active"></span>
                                <span class="mini-bit"></span>
                                <span class="mini-bit active"></span>
                                <span class="mini-bit active"></span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <small>Hash space</small>
                        <strong>2<sup>256</sup></strong>
                    </div>
                    <div class="stat-card">
                        <small>Collision risk</small>
                        <strong>Low</strong>
                    </div>
                    <div class="stat-card">
                        <small>Mode</small>
                        <strong>Interactive</strong>
                    </div>
                </div>
            </div>
        </header>

        <div class="section-wrap">
            {{-- 1. Hash Generator --}}
            <section class="shell-card section-card animate-fade-up" style="animation-delay: 0.06s">
                <div class="section-head">
                    <span class="section-num">1</span>
                    <h2>Hash Generator</h2>
                </div>

                <label for="generator-text" class="mb-2 block text-sm text-[var(--ink-soft)]">Input text</label>
                <textarea id="generator-text" rows="2" maxlength="2000" class="field" placeholder="Type something, e.g. Hello World">Hello World</textarea>

                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($algorithms as $key => $meta)
                        <label class="relative">
                            <input type="checkbox" class="peer sr-only generator-algorithm" value="{{ $key }}" {{ $key !== 'toy' ? 'checked' : '' }}>
                            <span class="chip">{{ $meta['label'] }}</span>
                        </label>
                    @endforeach
                </div>

                <button id="generate-btn" type="button" class="btn-primary mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8Z"/></svg>
                    Generate Hash
                </button>

                <div id="generator-results" class="mt-4 space-y-2"></div>
            </section>

            {{-- 2. Avalanche Effect Visualizer --}}
            <section class="shell-card section-card animate-fade-up" style="animation-delay: 0.12s">
                <div class="section-head">
                    <span class="section-num">2</span>
                    <h2>Avalanche Effect</h2>
                </div>

                <p class="mb-4 text-sm text-[var(--ink-soft)]">Compare two inputs that differ by one character and observe the corresponding bit shift.</p>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="compare-a" class="mb-2 block text-sm text-[var(--ink-soft)]">Text A</label>
                        <input id="compare-a" type="text" maxlength="2000" value="Hello World" class="field">
                    </div>
                    <div>
                        <label for="compare-b" class="mb-2 block text-sm text-[var(--ink-soft)]">Text B</label>
                        <input id="compare-b" type="text" maxlength="2000" value="Hello world" class="field">
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:gap-4">
                    <label for="compare-algorithm" class="text-[var(--ink-soft)]">Algorithm</label>
                    <select id="compare-algorithm" class="field sm:w-auto">
                        @foreach ($algorithms as $key => $meta)
                            <option value="{{ $key }}" {{ $key === 'sha256' ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                    <button id="compare-btn" type="button" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18M3 12l6-6M3 12l6 6M21 12l-6-6M21 12l-6 6"/></svg>
                        Compare &amp; Visualize
                    </button>
                </div>

                <div id="compare-results" class="mt-4 space-y-4"></div>
            </section>

            {{-- 3. Collision Explainer --}}
            <section class="shell-card section-card animate-fade-up" style="animation-delay: 0.18s">
                <div class="section-head">
                    <span class="section-num">3</span>
                    <h2>Collision Explainer</h2>
                </div>

                <div class="info-grid">
                    <div class="info-box">
                        <h3>Preimage</h3>
                        <p>Given a digest, it should be impossible to reconstruct the original input.</p>
                    </div>
                    <div class="info-box">
                        <h3>Second-preimage</h3>
                        <p>Given one input, it should be infeasible to find another with the same output.</p>
                    </div>
                    <div class="info-box">
                        <h3>Collision</h3>
                        <p>Two different messages should not map to the same hash in a practical setting.</p>
                    </div>
                </div>

                <p class="mt-4 text-sm text-[var(--ink-soft)]">
                    Use the <strong class="font-semibold text-[var(--ink)]">Toy Sum-8</strong> demo below to trigger a real collision. The output space is tiny, which makes collisions easy to find. Real secure hashes like SHA-256 have a vastly larger space, which is why collision discovery is computationally impractical.
                </p>

                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="collision-a" class="mb-2 block text-sm text-[var(--ink-soft)]">Text A</label>
                        <input id="collision-a" type="text" maxlength="2000" value="ab" class="field">
                    </div>
                    <div>
                        <label for="collision-b" class="mb-2 block text-sm text-[var(--ink-soft)]">Text B</label>
                        <input id="collision-b" type="text" maxlength="2000" value="ba" class="field">
                    </div>
                </div>

                <div class="mt-4 flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:gap-4">
                    <label for="collision-algorithm" class="text-[var(--ink-soft)]">Algorithm</label>
                    <select id="collision-algorithm" class="field sm:w-auto">
                        @foreach ($algorithms as $key => $meta)
                            <option value="{{ $key }}" {{ $key === 'toy' ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                    <button id="collision-btn" type="button" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                        Test for Collision
                    </button>
                </div>

                <div id="collision-results" class="mt-4"></div>
            </section>

            {{-- 4. Algorithm comparison --}}
            <section class="shell-card section-card animate-fade-up" style="animation-delay: 0.24s">
                <div class="section-head">
                    <span class="section-num">4</span>
                    <h2>Algorithm Comparison</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-[var(--line)] text-[var(--ink-soft)]">
                            <tr>
                                <th class="py-2 pr-4">Algorithm</th>
                                <th class="py-2 pr-4">Output size</th>
                                <th class="py-2 pr-4">Status</th>
                                <th class="py-2">Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($algorithms as $key => $meta)
                                @php
                                    $statusColor = match ($meta['status']) {
                                        'secure' => 'bg-[#7ca56e]',
                                        'deprecated' => 'bg-[#c77756]',
                                        'broken' => 'bg-[#d86d6d]',
                                        default => 'bg-[#7a6ec4]',
                                    };
                                @endphp
                                <tr class="border-b border-[var(--line)]">
                                    <td class="py-3 pr-4 font-semibold text-[var(--ink)]">{{ $meta['label'] }}</td>
                                    <td class="py-3 pr-4 text-[var(--ink-soft)] font-mono">{{ $meta['bits'] }} bits</td>
                                    <td class="py-3 pr-4">
                                        <span class="inline-flex items-center gap-2 rounded-full border border-[var(--line)] bg-white/60 px-2.5 py-1 text-[10px] uppercase tracking-[0.08em] text-[var(--ink)]">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $statusColor }}"></span>
                                            {{ $meta['status_label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-[var(--ink-soft)]">{{ $meta['note'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
