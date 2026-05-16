<x-layouts::auth :title="__('messages.register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('messages.register_title')" :description="__('messages.register_subtitle')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('messages.name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('messages.full_name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('messages.email_address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('messages.password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('messages.password')"
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

            <!-- Rôle -->
            <div class="flex flex-col gap-2">
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('messages.i_am') }}</label>
                <div class="flex flex-col gap-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-zinc-200 p-3 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                        <input type="radio" name="role" value="ong_representant" checked class="accent-blue-600" />
                        <div>
                            <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ __('messages.role_representative') }}</p>
                            <p class="text-xs text-zinc-500">{{ __('messages.role_representative_desc') }}</p>
                        </div>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-zinc-200 p-3 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                        <input type="radio" name="role" value="ong_agent" class="accent-blue-600" />
                        <div>
                            <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ __('messages.role_agent') }}</p>
                            <p class="text-xs text-zinc-500">{{ __('messages.role_agent_desc') }}</p>
                        </div>
                    </label>
                </div>
                @error('role')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('messages.register') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('messages.already_account') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('messages.log_in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
