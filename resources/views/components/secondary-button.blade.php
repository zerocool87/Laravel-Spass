<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs text-gray-100 uppercase tracking-widest neon-outline hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-cyan-400 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
