<div class="flex flex-col gap-6 p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('messages.coord_dashboard_title') }}</flux:heading>
            <flux:text class="mt-1">{{ __('messages.coord_sub') }}</flux:text>
        </div>
        <flux:button :href="route('dashboard')" wire:navigate variant="ghost" size="sm">
            {{ __('messages.back_to_dashboard') }}
        </flux:button>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-700 dark:bg-blue-900/20">
            <p class="text-3xl font-bold text-blue-800 dark:text-blue-100">{{ $kpis['totalBeneficiaires'] }}</p>
            <p class="mt-1 text-sm text-blue-600 dark:text-blue-400">{{ __('messages.beneficiaries_registered') }}</p>
        </div>
        <div class="rounded-xl border border-green-200 bg-green-50 p-5 dark:border-green-700 dark:bg-green-900/20">
            <p class="text-3xl font-bold text-green-800 dark:text-green-100">{{ $kpis['aidesActives'] }}</p>
            <p class="mt-1 text-sm text-green-600 dark:text-green-400">{{ __('messages.active_aids') }}</p>
        </div>
        <div class="rounded-xl border border-purple-200 bg-purple-50 p-5 dark:border-purple-700 dark:bg-purple-900/20">
            <p class="text-3xl font-bold text-purple-800 dark:text-purple-100">{{ $kpis['ongsActives'] }}</p>
            <p class="mt-1 text-sm text-purple-600 dark:text-purple-400">{{ __('messages.active_ongs') }}</p>
        </div>
        <div class="rounded-xl border p-5
            @if($kpis['tauxCouverture'] >= 70) border-green-200 bg-green-50 dark:border-green-700 dark:bg-green-900/20
            @elseif($kpis['tauxCouverture'] > 0) border-yellow-200 bg-yellow-50 dark:border-yellow-700 dark:bg-yellow-900/20
            @else border-red-200 bg-red-50 dark:border-red-700 dark:bg-red-900/20 @endif">
            <p class="text-3xl font-bold
                @if($kpis['tauxCouverture'] >= 70) text-green-800 dark:text-green-100
                @elseif($kpis['tauxCouverture'] > 0) text-yellow-800 dark:text-yellow-100
                @else text-red-800 dark:text-red-100 @endif">
                {{ $kpis['tauxCouverture'] }} %
            </p>
            <p class="mt-1 text-sm
                @if($kpis['tauxCouverture'] >= 70) text-green-600 dark:text-green-400
                @elseif($kpis['tauxCouverture'] > 0) text-yellow-600 dark:text-yellow-400
                @else text-red-600 dark:text-red-400 @endif">
                {{ __('messages.global_coverage_rate') }}
            </p>
            @if($kpis['totalBeneficiaires'] > 0)
                <p class="mt-0.5 text-xs
                    @if($kpis['tauxCouverture'] >= 70) text-green-500 dark:text-green-500
                    @elseif($kpis['tauxCouverture'] > 0) text-yellow-500 dark:text-yellow-500
                    @else text-red-500 dark:text-red-500 @endif">
                    {{ $kpis['beneficiairesCouverts'] }} / {{ $kpis['totalBeneficiaires'] }} {{ __('messages.beneficiary') }}
                </p>
            @endif
        </div>
    </div>

    {{-- Couverture par ONG --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
            <flux:heading size="lg">{{ __('messages.coverage_by_org') }}</flux:heading>
        </div>

        @if($couvByOng->isEmpty())
            <div class="p-8 text-center text-sm text-zinc-400">
                {{ __('messages.no_active_ong') }}
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.organization') }}</th>
                        <th class="px-5 py-3 text-right font-medium text-zinc-500">{{ __('messages.beneficiary') }}</th>
                        <th class="px-5 py-3 text-right font-medium text-zinc-500">{{ __('messages.covered') }}</th>
                        <th class="px-5 py-3 text-right font-medium text-zinc-500">{{ __('messages.not_covered_col') }}</th>
                        <th class="px-5 py-3 text-center font-medium text-zinc-500">{{ __('messages.rate') }}</th>
                        <th class="px-5 py-3 text-center font-medium text-zinc-500">{{ __('messages.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($couvByOng as $row)
                        <tr class="
                            @if($row['statut'] === 'nul') bg-red-50 hover:bg-red-100 dark:bg-red-900/10 dark:hover:bg-red-900/20
                            @elseif($row['statut'] === 'partiel') bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50
                            @elseif($row['statut'] === 'bon') bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50
                            @else bg-zinc-50 dark:bg-zinc-800/50 @endif
                        ">
                            <td class="px-5 py-3 font-medium text-zinc-800 dark:text-zinc-200">{{ $row['nom'] }}</td>
                            <td class="px-5 py-3 text-right text-zinc-600 dark:text-zinc-400">{{ $row['beneficiaires'] }}</td>
                            <td class="px-5 py-3 text-right text-green-600 dark:text-green-400">{{ $row['couverts'] }}</td>
                            <td class="px-5 py-3 text-right
                                @if($row['statut'] === 'nul' && $row['beneficiaires'] > 0) text-red-600 dark:text-red-400 font-semibold
                                @else text-zinc-500 @endif">
                                {{ $row['beneficiaires'] - $row['couverts'] }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($row['taux'] === null)
                                    <span class="text-xs text-zinc-400">—</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        @if($row['statut'] === 'bon') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                                        @elseif($row['statut'] === 'partiel') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300
                                        @else bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 @endif">
                                        {{ $row['taux'] }} %
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center text-xs">
                                @if($row['statut'] === 'bon')
                                    <span class="text-green-600 dark:text-green-400">● {{ __('messages.covered_status') }}</span>
                                @elseif($row['statut'] === 'partiel')
                                    <span class="text-yellow-600 dark:text-yellow-400">● {{ __('messages.partial_status') }}</span>
                                @elseif($row['statut'] === 'nul')
                                    <span class="font-semibold text-red-600 dark:text-red-400">● {{ __('messages.not_covered_status') }}</span>
                                @else
                                    <span class="text-zinc-400">● {{ __('messages.empty_status') }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Bénéficiaires sans aide active --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
            <flux:heading size="lg">{{ __('messages.beneficiaries_without_aid') }}</flux:heading>
            @if($nonCouverts->isNotEmpty())
                <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900/40 dark:text-red-300">
                    {{ $nonCouverts->count() }}{{ $nonCouverts->count() >= 20 ? '+' : '' }}
                </span>
            @endif
        </div>

        @if($nonCouverts->isEmpty())
            <div class="flex items-center gap-3 p-6">
                <span class="text-xl text-green-500">✓</span>
                <p class="text-sm font-medium text-green-700 dark:text-green-400">
                    {{ __('messages.all_covered_text') }}
                </p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.beneficiary') }}</th>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.category') }}</th>
                        @if($isAdmin)
                            <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.ong') }}</th>
                        @endif
                        @if(!$isAdmin)
                            <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.action') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($nonCouverts as $b)
                        <tr class="bg-white hover:bg-red-50 dark:bg-zinc-900 dark:hover:bg-red-900/10">
                            <td class="px-5 py-3 font-medium text-zinc-800 dark:text-zinc-200">
                                {{ $b->prenom }} {{ $b->nom }}
                            </td>
                            <td class="px-5 py-3 text-zinc-500 capitalize">
                                {{ str_replace('_', ' ', $b->categorie) }}
                            </td>
                            @if($isAdmin)
                                <td class="px-5 py-3 text-zinc-500">{{ $b->ong->nom }}</td>
                            @endif
                            @if(!$isAdmin)
                                <td class="px-5 py-3">
                                    <a
                                        href="{{ route('ong.aides.nouvelle') }}?beneficiaire_id={{ $b->id }}"
                                        wire:navigate
                                        class="text-xs font-medium text-blue-600 hover:underline dark:text-blue-400"
                                    >
                                        {{ __('messages.distribute_aid_arrow') }}
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
