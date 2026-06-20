<footer class="border-t border-white/10 mt-16 bg-black/20">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-400">
            <p>&copy; {{ date('Y') }} Condominio Threads. Classificação simbólica e recreativa.</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('legal.privacy') }}" class="hover:text-teal-300 transition">Privacidade</a>
                <a href="{{ route('legal.terms') }}" class="hover:text-teal-300 transition">Termos</a>
                <a href="{{ route('legal.data-deletion') }}" class="hover:text-teal-300 transition">Exclusão de dados</a>
            </div>
        </div>
    </div>
</footer>
