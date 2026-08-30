<?php
/**
 * Crawl every URL in the sitemap and report the title and description each one
 * actually renders, flagging anything a search engine would truncate.
 *
 * Checking the copy file alone is not enough: the finished tag is the copy plus
 * the site suffix, and pages without hand-written copy fall back to composed
 * text that has its own lengths.
 *
 *   php bin/audit-seo.php [base-url]
 *
 * Defaults to the local dev server. Exits non-zero if anything is over budget.
 */

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Seo;

$base = rtrim($argv[1] ?? 'http://127.0.0.1:8899', '/');

$sitemap = @file_get_contents($base . '/sitemap.xml');

if ($sitemap === false) {
    exit("Could not read {$base}/sitemap.xml — is the server running?\n");
}

preg_match_all('#<loc>(.*?)</loc>#', $sitemap, $matches);
$urls = array_unique($matches[1]);

if ($urls === []) {
    exit("No URLs in the sitemap.\n");
}

$problems = [];
$rows     = [];

foreach ($urls as $url) {
    // The sitemap carries whatever host it was generated for; follow the base
    // we were asked to audit instead.
    $path = parse_url($url, PHP_URL_PATH) ?: '/';
    $html = @file_get_contents($base . $path);

    if ($html === false) {
        $problems[] = "could not fetch {$path}";
        continue;
    }

    preg_match('#<title>(.*?)</title>#s', $html, $t);
    preg_match('#<meta name="description" content="(.*?)"#s', $html, $d);
    preg_match('#<meta property="og:image" content="(.*?)"#s', $html, $i);

    $title = html_entity_decode($t[1] ?? '', ENT_QUOTES, 'UTF-8');
    $desc  = html_entity_decode($d[1] ?? '', ENT_QUOTES, 'UTF-8');
    $image = $i[1] ?? '';

    $titleLen = mb_strlen($title);
    $descLen  = mb_strlen($desc);

    if ($title === '')            { $problems[] = "no title: {$path}"; }
    if ($desc === '')             { $problems[] = "no description: {$path}"; }
    if ($image === '')            { $problems[] = "no og:image: {$path}"; }
    if ($titleLen > Seo::TITLE_MAX) { $problems[] = "title {$titleLen} chars: {$path}"; }
    if ($descLen > Seo::DESC_MAX)   { $problems[] = "description {$descLen} chars: {$path}"; }

    // A clamped string keeps its ellipsis, which is the tell that copy was cut.
    if (str_ends_with(rtrim($desc), '…')) { $problems[] = "description truncated: {$path}"; }
    if (str_ends_with(rtrim($title), '…')) { $problems[] = "title truncated: {$path}"; }

    $rows[] = [$path, $titleLen, $descLen];
}

usort($rows, static fn (array $a, array $b): int => $b[1] <=> $a[1]);

printf("%d pages audited\n\n", count($rows));
printf("%-44s %6s %6s\n", 'PATH', 'TITLE', 'DESC');
echo str_repeat('-', 58), PHP_EOL;

foreach (array_slice($rows, 0, 12) as [$path, $titleLen, $descLen]) {
    printf("%-44s %6d %6d\n", mb_strimwidth($path, 0, 43, '…'), $titleLen, $descLen);
}

printf("\nlimits: title %d, description %d\n\n", Seo::TITLE_MAX, Seo::DESC_MAX);

if ($problems === []) {
    echo "Every page has a title, a description and a share image, all within budget.\n";
    exit(0);
}

echo count($problems) . " problem(s):\n";
foreach ($problems as $problem) {
    echo "  - {$problem}\n";
}

exit(1);
