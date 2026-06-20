<header class="border-b border-white/10 backdrop-blur-sm bg-black/20">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            @if (file_exists(public_path('logo-condominio-threads.png')))
                <img src="{{ asset('logo-condominio-threads.png') }}" alt="Condominio Threads" class="h-10 w-10 rounded-lg object-cover ring-2 ring-teal-400/30">
            @else
                <div class="h-10 w-10 rounded-lg bg-teal-500/30 flex items-center justify-center text-teal-300 font-bold text-sm">CT</div>
            @endif
            <span class="font-semibold text-lg tracking-tight group-hover:text-teal-300 transition">Condominio Threads</span>
        </a>

        <nav class="hidden sm:flex items-center gap-6 text-sm text-slate-300">
            <a href="{{ route('legal.privacy') }}" class="hover:text-teal-300 transition">Privacidade</a>
            <a href="{{ route('legal.terms') }}" class="hover:text-teal-300 transition">Termos</a>
            <a href="{{ route('premium.show') }}" class="hover:text-condo-gold transition">Premium</a>
        </nav>
    </div>
</header>
