<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">📖 Panduan Sistem</h2>
        @if (!$noAccess)
            <p class="mt-1 text-sm text-gray-500">
                Panduan untuk: <span class="font-semibold text-teal-700">{{ $roleLabel }}</span>
            </p>
        @endif
    </x-slot>

    {{-- Gaya dasar isi modul, supaya file modul cukup ditulis HTML biasa --}}
    <style>
        .panduan-body {
            font-size: 0.875rem;
            line-height: 1.6;
            color: #374151;
        }

        .panduan-body>*+* {
            margin-top: 0.75rem;
        }

        .panduan-body h4 {
            font-weight: 600;
            color: #1f2937;
            margin-top: 1.25rem;
        }

        .panduan-body ul {
            list-style: disc;
            margin-left: 1.25rem;
        }

        .panduan-body ol {
            list-style: decimal;
            margin-left: 1.25rem;
        }

        .panduan-body li+li {
            margin-top: 0.25rem;
        }

        .panduan-body img {
            max-width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
        }

        .panduan-body .tip {
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            color: #115e59;
            padding: 0.625rem 0.75rem;
            border-radius: 0.5rem;
        }

        .panduan-body .warn {
            background: #fefce8;
            border: 1px solid #fde68a;
            color: #854d0e;
            padding: 0.625rem 0.75rem;
            border-radius: 0.5rem;
        }
    </style>

    <div class="py-8 sm:py-12">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">

            @if ($noAccess)
                <div class="max-w-xl p-8 mx-auto text-center bg-white border border-gray-200 rounded-xl">
                    <p class="text-3xl">📭</p>
                    <p class="mt-2 text-sm font-semibold text-gray-800">Panduan ini khusus peran dalam sistem KPI</p>
                    <p class="mt-1 text-sm text-gray-500">
                        Akun Anda tidak punya peran di Sistem Tugas & KPI, jadi tidak ada bagian yang perlu ditampilkan.
                        Kalau ini keliru, hubungi admin.
                    </p>
                </div>
            @else
                @php
                    $faq = $sections->firstWhere('slug', 'faq');
                    $dutySections = $sections->where('mode', 'duty')->reject(fn($s) => $s['slug'] === 'faq')->values();
                    $contextSections = $sections->where('mode', 'context')->values();
                @endphp

                {{-- Semua state (buka/tutup + menu aktif) dipegang di sini --}}
                <div x-data="panduan" @hashchange.window="onHash()"
                    class="lg:grid lg:grid-cols-[16rem_minmax(0,1fr)] lg:gap-6 lg:items-start">

                    {{-- Pembungkus ini yang di-sticky di desktop (bukan <nav>-nya), supaya tingginya
                         tidak "terkunci" setinggi isi sidebar. Di HP dibuat `contents` agar bar
                         dropdown bisa sticky terhadap seluruh halaman. --}}
                    <div class="contents lg:block lg:sticky lg:top-6 lg:self-start">
                        {{-- HP: bar sticky dengan dropdown --}}
                        <div
                            class="sticky top-0 z-20 px-4 py-2 mb-2 -mx-4 border-b border-gray-200 bg-gray-100/90 backdrop-blur lg:hidden">
                            <label class="block mb-1 text-xs font-medium text-gray-500">Loncat ke bagian</label>
                            <select :value="active" @change="if ($event.target.value) go($event.target.value)"
                                class="w-full px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">— Pilih bagian —</option>
                                <optgroup label="Tugas Anda">
                                    @foreach ($dutySections as $s)
                                        <option value="{{ $s['slug'] }}">{{ $s['title'] }}</option>
                                    @endforeach
                                </optgroup>
                                @if ($contextSections->isNotEmpty())
                                    <optgroup label="Untuk memahami alur">
                                        @foreach ($contextSections as $s)
                                            <option value="{{ $s['slug'] }}">{{ $s['title'] }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if ($faq)
                                    <option value="faq">{{ $faq['title'] }}</option>
                                @endif
                            </select>
                        </div>

                        {{-- Desktop: sticky, tinggi dibatasi & bisa discroll sendiri kalau menunya panjang --}}
                        <nav x-ref="nav" aria-label="Daftar isi panduan"
                            class="relative hidden p-4 bg-white border border-gray-200 lg:block rounded-xl max-h-[calc(100vh-3rem)] overflow-y-auto">

                            <div class="flex items-center justify-between mb-3">
                                <button type="button" @click="expandAll()"
                                    class="text-xs font-medium text-teal-700 hover:underline">Buka semua</button>
                                <button type="button" @click="collapseAll()"
                                    class="text-xs font-medium text-gray-500 hover:underline">Tutup semua</button>
                            </div>

                            <p class="mb-2 text-xs font-semibold tracking-wider text-gray-400 uppercase">Tugas Anda</p>
                            <ul class="space-y-0.5">
                                @foreach ($dutySections as $s)
                                    <li>
                                        <a href="#{{ $s['slug'] }}" data-nav="{{ $s['slug'] }}"
                                            @click.prevent="go('{{ $s['slug'] }}')"
                                            class="flex items-center gap-2 px-2 py-1.5 text-sm rounded-lg border-l-2 transition-colors"
                                            :class="active === '{{ $s['slug'] }}' ?
                                                'bg-teal-50 text-teal-700 font-medium border-teal-500' :
                                                'text-gray-700 border-transparent hover:bg-gray-50 hover:text-teal-700'">
                                            <span class="truncate">{{ $s['title'] }}</span>
                                            <span x-show="isOpen('{{ $s['slug'] }}')" x-cloak
                                                class="w-1.5 h-1.5 ml-auto bg-teal-400 rounded-full shrink-0"
                                                title="Sedang dibuka"></span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            @if ($contextSections->isNotEmpty())
                                <p class="mt-4 mb-2 text-xs font-semibold tracking-wider text-gray-400 uppercase">
                                    Untuk memahami alur
                                </p>
                                <ul class="space-y-0.5">
                                    @foreach ($contextSections as $s)
                                        <li>
                                            <a href="#{{ $s['slug'] }}" data-nav="{{ $s['slug'] }}"
                                                @click.prevent="go('{{ $s['slug'] }}')"
                                                class="flex items-center gap-2 px-2 py-1.5 text-sm rounded-lg border-l-2 transition-colors"
                                                :class="active === '{{ $s['slug'] }}' ?
                                                    'bg-gray-100 text-gray-800 font-medium border-gray-400' :
                                                    'text-gray-500 border-transparent hover:bg-gray-50 hover:text-gray-700'">
                                                <span class="truncate">{{ $s['title'] }}</span>
                                                <span x-show="isOpen('{{ $s['slug'] }}')" x-cloak
                                                    class="w-1.5 h-1.5 ml-auto bg-gray-400 rounded-full shrink-0"></span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @if ($faq)
                                <div class="pt-3 mt-4 border-t border-gray-100">
                                    <a href="#faq" data-nav="faq" @click.prevent="go('faq')"
                                        class="flex items-center gap-2 px-2 py-1.5 text-sm rounded-lg border-l-2 transition-colors"
                                        :class="active === 'faq' ?
                                            'bg-teal-50 text-teal-700 font-medium border-teal-500' :
                                            'text-gray-700 border-transparent hover:bg-gray-50 hover:text-teal-700'">
                                        <span class="truncate">{{ $faq['title'] }}</span>
                                        <span x-show="isOpen('faq')" x-cloak
                                            class="w-1.5 h-1.5 ml-auto bg-teal-400 rounded-full shrink-0"></span>
                                    </a>
                                </div>
                            @endif
                        </nav>
                    </div>

                    {{-- Isi --}}
                    <div class="min-w-0 mt-4 space-y-3 lg:mt-0">

                        @foreach ($dutySections as $s)
                            @include('panduan-sistem.partials.section', ['section' => $s])
                        @endforeach

                        @if ($contextSections->isNotEmpty())
                            <div class="p-4 mt-6 text-sm text-gray-600 border border-gray-200 bg-gray-50 rounded-xl">
                                <p class="font-semibold text-gray-700">Untuk memahami alur kerja tim</p>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    Bagian di bawah ini bukan tugas {{ $roleLabel }}. Ditampilkan supaya Anda paham
                                    apa yang dikerjakan dan dilihat oleh tim Anda.
                                </p>
                            </div>

                            @foreach ($contextSections as $s)
                                @include('panduan-sistem.partials.section', ['section' => $s])
                            @endforeach
                        @endif

                        @if ($faq)
                            <div class="pt-3">
                                @include('panduan-sistem.partials.section', ['section' => $faq])
                            </div>
                        @endif
                    </div>
                </div>

                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('panduan', () => ({
                            open: [], // default: semua tertutup
                            active: '',
                            ids: [],
                            locked: false, // abaikan scrollspy sementara saat auto-scroll dari klik menu
                            ticking: false,

                            init() {
                                this.ids = [...this.$root.querySelectorAll('[data-section]')].map(el => el.id);

                                // Sorot menu mengikuti posisi scroll
                                window.addEventListener('scroll', () => {
                                    if (this.ticking) return;
                                    this.ticking = true;
                                    requestAnimationFrame(() => {
                                        this.spy();
                                        this.ticking = false;
                                    });
                                }, {
                                    passive: true
                                });

                                // Jaga menu aktif tetap terlihat di sidebar yang bisa discroll
                                this.$watch('active', id => this.keepNavVisible(id));

                                // Buka otomatis kalau datang dari link ber-hash (#slug)
                                if (location.hash) this.$nextTick(() => setTimeout(() => this.onHash(false), 60));
                                else this.spy();
                            },

                            isOpen(id) {
                                return this.open.includes(id);
                            },

                            toggle(id) {
                                if (this.isOpen(id)) {
                                    this.open = this.open.filter(x => x !== id);
                                } else {
                                    this.open.push(id);
                                    this.active = id;
                                }
                            },

                            go(id) {
                                if (!this.isOpen(id)) this.open.push(id);
                                this.active = id;
                                this.locked = true;
                                history.replaceState(null, '', '#' + id);
                                this.scrollToSection(id);
                                setTimeout(() => this.locked = false, 900);
                            },

                            onHash(smooth = true) {
                                const id = location.hash.slice(1);
                                if (!this.ids.includes(id)) return;
                                if (!this.isOpen(id)) this.open.push(id);
                                this.active = id;
                                this.locked = true;
                                this.scrollToSection(id, smooth);
                                setTimeout(() => this.locked = false, 900);
                            },

                            scrollToSection(id, smooth = true) {
                                const el = document.getElementById(id);
                                if (!el) return;
                                const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                                el.scrollIntoView({
                                    behavior: smooth && !reduce ? 'smooth' : 'auto',
                                    block: 'start'
                                });
                            },

                            expandAll() {
                                this.open = [...this.ids];
                            },

                            collapseAll() {
                                this.open = [];
                            },

                            // Scrollspy: bagian terakhir yang sudah melewati garis 140px dari atas layar
                            spy() {
                                if (this.locked || !this.ids.length) return;
                                const line = 140;
                                let current = this.ids[0];
                                for (const id of this.ids) {
                                    const el = document.getElementById(id);
                                    if (el && el.getBoundingClientRect().top <= line) current = id;
                                }
                                // Mentok di dasar halaman -> bagian terakhir (biasanya FAQ yang pendek)
                                if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight -
                                    4) {
                                    current = this.ids[this.ids.length - 1];
                                }
                                if (current !== this.active) this.active = current;
                            },

                            keepNavVisible(id) {
                                const nav = this.$refs.nav;
                                const link = nav?.querySelector(`[data-nav="${id}"]`);
                                if (!nav || !link || nav.offsetParent === null) return;
                                const top = link.offsetTop;
                                const bottom = top + link.offsetHeight;
                                if (top < nav.scrollTop + 40 || bottom > nav.scrollTop + nav.clientHeight - 20) {
                                    nav.scrollTo({
                                        top: top - nav.clientHeight / 2,
                                        behavior: 'smooth'
                                    });
                                }
                            },
                        }));
                    });
                </script>
            @endif

        </div>
    </div>
</x-app-layout>
