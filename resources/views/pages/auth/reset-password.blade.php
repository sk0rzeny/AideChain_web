<x-layouts::auth :title="__('messages.reset_password_title')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('messages.reset_password_title')" :description="__('messages.reset_password_subtitle')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <flux:input
                name="email"
                value="{{ request('email') }}"
                :label="__('messages.email')"
                type="email"
                required
                autocomplete="email"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('messages.new_password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('messages.new_password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('messages.confirm_password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('messages.confirm_password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="reset-password-button">
                    {{ __('messages.reset_password') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts::auth>
