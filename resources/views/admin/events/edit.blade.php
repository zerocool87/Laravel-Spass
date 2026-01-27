<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Modifier l'événement</h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="max-w-4xl mx-auto relative">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold">Modifier l'événement</h1>
                <a href="{{ route('admin.events.index') }}" class="text-sm text-cyan-200 hover:underline">&larr; Retour</a>
            </div>

            @if($errors->any())
                <div class="bg-red-600 text-white p-3 rounded mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.events.update', $event) }}" class="bg-transparent grid grid-cols-1 gap-6">
                @csrf
                @method('PATCH')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Titre</label>
                        <input name="title" class="w-full py-3 px-4 text-lg rounded-lg bg-slate-800 border border-slate-700" value="{{ old('title', $event->title) }}" placeholder="Titre de l'événement">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Type</label>
                        <select name="type" class="w-full py-3 px-4 text-lg rounded-lg bg-slate-800 border border-slate-700">
                            <option value="assemblee" {{ old('type', $event->type) === 'assemblee' ? 'selected' : '' }}>Assemblée plénière</option>
                            <option value="bureau" {{ old('type', $event->type) === 'bureau' ? 'selected' : '' }}>Réunion bureau</option>
                            <option value="commissions" {{ old('type', $event->type) === 'commissions' ? 'selected' : '' }}>Commissions</option>
                            <option value="autre" {{ old('type', $event->type) === 'autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Description</label>
                        <textarea name="description" rows="5" class="w-full py-3 px-4 text-lg rounded-lg bg-slate-800 border border-slate-700 resize-vertical" placeholder="Description...">{{ old('description', $event->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-200 mb-1">Début</label>
                            <input id="start_at" name="start_at" type="datetime-local" class="w-full py-3 px-4 text-lg rounded-lg bg-slate-800 border border-slate-700" value="{{ old('start_at', optional($event->start_at)->format('Y-m-d\\TH:i')) }}">
                            <p class="text-xs text-gray-400 mt-1">Timezone: {{ now()->getTimezone()->getName() }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-200 mb-1">Fin</label>
                            <input id="end_at" name="end_at" type="datetime-local" class="w-full py-3 px-4 text-lg rounded-lg bg-slate-800 border border-slate-700" value="{{ old('end_at', optional($event->end_at)->format('Y-m-d\\TH:i')) }}">
                        </div>
                    </div>

                    <div>
                        <label class="inline-flex items-center gap-2">
                            <input id="is_all_day" name="is_all_day" type="checkbox" value="1" class="form-check-input h-5 w-5" {{ old('is_all_day', $event->is_all_day) ? 'checked' : '' }}>
                            <span class="text-sm ml-2">Toute la journée</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Lieu</label>
                        <input name="location" class="w-full py-3 px-4 text-lg rounded-lg bg-slate-800 border border-slate-700" value="{{ old('location', $event->location) }}" placeholder="Ex: Salle principale">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 text-sm bg-cyan-600 text-white rounded px-4 py-2">Enregistrer</button>
                        <button type="button" onclick="if(confirm('{{ __('Are you sure you want to delete this event?') }}')) document.getElementById('delete-event-form').submit();" class="inline-flex items-center gap-2 text-sm bg-red-600 text-white rounded px-4 py-2">Supprimer</button>
                    </div>
                </div>
            </form>

            <form id="delete-event-form" method="POST" action="{{ route('admin.events.destroy', $event) }}" style="display:none;">
                @csrf
                @method('DELETE')
            </form>

            
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const start = document.getElementById('start_at');
        const end = document.getElementById('end_at');

        if (!start || !end) return;

        start.addEventListener('change', function(){
            if (!end.value) {
                try {
                    const d = new Date(start.value);
                    d.setHours(d.getHours() + 1);
                    end.value = d.toISOString().slice(0,16);
                } catch (err) {
                    // ignore
                }
            }
        });
    });
    </script>
</x-app-layout>
