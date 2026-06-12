<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $blog->title }} — {{ config('app.name', 'MannaPOS') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('icons8-dynamics-365-100.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' }
                    },
                    fontFamily: {
                        sans:  ['Inter','ui-sans-serif','system-ui','sans-serif'],
                        prose: ['Merriweather','Georgia','serif'],
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }

        /* ── Article prose ─────────────────────────────── */
        .article-body { font-family: 'Inter', sans-serif; color: #374151; line-height: 1.85; font-size: 1.0625rem; }
        .article-body p  { margin-bottom: 1.4rem; }
        .article-body p.lead { font-size: 1.175rem; color: #1e293b; font-weight: 500; line-height: 1.75; margin-bottom: 1.75rem; }
        .article-body h2 { font-size: 1.4rem; font-weight: 800; color: #0f172a; margin: 2.25rem 0 0.9rem; letter-spacing: -0.02em; }
        .article-body h3 { font-size: 1.15rem; font-weight: 700; color: #1e293b; margin: 1.75rem 0 0.6rem; }
        .article-body ul, .article-body ol { padding-left: 1.6rem; margin-bottom: 1.4rem; }
        .article-body ul { list-style: disc; }
        .article-body ol { list-style: decimal; }
        .article-body li { margin-bottom: 0.55rem; }
        .article-body a  { color: #2563eb; text-decoration: underline; }
        .article-body strong { color: #0f172a; font-weight: 700; }
        .article-body blockquote { border-left: 4px solid #2563eb; background: #f0f7ff; padding: 1rem 1.5rem; margin: 1.75rem 0; border-radius: 0 8px 8px 0; font-style: italic; color: #1e40af; }

        /* ── Cover hero ────────────────────────────────── */
        .cover-hero { position: relative; height: 460px; }
        @media (max-width: 640px) { .cover-hero { height: 300px; } }

        /* ── Sidebar newsletter ─────────────────────────── */
        .subscribe-btn { background: #2563eb; color: #fff; display: block; width: 100%; text-align: center; padding: 0.75rem 1rem; border-radius: 10px; font-weight: 600; font-size: 0.95rem; transition: background 0.2s; }
        .subscribe-btn:hover { background: #1d4ed8; }

        /* ── Comments ───────────────────────────────────── */
        .comment-avatar { width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #7c3aed); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; color: #fff; flex-shrink: 0; }

        /* ── Share buttons ──────────────────────────────── */
        .share-btn { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 8px; background: #f1f5f9; color: #64748b; transition: background 0.2s, color 0.2s; }
        .share-btn:hover { background: #2563eb; color: #fff; }
    </style>
</head>
<body class="bg-slate-50 font-sans">

    @include('partials.header')

    <div class="pt-[68px]">

        {{-- ── Cover Image Hero ─────────────────────────────── --}}
        <div class="cover-hero">
            <img src="{{ $blog->cover_image }}" alt="{{ $blog->title }}" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-black/20"></div>
            <div class="absolute inset-0 flex flex-col justify-end px-4 pb-10 max-w-4xl mx-auto w-full" style="left:50%;transform:translateX(-50%);">
                <span class="inline-block text-xs font-bold uppercase tracking-widest text-white/70 mb-3">
                    Published in <strong class="text-white">{{ $blog->category }}</strong>
                </span>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white leading-tight tracking-tight mb-3">{{ $blog->title }}</h1>
                <p class="text-white/75 text-base max-w-2xl">{{ $blog->excerpt }}</p>
            </div>
        </div>

        {{-- ── Main Layout ───────────────────────────────────── --}}
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col lg:flex-row gap-10">

                {{-- ────────────────────────────────────────────────
                     LEFT: Article Content
                ──────────────────────────────────────────────── --}}
                <main class="flex-1 min-w-0">

                    {{-- Meta bar --}}
                    <div class="bg-white rounded-2xl border border-slate-200 px-6 py-4 flex flex-wrap items-center justify-between gap-4 mb-8 shadow-sm">
                        <div class="flex items-center gap-3">
                            @if($blog->author_avatar)
                            <img src="{{ $blog->author_avatar }}" alt="{{ $blog->author_name }}" class="w-10 h-10 rounded-full object-cover border-2 border-slate-200">
                            @endif
                            <div>
                                <span class="font-semibold text-sm text-gray-800">{{ $blog->author_name }}</span>
                                @if($blog->author_title)
                                <span class="text-slate-400 text-xs"> &mdash; {{ $blog->author_title }}</span>
                                @endif
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $blog->published_at?->format('F j, Y, g:ia') }}
                                    &nbsp;&bull;&nbsp;
                                    {{ $blog->read_time }} min read
                                    &nbsp;&bull;&nbsp;
                                    {{ number_format($blog->views) }} views
                                </p>
                            </div>
                        </div>
                        {{-- Share --}}
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 mr-1">Share:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="share-btn" title="Facebook">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($blog->title) }}" target="_blank" class="share-btn" title="X / Twitter">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                            <button onclick="navigator.clipboard.writeText(window.location.href);this.title='Copied!'" class="share-btn" title="Copy link">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </button>
                            <button class="share-btn" title="Bookmark">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Article body --}}
                    <div class="bg-white rounded-2xl border border-slate-200 px-6 sm:px-10 py-10 shadow-sm mb-8">
                        <div class="article-body max-w-none">
                            {!! $blog->content !!}
                        </div>
                    </div>

                    {{-- Author card --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 flex items-start gap-5 shadow-sm mb-10">
                        @if($blog->author_avatar)
                        <img src="{{ $blog->author_avatar }}" alt="{{ $blog->author_name }}" class="w-16 h-16 rounded-full object-cover border-2 border-slate-200 flex-shrink-0">
                        @endif
                        <div>
                            <p class="font-bold text-gray-900 text-base">{{ $blog->author_name }}</p>
                            @if($blog->author_title)
                            <p class="text-sm text-blue-600 mb-1">{{ $blog->author_title }}</p>
                            @endif
                            <p class="text-sm text-slate-500 leading-relaxed">Expert contributor at MannaPOS Blog. Passionate about helping businesses in East Africa grow through smart technology and practical business insights.</p>
                        </div>
                    </div>

                    {{-- ── Comments Section ─────────────────────── --}}
                    <div id="comments" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">

                        <h2 class="text-xl font-bold text-gray-900 mb-1">
                            Comments
                            <span class="text-slate-400 font-normal text-base ml-1">({{ $comments->count() }})</span>
                        </h2>
                        <p class="text-sm text-slate-500 mb-7">Share your thoughts. We read every comment.</p>

                        {{-- Success flash --}}
                        @if(session('comment_success'))
                        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-6 text-sm font-medium">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            {{ session('comment_success') }}
                        </div>
                        @endif

                        {{-- Comment form --}}
                        <form action="{{ route('blog.comment', $blog->slug) }}" method="POST" class="mb-10">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Your Name <span class="text-red-500">*</span></label>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="e.g. Amina Hassan"
                                        class="w-full px-4 py-2.5 rounded-xl border @error('name') border-red-400 bg-red-50 @else border-slate-200 @enderror text-sm text-gray-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    >
                                    @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number <span class="text-red-500">*</span></label>
                                    <input
                                        type="tel"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="e.g. +255 712 345 678"
                                        class="w-full px-4 py-2.5 rounded-xl border @error('phone') border-red-400 bg-red-50 @else border-slate-200 @enderror text-sm text-gray-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    >
                                    @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Your Comment <span class="text-red-500">*</span></label>
                                <textarea
                                    name="body"
                                    rows="4"
                                    placeholder="Write your comment here..."
                                    class="w-full px-4 py-3 rounded-xl border @error('body') border-red-400 bg-red-50 @else border-slate-200 @enderror text-sm text-gray-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                                >{{ old('body') }}</textarea>
                                @error('body')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-all duration-200 hover:-translate-y-0.5 shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Post Comment
                            </button>
                        </form>

                        {{-- Comments list --}}
                        @forelse ($comments as $comment)
                        <div class="flex gap-4 py-5 @unless($loop->last) border-b border-slate-100 @endunless">
                            <div class="comment-avatar flex-shrink-0">{{ mb_strtoupper(mb_substr($comment->name, 0, 1)) }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="font-semibold text-gray-900 text-sm">{{ $comment->name }}</span>
                                    <span class="text-xs text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-slate-600 leading-relaxed">{{ $comment->body }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <p class="text-slate-400 text-sm font-medium">No comments yet. Be the first to share your thoughts!</p>
                        </div>
                        @endforelse
                    </div>

                </main>

                {{-- ────────────────────────────────────────────────
                     RIGHT: Sidebar
                ──────────────────────────────────────────────── --}}
                <aside class="w-full lg:w-80 flex-shrink-0 space-y-6">

                    {{-- Newsletter --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-600 mb-2">MANNAPOS NEWSLETTER</p>
                        <h3 class="font-bold text-gray-900 text-base leading-snug mb-2">Get weekly business tips delivered to your inbox</h3>
                        <p class="text-sm text-slate-500 mb-5 leading-relaxed">Join 5,000+ business owners who read our newsletter every Friday morning.</p>
                        <form action="#" method="POST">
                            @csrf
                            <input type="email" placeholder="Enter your email" class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <a href="#" class="subscribe-btn">Subscribe — It&rsquo;s Free</a>
                        </form>
                    </div>

                    {{-- Latest posts --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-4">LATEST ARTICLES</p>
                        <div class="space-y-4">
                            @foreach ($latest as $item)
                            <a href="{{ route('blog.show', $item->slug) }}" class="flex gap-3 group">
                                <img src="{{ $item->cover_image }}" alt="{{ $item->title }}" class="w-16 h-16 rounded-xl object-cover flex-shrink-0 border border-slate-100">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors leading-snug line-clamp-2">{{ $item->title }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ $item->read_time }} min read</p>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Categories --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-4">CATEGORIES</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach (['Inventory','Payments','Analytics','Security','Operations','Guide','Marketing','Technology','Customer Management'] as $cat)
                            <a href="{{ route('blog.index') }}" class="text-xs font-medium px-3 py-1.5 rounded-full border border-slate-200 text-slate-600 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all">{{ $cat }}</a>
                            @endforeach
                        </div>
                    </div>

                </aside>
            </div>

            {{-- ── Related Articles ─────────────────────────────── --}}
            @if($related->isNotEmpty())
            <div class="mt-14">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Related Articles</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($related as $post)
                    <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl">
                        <a href="{{ route('blog.show', $post->slug) }}" class="block h-44 overflow-hidden flex-shrink-0">
                            <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                        </a>
                        <div class="p-5 flex flex-col flex-grow">
                            <span class="text-xs font-semibold text-violet-700 bg-violet-50 px-2.5 py-1 rounded-full mb-2.5 w-fit">{{ $post->category }}</span>
                            <a href="{{ route('blog.show', $post->slug) }}" class="group">
                                <h3 class="font-bold text-gray-900 text-sm leading-snug mb-2 group-hover:text-blue-600 transition-colors">{{ $post->title }}</h3>
                            </a>
                            <p class="text-xs text-slate-500 flex items-center gap-1.5 mt-auto pt-3 border-t border-slate-100">
                                @if($post->author_avatar)
                                <img src="{{ $post->author_avatar }}" alt="{{ $post->author_name }}" class="w-6 h-6 rounded-full object-cover">
                                @endif
                                <span class="font-medium text-gray-700">{{ $post->author_name }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span>{{ $post->read_time }} min</span>
                            </p>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- /max-w --}}
    </div>{{-- /pt --}}

    @include('partials.footer')

</body>
</html>
