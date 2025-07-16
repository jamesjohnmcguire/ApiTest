# ApiTest
A library for testing web APIs with PHPUnit.

Please :star: star this project!

## Getting Started

### Requirements/dependencies

* [PHP >= 8.1.1](http://php.net/)

This requirement could potentially be relaxed to older version of PHP upon request.


## Installation
### Git
git clone https://github.com/jamesjohnmcguire/ApiTest

### Composer
composer require --dev https://packagist.org/packages/digitalzenworks/api-test

## Usage:

### API Testing

There is one main class with one main method.  You can call it like this:

```php
require_once 'vendor/autoload.php';

use DigitalZenWorks\ApiTest\ApiTester;

final class UnitTests extends TestCase
{
    #[Test]
    public function ApiEndPointPostSuccess()
    {
        $data =
        [
            'name' => 'Somebody',
            'email' => 'Somebody@example.com'
        ];

        $apiTester = new APITester('https://httpbin.org');

        $response =
            $this->apiTester->apiEndPointTest('POST', 'post', $data);

        $this->assertIsArray($response);
    }
)
```

The main method parameters are:

| Type:       | Parameter:                                          |
| ----------- | --------------------------------------------------- |
| string      | The HTTP method to use.                             |
| string      | The API end point.                                  |
| ?string     | The request data.                                   |
| TestOptions | A set of options. Refer to TestOptions for details. |

TestOptions is an object with the following properties:

| Property                | Default | Description                            |
| ----------------------- | ------------------------------------------------ |
| contentRequired         | true    | Indicates whether content is required  |
|                         |         | in the response body.                  |
| errorExpected           | false   | Indicates whether an error is          |
|                         |         | expected.                              |
| guzzleAdditionalOptions | null    | Additional Guzzle options to be        |
|                         |         | included.                              |
| requestDataType         | null    | The data type for the request.  Can be |
|                         |         | one of the following values: 'body',   |
|                         |         | 'form_params', 'json' or 'multipart'.  |
| tryBasicAsserts         | true    | Indicates whether included basic       |
|                         |         | assertions should be tried.            |


### Page or End-to-End Testing

This also supports testing HTML pages directly.  You can call it like this:

```php
require_once 'vendor/autoload.php';

use DigitalZenWorks\ApiTest\PageTester;

final class UnitTests extends TestCase
{
    #[Test]
    public function SimplePage()
    {
        PageTester $pageTester =
            new PageTester('https://httpbin.org', 'text/html', 'text/html');

        $endPoint = 'https://httpbin.org/get';

        $content = $pageTester->webPageTest('GET', $endPoint, null);

        $this->assertNotNull($content);
        $this->assertNotEmpty($content);
        $this->assertStringContainsStringIgnoringCase(
            '<!DOCTYPE html>', $content);
    }
}
```
### Additional Examples

You can refer to the unit tests for additional example usage.


## Additional Notes
This uses Guzzle to process the API request.

### Guzzle Response Object

You can access the Guzzle response object by accessing the public $response property of PageTester. like:

```
    $response = $pageTester->response;
    $status = $response->getStatusCode();
    $this->assertEquals(200, $status);
```

Note: Status is already checked within TestSitePage. It's just included here for the purposes of example.

### Guzzle History Object

You can also access the Guzzle history (handler) object by accessing the public $response property of PageTester. like:

```
    $history = $pageTester->history;
    $redirects = count($history) - 1;
    $this->assertEquals(1, $redirects);
```

This can be useful for tracking redirects, among other things.

## Contributing

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".

### Process:

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding style
Please match the current coding style.  Most notably:  
1. One operation per line
2. Use complete English words in variable and method names
3. Attempt to declare variable and method names in a self-documenting manner


## License

Distributed under the MIT License. See `LICENSE` for more information.

## Contact

James John McGuire - [@jamesmc](https://twitter.com/jamesmc) - jamesjohnmcguire@gmail.com

Project Link: [https://github.com/jamesjohnmcguire/ApiTest](https://github.com/jamesjohnmcguire/ApiTest)
