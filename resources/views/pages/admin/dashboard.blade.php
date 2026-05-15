<x-layouts::app :title="__('Administration — AideChain')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Panneau d'administration</flux:heading>
                <flux:text class="mt-1">Gérez les organisations partenaires de la plateforme AideChain.</flux:text>
            </div>
            <flux:button :href="route('coordination')" wire:navigate variant="primary" size="sm">
                Tableau de coordination
            </flux:button>
        </div>

        <livewire:admin.gestion-ongs />

    </div>
</x-layouts::app>
