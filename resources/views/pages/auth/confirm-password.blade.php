<x-layouts::auth :title="__('messages.confirm_password')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('messages.confirm_password')"
            :description="__('messages.confirm_password_hint')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="password"
                :label="__('messages.password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('messages.password')"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full" data-test="confirm-password-button">
                {{ __('messages.confirm') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
