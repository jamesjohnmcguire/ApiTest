# ApiTest
A library for testing web APIs with PHPUnit.

Please :star: star this project!

## Getting Started

### Requirements/dependencies

* [PHP >= 8.1.1](https://php.net/)

This requirement could potentially be relaxed to older version of PHP upon request.

* [php-http/guzzle7-adapter >= 1.0](https://github.com/php-http/guzzle7-adapter)

* [PHPUnit >= 11 || 12 || 13](https://phpunit.de/)


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
        $testOptions = new TestOptions();
        $testOptions->tryBasicAsserts = false;

        $response =
            $this->apiTester->apiEndPointTest(
                'POST', 'https://httpbin.org/post', $data, $testOptions);

        $this->assertIsArray($response);
    }
)
```

The apiEndPointTest parameters are:

| Parameter   | Type        | Parameter                                       |
| ----------- | ----------- | ----------------------------------------------- |
| method      | string      | The HTTP method to use.                         |
| endPoint    | string      | The API end point.                              |
| data        | ?string     | The request data.                               |
| testOptions | TestOptions | A set of options.                               |
|             |             | Refer to TestOptions for details.               |

TestOptions is an object with the following properties:

| Property                | Default                    | Description          |
| ----------------------- | -------------------------- | -------------------- |
| contentRequired         | true                       | Indicates whether    |
|                         |                            | content is required  |
|                         |                            | in the response      |
|                         |                            | body.                |
| errorExpected           | false                      | Indicates whether an |
|                         |                            | error is expected.   |
| guzzleAdditionalOptions | null                       | Additional Guzzle    |
|                         |                            | options to be        |
|                         |                            | included.            |
| requestDataType         | null                       | The data type for    |
|                         |                            | the request.  Can be |
|                         |                            | one of the following |
|                         |                            | values: 'body',      |
|                         |                            | 'form_params',       |
|                         |                            | 'json' or            |
|                         |                            | 'multipart'.         |
| tryBasicAsserts         | true                       | Indicates whether    |
|                         |                            | included basic       |
|                         |                            | assertions should be |
|                         |                            | tried.               |
| userAgent               | <recent Chrome user agent> | The user agent to    |
|                         |                            | include in           |
|                         |                            | the headers.         |

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

You can now directly access the Guzzle client with:
```
$guzzleClient = $pageTester->client;
```

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

### Cookies Support

This now supports cookies over multiple calls.
 You can also access the cookies by accessing the public $cookies property of PageTester.

```
    $cookieJar = $pageTester->cookieJar;
```

### Details on `tryBasicAsserts`

If `tryBasicAsserts` is true, several basic assertions will be performed based on the provided options and response. Below is a summary of the assertions, their purposes, and the conditions under which they are applied.

| Assertion | Description | Condition |
|-----------|-------------|-----------|
| `assertNotEmpty($body)` | Verifies that the content body is not empty. | Checked if `$contentRequired` is `true`. |
| `assertJson($body)` | Validates that the content body is valid JSON. | Checked if `$jsonRequired` is `true`. |
| `assertEquals(200, $status)` | Ensures the HTTP status code is 200 (OK). | Checked if `$errorExpected` is `false`. |
| `assertNotEquals(200, $status)` | Ensures the HTTP status code is not 200. | Checked if `$errorExpected` is `true`. |
| `assertArrayHasKey('error', $data)` | Checks if the `error` key exists in the data array. | Checked if `$errorExpected` is `true`. |
| `assertArrayNotHasKey('error', $data)` | Checks if the `error` key does not exist in the data array. | Checked if `$errorExpected` is `false`. |
| `assertTrue($errorExpected)` | Verifies that `$errorExpected` is `true`. | Checked during exception handling. |

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
