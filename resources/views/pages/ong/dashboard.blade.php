<x-layouts::app :title="__('messages.ong_dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        @if(!$ong)
            <div>
                <flux:heading size="xl">{{ __('messages.ong_dashboard') }}</flux:heading>
                <flux:text class="mt-1">{{ __('messages.ong_welcome') }}</flux:text>
            </div>

            <div class="rounded-xl border-2 border-dashed border-zinc-300 p-10 text-center dark:border-zinc-600">
                <div class="mb-4 text-4xl">🏢</div>
                <p class="mb-1 text-lg font-semibold text-zinc-800 dark:text-zinc-200">{{ __('messages.ong_none_registered') }}</p>
                <p class="mb-6 text-sm text-zinc-500">{{ __('messages.ong_register_prompt') }}</p>
                <flux:button :href="route('ong.inscription')" variant="primary" wire:navigate>
                    {{ __('messages.register_ong_btn') }}
                </flux:button>
            </div>

        @elseif($ong->statut === 'pending')
            <div>
                <flux:heading size="xl">{{ $ong->nom }}</flux:heading>
                <flux:text class="mt-1">{{ __('messages.ong_dashboard_sub') }}</flux:text>
            </div>

            <div class="rounded-lg border border-yellow-300 bg-yellow-50 p-5 dark:border-yellow-700 dark:bg-yellow-900/20">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 text-xl">⏳</span>
                    <div>
                        <p class="font-semibold text-yellow-800 dark:text-yellow-200">{{ __('messages.ong_status_pending') }}</p>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                            {{ __('messages.ong_pending_text') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="mb-3 text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ __('messages.submitted_info') }}</p>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-zinc-400">{{ __('messages.email') }}</dt>
                        <dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $ong->email }}</dd>
                    </div>
                    @if($ong->telephone)
                    <div>
                        <dt class="text-zinc-400">{{ __('messages.phone') }}</dt>
                        <dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $ong->telephone }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-zinc-400">{{ __('messages.documents') }}</dt>
                        <dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $ong->documents->count() }} {{ __('messages.files_submitted') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-400">{{ __('messages.submission_date') }}</dt>
                        <dd class="font-medium text-zinc-800 dark:text-zinc-200">{{ $ong->created_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </div>

        @elseif($ong->statut === 'rejected')
            <div>
                <flux:heading size="xl">{{ $ong->nom }}</flux:heading>
            </div>

            <div class="rounded-lg border border-red-300 bg-red-50 p-5 dark:border-red-700 dark:bg-red-900/20">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 text-xl">❌</span>
                    <div class="flex-1">
                        <p class="font-semibold text-red-800 dark:text-red-200">{{ __('messages.ong_status_rejected') }}</p>
                        <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                            {{ __('messages.ong_rejected_text') }}
                        </p>
                        <flux:button :href="route('ong.inscription')" variant="danger" class="mt-3" wire:navigate>
                            {{ __('messages.ong_resubmit') }}
                        </flux:button>
                    </div>
                </div>
            </div>

        @elseif($ong->statut === 'active')
            <div>
                <flux:heading size="xl">{{ $ong->nom }}</flux:heading>
                <flux:text class="mt-1">{{ __('messages.ong_active_sub') }}</flux:text>
            </div>

            <livewire:ong.dashboard-ong />
        @endif

    </div>
</x-layouts::app>
