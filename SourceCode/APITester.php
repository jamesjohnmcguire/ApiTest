<?php

/**
 * An end to end API tester.
 *
 * @package   APITest
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2023 - 2026 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @version   1.8.39
 * @link      https://github.com/jamesjohnmcguire/ApiTest
 */

declare(strict_types=1);

namespace DigitalZenWorks\ApiTest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

// phpcs:disable Universal.UseStatements.DisallowUseFunction.FoundWithoutAlias
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertArrayNotHasKey;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertJson;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertTrue;

// phpcs:enable Universal.UseStatements.DisallowUseFunction.FoundWithoutAlias

// phpcs:disable Squiz.Commenting.ClassComment.TagNotAllowed
/**
 * API Tester class.
 *
 * Contains all the automated API tests.
 *
 * @internal Consider renaming to ApiTester or something more aligned with
 * naming scheme in v2.
 */
class APITester
{
	// phpcs:enable Squiz.Commenting.ClassComment.TagNotAllowed

	/**
	 * The history container.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	public array $history = [];

	/**
	 * The complete response object.
	 *
	 * @var \Psr\Http\Message\ResponseInterface
	 */
	public \Psr\Http\Message\ResponseInterface $response;

	/**
	 * Guzzle client object.
	 *
	 * @var Client
	 */
	public Client $client;

	/**
	 * Cookie jar object.
	 *
	 * @var \GuzzleHttp\Cookie\CookieJar
	 */
	public \GuzzleHttp\Cookie\CookieJar $cookieJar;

	/**
	 * Response content data type.
	 *
	 * @var string
	 */
	private string $responseContentType;

	/**
	 * Request content data type.
	 *
	 * @var string
	 */
	private string $requestContentType;

	/**
	 * Constructor method.
	 *
	 * @param string $host                The host for the tests.
	 * @param string $requestContentType  The request data type.
	 * @param string $responseContentType The response data type.
	 */
	public function __construct(
		string $host,
		string $requestContentType = 'application/json',
		string $responseContentType = 'application/json')
	{
		$this->requestContentType = $requestContentType;
		$this->responseContentType = $responseContentType;

		$this->cookieJar = new \GuzzleHttp\Cookie\CookieJar();

		$options =
		[
			'base_uri' => $host,
			'cookies'  => $this->cookieJar,
			'headers'  =>
			[
				'Content-Type' => $this->requestContentType,
				'Accept'       => $this->responseContentType
			]
		];

		$this->client = new Client($options);
	}

	/**
	 * Api end point test method.
	 *
	 * @param string                   $method      The HTTP method to use.
	 * @param string                   $endPoint    The API end point.
	 * @param null|string|array<mixed> $data        The JSON or form data to
	 *                                              process.  Can be null,
	 *                                              string, or various array
	 *                                              structures.
	 * @param TestOptions              $testOptions The options object.
	 *                                              Contains various options.
	 *
	 * @throws \Exception If an unexpected exception occurs during the request.
	 *
	 * @return string
	 */
	public function apiEndPointTest(
		string $method,
		string $endPoint,
		null | string | array $data = null,
		TestOptions $testOptions = new TestOptions()) : string
	{
		$responseContent = null;

		try
		{
			$options = $this->getGuzzleOptions($data, $testOptions);

			if ($this->responseContentType === 'application/json')
			{
				$expectedJson = true;
			}
			else
			{
				$expectedJson = false;
			}

			$request = new Request($method, $endPoint);

			$response = $this->client->send($request, $options);
			$this->response = $response;

			self::checkStatus(
				$response,
				$testOptions->errorExpected,
				$testOptions->tryBasicAsserts);
			$responseContent = self::checkBody(
				$response,
				$expectedJson,
				$testOptions->errorExpected,
				$testOptions->contentRequired,
				$testOptions->tryBasicAsserts);
		}
		// Guzzle high level super class exception.
		catch (BadResponseException $exception)
		{
			$this->displayException($exception, $testOptions->errorExpected);
		}
		// Guzzle low level super class exception.
		catch (RequestException $exception)
		{
			$this->displayException(
				$exception,
				$testOptions->errorExpected,
				$testOptions->tryBasicAsserts);
		}
		catch (\Exception $exception)
		{
			if ($testOptions->errorExpected === false)
			{
				// Not expecting any general exceptions.
				$class = get_class($exception);
				echo "Unexpected Exception class: $class\n";
			}

			throw $exception;
		}

		return $responseContent;
	}

