<?php
require __DIR__ . '/vendor/autoload.php';



/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('test');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Sheets($client);

// Prints the names and majors of students in a sample spreadsheet:
// https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
$spreadsheetId = '1LH-hiJfBk3YrhHaLjzpNg9RnFoBV9H7tSIoVqSn1liA';

$mysqli = new Mysqli('localhost', 'root', 'root', 'mybase');
$products = mysqli_query($mysqli, "SELECT * FROM `userss` WHERE age>18");
$products = mysqli_fetch_all($products);

$a = 2;


foreach ($products as $product) {
    $range = "list1!A{$a}:D";
    $values = [
        ["$product[0]", "$product[1]", "$product[2]", $product[3]],
    ];
    $a++;
    $data = [];
    $data[] = new Google_Service_Sheets_ValueRange([
        'range' => $range,
        'values' => $values
    ]);
    // Additional ranges to update ...
    $body = new Google_Service_Sheets_BatchUpdateValuesRequest([
        'valueInputOption' => 'RAW',
        'data' => $data
    ]);

    $result = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);
}
printf("%d Добавлен в эксел.", $result->getTotalUpdatedCells());
header('Location: /');


