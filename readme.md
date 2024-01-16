# Whatsapp Bot Gateway from modules baileys

#### How to running app

The first you watch dependencies version in [www.npmjs.com](https://www.npmjs.com/ "www.npmjs.com")

```bash
npm update
```

#### How to running app

```bash
npm start
```

or

```bash
node index.js
```

#### How to use

**How to authenticated**
Scan Qr Code in terminal or your browser with link http://localhost:8000/scan, then open your Whatsapp Mobile in Link Device.

**How to autoreply**
After you scan Qr Code and Application created your session, open your Whatsapp Mobile and type ping.

**How send message**
**Endpoint** : http://localhost:8000/send-message
**Endpoint** : http://localhost:8000/send-group-message

- **PHP**

```php
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
  CURLOPT_POSTFIELDS => array('message' => 'Hi this whatsapp bot','number' => '6282246686900','file_dikirim'=> new CURLFILE('https://dataload.goican.id/assets/resource/promosi_goican.jpg')),
));
$response = curl_exec($curl);
curl_close($curl);
echo $response;
?>
```

- **Javascript (jquery)**

```javascript
// send-message
var form = new FormData();
form.append("message", "Hi this whatsapp bot");
form.append("number", "6282246686900");
form.append(
  "file_dikirim",
  fileInput.files[0],
  "https://dataload.goican.id/assets/resource/promosi_goican.jpg"
);
var settings = {
  url: "http://localhost:8000/send-message",
  method: "POST",
  timeout: 0,
  processData: false,
  mimeType: "multipart/form-data",
  contentType: false,
  data: form,
};
$.ajax(settings).done(function (response) {
  console.log(response);
});
```

- **Javascript (XHR)**

```javascript
// WARNING: For POST requests, body is set to null by browsers.
var data = new FormData();
data.append("message", "Hi this whatsapp bot");
data.append("number", "6282246686900");
data.append(
  "file_dikirim",
  fileInput.files[0],
  "https://dataload.goican.id/assets/resource/promosi_goican.jpg"
);
var xhr = new XMLHttpRequest();
xhr.withCredentials = true;
xhr.addEventListener("readystatechange", function () {
  if (this.readyState === 4) {
    console.log(this.responseText);
  }
});
xhr.open("POST", "http://localhost:8000/send-message");
xhr.send(data);
```

#### Depedencies

```json
  "dependencies": {
    "@whiskeysockets/baileys": "^6.3.1",
    "@adiwajshing/keyed-db": "^0.2.4",
    "eslint": "^8.14.0",
    "express": "^4.18.1",
    "express-fileupload": "^1.4.0",
    "ffmpeg": "^0.0.4",
    "jest": "^28.0.3",
    "jimp": "^0.16.1",
    "link-preview-js": "^2.1.13",
    "nodemon": "^2.0.16",
    "qrcode": "^1.5.1",
    "qrcode-terminal": "^0.12.0",
    "request": "^2.88.2",
    "sharp": "^0.29.3",
    "socket.io": "^4.5.0"
  }
```

#### Depedencies

This project start from

- node v16.16.0
- npm v6.14.18
- coffee ☕

#### Insight From

[https://www.youtube.com/watch?v=xPeI0VAzIhM](https://www.youtube.com/watch?v=xPeI0VAzIhM "https://www.youtube.com/watch?v=xPeI0VAzIhM")
[https://www.youtube.com/watch?v=Cr72Nef0x6w](https://www.youtube.com/watch?v=Cr72Nef0x6w "https://www.youtube.com/watch?v=Cr72Nef0x6w")
[https://www.youtube.com/watch?v=xF0Z6Te2yO8](https://www.youtube.com/watch?v=xF0Z6Te2yO8 "https://www.youtube.com/watch?v=xF0Z6Te2yO8")
