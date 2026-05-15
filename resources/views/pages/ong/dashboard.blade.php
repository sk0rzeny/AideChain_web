<x-layouts::app :title="__('Tableau de bord ONG')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        @if(!$ong)
            {{-- Aucune ONG enregistrée --}}
            <div>
                <flux:heading size="xl">Tableau de bord ONG</flux:heading>
                <flux:text class="mt-1">Bienvenue sur AideChain. Commencez par enregistrer votre organisation.</flux:text>
            </div>

            <div class="rounded-xl border-2 border-dashed border-zinc-300 p-10 text-center dark:border-zinc-600">
                <div class="mb-4 text-4xl">🏢</div>
                <p class="mb-1 text-lg font-semibold text-zinc-800 dark:text-zinc-200">Aucune ONG enregistrée</p>
                <p class="mb-6 text-sm text-zinc-500">Enregistrez votre organisation pour accéder aux fonctionnalités de coordination humanitaire.</p>
                <flux:button :href="route('ong.inscription')" variant="primary" wire:navigate>
                    Enregistrer mon ONG
                </flux:button>
            </div>

        @elseif($ong->statut === 'pending')
            {{-- En attente de validation --}}
            <div>
                <flux:heading size="xl">{{ $ong->nom }}</flux:heading>
                <flux:text class="mt-1">Tableau de bord de votre organisation.</flux:text>
            </div>

            <div class="rounded-lg border border-yellow-300 bg-yellow-50 p-5 dark:border-yellow-700 dark:bg-yellow-900/20">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 text-xl">⏳</span>
                    <div>
                        <p class="font-semibold text-yellow-800 dark:text-yellow-200">En attente de validation</p>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                            Votre demande d'enregistrement a bien été reçue. L'administrateur examine votre dossier et les documents fournis.
                            Vous serez notifié dès que votre ONG sera validée.
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="mb-3 text-sm font-medium text-zinc-600 dark:text-zinc-400">Informations soumises</p>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-zinc-400">Email</dt>
                        <dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $ong->email }}</dd>
                    </div>
                    @if($ong->telephone)
                    <div>
                        <dt class="text-zinc-400">Téléphone</dt>
                        <dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $ong->telephone }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-zinc-400">Documents</dt>
                        <dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $ong->documents->count() }} fichier(s) soumis</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">Date de soumission</dt>
                        <dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $ong->created_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </div>

        @elseif($ong->statut === 'rejected')
            {{-- Demande rejetée --}}
            <div>
                <flux:heading size="xl">{{ $ong->nom }}</flux:heading>
            </div>

            <div class="rounded-lg border border-red-300 bg-red-50 p-5 dark:border-red-700 dark:bg-red-900/20">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 text-xl">❌</span>
                    <div class="flex-1">
                        <p class="font-semibold text-red-800 dark:text-red-200">Demande rejetée</p>
                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                            Votre demande d'enregistrement a été refusée par l'administrateur. Vous pouvez soumettre une nouvelle demande avec des documents mis à jour.
                        </p>
                        <flux:button :href="route('ong.inscription')" variant="danger" class="mt-3" wire:navigate>
                            Soumettre une nouvelle demande
                        </flux:button>
                    </div>
                </div>
            </div>

        @elseif($ong->statut === 'active')
            {{-- ONG active --}}
            <div>
                <flux:heading size="xl">{{ $ong->nom }}</flux:heading>
                <flux:text class="mt-1">Gérez vos bénéficiaires, vos agents et vos aides distribuées.</flux:text>
            </div>

            <livewire:ong.dashboard-ong />
        @endif

    </div>
</x-layouts::app>
