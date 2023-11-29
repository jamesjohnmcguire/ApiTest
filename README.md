# ApiTest
A library for testing web APIs with PHPUnit.

Please :star: star this project!

## Getting Started

### Requirements/dependencies

* [PHP >= 8.1.1](http://php.net/)

## Installation
### Git
git clone https://github.com/jamesjohnmcguire/ApiTest


## Usage:

There is one class with one main method.  You can call it like this:

```php
require_once  'vendor/autoload.php';

use DigitalZenWorks\ApiTest\ApiTester;

final class UnitTests extends TestCase
{
	public function testApiEndPointAccountCount()
	{
		$data = [];
		$apiTester = new APITester('https://example.com');
		$response =
			$this->apiTester->testApiEndPoint('PUT', 'accounts_count', $data);

		$this->assertIsArray($response);
	}
)
```

The main method parameters are:

| Type:   | Parameter:              |
| ------- | ----------------------- |
| string  | The HTTP method to use. |
| string  | The API end point.      |
| ?string | The request data.       |


## Additional Notes
This uses Guzzle to process the API request.

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
