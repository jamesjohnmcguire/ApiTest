<?php

/**
 * An end to end API tester.
 *
 * @package   APITest
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2023 - 2025 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @version   1.5.15
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
	 * @var array<object>
	 */
	public $history = [];

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
	private Client $client;

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

		$options =
		[
			'base_uri' => $host,
			'headers'  =>
			[
				'Content-Type' => $this->requestContentType,
				'Accept'       => $this->responseContentType
			]
		];

		$this->client = new Client($options);
	}

	/**
	 * Test API end point method.
	 *
	 * @param string                    $method     The HTTP method to use.
	 * @param string                    $endPoint   The API end point.
	 * @param null|array<string>|string $data       The JSON data to process.
	 * @param ApiOptions                $apiOptions The options object.
	 *                                              Contains various options.
	 *
	 * @throws \Exception If an unexpected exception occurs during the request.
	 *
	 * @return string
	 */
	public function apiEndPointTest(
		string $method,
		string $endPoint,
		null | array | string $data = null,
		ApiOptions $apiOptions = new ApiOptions()) : string
	{
		$responseContent = null;

		try
		{
			if ($data !== null)
			{
				if ($apiOptions->requestDataType === 'multipart')
				{
					// Multipart - Usually forms with file uploads.
					$options = ['multipart' => $data];
				}
				else
				{
					$isString = is_string($apiOptions->requestDataType);

					if ($isString === true)
					{
						$options = [$apiOptions->requestDataType => $data];
					}
					else
					{
						// Normal form data.
						$options = ['form_params' => $data];
					}
				}
			}
			else
			{
				$options = [];
			}

			// Track the history of requests.
			$handlerStack = HandlerStack::create();
			$handlerStack->push(Middleware::history($this->history));
			$options['handler'] = $handlerStack;

			if ($this->responseContentType === 'application/json')
			{
				$expectedJson = true;
			}
			else
			{
				$expectedJson = false;
			}

			if ($apiOptions->errorExpected === true)
			{
				// Disable throwing exceptions on an HTTP protocol errors.
				$options['http_errors'] = false;
			}

			$additionalOptions = $apiOptions->guzzleAdditionalOptions;
			$exists = !empty($additionalOptions);

			if ($exists === true)
			{
				foreach ($additionalOptions as $key => $option)
				{
					$options[$key] = $option;
				}
			}

			$request = new Request($method, $endPoint);

			$response = $this->client->send($request, $options);
			$this->response = $response;

			self::checkStatus(
				$response,
				$apiOptions->errorExpected,
				$apiOptions->tryBasicAsserts);
			$responseContent = self::checkBody(
				$response,
				$expectedJson,
				$apiOptions->errorExpected,
				$apiOptions->contentRequired,
				$apiOptions->tryBasicAsserts);
		}
		// Guzzle high level super class exception.
		catch (BadResponseException $exception)
		{
			$this->displayException($exception, $apiOptions->errorExpected);
		}
		// Guzzle low level super class exception.
		catch (RequestException $exception)
		{
			$this->displayException(
				$exception,
				$apiOptions->errorExpected,
				$apiOptions->tryBasicAsserts);
		}
		catch (\Exception $exception)
		{
			if ($apiOptions->errorExpected === false)
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
	 * @deprecated since v1.5.18, use apiEndPointTest() instead.
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
		$options = new ApiOptions();

		$options->contentRequired = $contentRequired;
		$options->requestDataType = $dataType;

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
	 * @param object  $exception       The exception to process.
	 * @param boolean $errorExpected   Indicates whether an error is expected
	 *                                 or not.
	 * @param boolean $tryBasicAsserts Indicates whether to try running basic
	 *                                 asserts or not.
	 *
	 * @return void
	 */
	private static function displayException(
		object $exception,
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
}
