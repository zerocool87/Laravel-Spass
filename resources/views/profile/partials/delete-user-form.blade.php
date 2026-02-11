<div class="bg-white rounded-xl shadow-lg border border-[#faa21b]/20 p-6 mt-6">
    <form method="post" action="{{ route('profile.destroy') }}">
        @csrf
        @method('delete')

        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('Supprimer le compte') }}</h2>
            <p class="text-sm text-gray-500 mb-6">
                {{ __('Une fois supprimé, toutes vos données seront définitivement effacées. Téléchargez ce que vous souhaitez conserver avant de supprimer votre compte.') }}
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-red-600 text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition" onclick="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}')">
                {{ __('Supprimer le compte') }}
            </button>
        </div>
    </form>
</div>
