<div class="mb-12 w-full text-slate-200">
    <div class="mb-8 w-full max-w-md">
        <div class="relative flex items-center py-1">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="absolute left-5 z-50 size-5"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"></path>
            </svg>

            <x-text-input
                x-ref="searchInput"
                x-init="if ($wire.focusInput) $refs.searchInput.focus()"
                wire:model.live.debounce.500ms="query"
                name="q"
                placeholder="Search for users..."
                class="w-full !rounded-2xl !bg-slate-950 !bg-opacity-80 py-3 pl-14"
            />
        </div>
    </div>

    @if ($users->isEmpty())
        <section class="rounded-lg">
            <p class="my-8 text-center text-lg text-slate-500">No users found.</p>
        </section>
    @else
        <section class="max-w-2xl">
            <ul class="flex flex-col gap-2">
                @foreach ($users as $user)
                    <li>
                        <a
                            href="{{ route('profile.show', ['username' => $user->username]) }}"
                            class="group flex items-center gap-3 rounded-2xl border border-slate-900 bg-slate-950 bg-opacity-80 p-4 transition-colors hover:bg-slate-900"
                            wire:navigate
                        >
                            <figure class="h-12 w-12 flex-shrink-0 overflow-hidden {{ $user->is_company_verified ? 'rounded-md' : 'rounded-full' }} bg-slate-800 transition-opacity group-hover:opacity-90">
                                <img
                                    class="h-12 w-12 {{ $user->is_company_verified ? 'rounded-md' : 'rounded-full' }}"
                                    src="{{ $user->avatar ? url($user->avatar) : $user->avatar_url }}"
                                    alt="{{ $user->username }}"
                                />
                            </figure>
                            <div class="flex flex-col overflow-hidden text-sm">
                                <div class="flex items-center space-x-2">
                                    <p class="truncate font-medium">
                                        {{ $user->name }}
                                    </p>

                                    @if ($user->is_verified && $user->is_company_verified)
                                        <x-icons.verified-company :color="$user->right_color" class="size-4" />
                                    @elseif ($user->is_verified)
                                        <x-icons.verified :color="$user->right_color" class="size-4" />
                                    @endif
                                </div>
                                <p class="truncate text-slate-500 transition-colors group-hover:text-slate-400">
                                    {{ '@'.$user->username }}
                                </p>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
