<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog — {{ config('app.name', 'MannaPOS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' }
                    },
                    fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] }
                }
            }
        }
    </script>
    <style>html { scroll-behavior: smooth; }</style>
</head>
<body class="bg-slate-50 text-gray-900 font-sans">

    @include('partials.header')

    {{-- Hero --}}
    <div class="pt-[68px]">
        <div class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 py-20 px-4 text-center relative overflow-hidden">
            <div class="absolute inset-0" style="background-image:linear-gradient(rgba(99,179,255,0.06) 1px,transparent 1px),linear-gradient(90deg,rgba(99,179,255,0.06) 1px,transparent 1px);background-size:48px 48px;"></div>
            <div class="relative z-10 max-w-2xl mx-auto">
                <span class="inline-block text-xs font-bold tracking-widest uppercase text-blue-300 bg-blue-900/40 border border-blue-700/50 px-4 py-1.5 rounded-full mb-5">MannaPOS Blog</span>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white leading-tight mb-4 tracking-tight">Insights, Tips &amp; Guides<br>for Your Business</h1>
                <p class="text-blue-100/70 text-lg">Stay ahead with expert articles on retail, payments, inventory, and growing your business with MannaPOS.</p>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

        {{-- Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
            @foreach ($blogs as $post)
            <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-slate-200/80">
                <a href="{{ route('blog.show', $post->slug) }}" class="block overflow-hidden h-52 flex-shrink-0">
                    <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                </a>
                <div class="p-6 flex flex-col flex-grow">
                    <span class="inline-block text-xs font-semibold text-violet-700 bg-violet-50 px-3 py-1 rounded-full mb-3 w-fit">{{ $post->category }}</span>
                    <a href="{{ route('blog.show', $post->slug) }}" class="group">
                        <h2 class="font-bold text-gray-900 text-[1.05rem] leading-snug mb-2.5 group-hover:text-blue-600 transition-colors">{{ $post->title }}</h2>
                    </a>
                    <p class="text-sm text-slate-500 leading-relaxed mb-5 flex-grow">{{ $post->excerpt }}</p>
                    <div class="flex items-center gap-3 pt-4 border-t border-slate-100 mt-auto">
                        @if($post->author_avatar)
                        <img src="{{ $post->author_avatar }}" alt="{{ $post->author_name }}" class="w-9 h-9 rounded-full object-cover border-2 border-slate-200 flex-shrink-0">
                        @endif
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $post->author_name }}</p>
                            <p class="text-xs text-slate-400">{{ $post->published_at?->format('M j, Y') }} &middot; {{ $post->read_time }} min read</p>
                        </div>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($blogs->hasPages())
        <div class="mt-10 flex justify-center">
            {{ $blogs->links() }}
        </div>
        @endif
    </div>

    @include('partials.footer')
</body>
</html>
