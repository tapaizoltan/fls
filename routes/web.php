<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    /*
    // nav lekérdezés

    $userData = array(
        "login" => "iwooyht42vzz9mv",
        "password" => "Zc3SGm35z9kRAX5e",
        // "passwordHash" => "...", // Opcionális, a jelszó már SHA512 hashelt változata. Amennyiben létezik ez a változó, akkor az authentikáció során ezt használja
        "taxNumber" => "57516642",
        "signKey" => "37-b131-10d67eb4e8134QW6JJ4GMVL3",
        "exchangeKey" => "81244QW6JJ4GM03U",
    );
    
    $softwareData = array(
        "softwareId" => "123456789123456789",
        "softwareName" => "string",
        "softwareOperation" => "ONLINE_SERVICE",
        "softwareMainVersion" => "string",
        "softwareDevName" => "string",
        "softwareDevContact" => "string",
        "softwareDevCountryCode" => "HU",
        "softwareDevTaxNumber" => "string",
    );
    
    $apiUrl = "https://api.onlineszamla.nav.gov.hu/invoiceService/v3";
    
    $config = new NavOnlineInvoice\Config($apiUrl, $userData, $softwareData);
    $config->setCurlTimeout(70); // 70 másodperces cURL timeout (NAV szerver hívásnál), opcionális
    
    // "Connection error. CURL error code: 60" hiba esetén add hozzá a következő sort:
    // $config->verifySSL = false;
    
    $reporter = new NavOnlineInvoice\Reporter($config);

    try {
        $result = $reporter->queryTaxpayer("13638722");
    
        if ($result) {
            print "Az adószám valid.\n";
            print "Az adószámhoz tartozó név: $result->taxpayerName\n";
    
            print "További lehetséges információk az adózóról:\n";
            print_r($result->taxpayerShortName);
            print_r($result->taxNumberDetail);
            print_r($result->vatGroupMembership);
            print_r($result->taxpayerAddressList);
            dd($result);
        } else {
            print "Az adószám nem valid.";
        }
    
    } catch(Exception $ex) {
        print get_class($ex) . ": " . $ex->getMessage();
    }
    */

    /*
    $url = 'https://e-cegkivonat.hu/talalat?adoszam=13638722';  // URL you're scraping.
    $html = file_get_contents($url);
    $text = strip_tags($html);
    echo $text;
    //echo "<PRE>$text</PRE>";
    */

    /*
    $url = 'https://e-cegkivonat.hu/talalat?adoszam=Rosenberger';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response !== false) {
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML($response);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $inputName = $xpath->query('//input[@type="hidden" and @name="Name"]');

        if ($inputName->length > 0) {
            $nameValue = $inputName->item(0)->getAttribute('value');
            echo "Name value értéke: " . htmlspecialchars($nameValue);
        } else {
            echo "Name input nem található.";
        }
        echo '<br>';
        $inputRegNumber = $xpath->query('//input[@type="hidden" and @name="RegNumber"]');

        if ($inputRegNumber ->length > 0) {
            $regNumberValue = $inputRegNumber ->item(0)->getAttribute('value');
            echo "RegNumber value értéke: " . htmlspecialchars($regNumberValue);
        } else {
            echo "RegNumber input nem található.";
        }
    } else {
        echo "Hiba a lekérés során.";
    }
    */


/*
//ez már egész jó

    // Az oldal URL-je
$url = "https://e-cegkivonat.hu/talalat?adoszam=Rosenberger";

// 1. cURL inicializálása
$ch = curl_init();

// 2. Állítsuk be a cURL opciókat
curl_setopt($ch, CURLOPT_URL, $url); // Cél URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Az eredményt visszakapjuk, nem közvetlenül a kimenetre írjuk
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Kövessük az átirányításokat

// 3. Lekérjük az oldal tartalmát
$html = curl_exec($ch);

// 4. Ellenőrizzük a cURL hibákat
if (curl_errno($ch)) {
    echo 'Hiba történt a cURL hívásban: ' . curl_error($ch);
    curl_close($ch);
    exit();
}

// 5. Zárjuk le a cURL session-t
curl_close($ch);

// 6. Hozz létre egy DOMDocument objektumot
$dom = new DOMDocument();

// 7. A libxml használata a HTML speciális karakterek miatt
libxml_use_internal_errors(true); // Elkerüljük a HTML hibák megjelenítését

// 8. Töltsd be a HTML tartalmat a DOMDocument objektumba
$dom->loadHTML($html);

// 9. Hozz létre egy DOMXPath objektumot a könnyebb kereséshez
$xpath = new DOMXPath($dom);

// 10. Keresd meg a hidden input típusú form elemeket
$hiddenInputs = $xpath->query('//input[@type="hidden"]');

// 11. Ellenőrizzük, hogy vannak-e rejtett mezők
if ($hiddenInputs->length > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Név</th><th>Érték</th></tr>"; // Táblázat fejléc

    // 12. Iteráljunk végig a hidden input mezőkön és jelenítsük meg őket soronként
    foreach ($hiddenInputs as $input) {
        $name = $input->getAttribute('name');
        $value = $input->getAttribute('value');

        echo "<tr>";
        echo "<td>" . htmlspecialchars($name) . "</td>";
        echo "<td>" . htmlspecialchars($value) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Nincsenek rejtett input mezők az oldalon.";
}
    */


$url = "https://e-cegkivonat.hu/talalat?adoszam=Rosenberger";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$html = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Hiba történt a cURL hívásban: ' . curl_error($ch);
    curl_close($ch);
    exit();
}

curl_close($ch);
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
$dom->loadHTML($html);
$xpath = new DOMXPath($dom);
$fields = ['Name', 'Zip', 'City', 'StreetAndNum', 'RegNumber', 'ShortTaxNumber'];
$forms = $xpath->query('//form');
$allData = [];

foreach ($forms as $form) {
    $data = [];
    $inputs = $form->getElementsByTagName('input');
    foreach ($inputs as $input) {
        $name = $input->getAttribute('name');
        $value = $input->getAttribute('value');
        if (in_array($name, $fields)) {
            $data[$name] = $value;
        }
    }
    if (!empty($data)) {
        $allData[] = $data;
    }
}

if (!empty($allData)) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>";
    foreach ($fields as $field) {
        echo "<th>" . htmlspecialchars($field) . "</th>";
    }
    echo "</tr>";

    foreach ($allData as $data) {
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<td>" . (isset($data[$field]) ? htmlspecialchars($data[$field]) : 'N/A') . "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "Nincsenek megfelelő találatok az oldalon.";
}

});
