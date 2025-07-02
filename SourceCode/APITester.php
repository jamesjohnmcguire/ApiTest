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

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertArrayNotHasKey;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertJson;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertTrue;

/**
 * API Tester class.
 *
 * Contains all the automated API tests.
 *
 * @internal
 * @todo Consider renaming to ApiTester or something more aligned with
 * naming scheme in v2.
 */
class APITester
{
	/**
	 * The history container.
	 *
	 * @var array
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
	 * @var GuzzleClient
	 */
	private GuzzleClient $client;

	/**
	 * Response data type.
	 *
	 * @var string
	 */
	private string $responseDataType;

	/**
	 * Request data type.
	 *
	 * @var string
	 */
	private string $requestDataType;

	/**
	 * Constructor method.
	 *
	 * @param string $host             The host for the tests.
	 * @param string $requestDataType  The request data type.
	 * @param string $responseDataType The response data type.
	 */
	public function __construct(
		string $host,
		string $requestDataType = 'application/json',
		string $responseDataType = 'application/json')
	{
		$this->requestDataType = $requestDataType;
		$this->responseDataType = $responseDataType;

		$options =
		[
			'base_uri' => $host,
			'headers'  =>
			[
				'Content-Type' => $requestDataType,
				'Accept'       => $responseDataType
			]
		];

		$this->client = new GuzzleClient($options);
	}

	/**
	 * Test API end point method.
	 *
	 * @param string                $method          The HTTP method to use.
	 * @param string                $endPoint        The API end point.
	 * @param null | array | string $data            The JSON data to process.
	 * @param ApiOptions            $options         The options object.
	 *                                               Contains various options.
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
				if ($apiOptions->dataType === true)
				{
					// Multipart - Usually forms with file uploads.
					$options = ['multipart' => $data];
				}
				else
				{
					$isString = is_string($apiOptions->dataType);

					if ($isString === true)
					{
						$options = [$apiOptions->dataType => $data];
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

			if ($this->responseDataType === 'application/json')
			{
				$expectedJson = true;
			}
			else
			{
				$expectedJson = false;
			}

			if ($apiOptions->isError === true)
			{
				// Disable throwing exceptions on an HTTP protocol errors.
				$options['http_errors'] = false;
			}

			$request = new Request($method, $endPoint);

			$response = $this->client->send($request, $options);
			$this->response = $response;

			self::checkStatus($response, $apiOptions->isError);
			$responseContent = self::checkBody(
				$response,
				$expectedJson,
				$apiOptions->isError,
				$apiOptions->errorRequired,
				$apiOptions->contentRequired);
		}
		// Guzzle high level super class exception.
		catch (BadResponseException $exception)
		{
			$this->displayException($exception, $apiOptions->isError);
		}
		// Guzzle low level super class exception.
		catch (RequestException $exception)
		{
			$this->displayException($exception, $apiOptions->isError);
		}
		catch (\Exception $exception)
		{
			if ($apiOptions->isError === false)
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
	 * @deprecated since v1.5.18, use apiEndPointTest() instead.
	 *
	 * @param string                $method          The HTTP method to use.
	 * @param string                $endPoint        The API end point.
	 * @param null | array | string $data            The JSON data to process.
	 * @param boolean | string      $dataType        The data type.  True, if it
	 *                                               is multipart form data.
	 *                                               Implying some of the data
	 *                                               may be binary. If it is a
	 *                                               string, the string
	 *                                               indicates the type of data
	 *                                               ('body', 'json',
	 *                                               'form_params').
	 * @param boolean               $isError         Indicates whether an error
	 *                                               is expected or not.
	 * @param boolean               $errorRequired   Indicates whether an error
	 *                                               field is expected in the
	 *                                               response or not.
	 * @param boolean               $contentRequired Indicates whether content
	 *                                               is required in the response
	 *                                               body.
	 *
	 * @return string
	 */
	public function testApiEndPoint(
		string $method,
		string $endPoint,
		null | array | string $data,
		bool | string $dataType = false,
		bool $isError = false,
		bool $errorRequired = true,
		bool $contentRequired = true) : string
	{
		$options = new ApiOptions();

		$options->dataType = $dataType;
		$options->isError = $isError;
		$options->errorRequired = $errorRequired;
		$options->contentRequired = $contentRequired;

		return $this->apiEndPointTest($method, $endPoint, $data, $options);
	}

	/**
	 * Check body method.
	 *
	 * @param ResponseInterface $response        The PSR7 response object.
	 * @param boolean           $expectedJson    Indicates whether the body is
	 *                                           expected to be json or not.
	 * @param boolean           $isError         Indicates whether an error is
	 *                                           expected or not.
	 * @param boolean           $errorRequired   Indicates whether an error
	 *                                           field is expected in the
	 *                                           response or not.
	 * @param boolean           $contentRequired Indicates whether content is
	 *                                           required in the response body.
	 *
	 * @return string
	 */
	public static function checkBody(
		ResponseInterface $response,
		bool $expectedJson = true,
		bool $isError = false,
		bool $errorRequired = true,
		bool $contentRequired = true) : string
	{
		$stream = $response->getBody();
		$body = $stream->getContents();

		if ($expectedJson === true)
		{
			assertJson($body);
			$data = json_decode($body, true);
		}
		else
		{
			$data = $body;
		}

		if ($contentRequired === true)
		{
			assertNotEmpty($data);
		}

		if ($isError === false)
		{
			$isArray = is_array($data);

			if ($isArray === true)
			{
				assertArrayNotHasKey('error', $data);
			}
		}
		elseif ($errorRequired === true)
		{
			assertArrayHasKey('error', $data);
		}

		return $body;
	}

	/**
	 * Check status method.
	 *
	 * @param \Psr\Http\Message\ResponseInterface $response The PSR7 response
	 *                                                      object.
	 * @param boolean                             $isError  Indicates whether an
	 *                                                      error is expected or
	 *                                                      not.
	 *
	 * @return void
	 */
	public static function checkStatus(
		ResponseInterface $response,
		bool $isError = false)
	{
		$status = $response->getStatusCode();

		if ($isError === true)
		{
			assertNotEquals(200, $status);
		}
		else
		{
			assertEquals(200, $status);
		}
	}

	/**
	 * Display exception method.
	 *
	 * @param object  $exception The exception to process.
	 * @param boolean $isError   Indicates whether an error is expected or not.
	 *
	 * @return void
	 */
	private static function displayException(
		object $exception,
		bool $isError)
	{
		$class = get_class($exception);
		echo "Exception class: $class\n";

		$exists = $exception->hasResponse();

		if ($exists === true)
		{
			$response = $exception->getResponse();

			$message = $response->getReasonPhrase();
			$code = $response->getStatusCode();
			echo "Exception code: $code\t$message\n";

			$exists = !empty($response);

			if ($exists === true)
			{
				$body = $response->getBody();
				$contents = $body->getContents();
				print_r($contents);
			}
			else
			{
				echo "WARNING: Empty exception object\n";
			}
		}

		assertTrue($isError);
	}
}
