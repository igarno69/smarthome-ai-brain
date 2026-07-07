<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$input = json_decode(file_get_contents('php://input'), true);
$userPrompt = isset($input['prompt']) ? trim($input['prompt']) : '';

if (empty($userPrompt)) {
    echo json_encode(['error' => 'Empty prompt provided.']);
    exit;
}

// ⚠️ PASTE YOUR COPIED GEMINI API KEY STRING HERE
// Fetch the API key securely from the server environment
define('GEMINI_API_KEY', getenv('AQ.Ab8RN6KH0TnmZNM0s4lRmsWaOl_Z_Nm-DE2eAWl7uTYIivXjDw'));
define('GEMINI_MODEL', 'gemini-1.5-flash');
$systemInstruction = "You are Jarvis, the autonomous assistant for a Smart Home Dashboard.\n\n"
    . "CRITICAL: Always respond in a strict JSON format matching this structure:\n"
    . "{\n"
    . "  \"reply\": \"Your spoken feedback to the user inside the chatbox.\",\n"
    . "  \"action\": { \"type\": \"control\", \"device\": \"lamp\", \"status\": \"on\" }\n"
    . "}\n"
    . "If no hardware operation is required, set \"action\" to null.";

$payload = [
    'contents' => [['parts' => [['text' => $userPrompt]]]],
    'systemInstruction' => ['parts' => [['text' => $systemInstruction]]],
    'generationConfig' => ['responseMimeType' => 'application/json']
];

$url = "https://generativelanguage.googleapis.com/v1beta/models/" . GEMINI_MODEL . ":generateContent?key=" . GEMINI_API_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
$aiRawText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

echo $aiRawText;
