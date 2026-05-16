<div class="flex flex-col gap-6">

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
            <p class="text-3xl font-bold text-yellow-700 dark:text-yellow-300">{{ $countPending }}</p>
            <p class="mt-1 text-sm font-medium text-yellow-600 dark:text-yellow-400">{{ __('messages.pending') }}</p>
        </div>
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <p class="text-3xl font-bold text-green-700 dark:text-green-300">{{ $countActive }}</p>
            <p class="mt-1 text-sm font-medium text-green-600 dark:text-green-400">{{ __('messages.validated') }}</p>
        </div>
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
            <p class="text-3xl font-bold text-red-700 dark:text-red-300">{{ $countRejected }}</p>
            <p class="mt-1 text-sm font-medium text-red-600 dark:text-red-400">{{ __('messages.rejected_status') }}</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="flex gap-2">
        @foreach([
            'all'      => __('messages.all_label') . ' (' . $countTotal . ')',
            'pending'  => __('messages.pending') . ' (' . $countPending . ')',
            'active'   => __('messages.validated') . ' (' . $countActive . ')',
            'rejected' => __('messages.rejected_status') . ' (' . $countRejected . ')',
        ] as $valeur => $label)
            <button
                wire:click="$set('filtre', '{{ $valeur }}')"
                class="rounded-lg px-4 py-2 text-sm font-medium transition
                    {{ $filtre === $valeur
                        ? 'bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900'
                        : 'border border-zinc-200 text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Tableau --}}
    @if($ongs->isEmpty())
        <div class="rounded-xl border border-zinc-200 p-10 text-center dark:border-zinc-700">
            <p class="text-zinc-500">{{ __('messages.no_ong_in_category') }}</p>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-zinc-500">{{ __('messages.organization') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-500">{{ __('messages.representative') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-500">{{ __('messages.documents') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-500">{{ __('messages.status') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-zinc-500">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($ongs as $ong)
                        <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">

                            <td class="px-4 py-4">
                                <p class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $ong->nom }}</p>
                                <p class="text-xs text-zinc-400">{{ $ong->email }}</p>
                                @if($ong->telephone)
                                    <p class="text-xs text-zinc-400">{{ $ong->telephone }}</p>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                @if($ong->representant)
                                    <p class="font-medium text-zinc-700 dark:text-zinc-300">{{ $ong->representant->name }}</p>
                                    <p class="text-xs text-zinc-400">{{ $ong->representant->email }}</p>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                @if($ong->documents->isEmpty())
                                    <span class="text-zinc-400">{{ __('messages.none_doc') }}</span>
                                @else
                                    <div class="flex flex-col gap-1">
                                        @foreach($ong->documents as $doc)
                                            <a
                                                href="{{ Storage::url($doc->chemin_fichier) }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 text-xs text-blue-600 hover:underline dark:text-blue-400"
                                            >
                                                <span>📄</span>
                                                {{ $doc->type_document }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                @if($ong->statut === 'pending')
                                    <span class="rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300">
                                        {{ __('messages.pending') }}
                                    </span>
                                @elseif($ong->statut === 'active')
                                    <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                        {{ __('messages.validated') }}
                                    </span>
                                @elseif($ong->statut === 'rejected')
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                        {{ __('messages.rejected_status') }}
                                    </span>
                                @endif
                                <p class="mt-1 text-xs text-zinc-400">{{ $ong->created_at->format('d/m/Y') }}</p>
                            </td>

                            <td class="px-4 py-4">
                                @if($ong->statut === 'pending')
                                    <div class="flex items-center gap-2">
                                        <button
                                            wire:click="valider({{ $ong->id }})"
                                            wire:confirm="{{ __('messages.validate') }} « {{ $ong->nom }} » ?"
                                            class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700"
                                        >
                                            {{ __('messages.validate') }}
                                        </button>
                                        <button
                                            wire:click="rejeter({{ $ong->id }})"
                                            wire:confirm="{{ __('messages.reject_btn') }} « {{ $ong->nom }} » ?"
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700"
                                        >
                                            {{ __('messages.reject_btn') }}
                                        </button>
                                    </div>
                                @elseif($ong->statut === 'active')
                                    <button
                                        wire:click="rejeter({{ $ong->id }})"
                                        wire:confirm="{{ __('messages.suspend_btn') }} « {{ $ong->nom }} » ?"
                                        class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                                    >
                                        {{ __('messages.suspend_btn') }}
                                    </button>
                                @elseif($ong->statut === 'rejected')
                                    <button
                                        wire:click="valider({{ $ong->id }})"
                                        wire:confirm="{{ __('messages.reactivate') }} « {{ $ong->nom }} » ?"
                                        class="rounded-lg border border-green-300 px-3 py-1.5 text-xs font-medium text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20"
                                    >
                                        {{ __('messages.reactivate') }}
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
