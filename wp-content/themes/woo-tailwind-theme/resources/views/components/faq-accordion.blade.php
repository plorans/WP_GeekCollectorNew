@props(['question'])

<div x-data="{ open: false }" class="w-full">

    <button @click="open = !open" class="inline-flex w-full items-center gap-x-2 bg-black px-4 py-3 text-left text-lg font-medium uppercase text-white md:text-xl">
        <svg class="size-4 transition-transform duration-300" :class="{ 'rotate-90': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2">
            <path d="m9 6 6 6-6 6"></path>
        </svg>

        {{ $question }}

    </button>

    <div x-show="open" x-collapse.duration.400ms x-transition.opacity x-cloak class="w-full overflow-hidden uppercase">
        <div class="rounded-lg bg-black px-4 py-3">
            <p class="text-white dark:text-neutral-400">
                {{ $slot }}
            </p>
        </div>
    </div>
</div>
