<?php
// Database connection settings
$host = 'localhost';
$db = 'task2-db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Set up the PDO connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Database connection established successfully.\n";
} catch (\PDOException $e) {
    echo "Failed to connect to database: " . $e->getMessage() . "\n";
    exit();
}

/**
 * Download the HTML content of the specified URL.
 *
 * @param string $url URL of the page to download
 *
 * @return string HTML content of the page
 */
function downloadPage($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    curl_close($ch);

    return $html;
}

/**
 * Extract the required data (title, slogan, logo) from the HTML content.
 *
 * @param string $html HTML content of the page
 *
 * @return array Extracted data
 */
function extractData($html)
{
    $data = [];
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    // Extracting title
    $titleNode = $xpath->query('//h1[contains(@class, "central-textlogo-wrapper")]/span[contains(@class, "central-textlogo__image")]');
    if ($titleNode->length > 0) {
        $title = trim($titleNode->item(0)->textContent);
    } else {
        $title = '';
        echo "Title extraction failed.\n";
    }

    // Extracting slogan
    $sloganNode = $xpath->query('//h1[contains(@class, "central-textlogo-wrapper")]/strong[contains(@class, "jsl10n")]');
    $slogan = $sloganNode->length > 0 ? trim($sloganNode->item(0)->textContent) : '';

    // Extracting logo
    $logoNode = $xpath->query('//img[contains(@class, "central-featured-logo")]');
    $logo = $logoNode->length > 0 ? $logoNode->item(0)->getAttribute('src') : '';

    // Prepare data array
    if ($title && $slogan && $logo) {
        $data = [
            'title' => substr($title, 0, 230),
            'abstract' => substr($slogan, 0, 256),
            'picture' => substr($logo, 0, 240),
            'url' => 'https://www.wikipedia.org/' // Example URL, adjust as needed
        ];
    } else {
        echo "Data extraction failed.\n";
    }

    return $data;
}

/**
 * Save the extracted data into the database.
 *
 * @param PDO   $pdo  PDO connection object
 * @param array $data Array of extracted data
 *
 * @return void
 */
function saveData($pdo, $data)
{
    $stmt = $pdo->prepare("INSERT INTO wiki_sections (date_created, title, url, picture, abstract) VALUES (NOW(), :title, :url, :picture, :abstract)");

    try {
        $stmt->execute([
            'title' => $data['title'],
            'url' => $data['url'],
            'picture' => $data['picture'],
            'abstract' => $data['abstract']
        ]);
        echo "Data extraction and saving completed.\n";
    } catch (\PDOException $e) {
        echo 'Error: ' . $e->getMessage() . "\n";
    }
}

$url = 'https://www.wikipedia.org/';
$html = downloadPage($url);
$data = extractData($html);

if (!empty($data)) {
    saveData($pdo, $data);
} else {
    echo "No data extracted.\n";
}
?>
