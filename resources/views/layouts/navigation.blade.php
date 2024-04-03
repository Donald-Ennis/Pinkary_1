<nav>
    <div class="fixed right-0 top-0 z-50 mx-auto px-4">
        <div class="flex h-16 justify-between">
            <div class="flex"></div>
            <div class="flex items-center" x-data>
                @auth
                    <a href="{{ route('feed') }}" class="mr-2" wire:navigate>
                        <button
                            type="button"
                            class="{{ request()->routeIs('feed') ? 'text-slate-100' : 'text-slate-500 hover:text-slate-100' }} inline-flex items-center rounded-md border border-transparent bg-slate-900 px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out focus:outline-none"
                        >
                            <x-icons.home class="h-6 w-6" />
                        </button>
                    </a>

                    <a href="{{ route('profile.show', ['username' => auth()->user()->username]) }}" class="mr-2" wire:navigate>
                        <button
                            type="button"
                            class="{{ request()->fullUrlIs(route('profile.show', ['username' => auth()->user()->username])) ? 'text-slate-100' : 'text-slate-500 hover:text-slate-100' }} inline-flex items-center rounded-md border border-transparent bg-slate-900 px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out focus:outline-none"
                        >
                            <x-icons.user class="h-6 w-6" />
                        </button>
                    </a>

                    <a href="{{ route('explore') }}" class="mr-2" wire:navigate>
                        <button
                            type="button"
                            class="{{ request()->routeIs('explore') ? 'text-slate-100' : 'text-slate-500 hover:text-slate-100' }} inline-flex items-center rounded-md border border-transparent bg-slate-900 px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out focus:outline-none"
                        >
                            <x-icons.magnifying-glass class="h-6 w-6" />
                        </button>
                    </a>

                    <a href="{{ route('notifications.index') }}" class="mr-2" wire:navigate>
                        <button
                            type="button"
                            class="{{ request()->routeIs('notifications.index') ? 'text-slate-100' : 'text-slate-500 hover:text-slate-100' }} inline-flex items-center rounded-md border border-transparent bg-slate-900 px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out focus:outline-none"
                        >
                            <x-icons.bell class="h-6 w-6" />

                            <livewire:navigation.notifications-count.show />
                        </button>
                    </a>
                @endauth

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center rounded-md border border-transparent bg-slate-900 px-3 py-2 text-sm font-medium leading-4 text-slate-500 transition duration-150 ease-in-out hover:text-slate-100 focus:outline-none"
                        >
                            <x-icons.bars class="size-6" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @auth
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Settings') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-button onclick="event.preventDefault();this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-button>
                            </form>
                        @else
                            <x-dropdown-link :href="route('welcome')">
                                {{ __('Home') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('feed')" :class="request()->routeIs('feed') ? 'bg-slate-800' : ''">
                                {{ __('Feed') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('explore')" :class="request()->routeIs('explore') ? 'bg-slate-800' : ''">
                                {{ __('Explore') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('login')" :class="request()->routeIs('login') ? 'bg-slate-800' : ''">
                                {{ __('Log in') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('register')" :class="request()->routeIs('register') ? 'bg-slate-800' : ''">
                                {{ __('Register') }}
                            </x-dropdown-link>
                        @endauth
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</nav>
