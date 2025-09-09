@php
    // Use provided crumbs or infer from URL segments
    $crumbs = $crumbs ?? null;
    if (!$crumbs) {
        $segments = collect(request()->segments());
        $crumbs = [];
        $url = url('/');
        $crumbs[] = ['label' => 'Home', 'url' => $url, 'icon' => 'home'];
        foreach ($segments as $i => $seg) {
            $url .= '/' . $seg;
            $isLast = $i === $segments->count() - 1;
            // Map common routes to icons
            $icon = match(strtolower($seg)) {
                'login' => 'login',
                'forgot-password' => 'forgot',
                'dashboard' => 'dashboard',
                'auth' => 'user',
                default => 'page',
            };
            $crumbs[] = [
                'label' => ucfirst(str_replace('-', ' ', $seg)),
                'url' => $isLast ? null : $url,
                'icon' => $icon
            ];
        }
    }
    // SVG icon map
    $inline = $inline ?? false;
    $isWelcome = request()->routeIs('welcome');
    $iconColor = $inline ? 'text-white' : 'text-maroon-800';
    $icons = [
        'home' => '<svg class="w-4 h-4 mr-1 ' . $iconColor . ' inline font-bold" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 2v6a2 2 0 002 2h4a2 2 0 002-2v-6m-6 0h6" /></svg>',
        'login' => '<svg class="w-4 h-4 mr-1 ' . $iconColor . ' inline font-bold" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3m6-6v12m13-6a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'forgot' => '<svg class="w-4 h-4 mr-1 ' . $iconColor . ' inline font-bold" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'dashboard' => '<svg class="w-4 h-4 mr-1 ' . $iconColor . ' inline font-bold" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8v-10h-8v10zm0-18v6h8V3h-8z" /></svg>',
        'user' => '<svg class="w-4 h-4 mr-1 ' . $iconColor . ' inline font-bold" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A9 9 0 1112 21a9 9 0 01-6.879-3.196z" /></svg>',
        'page' => '<svg class="w-4 h-4 mr-1 ' . $iconColor . ' inline font-bold" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4V4z" /></svg>',
    ];
@endphp
@if ($inline)
    <nav class="flex items-center space-x-2 text-sm font-bold text-white">
        @foreach ($crumbs as $i => $crumb)
            @if ($i > 0)
                <span class="mx-1 flex items-center">
                    <svg class="w-4 h-4" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </span>
            @endif
            <span class="inline-flex items-center">
                {!! $icons[$crumb['icon']] ?? $icons['page'] !!}
                @if (!empty($crumb['url']))
                    <a href="{{ $crumb['url'] }}" class="hover:underline text-white font-bold">{{ $crumb['label'] }}</a>
                @else
                    <span>{{ $crumb['label'] }}</span>
                @endif
            </span>
        @endforeach
    </nav>
@elseif (!$isWelcome)
    <div class="fixed top-16 left-0 z-50 w-full flex justify-start pointer-events-none">
        <nav class="ml-6 mt-2 flex items-center space-x-2 pointer-events-auto">
            @foreach ($crumbs as $i => $crumb)
                @if ($i > 0)
                    <span class="mx-1 flex items-center">
                        <svg class="w-4 h-4" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </span>
                @endif
                <span class="inline-flex items-center bg-white/60 backdrop-blur border border-white/40 rounded-full px-3 py-1 shadow text-maroon-800 font-bold text-sm drop-shadow-sm @if(empty($crumb['url'])) text-maroon-900 @endif">
                    {!! $icons[$crumb['icon']] ?? $icons['page'] !!}
                    @if (!empty($crumb['url']))
                        <a href="{{ $crumb['url'] }}" class="hover:underline text-maroon-800 font-bold">{{ $crumb['label'] }}</a>
                    @else
                        <span>{{ $crumb['label'] }}</span>
                    @endif
                </span>
            @endforeach
        </nav>
    </div>
@endif 