<div class="mx-auto max-w-2xl p-6">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Enregistrer une aide distribuée</flux:heading>
            <flux:text class="mt-1">L'enregistrement est immutable — signé par hash de transaction.</flux:text>
        </div>
        <flux:button :href="route('dashboard')" wire:navigate variant="ghost" size="sm">
            ← Tableau de bord
        </flux:button>
    </div>

    @if($success)
        {{-- Succès --}}
        <div class="rounded-xl border border-green-200 bg-green-50 p-6 dark:border-green-700 dark:bg-green-900/20">
            <div class="flex items-start gap-4">
                <span class="text-2xl">✅</span>
                <div class="flex-1">
                    <p class="font-semibold text-green-800 dark:text-green-200">Aide enregistrée avec succès</p>
                    <dl class="mt-3 grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                        <div>
                            <dt class="text-green-600 dark:text-green-400">Bénéficiaire</dt>
                            <dd class="font-medium text-green-800 dark:text-green-200">{{ $aideInfo['beneficiaire'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-green-600 dark:text-green-400">Type d'aide</dt>
                            <dd class="font-medium text-green-800 dark:text-green-200">{{ $aideInfo['type'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-green-600 dark:text-green-400">Distribuée le</dt>
                            <dd class="font-medium text-green-800 dark:text-green-200">{{ $aideInfo['distribution'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-green-600 dark:text-green-400">Valide jusqu'au</dt>
                            <dd class="font-medium text-green-800 dark:text-green-200">{{ $aideInfo['expiration'] }}</dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="text-green-600 dark:text-green-400">Hash transaction</dt>
                            <dd class="font-mono text-xs text-green-700 dark:text-green-300">{{ $aideInfo['hash'] }}</dd>
                        </div>
                    </dl>
                    <div class="mt-4 flex gap-3">
                        <flux:button wire:click="nouvelleAide" variant="primary" size="sm">
                            Nouvelle aide pour ce bénéficiaire
                        </flux:button>
                        <flux:button wire:click="reinitialiser" variant="ghost" size="sm">
                            Autre bénéficiaire
                        </flux:button>
                        <flux:button :href="route('dashboard')" wire:navigate variant="ghost" size="sm">
                            Tableau de bord
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

    @elseif($step === 1)
        {{-- Étape 1 — Recherche bénéficiaire --}}
        <div class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <div>
                <flux:heading size="lg">Identifier le bénéficiaire</flux:heading>
                <flux:text class="mt-1 text-sm">Saisissez l'identité exacte telle qu'elle a été enregistrée.</flux:text>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:input wire:model="prenom" label="Prénom" placeholder="Ex: Amina" required />
                    @error('prenom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <flux:input wire:model="nom" label="Nom de famille" placeholder="Ex: Hassan" required />
                    @error('nom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="max-w-xs">
                <flux:input wire:model="dateNaissance" label="Date de naissance" type="date" required />
                @error('dateNaissance') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            @if($rechercheErreur)
                <div class="flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 dark:border-red-700 dark:bg-red-900/20">
                    <span class="text-red-500">⚠</span>
                    <p class="text-sm text-red-700 dark:text-red-300">{{ $rechercheErreur }}</p>
                    <flux:button :href="route('ong.beneficiaires.nouveau')" wire:navigate variant="ghost" size="sm" class="ml-auto">
                        Enregistrer →
                    </flux:button>
                </div>
            @endif

            <div class="flex justify-end">
                <flux:button wire:click="rechercherBeneficiaire" variant="primary">
                    Rechercher le bénéficiaire
                </flux:button>
            </div>
        </div>

    @elseif($step === 2)
        {{-- Étape 2 — Formulaire aide --}}

        {{-- Carte bénéficiaire --}}
        <div class="mb-4 flex items-center justify-between rounded-xl border border-zinc-200 bg-zinc-50 px-5 py-4 dark:border-zinc-700 dark:bg-zinc-800">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">Bénéficiaire</p>
                <p class="mt-0.5 font-semibold text-zinc-800 dark:text-zinc-100">{{ $beneficiaireInfo['nom'] }}</p>
                <p class="text-xs text-zinc-500">Enregistré par {{ $beneficiaireInfo['ong'] }}</p>
            </div>
            <button wire:click="reinitialiser" class="text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                Changer
            </button>
        </div>

        <form wire:submit="distribuer" class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <flux:heading size="lg">Détails de l'aide</flux:heading>

                {{-- Projet d'aide --}}
                @if($projets->isEmpty())
                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 dark:border-yellow-700 dark:bg-yellow-900/20">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                Aucun projet d'aide actif. Créez un projet avant de distribuer.
                            </p>
                            <flux:button :href="route('ong.projets.creer')" wire:navigate variant="ghost" size="sm">
                                Créer un projet →
                            </flux:button>
                        </div>
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Projet d'aide <span class="text-red-500">*</span>
                        </label>
                        <select
                            wire:model="projetAideId"
                            wire:change="checkDuplicate"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                        >
                            <option value="">Sélectionner un projet...</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}">
                                    {{ $projet->nom }} ({{ $projet->typeAide->nom }} · exp. {{ $projet->date_expiration->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('projetAideId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Alerte doublon --}}
                @if($duplicateInfo)
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 dark:border-red-700 dark:bg-red-900/20">
                        <div class="flex items-start gap-2">
                            <span class="mt-0.5 font-bold text-red-600 dark:text-red-400">⛔</span>
                            <div>
                                <p class="text-sm font-semibold text-red-800 dark:text-red-200">DOUBLON ACTIF — Distribution bloquée</p>
                                <p class="mt-0.5 text-xs text-red-700 dark:text-red-300">
                                    Ce bénéficiaire reçoit déjà une aide
                                    <strong>{{ $duplicateInfo['aide'] }}</strong>
                                    distribuée par <strong>{{ $duplicateInfo['ong'] }}</strong>,
                                    valide jusqu'au <strong>{{ $duplicateInfo['expiration'] }}</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Notes (optionnel)</label>
                    <textarea
                        wire:model="notes"
                        rows="2"
                        placeholder="Observations sur la distribution..."
                        class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                    ></textarea>
                    @error('notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <flux:button wire:click="reinitialiser" variant="ghost">
                    Annuler
                </flux:button>
                <flux:button
                    type="submit"
                    variant="primary"
                    :disabled="(bool) $duplicateInfo"
                >
                    Enregistrer l'aide distribuée
                </flux:button>
            </div>
        </form>
    @endif

</div>
