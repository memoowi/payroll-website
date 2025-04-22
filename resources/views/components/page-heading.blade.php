<div class="relative mb-4 w-full" :headingText="$headingText" :descText="$descText">
    <flux:heading size="xl" level="1">{{ $headingText ?? 'Page Heading' }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ $descText ?? 'Page Description' }}</flux:subheading>
    <flux:separator variant="subtle" />
</div>
