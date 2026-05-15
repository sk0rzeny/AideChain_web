<div class="mx-auto max-w-2xl p-6">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Créer un projet d'aide</flux:heading>
            <flux:text class="mt-1">Définissez le cadre de l'aide — les agents sélectionneront ce projet lors des distributions.</flux:text>
        </div>
        <flux:button :href="route('dashboard')" wire:navigate variant="ghost" size="sm">
            ← Tableau de bord
        </flux:button>
    </div>

    <form wire:submit="enregistrer" class="flex flex-col gap-6">

        <div class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="lg">Informations du projet</flux:heading>

            <div>
                <flux:input
                    wire:model="nom"
                    label="Nom du projet"
                    placeholder="Ex: Aide aux enfants de Mayo Kebbi"
                    required
                />
                @error('nom') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Type d'aide <span class="text-red-500">*</span>
                    </label>
                    <select
                        wire:model="typeAideId"
                        class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-blue-500 focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                    >
                        <option value="">Sélectionner...</option>
                        @foreach($typesAide as $type)
                            <option value="{{ $type->id }}">{{ $type->nom }}</option>
                        @endforeach
                    </select>
                    @error('typeAideId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <flux:input
                        wire:model="dateExpiration"
                        label="Valide jusqu'au"
                        type="date"
                        :min="now()->addDay()->toDateString()"
                        required
                    />
                    @error('dateExpiration') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <flux:input
                    wire:model="zoneCible"
                    label="Zone ciblée (optionnel)"
                    placeholder="Ex: Région de Mayo Kebbi, villages de Bongor"
                />
                @error('zoneCible') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Description (optionnel)</label>
                <textarea
                    wire:model="description"
                    rows="3"
                    placeholder="Contexte, critères d'éligibilité, partenaires financeurs..."
                    class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                ></textarea>
                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <flux:button :href="route('dashboard')" wire:navigate variant="ghost">
                Annuler
            </flux:button>
            <flux:button type="submit" variant="primary">
                Créer le projet
            </flux:button>
        </div>

    </form>

</div>
