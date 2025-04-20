<?php
// API klíč pro OpenAI
$apiKey = 'TVOJ_API_KLÍČ';

// Získání dat odeslaných z front-endu
$data = json_decode(file_get_contents('php://input'), true);

// Ověření, že všechny údaje byly poskytnuty
if (empty($data['jmeno']) || empty($data['vzdelani']) || empty($data['zkusenosti']) || empty($data['dovednosti'])) {
    echo json_encode(['error' => 'Chybí některé údaje.']);
    exit;
}

// Příprava promptu pro OpenAI
$prompt = "Vytvoř životopis na základě těchto údajů:\n\n";
$prompt .= "Jméno: " . $data['jmeno'] . "\n";
$prompt .= "Vzdělání: " . $data['vzdelani'] . "\n";
$prompt .= "Pracovní zkušenosti: " . $data['zkusenosti'] . "\n";
$prompt .= "Dovednosti: " . $data['dovednosti'] . "\n";

// Inicializace cURL
$ch = curl_init();

// Nastavení cURL pro OpenAI API
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
]);

// Příprava dat pro API
$dataApi = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [['role' => 'user', 'content' => $prompt]],
    'temperature' => 0.7,
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataApi));

// Odeslání požadavku a získání odpovědi
$response = curl_exec($ch);

// Uzavření cURL spojení
curl_close($ch);

// Dekódování odpovědi z API
$responseData = json_decode($response, true);

// Získání vygenerovaného životopisu
$zivotopis = $responseData['choices'][0]['message']['content'] ?? 'Nepodařilo se vygenerovat.';

// Vrácení životopisu jako JSON odpověď
echo json_encode(['zivotopis' => $data]);
?>
