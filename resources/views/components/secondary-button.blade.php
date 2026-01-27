@if($attributes->has('href'))
    <a {{ $attributes->except('type')->merge(['class' => 'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs text-gray-100 uppercase tracking-widest border-gray-700 hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-25 transition ease-in-out duration-150']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => $attributes->get('type', 'button'), 'class' => 'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs text-gray-100 uppercase tracking-widest border-gray-700 hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-25 transition ease-in-out duration-150']) }}>
        {{ $slot }}
    </button>
@endif
