<div {{ $attributes->merge(['class' => '-mx-px overflow-x-auto rounded-xl border border-zinc-200/80']) }}>
    <table class="w-full min-w-[640px] border-collapse text-left text-sm text-zinc-700">
        {{ $slot }}
    </table>
</div>
