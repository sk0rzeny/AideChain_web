<div class="max-w-lg mx-auto p-6">
    <flux:heading size="xl" class="mb-1">Rattachement à une ONG</flux:heading>
    <flux:text class="mb-6">Sélectionnez l'ONG pour laquelle vous travaillez en tant qu'agent terrain.</flux:text>

    @if ($statutDemande === 'pending')
        <div class="rounded-lg border border-yellow-300 bg-yellow-50 p-4 dark:border-yellow-700 dark:bg-yellow-900/20">
            <p class="font-medium text-yellow-800 dark:text-yellow-200">Demande en attente de confirmation</p>
            <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                Votre demande de rattachement à <strong>{{ $nomOng }}</strong> est en cours de traitement par le représentant de l'ONG.
            </p>
        </div>

    @elseif ($statutDemande === 'accepted')
        <div class="rounded-lg border border-green-300 bg-green-50 p-4 dark:border-green-700 dark:bg-green-900/20">
            <p class="font-medium text-green-800 dark:text-green-200">Rattachement confirmé</p>
            <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                Vous êtes rattaché à <strong>{{ $nomOng }}</strong>. Vous pouvez maintenant enregistrer des bénéficiaires et distribuer des aides.
            </p>
            <a
                href="{{ route('dashboard') }}"
                wire:navigate
                class="mt-3 inline-block rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
            >
                Accéder au tableau de bord →
            </a>
        </div>

    @else
        @if ($statutDemande === 'rejected')
            <div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-4 dark:border-red-700 dark:bg-red-900/20">
                <p class="font-medium text-red-800 dark:text-red-200">Demande refusée</p>
                <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                    Votre demande a été refusée. Vous pouvez choisir une autre ONG.
                </p>
            </div>
        @endif

        <form wire:submit="soumettre" class="flex flex-col gap-4">
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Choisir une ONG</label>
                <select
                    wire:model="ong_id"
                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
                >
                    <option value="">-- Sélectionner une ONG --</option>
                    @foreach($ongs as $ong)
                        <option value="{{ $ong->id }}">{{ $ong->nom }}</option>
                    @endforeach
                </select>
                @error('ong_id')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <flux:button type="submit" variant="primary">
                Soumettre la demande
            </flux:button>
        </form>
    @endif
</div>
