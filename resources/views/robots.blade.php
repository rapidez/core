User-agent: *
Crawl-delay: 1
Allow: /*.js?
Allow: /*.css?
Allow: /*?page=
Disallow: /*?
Disallow: /account$
Disallow: /account/*
Disallow: /cart$
Disallow: /cart/*
Disallow: /checkout$
Disallow: /checkout/*
Disallow: /search$
Disallow: /search/*

{{--
    Includes the Magento robots.txt instructions.
    NOTE: Make sure to clean these instructions up, or to not include them at all.
--}}
{{ Rapidez::config('design/search_engine_robots/custom_instructions') }}

Sitemap: {{ url('sitemap.xml') }}
