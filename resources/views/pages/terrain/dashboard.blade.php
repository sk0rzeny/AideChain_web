<x-layouts::app :title="__('messages.field_interface')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('messages.field_agent_interface') }}</flux:heading>
                <flux:text class="mt-1">{{ __('messages.field_agent_sub') }}</flux:text>
            </div>
        </div>

        {{-- ONG badge --}}
        <div class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-zinc-50 px-5 py-3 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">{{ __('messages.attached_ong') }}</p>
                <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $ong->nom }}</p>
            </div>
        </div>

        {{-- Actions principales --}}
        <div class="grid grid-cols-2 gap-4">
            <a
                href="{{ route('ong.beneficiaires.nouveau') }}"
                wire:navigate
                class="flex flex-col gap-3 rounded-xl border-2 border-blue-200 bg-white p-6 transition hover:border-blue-400 hover:shadow-sm dark:border-blue-800 dark:bg-zinc-900 dark:hover:border-blue-600"
            >
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ __('messages.register_beneficiary_title') }}</p>
                    <p class="mt-0.5 text-sm text-zinc-500">{{ __('messages.auto_duplicate_check') }}</p>
                </div>
            </a>

            <a
                href="{{ route('ong.aides.nouvelle') }}"
                wire:navigate
                class="flex flex-col gap-3 rounded-xl border-2 border-green-200 bg-white p-6 transition hover:border-green-400 hover:shadow-sm dark:border-green-800 dark:bg-zinc-900 dark:hover:border-green-600"
            >
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-zinc-800 dark:text-zinc-100">{{ __('messages.distribute_aid_btn') }}</p>
                    <p class="mt-0.5 text-sm text-zinc-500">{{ __('messages.select_active_project') }}</p>
                </div>
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-3xl font-bold text-zinc-800 dark:text-zinc-100">{{ $nbBeneficiaires }}</p>
                <p class="mt-1 text-sm text-zinc-500">{{ __('messages.beneficiaries_registered') }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-3xl font-bold text-zinc-800 dark:text-zinc-100">{{ $nbDistributions }}</p>
                <p class="mt-1 text-sm text-zinc-500">{{ __('messages.aids_distributed') }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-3xl font-bold text-zinc-800 dark:text-zinc-100">{{ $nbProjets }}</p>
                <p class="mt-1 text-sm text-zinc-500">{{ __('messages.active_projects') }}</p>
            </div>
        </div>

        {{-- Dernières distributions --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
                <flux:heading size="lg">{{ __('messages.recent_distributions') }}</flux:heading>
            </div>

            @if($aidesRecentes->isEmpty())
                <div class="p-8 text-center text-sm text-zinc-400">
                    {{ __('messages.no_aids_yet') }}
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.beneficiary') }}</th>
                            <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.project') }}</th>
                            <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.aid_type') }}</th>
                            <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.date_col') }}</th>
                            <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.expires_on') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @foreach($aidesRecentes as $aide)
                            <tr class="bg-white dark:bg-zinc-900">
                                <td class="px-5 py-3 font-medium text-zinc-800 dark:text-zinc-200">
                                    {{ $aide->beneficiaire->prenom }} {{ $aide->beneficiaire->nom }}
                                </td>
                                <td class="px-5 py-3 text-zinc-500">{{ $aide->projetAide?->nom ?? '—' }}</td>
                                <td class="px-5 py-3 text-zinc-600 dark:text-zinc-400">{{ $aide->typeAide->nom }}</td>
                                <td class="px-5 py-3 text-zinc-400">{{ $aide->date_distribution->format('d/m/Y') }}</td>
                                <td class="px-5 py-3">
                                    @if($aide->date_expiration->isPast())
                                        <span class="text-xs text-zinc-400">{{ __('messages.expired') }}</span>
                                    @else
                                        <span class="text-xs text-green-600 dark:text-green-400">
                                            {{ $aide->date_expiration->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</x-layouts::app>
