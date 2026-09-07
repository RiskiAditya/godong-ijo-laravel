<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- Include SEO Meta Tags Partial --}}
    @include('partials.seo-meta', ['seoData' => $seoData])
    
</head>
<body>
    <h1>SEO Meta Tags Test Page</h1>
    <p>View page source to see the rendered meta tags.</p>
    
    <h2>SEO Data:</h2>
    <pre>{{ json_encode($seoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
</body>
</html>
