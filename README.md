
   ![Logo-center](https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ-7wTdr6GfeRzbPNyHMZ2FEwpw_JCIS3--Fg&usqp=CAU)

# BigCommerce Public Plugin


## Features
* Adding custom JS Script to the store
* Creating webhooks for new order created, product updated.
* Custom API for fetching product and customer details.





## Installation

### Clone the repo to your local host

* Change API key, Client Secret in the gettoken.php file
```bash
  $client_id = 'YOUR-CLIENT-ID';
```
```bash
  $client_secret = 'YOUR-CLIENT-SECRET';
```
* Change the app domain in includes/function.php
```bash
$app_domain = "your_domain.com";
```
* Change the app ID in gettoken.php
```bash
$app_id = "YOUR-APP-ID"; 
```




## Tech

**PHP 8.1.12**

