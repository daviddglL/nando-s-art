{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url><loc>{{ route('landing') }}</loc></url>
    <url><loc>{{ route('galeria') }}</loc></url>
    <url><loc>{{ route('paginas.sobre-mi') }}</loc></url>
    <url><loc>{{ route('paginas.contacto') }}</loc></url>
@foreach ($obras as $obra)
    <url>
        <loc>{{ route('obras.show', $obra->slug) }}</loc>
        <lastmod>{{ $obra->updated_at?->toAtomString() }}</lastmod>
    </url>
@endforeach
</urlset>
