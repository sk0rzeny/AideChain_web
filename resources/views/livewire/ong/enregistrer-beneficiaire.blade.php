<div class="mx-auto max-w-2xl p-6">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Enregistrer un bénéficiaire</flux:heading>
            <flux:text class="mt-1">Les informations identitaires sont hashées avant stockage.</flux:text>
        </div>
        <flux:button :href="route('dashboard')" wire:navigate variant="ghost" size="sm">
            ← Tableau de bord
        </flux:button>
    </div>

    @if($success)
        {{-- État succès --}}
        <div class="rounded-xl border border-green-200 bg-green-50 p-6 dark:border-green-700 dark:bg-green-900/20">
            <div class="flex items-start gap-4">
                <span class="text-2xl">✅</span>
                <div class="flex-1">
                    <p class="font-semibold text-green-800 dark:text-green-200">Bénéficiaire enregistré avec succès</p>
                    @if($checkResult && $checkResult['type'] !== 'new')
                        <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                            Cette personne était déjà dans le registre — aucun doublon créé.
                        </p>
                    @else
                        <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                            Le bénéficiaire a été ajouté au registre partagé AideChain.
                        </p>
                    @endif
                    <div class="mt-4 flex gap-3">
                        <flux:button
                            :href="route('ong.aides.nouvelle') . '?beneficiaire_id=' . $beneficiaireId"
                            wire:navigate
                            variant="primary"
                            size="sm"
                        >
                            Distribuer une aide →
                        </flux:button>
                        <flux:button wire:click="reinitialiser" variant="ghost" size="sm">
                            Enregistrer un autre bénéficiaire
                        </flux:button>
                        <flux:button :href="route('dashboard')" wire:navigate variant="ghost" size="sm">
                            Tableau de bord
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- Formulaire --}}
        <form wire:submit="enregistrer" class="flex flex-col gap-6">

            {{-- Identité --}}
            <div class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <flux:heading size="lg">Identité</flux:heading>
                <flux:text class="text-sm">Ces trois champs génèrent l'identifiant unique — renseignez-les avec soin.</flux:text>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:input
                            wire:model="prenom"
                            wire:blur="checkIdentity"
                            label="Prénom"
                            placeholder="Ex: Amina"
                            required
                        />
                        @error('prenom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <flux:input
                            wire:model="nom"
                            wire:blur="checkIdentity"
                            label="Nom de famille"
                            placeholder="Ex: Hassan"
                            required
                        />
                        @error('nom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="max-w-xs">
                    <flux:input
                        wire:model="dateNaissance"
                        wire:blur="checkIdentity"
                        label="Date de naissance"
                        type="date"
                        required
                    />
                    @error('dateNaissance') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Résultat de vérification doublon --}}
                @if($checkResult)
                    @if($checkResult['type'] === 'new')
                        <div class="flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 dark:border-green-700 dark:bg-green-900/20">
                            <span class="text-green-600 dark:text-green-400">●</span>
                            <p class="text-sm font-medium text-green-700 dark:text-green-300">
                                Nouveau bénéficiaire — aucun enregistrement trouvé dans le registre.
                            </p>
                        </div>

                    @elseif($checkResult['type'] === 'exists')
                        <div class="flex items-center gap-2 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 dark:border-yellow-700 dark:bg-yellow-900/20">
                            <span class="text-yellow-600 dark:text-yellow-400">●</span>
                            <div>
                                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                    Personne déjà dans le registre — {{ $checkResult['nom'] }}
                                </p>
                                <p class="mt-0.5 text-xs text-yellow-700 dark:text-yellow-300">
                                    Enregistrée par {{ $checkResult['ong'] }}. Aucune aide active en cours — vous pouvez continuer.
                                </p>
                            </div>
                        </div>

                    @elseif($checkResult['type'] === 'duplicate')
                        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 dark:border-red-700 dark:bg-red-900/20">
                            <div class="flex items-start gap-2">
                                <span class="mt-0.5 text-red-600 dark:text-red-400">⚠</span>
                                <div>
                                    <p class="text-sm font-semibold text-red-800 dark:text-red-200">
                                        DOUBLON ACTIF — {{ $checkResult['nom'] }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-red-700 dark:text-red-300">
                                        Reçoit déjà une aide <strong>{{ $checkResult['aide'] }}</strong>
                                        distribuée par <strong>{{ $checkResult['ong'] }}</strong>,
                                        valide jusqu'au <strong>{{ $checkResult['expiration'] }}</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Profil --}}
            <div class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <flux:heading size="lg">Profil</flux:heading>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Genre <span class="text-red-500">*</span></label>
                        <select
                            wire:model="genre"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                        >
                            <option value="">Sélectionner...</option>
                            <option value="homme">Masculin</option>
                            <option value="femme">Féminin</option>
                            <option value="autre">Autre</option>
                        </select>
                        @error('genre') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Catégorie <span class="text-red-500">*</span></label>
                        <select
                            wire:model="categorie"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                        >
                            <option value="">Sélectionner...</option>
                            <option value="individu">Individu</option>
                            <option value="famille">Famille</option>
                            <option value="enfant">Enfant</option>
                            <option value="femme_chef_menage">Femme chef de ménage</option>
                            <option value="deplacement_interne">Personne déplacée interne</option>
                        </select>
                        @error('categorie') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Notes (optionnel)</label>
                    <textarea
                        wire:model="notes"
                        rows="3"
                        placeholder="Informations complémentaires sur la situation du bénéficiaire..."
                        class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                    ></textarea>
                    @error('notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <flux:button :href="route('dashboard')" wire:navigate variant="ghost">
                    Annuler
                </flux:button>
                <flux:button type="submit" variant="primary">
                    @if($checkResult && in_array($checkResult['type'], ['exists', 'duplicate']))
                        Continuer avec ce bénéficiaire
                    @else
                        Enregistrer le bénéficiaire
                    @endif
                </flux:button>
            </div>

        </form>
    @endif

</div>
