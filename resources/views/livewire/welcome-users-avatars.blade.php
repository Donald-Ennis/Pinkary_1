<div class="relative flex w-full items-center justify-center gap-3 overflow-hidden text-2xl">
    <div class="absolute left-0 h-full w-24 bg-gradient-to-r from-gray-950 to-transparent"></div>
    <div class="absolute right-0 h-full w-24 bg-gradient-to-l from-gray-950 to-transparent"></div>

    @foreach ($users as $user)
        <a
            class="flex-shrink-0 transition-opacity hover:opacity-90"
            href="{{ route('profile.show', ['user' => $user->username]) }}"
        >
            <img
                src="{{ $user->avatar ? url($user->avatar) : $user->avatar_url }}"
                alt="{{ $user->username }}"
                class="h-12 w-12 rounded-full"
            />
        </a>
    @endforeach
</div>
