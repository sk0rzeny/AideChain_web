<div class="mx-auto max-w-2xl p-6">
    <div class="mb-6">
        <flux:heading size="xl">Enregistrer mon ONG</flux:heading>
        <flux:text class="mt-1">Remplissez les informations de votre organisation. Votre demande sera examinée par l'administrateur.</flux:text>
    </div>

    <form wire:submit="soumettre" class="flex flex-col gap-8">

        {{-- Informations ONG --}}
        <div class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="lg">Informations de l'organisation</flux:heading>

            <flux:input
                wire:model="nom"
                label="Nom de l'ONG"
                placeholder="Ex: Action contre la Faim Tchad"
                required
            />
            @error('nom') <p class="text-sm text-red-500">{{ $message }}</p> @enderror

            <flux:input
                wire:model="email"
                label="Adresse email officielle"
                type="email"
                placeholder="contact@ong.td"
                required
            />
            @error('email') <p class="text-sm text-red-500">{{ $message }}</p> @enderror

            <flux:input
                wire:model="telephone"
                label="Téléphone"
                placeholder="+235 XX XX XX XX"
            />

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Adresse / Siège social</label>
                <textarea
                    wire:model="adresse"
                    rows="3"
                    placeholder="Quartier, ville, pays..."
                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-blue-500 focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                ></textarea>
            </div>
        </div>

        {{-- Documents --}}
        <div class="flex flex-col gap-4 rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <div>
                <flux:heading size="lg">Documents officiels</flux:heading>
                <flux:text class="mt-1 text-sm">Formats acceptés : PDF, JPG, PNG (max 5 Mo par fichier). Au moins un document est requis.</flux:text>
            </div>

            {{-- Document 1 (obligatoire) --}}
            <div class="flex flex-col gap-2 rounded-lg border border-zinc-100 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Document 1 <span class="text-red-500">*</span></p>
                <flux:input wire:model="type1" label="Type de document" placeholder="Ex: Accréditation, Statuts, Lettre d'autorisation" required />
                @error('type1') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Fichier</label>
                    <input
                        type="file"
                        wire:model="fichier1"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-sm text-zinc-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-300"
                    />
                    <div wire:loading wire:target="fichier1" class="text-xs text-zinc-400">Chargement...</div>
                </div>
                @error('fichier1') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Document 2 (optionnel) --}}
            <div class="flex flex-col gap-2 rounded-lg border border-zinc-100 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Document 2 <span class="text-zinc-400">(optionnel)</span></p>
                <flux:input wire:model="type2" label="Type de document" placeholder="Ex: Rapport d'activité, Budget prévisionnel" />
                @error('type2') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Fichier</label>
                    <input
                        type="file"
                        wire:model="fichier2"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-sm text-zinc-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-300"
                    />
                    <div wire:loading wire:target="fichier2" class="text-xs text-zinc-400">Chargement...</div>
                </div>
                @error('fichier2') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Document 3 (optionnel) --}}
            <div class="flex flex-col gap-2 rounded-lg border border-zinc-100 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Document 3 <span class="text-zinc-400">(optionnel)</span></p>
                <flux:input wire:model="type3" label="Type de document" placeholder="Ex: Carte d'identité du représentant" />
                @error('type3') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Fichier</label>
                    <input
                        type="file"
                        wire:model="fichier3"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-sm text-zinc-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-300"
                    />
                    <div wire:loading wire:target="fichier3" class="text-xs text-zinc-400">Chargement...</div>
                </div>
                @error('fichier3') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="soumettre">Soumettre pour validation</span>
                <span wire:loading wire:target="soumettre">Envoi en cours...</span>
            </flux:button>
            <flux:link :href="route('dashboard')" wire:navigate class="text-sm text-zinc-500">Annuler</flux:link>
        </div>

    </form>
</div>
