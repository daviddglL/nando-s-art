@props(['estado'])
<span {{ $attributes->class(['inline-block text-sm font-semibold px-3 py-1 rounded-full whitespace-nowrap', $estado->badgeClasses()]) }}>
    {{ $estado->label() }}
</span>