	/**
	 * Test API end point method.
	 *
	 * @param string                    $method          The HTTP method to use.
	 * @param string                    $endPoint        The API end point.
	 * @param null|array<string>|string $data            The JSON data to
	 *                                                   process.
	 * @param boolean | string          $dataType        The data type.  True,
	 *                                                   if it is multipart form
	 *                                                   data. Implying some of
	 *                                                   the data may be binary.
	 *                                                   If it is a string, the
	 *                                                   string indicates the
	 *                                                   type of data ('body',
	 *                                                   'json', 'form_params').
	 * @param boolean                   $isError         Indicates whether an
	 *                                                   error is expected or
	 *                                                   not.
	 * @param boolean                   $errorRequired   Indicates whether an
	 *                                                   error field is expected
	 *                                                   in the response or not.
	 * @param boolean                   $contentRequired Indicates whether
	 *                                                   content is required in
	 *                                                   the response body.
	 *
	 * @return string
	 *
	 * @deprecated since v1.6.32, use apiEndPointTest() instead.
	 */
	public function testApiEndPoint(
		string $method,
		string $endPoint,
		null | array | string $data,
		bool | string $dataType = false,
		bool $isError = false,
		bool $errorRequired = false,
		bool $contentRequired = true) : string
	{
		$options = new TestOptions();

		$options->contentRequired = $contentRequired;

		if ($dataType === true)
		{
			$options->requestDataType = 'multipart';
		}

		$isString = is_string($dataType);

		if ($isString === true)
		{
			$options->requestDataType = $dataType;
		}

		if ($isError === true || $errorRequired === true)
		{
			$options->errorExpected = true;
		}

		return $this->apiEndPointTest($method, $endPoint, $data, $options);
	}

	/**
	 * Check body method.
	 *
	 * @param ResponseInterface $response        The PSR7 response object.
	 * @param boolean           $expectedJson    Indicates whether the body is
	 *                                           expected to be json or not.
	 * @param boolean           $errorExpected   Indicates whether an error
	 *                                           field is expected in the
	 *                                           response or not.
	 * @param boolean           $contentRequired Indicates whether content is
	 *                                           required in the response body.
	 * @param boolean           $tryBasicAsserts Indicates whether to try
	 *                                           running basic asserts or not.
	 *
	 * @return string
	 */
	public static function checkBody(
		ResponseInterface $response,
		bool $expectedJson = true,
		bool $errorExpected = false,
		bool $contentRequired = true,
		bool $tryBasicAsserts = true) : string
	{
		$stream = $response->getBody();
		$body = $stream->getContents();

		if ($expectedJson === true)
		{
			if ($tryBasicAsserts === true)
			{
				assertJson($body);
			}

			$data = json_decode($body, true);
		}
		else
		{
			$data = $body;
		}

		if ($contentRequired === true && $tryBasicAsserts === true)
		{
			assertNotEmpty($data);
		}

		$isArray = is_array($data);

		if ($isArray === true && $tryBasicAsserts === true)
		{
			if ($errorExpected === false)
			{
				assertArrayNotHasKey('error', $data);
			}
			else
			{
				assertArrayHasKey('error', $data);
			}
		}

		return $body;
	}

