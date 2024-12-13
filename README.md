# Helix API Wrapper

## Features
- Integrates [Helix API](https://docs.helix.q2.com/reference/authentication) to Symfony framework

## Installation
- Install bundle:
```composer require idapgroup/helix-api-bundle```
- Add these lines to ```.env``` file
```angular2html
#Helix
HELIX_API_URL='https://sandbox-api.helix.q2.com'
HELIX_API_KEY='example1'
HELIX_API_SECRET='example1'
HELIX_API_DEFAULT_CULTURE=en-US
```

## Usage

### GET request
```injectablephp
public function getProgram(HelixApiService $service): mixed
{
    return $service->helixGetRequest('program/get');
}
```

### POST request
```injectablephp
public function createCustomer(HelixApiService $service, array $data): mixed
{
    $data = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        //... Fill data according Helix documentation
    ];

    return $service->helixPostRequest('customer/create', $data);
}
```
