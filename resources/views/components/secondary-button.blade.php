<button {{ $attributes->merge(['type' => 'button', 'class' => 'group relative bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 inline-flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