	/**
	 * Check status method.
	 *
	 * @param \Psr\Http\Message\ResponseInterface $response        The PSR7
	 *                                                             response
	 *                                                             object.
	 * @param boolean                             $errorExpected   Indicates
	 *                                                             whether an
	 *                                                             error is
	 *                                                             expected or
	 *                                                             not.
	 * @param boolean                             $tryBasicAsserts Indicates
	 *                                                             whether to
	 *                                                             try running
	 *                                                             basic asserts
	 *                                                             or not.
	 *
	 * @return integer
	 */
	public static function checkStatus(
		ResponseInterface $response,
		bool $errorExpected = false,
		bool $tryBasicAsserts = true) : int
	{
		$status = $response->getStatusCode();

		if ($tryBasicAsserts === true)
		{
			if ($errorExpected === true)
			{
				assertNotEquals(200, $status);
			}
			else
			{
				assertEquals(200, $status);
			}
		}

		return $status;
	}

	/**
	 * Display exception method.
	 *
	 * @param RequestException $exception       The exception to process.
	 * @param boolean          $errorExpected   Indicates whether an error is
	 *                                          expected or not.
	 * @param boolean          $tryBasicAsserts Indicates whether to try running
	 *                                          basic asserts or not.
	 *
	 * @return void
	 */
	private static function displayException(
		RequestException $exception,
		bool $errorExpected,
		bool $tryBasicAsserts = true)
	{
		$class = get_class($exception);
		echo "Exception class: $class\n";

		$exists = $exception->hasResponse();

		if ($exists === true)
		{
			$response = $exception->getResponse();

			$exists = !empty($response);

			if ($exists === true)
			{
				$message = $response->getReasonPhrase();
				$code = $response->getStatusCode();
				echo "Exception code: $code\t$message\n";

				$body = $response->getBody();
				$contents = $body->getContents();
				print_r($contents);
			}
			else
			{
				echo "WARNING: Empty exception object\n";
			}
		}

		if ($tryBasicAsserts === true)
		{
			assertTrue($errorExpected);
		}
	}

	/**
	 * Get guzzle options.
	 *
	 * @param null|array<string>|string $data        The JSON data to process.
	 * @param TestOptions               $testOptions The options object.
	 *                                               Contains various options.
	 *
	 * @return array<string, array<string>|boolean|integer|string|object>
	 */
	private function getGuzzleOptions(
		null | array | string $data,
		TestOptions $testOptions) : array
	{
		$options = [];

		if ($data !== null &&
			($testOptions->requestDataType === 'body' ||
			$testOptions->requestDataType === 'form_params' ||
			$testOptions->requestDataType === 'json' ||
			$testOptions->requestDataType === 'multipart'))
		{
			$options = [$testOptions->requestDataType => $data];
		}

		// Track the history of requests.
		$handlerStack = $this->getHandlerStack();
		$options['handler'] = $handlerStack;

		if ($testOptions->errorExpected === true)
		{
			// Disable throwing exceptions on an HTTP protocol errors.
			$options['http_errors'] = false;
		}

		$additionalOptions = $testOptions->guzzleAdditionalOptions;
		$exists = !empty($additionalOptions);

		if ($exists === true)
		{
			foreach ($additionalOptions as $key => $option)
			{
				$options[$key] = $option;
			}
		}

		// Do this after processing guzzleAdditionalOptions,
		// as it more specific.
		$options['headers'] =
		[
			'User-Agent' => $testOptions->userAgent
		];

		return $options;
	}

	// phpcs:disable Squiz.Commenting.BlockComment.WrongStart
	// phpcs:disable Squiz.Commenting.InlineComment.DocBlock
	/**
	 * Get handler stack.
	 *
	 * @return HandlerStack
	 */
	private function getHandlerStack() : HandlerStack
	{
		$handlerStack = HandlerStack::create();

		/**
		 * @var array<int, array<string, mixed>> $history
		 */
		$history = &$this->history;
		$handlerStack->push(Middleware::history($history));

		return $handlerStack;
	}
	// phpcs:enable Squiz.Commenting.BlockComment.WrongStart
	// phpcs:enable Squiz.Commenting.InlineComment.DocBlock
}
