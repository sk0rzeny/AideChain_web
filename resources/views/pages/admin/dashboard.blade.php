<x-layouts::app :title="__('messages.admin_title')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('messages.admin_panel') }}</flux:heading>
                <flux:text class="mt-1">{{ __('messages.admin_panel_sub') }}</flux:text>
            </div>
            <flux:button :href="route('coordination')" wire:navigate variant="primary" size="sm">
                {{ __('messages.coord_dashboard_link') }}
            </flux:button>
        </div>

        <livewire:admin.gestion-ongs />

    </div>
</x-layouts::app>
