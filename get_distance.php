<?php
// ===================================================
// get_distance.php
// Input : pincode
// Output: road distance in KM
// ===================================================

// 1. Validate input
if (!isset($_GET['pincode']) || !preg_match('/^[0-9]{6}$/', $_GET['pincode'])) {
    echo "";
    exit;
}

$pincode = $_GET['pincode'];

// ===================================================
// 2. Get centroid lat/lon of PIN code (Nominatim)
// ===================================================
$nominatimUrl =
    "https://nominatim.openstreetmap.org/search" .
    "?postalcode=" . urlencode($pincode) .
    "&country=India" .
    "&format=json" .
    "&limit=1";

// Nominatim requires a User-Agent
$opts = [
    "http" => [
        "header" => "User-Agent: HostelDistanceCalculator/1.0\r\n"
    ]
];

$context = stream_context_create($opts);
$response = file_get_contents($nominatimUrl, false, $context);

if ($response === false) {
    echo "";
    exit;
}

$data = json_decode($response, true);

// If PIN code not found
if (empty($data)) {
    echo "";
    exit;
}

// This is the *center of the PIN code area*
$homeLat = (float)$data[0]['lat'];
$homeLon = (float)$data[0]['lon'];

// ===================================================
// 3. College / Hostel fixed location
// 🔴 CHANGE THESE ONCE AND FOR ALL
// ===================================================
$collegeLat = 10.049102;   // example
$collegeLon = 76.331735;  // example

// ===================================================
// 4. OSRM road distance calculation
// IMPORTANT: lon,lat order
// ===================================================
$osrmUrl =
    "https://router.project-osrm.org/route/v1/driving/" .
    "{$homeLon},{$homeLat};{$collegeLon},{$collegeLat}" .
    "?overview=false";

$osrmResponse = file_get_contents($osrmUrl);

if ($osrmResponse === false) {
    echo "";
    exit;
}

$osrmData = json_decode($osrmResponse, true);

if (!isset($osrmData['routes'][0]['distance'])) {
    echo "";
    exit;
}

// ===================================================
// 5. Convert meters → KM and return
// ===================================================
$distanceKm = $osrmData['routes'][0]['distance'] / 1000;

echo round($distanceKm, 2);
