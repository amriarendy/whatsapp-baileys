<?php

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://localhost:8000/send-message',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('message' => 'This is whatsapp send test', 'number' => '6289624704850', 'file_dikirim' => new CURLFILE('https://dataload.goican.id/assets/resource/promosi_goican.jpg')),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
