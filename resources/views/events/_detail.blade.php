<div class="event-detail-modal-content space-y-6">
    {{-- Header avec Titre et Badge de type --}}
    <div class="border-b border-gray-100 pb-4">
        <div class="flex justify-between items-start gap-4">
            <h1 class="text-3xl font-bold text-gray-900 leading-tight">{{ $event->title }}</h1>
            @if($event->type)
                <span class="shrink-0 inline-flex items-center px-4 py-1.5 rounded-full bg-amber-100 text-amber-800 text-sm font-bold border border-amber-200 shadow-sm">
                    {{ $event->type }}
                </span>
            @endif
        </div>
    </div>

    {{-- Informations clés --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Date et Heure --}}
        @php
            $start = $event->start_at;
            $end = $event->end_at;
            $isAllDay = $event->is_all_day;

            $offsetHours = $start->getOffset() / 3600;
            $showTimezone = $offsetHours != 0;
            $timezoneForDisplay = $showTimezone ? 'UTC' . ($offsetHours > 0 ? '+' : '') . $offsetHours : '';

            if ($isAllDay) {
                $timeStr = 'Toute la journée';
                if ($end && !$start->isSameDay($end)) {
                    // Multi-day all-day. End date is exclusive.
                    $endInclusive = $end->copy()->subSecond();
                    $dateStr = match (true) {
                        $start->isSameMonth($endInclusive) && $start->isSameYear($endInclusive) 
                            => $start->format('j') . '–' . $endInclusive->translatedFormat('j F Y'),
                        $start->isSameYear($endInclusive) 
                            => $start->translatedFormat('j F') . '–' . $endInclusive->translatedFormat('j F Y'),
                        default 
                            => $start->translatedFormat('j F Y') . '–' . $endInclusive->translatedFormat('j F Y'),
                    };
                    $ariaLabel = 'Du ' . $start->translatedFormat('l j F Y') . ' au ' . $endInclusive->translatedFormat('l j F Y') . ', toute la journée';
                } else {
                    // Single all-day
                    $dateStr = $start->translatedFormat('l j F Y');
                    $ariaLabel = $dateStr . ', toute la journée';
                }
            } else {
                if ($end && !$start->isSameDay($end)) {
                    // Multi-day timed
                    $dateStr = 'Du ' . $start->translatedFormat('j M Y à H:i') . ' au ' . $end->translatedFormat('j M Y à H:i');
                    $ariaLabel = $dateStr;
                } else {
                    // Single-day timed
                    $dateStr = $start->translatedFormat('l j F Y');
                    $timeStr = $end ? $start->format('H:i') . '–' . $end->format('H:i') : $start->format('H:i');
                    $ariaLabel = $dateStr . ($end ? ' de ' : ' à ') . $timeStr;
                }
            }
        @endphp
        <div class="flex items-center p-3 rounded-xl bg-gray-50 border border-gray-100 transition-colors hover:bg-white hover:shadow-sm group">
            <div class="p-2.5 rounded-lg bg-cyan-100 text-cyan-600 mr-4 group-hover:bg-cyan-500 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Date et Heure</p>
                <div class="flex flex-wrap items-center gap-x-2 text-gray-900 font-medium" aria-label="{{ $ariaLabel }}">
                    <span class="font-semibold text-amber-700">{{ $dateStr }}</span>

                    @if($timeStr)
                        <span class="text-gray-500 font-sans px-1">·</span>
                        <span class="font-mono text-base text-gray-800">{{ $timeStr }}</span>
                    @endif

                    @if($showTimezone)
                        <span class="text-xs text-gray-400 ml-1">({{ $timezoneForDisplay }})</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Lieu --}}
        <div class="flex items-center p-3 rounded-xl bg-gray-50 border border-gray-100 transition-colors hover:bg-white hover:shadow-sm group">
            <div class="p-2.5 rounded-lg bg-emerald-100 text-emerald-600 mr-4 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a4 4 0 10-1.414 1.414l4.243 4.243a1 1 0 001.414-1.414z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Lieu</p>
                <p class="text-gray-900 font-medium">{{ $event->location ?: 'Non spécifié' }}</p>
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4 flex items-center">
            <svg class="w-4 h-4 mr-2 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            Description de l'événement
        </h3>
        <div class="prose prose-amber max-w-none text-gray-700 leading-relaxed">
            {!! nl2br(e($event->description)) !!}
        </div>
    </div>

    {{-- Footer/Actions --}}
    <div class="flex justify-end pt-2">
        <button type="button"
                data-event-detail-close
                class="px-6 py-2.5 bg-white border-2 border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 shadow-sm">
            Fermer
        </button>
    </div>
</div>
