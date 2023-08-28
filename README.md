## A simple PHP implementation of [JSON-RPC protocol](https://www.jsonrpc.org/) and a database driver ([MySQL](https://www.mysql.com/)) made with these words in mind: *efficient, secure, OOP, beautiful*

### PHP Client

```php
/* Build the request */
$client = new Client();
$client->buildRequest(1, 'getCars', array());
$dataEncoded = $client->encode();

/* Make the request to our localhost */
$response = Request::sendJson("http://127.0.0.1:4321", $dataEncoded);
// response: {"jsonrpc":"2.0","id":1,"result":[{...}]}
```

### JavaScript Client

```js
/* prepare the request */
const requestData = {
  jsonrpc: "2.0",
  id: 1,
  method: "getCars",
  params: [],
};

/* make the request */
const response = await fetch("http://127.0.0.1:4321", {
  method: "POST",
  mode: "cors",
  cache: "no-cache",
  credentials: "same-origin",
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
  },
  redirect: "follow",
  referrerPolicy: "no-referrer",
  body: JSON.stringify(requestData),
});

/* process the response */
const responseData = await response.json();
// responseData: {"jsonrpc":"2.0","id":1,"result":[{"prefix":"+40","name":"Romania"}]}
```

## Installation

You have to install the dependencies via [Composer](https://getcomposer.org/) from the root directory of this project:
```
composer install
```


## Get started

1. Finally, you are ready to go and start the development [PHP built-in server](https://www.php.net/manual/en/features.commandline.webserver.php) (port 4321):
```
php -S 127.0.0.1:4321
```

2. Try some examples; run the examples from the root directory like below or open the `example/client.html` page in a browser.
```
php example/client.php
```
Or if you prefer you can make a http request with curl from your terminal:
```
curl -w "\n" -i -H "Accept: application/json" -H "Content-Type: application/json" --data "{\"jsonrpc\":\"2.0\",\"id\": 1,\"method\":\"getCars\",\"params\":[]}" http://127.0.0.1:4321
```

3. Enjoy!
