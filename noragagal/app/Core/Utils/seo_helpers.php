<?php
/**
 * SEO Helper Functions for Homepage
 * Pillar 6.1: Security + SEO Optimization
 * Generates optimized meta tags based on SEO wordlist
 */

/**
 * Generate SEO-optimized title
 * Returns: Primary Keyword + Secondary + Brand
 */
function generateSEOTitle(): string {
    return 'Notaris Sri Anah SH.M.Kn Cirebon - Jasa Notaris & PPAT Terpercaya';
}

/**
 * Generate SEO-optimized description (155-160 characters)
 * Includes: Primary keywords + location + CTA
 */
function generateSEODescription(): string {
    return 'Kantor Notaris & PPAT Sri Anah SH.M.Kn di Cirebon. Melayani akta tanah, jual beli rumah, pendirian PT, CV, legalisasi dokumen. Profesional & berizin. Hub: 0857-4789-8811';
}

/**
 * Generate SEO keywords (comma-separated)
 * Based on seo_wordlist_notaris_sri_anah_complete.txt
 */
function generateSEOKeywords(): string {
    return 'notaris cirebon, notaris sri anah, ppat cirebon, jasa notaris cirebon, akta tanah cirebon, notaris jual beli tanah, notaris pendirian pt, legalisasi dokumen cirebon, notaris kedawung, notaris tengah tani, biaya notaris cirebon, kantor notaris cirebon, notaris profesional, notaris terpercaya';
}

/**
 * Generate Schema.org JSON-LD for Local Business
 * Pillar 6.1: Structured data for SEO
 */
function generateSchemaJSONLD(): string {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Notary',
        'name' => 'Notaris Sri Anah SH.M.Kn',
        'alternateName' => 'Notaris & PPAT Sri Anah',
        'url' => APP_URL,
        'logo' => APP_URL . '/public/assets/img/logo.png',
        'description' => 'Kantor Notaris & PPAT terpercaya di Cirebon, melayani pembuatan akta tanah, jual beli properti, pendirian PT, CV, dan legalisasi dokumen.',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Jl. Sultan Ageng Tirtayasa No. 123, Kedawung',
            'addressLocality' => 'Cirebon',
            'addressRegion' => 'Jawa Barat',
            'postalCode' => '45152',
            'addressCountry' => 'ID'
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => -6.7063,
            'longitude' => 108.557
        ],
        'telephone' => '+6285747898811',
        'email' => 'notaris.srianah@gmail.com',
        'openingHoursSpecification' => [
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => '08:00',
                'closes' => '16:00'
            ],
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => 'Saturday',
                'opens' => '08:00',
                'closes' => '12:00'
            ]
        ],
        'priceRange' => '$$',
        'areaServed' => [
            '@type' => 'City',
            'name' => 'Cirebon'
        ]
    ];
    
    return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}

/**
 * Generate Open Graph meta tags
 */
function generateOGTags($title, $description, $image): string {
    $ogTags = '';
    $ogTags .= '<meta property="og:type" content="website">' . "\n";
    $ogTags .= '<meta property="og:url" content="' . htmlspecialchars(APP_URL) . '">' . "\n";
    $ogTags .= '<meta property="og:site_name" content="' . APP_NAME . '">' . "\n";
    $ogTags .= '<meta property="og:title" content="' . htmlspecialchars($title) . '">' . "\n";
    $ogTags .= '<meta property="og:description" content="' . htmlspecialchars($description) . '">' . "\n";
    $ogTags .= '<meta property="og:image" content="' . htmlspecialchars($image) . '">' . "\n";
    $ogTags .= '<meta property="og:locale" content="id_ID">' . "\n";
    
    return $ogTags;
}

/**
 * Generate Twitter Card meta tags
 */
function generateTwitterTags($title, $description, $image): string {
    $twitterTags = '';
    $twitterTags .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
    $twitterTags .= '<meta name="twitter:url" content="' . htmlspecialchars(APP_URL) . '">' . "\n";
    $twitterTags .= '<meta name="twitter:title" content="' . htmlspecialchars($title) . '">' . "\n";
    $twitterTags .= '<meta name="twitter:description" content="' . htmlspecialchars($description) . '">' . "\n";
    $twitterTags .= '<meta name="twitter:image" content="' . htmlspecialchars($image) . '">' . "\n";
    
    return $twitterTags;
}

/**
 * Generate Geo meta tags for Local SEO
 */
function generateGeoTags(): string {
    $geoTags = '';
    $geoTags .= '<meta name="geo.region" content="ID-JB">' . "\n";
    $geoTags .= '<meta name="geo.placename" content="Cirebon">' . "\n";
    $geoTags .= '<meta name="geo.position" content="-6.7063;108.557">' . "\n";
    $geoTags .= '<meta name="ICBM" content="-6.7063, 108.557">' . "\n";
    
    return $geoTags;
}
