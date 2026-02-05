<?php
/* ================= HOSTEL LOCATION ================= */
define('HOSTEL_LAT', 10.059747068327198); // <-- CHANGE THIS
define('HOSTEL_LON', 76.33109129038162); // <-- CHANGE THIS

$pincode = $_GET['pincode'] ?? '';

if (strlen($pincode) !== 6) {
    echo '';
    exit;
}

/* ================= GET LAT/LON FROM PINCODE ================= */
function getLatLonFromPincode($pincode) {
    $url = "https://nominatim.openstreetmap.org/search?postalcode=$pincode&country=India&format=json&limit=1";

    $opts = [
        "http" => [
            "header" => "User-Agent: CollegeProject/1.0\r\n"
        ]
    ];

    $context = stream_context_create($opts);
    $response = @file_get_contents($url, false, $context);

    if (!$response) return null;

    $data = json_decode($response, true);

    if (!$data || empty($data[0]['lat']) || empty($data[0]['lon'])) {
        return null;
    }

    return [
        'lat' => (float)$data[0]['lat'],
        'lon' => (float)$data[0]['lon']
    ];
}

/* ================= HAVERSINE FORMULA ================= */
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);

    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return round($earthRadius * $c, 2);
}

/* ================= MAIN ================= */
$coords = getLatLonFromPincode($pincode);

if (!$coords) {
    echo '';
    exit;
}

$distance = calculateDistance(
    HOSTEL_LAT,
    HOSTEL_LON,
    $coords['lat'],
    $coords['lon']
);

echo $distance;
