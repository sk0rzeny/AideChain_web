<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            {{-- Language Switcher --}}
            <div class="absolute top-4 end-4 flex items-center gap-1">
                @foreach(['en' => 'EN', 'fr' => 'FR', 'ar' => 'AR'] as $locale => $label)
                    <form method="POST" action="{{ route('locale', $locale) }}">
                        @csrf
                        <button type="submit"
                            class="px-2 py-0.5 text-xs rounded font-medium transition cursor-pointer
                                   {{ app()->getLocale() === $locale
                                        ? 'bg-accent text-white'
                                        : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200' }}">
                            {{ $label }}
                        </button>
                    </form>
                @endforeach
            </div>

            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
