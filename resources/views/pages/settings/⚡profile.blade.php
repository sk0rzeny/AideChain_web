<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules, WithFileUploads;

    public string $name = '';
    public string $email = '';

    #[Validate(['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,gif,webp'])]
    public $photo = null;

    public function mount(): void
    {
        $this->name  = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($this->photo) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->profile_photo_path = $this->photo->store('profile-photos', 'public');
            $this->photo = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('messages.profile_updated'));
    }

    public function removePhoto(): void
    {
        $user = Auth::user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $user->save();
        }

        $this->photo = null;

        Flux::toast(variant: 'success', text: __('messages.photo_deleted'));
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Flux::toast(text: __('messages.verification_sent'));
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('messages.profile_settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('messages.profile')" :subheading="__('messages.update_profile')">

        {{-- Photo de profil --}}
        <div class="my-6 flex items-center gap-6">
            <div class="relative shrink-0">
                @if ($photo)
                    <img src="{{ $photo->temporaryUrl() }}" alt="Aperçu"
                         class="size-20 rounded-full object-cover ring-2 ring-accent ring-offset-2">
                @elseif (Auth::user()->profilePhotoUrl())
                    <img src="{{ Auth::user()->profilePhotoUrl() }}" alt="{{ Auth::user()->name }}"
                         class="size-20 rounded-full object-cover ring-2 ring-accent ring-offset-2">
                @else
                    <flux:avatar
                        :name="Auth::user()->name"
                        :initials="Auth::user()->initials()"
                        class="size-20 text-xl"
                    />
                @endif
            </div>

            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <label for="photo-input"
                           class="cursor-pointer rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                        {{ $photo ? __('messages.change') : __('messages.choose_photo') }}
                    </label>
                    <input id="photo-input" type="file" wire:model="photo"
                           accept="image/jpeg,image/png,image/gif,image/webp"
                           class="sr-only">

                    @if (Auth::user()->profilePhotoUrl() && ! $photo)
                        <flux:button wire:click="removePhoto" variant="ghost" size="sm"
                                     wire:confirm="{{ __('messages.delete_account_confirm') }}">
                            {{ __('messages.delete') }}
                        </flux:button>
                    @endif

                    @if ($photo)
                        <flux:button wire:click="$set('photo', null)" variant="ghost" size="sm">
                            {{ __('messages.cancel') }}
                        </flux:button>
                    @endif
                </div>

                <flux:text class="text-xs text-zinc-400">
                    JPG, PNG, GIF ou WebP · max 2 Mo
                </flux:text>

                @error('photo')
                    <flux:text class="text-xs text-red-500">{{ $message }}</flux:text>
                @enderror
            </div>
        </div>

        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('messages.name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('messages.email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('messages.email_unverified') }}
                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('messages.click_resend') }}
                            </flux:link>
                        </flux:text>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" data-test="update-profile-button">
                    {{ __('messages.save') }}
                </flux:button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
