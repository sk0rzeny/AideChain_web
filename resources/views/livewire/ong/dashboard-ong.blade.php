<div class="flex flex-col gap-6">

    {{-- Actions rapides --}}
    <div class="flex items-center justify-end gap-3">
        <flux:button :href="route('coordination')" wire:navigate variant="ghost" size="sm">
            {{ __('messages.global_coordination') }}
        </flux:button>
        <flux:button :href="route('ong.projets.creer')" wire:navigate variant="ghost" size="sm">
            {{ __('messages.create_project') }}
        </flux:button>
        <flux:button :href="route('ong.aides.nouvelle')" wire:navigate variant="ghost" size="sm">
            {{ __('messages.distribute_aid_btn') }}
        </flux:button>
        <flux:button :href="route('ong.beneficiaires.nouveau')" wire:navigate variant="primary" size="sm">
            {{ __('messages.new_beneficiary_btn') }}
        </flux:button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-3xl font-bold text-zinc-800 dark:text-zinc-100">{{ $nbBeneficiaires }}</p>
            <p class="mt-1 text-sm text-zinc-500">{{ __('messages.beneficiaries_registered') }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-3xl font-bold text-zinc-800 dark:text-zinc-100">{{ $nbDistributions }}</p>
            <p class="mt-1 text-sm text-zinc-500">{{ __('messages.aids_distributed') }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-3xl font-bold text-zinc-800 dark:text-zinc-100">{{ $nbAgents }}</p>
            <p class="mt-1 text-sm text-zinc-500">{{ __('messages.active_field_agents') }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-3xl font-bold text-zinc-800 dark:text-zinc-100">{{ $nbProjets }}</p>
            <p class="mt-1 text-sm text-zinc-500">{{ __('messages.active_projects') }}</p>
        </div>
    </div>

    {{-- Demandes d'adhésion en attente --}}
    @if($demandesPending->isNotEmpty())
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-5 dark:border-yellow-700 dark:bg-yellow-900/20">
            <p class="mb-3 font-semibold text-yellow-800 dark:text-yellow-200">
                {{ $demandesPending->count() }} {{ __('messages.pending_memberships') }}
            </p>
            <div class="flex flex-col gap-2">
                @foreach($demandesPending as $demande)
                    <div class="flex items-center justify-between rounded-lg bg-white px-4 py-3 dark:bg-zinc-800">
                        <div>
                            <p class="font-medium text-zinc-800 dark:text-zinc-200">{{ $demande->user->name }}</p>
                            <p class="text-xs text-zinc-400">{{ $demande->user->email }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                wire:click="accepterDemande({{ $demande->id }})"
                                wire:confirm="{{ $demande->user->name }} — {{ __('messages.accept') }} ?"
                                class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700"
                            >
                                {{ __('messages.accept') }}
                            </button>
                            <button
                                wire:click="rejeterDemande({{ $demande->id }})"
                                wire:confirm="{{ $demande->user->name }} — {{ __('messages.reject') }} ?"
                                class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700"
                            >
                                {{ __('messages.reject') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Projets d'aide --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
            <flux:heading size="lg">{{ __('messages.aid_projects') }}</flux:heading>
            <flux:button :href="route('ong.projets.creer')" wire:navigate variant="primary" size="sm">
                {{ __('messages.create_project') }}
            </flux:button>
        </div>

        @if($projets->isEmpty())
            <div class="p-8 text-center text-sm text-zinc-400">
                {{ __('messages.no_projects_yet') }}
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.project') }}</th>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.aid_type') }}</th>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.zone') }}</th>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.expires_on') }}</th>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.distributions_short') }}</th>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.status') }}</th>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($projets as $projet)
                        <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                            <td class="px-5 py-3 font-medium text-zinc-800 dark:text-zinc-200">{{ $projet->nom }}</td>
                            <td class="px-5 py-3 text-zinc-600 dark:text-zinc-400">{{ $projet->typeAide->nom }}</td>
                            <td class="px-5 py-3 text-zinc-400">{{ $projet->zone_cible ?? '—' }}</td>
                            <td class="px-5 py-3 text-zinc-400">
                                @if($projet->date_expiration->isPast())
                                    <span class="text-xs text-red-400">{{ $projet->date_expiration->format('d/m/Y') }}</span>
                                @else
                                    {{ $projet->date_expiration->format('d/m/Y') }}
                                @endif
                            </td>
                            <td class="px-5 py-3 text-zinc-600 dark:text-zinc-400">{{ $projet->distributions_count }}</td>
                            <td class="px-5 py-3">
                                @if($projet->statut === 'active')
                                    <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">{{ __('messages.status_active') }}</span>
                                @elseif($projet->statut === 'suspendue')
                                    <span class="rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">{{ __('messages.status_suspended') }}</span>
                                @else
                                    <span class="rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium text-zinc-500 dark:bg-zinc-800">{{ __('messages.status_finished') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if($projet->statut === 'active')
                                    <button
                                        wire:click="suspendreProjet({{ $projet->id }})"
                                        wire:confirm="{{ __('messages.suspend') }} « {{ $projet->nom }} » ?"
                                        class="text-xs text-yellow-600 hover:underline dark:text-yellow-400"
                                    >
                                        {{ __('messages.suspend') }}
                                    </button>
                                @elseif($projet->statut === 'suspendue')
                                    <button
                                        wire:click="reactiverProjet({{ $projet->id }})"
                                        wire:confirm="{{ __('messages.reactivate') }} « {{ $projet->nom }} » ?"
                                        class="text-xs text-green-600 hover:underline dark:text-green-400"
                                    >
                                        {{ __('messages.reactivate') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Agents actifs --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
            <flux:heading size="lg">{{ __('messages.field_agents') }}</flux:heading>
        </div>

        @if($agents->isEmpty())
            <div class="p-8 text-center text-sm text-zinc-400">
                {{ __('messages.no_agents_yet') }}
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.name') }}</th>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.email') }}</th>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.since') }}</th>
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($agents as $agent)
                        <tr class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800/50">
                            <td class="px-5 py-3 font-medium text-zinc-800 dark:text-zinc-200">{{ $agent->name }}</td>
                            <td class="px-5 py-3 text-zinc-500">{{ $agent->email }}</td>
                            <td class="px-5 py-3 text-zinc-400">{{ $agent->updated_at->format('d/m/Y') }}</td>
                            <td class="px-5 py-3">
                                <button
                                    wire:click="retirerAgent({{ $agent->id }})"
                                    wire:confirm="{{ __('messages.remove') }} {{ $agent->name }} ?"
                                    class="text-xs text-red-500 hover:underline"
                                >
                                    {{ __('messages.remove') }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Aides récentes --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
            <flux:heading size="lg">{{ __('messages.recent_aids') }}</flux:heading>
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
                        <th class="px-5 py-3 text-left font-medium text-zinc-500">{{ __('messages.distributed_on') }}</th>
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
