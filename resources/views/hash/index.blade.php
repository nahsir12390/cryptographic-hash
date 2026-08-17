<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cryptographic Hash Function Visualizer &amp; Collision Explainer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden text-[#EDEDEC] font-sans">
    <div class="app-shell">
        <header class="glass-card hero-shell animate-fade-in-up">
            <div class="hero-inner">
                <div>
                    <span class="section-label">
                        <span class="dot"></span>
                        Security Lab
                    </span>
                    <h1 class="hero-title gradient-text">
                        <span>Hash</span>
                        <span>Visualizer</span>
                    </h1>
                    <p class="hero-copy">
                        Explore how tiny changes in input create completely different digests, compare modern algorithms against legacy ones, and understand why collisions are a real security concern.
                    </p>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <span class="status-pill"><span class="pulse"></span>Live</span>
                        <span class="text-xs uppercase tracking-[0.18em] text-[#8d95a3]">MD5 • SHA-1 • SHA-256</span>
                    </div>
                </div>

                <div class="hero-metrics">
                    <div class="metric-card">
                        <small>Hash space</small>
                        <strong>2<sup>256</sup></strong>
                    </div>
                    <div class="metric-card">
                        <small>Collision risk</small>
                        <strong>Low</strong>
                    </div>
                    <div class="metric-card">
                        <small>Mode</small>
                        <strong>Interactive</strong>
                    </div>
                </div>
            </div>
        </header>

        {{-- 1. Hash Generator --}}
        <section class="glass-card animate-fade-in-up p-6 md:p-8 space-y-4" style="animation-delay: .05s">
            <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-fuchsia-600 text-sm font-bold shadow-lg shadow-indigo-500/30">1</span>
                <h2 class="text-lg font-semibold">Hash Generator</h2>
            </div>

            <label for="generator-text" class="block text-sm text-[#a1a09a]">Input text</label>
            <textarea id="generator-text" rows="2" maxlength="2000"
                class="field"
                placeholder="Type something, e.g. Hello World">Hello World</textarea>

            <div class="flex flex-wrap gap-2">
                @foreach ($algorithms as $key => $meta)
                    <label class="relative">
                        <input type="checkbox" class="peer sr-only generator-algorithm" value="{{ $key }}" {{ $key !== 'toy' ? 'checked' : '' }}>
                        <span class="chip">{{ $meta['label'] }}</span>
                    </label>
                @endforeach
            </div>

            <button id="generate-btn" type="button" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8Z"/></svg>
                Generate Hash
            </button>

            <div id="generator-results" class="space-y-2 pt-2"></div>
        </section>

        {{-- 2. Avalanche Effect Visualizer --}}
        <section class="glass-card animate-fade-in-up p-6 md:p-8 space-y-4" style="animation-delay: .1s">
            <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-fuchsia-600 text-sm font-bold shadow-lg shadow-indigo-500/30">2</span>
                <h2 class="text-lg font-semibold">Avalanche Effect Visualizer</h2>
            </div>
            <p class="text-sm text-[#a1a09a]">Compare two inputs that differ by only one character and see how much of the digest changes.</p>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="compare-a" class="block text-sm text-[#a1a09a] mb-1">Text A</label>
                    <input id="compare-a" type="text" maxlength="2000" value="Hello World" class="field">
                </div>
                <div>
                    <label for="compare-b" class="block text-sm text-[#a1a09a] mb-1">Text B</label>
                    <input id="compare-b" type="text" maxlength="2000" value="Hello world" class="field">
                </div>
            </div>

            <div class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:gap-4">
                <label for="compare-algorithm" class="text-[#a1a09a]">Algorithm</label>
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

            <div id="compare-results" class="space-y-4 pt-2"></div>
        </section>

        {{-- 3. Collision Explainer --}}
        <section class="glass-card animate-fade-in-up p-6 md:p-8 space-y-4" style="animation-delay: .15s">
            <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-fuchsia-600 text-sm font-bold shadow-lg shadow-indigo-500/30">3</span>
                <h2 class="text-lg font-semibold">Collision Explainer</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-4 text-sm text-[#a1a09a]">
                <div class="rounded-xl border border-white/5 bg-black/20 p-4 transition-colors duration-200 hover:border-indigo-400/30 hover:bg-black/30">
                    <p class="font-semibold text-[#EDEDEC] mb-1">Preimage resistance</p>
                    <p>Given a hash value, it should be infeasible to find any input that produces it.</p>
                </div>
                <div class="rounded-xl border border-white/5 bg-black/20 p-4 transition-colors duration-200 hover:border-indigo-400/30 hover:bg-black/30">
                    <p class="font-semibold text-[#EDEDEC] mb-1">Second-preimage resistance</p>
                    <p>Given one input, it should be infeasible to find a different input with the same hash.</p>
                </div>
                <div class="rounded-xl border border-white/5 bg-black/20 p-4 transition-colors duration-200 hover:border-indigo-400/30 hover:bg-black/30">
                    <p class="font-semibold text-[#EDEDEC] mb-1">Collision resistance</p>
                    <p>It should be infeasible to find any two different inputs that produce the same hash.</p>
                </div>
            </div>

            <p class="text-sm text-[#a1a09a]">
                Use the <strong class="text-[#EDEDEC]">Toy Sum-8</strong> algorithm below to trigger a real collision on purpose. Its output
                space is only 256 possible values, so by the pigeonhole principle, collisions are easy to find.
                Real algorithms like SHA-256 have an output space of 2<sup>256</sup> values, making collisions
                practically impossible to find &mdash; try the same two inputs with SHA-256 and notice the digests never match.
            </p>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label for="collision-a" class="block text-sm text-[#a1a09a] mb-1">Text A</label>
                    <input id="collision-a" type="text" maxlength="2000" value="ab" class="field">
                </div>
                <div>
                    <label for="collision-b" class="block text-sm text-[#a1a09a] mb-1">Text B</label>
                    <input id="collision-b" type="text" maxlength="2000" value="ba" class="field">
                </div>
            </div>

            <div class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:gap-4">
                <label for="collision-algorithm" class="text-[#a1a09a]">Algorithm</label>
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

            <div id="collision-results" class="pt-2"></div>
        </section>

        {{-- 4. Algorithm comparison --}}
        <section class="glass-card animate-fade-in-up p-6 md:p-8 space-y-4" style="animation-delay: .2s">
            <div class="flex items-center gap-3">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-fuchsia-600 text-sm font-bold shadow-lg shadow-indigo-500/30">4</span>
                <h2 class="text-lg font-semibold">Algorithm Comparison</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-[#a1a09a] border-b border-white/10">
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
                                    'secure' => 'bg-emerald-400',
                                    'deprecated' => 'bg-amber-400',
                                    'broken' => 'bg-red-400',
                                    default => 'bg-purple-400',
                                };
                            @endphp
                            <tr class="border-b border-white/5 transition-colors duration-200 hover:bg-white/[0.03]">
                                <td class="py-3 pr-4 font-medium">{{ $meta['label'] }}</td>
                                <td class="py-3 pr-4 font-mono text-[#a1a09a]">{{ $meta['bits'] }} bits</td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-black/20 px-2.5 py-1 text-xs">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $statusColor }}"></span>
                                        {{ $meta['status_label'] }}
                                    </span>
                                </td>
                                <td class="py-3 text-[#a1a09a]">{{ $meta['note'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="animate-fade-in-up pb-6 text-center text-xs text-[#6b6a65]" style="animation-delay: .25s">
            Built for educational purposes &mdash; no offensive cryptanalysis is performed here.
        </footer>
    </div>
</body>
</html>
