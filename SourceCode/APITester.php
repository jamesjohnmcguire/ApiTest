<?php

/**
 * API Test
 *
 * Description: An end to end API tester.
 * Version:     1.0.1
 * PHP version: 8.1.1
 *
 * @category  PHP
 * @package   APITest
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2023 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @version   1.0.1
 * @link      https://github.com/jamesjohnmcguire/MassMailer
 */

declare(strict_types=1);

namespace DotPix\MassMailer\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
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
 */
class APITester
{
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
	 * @param string                $method        The HTTP method to use.
	 * @param string                $endPoint      The API end point.
	 * @param null | array | string $data          The JSON data to process.
	 * @param boolean               $multiPartData Data is multipart form data.
	 *                                             Implying some of the data
	 *                                             may be binary.
	 * @param boolean               $isError       Indicates whether an error
	 *                                             is expected or not.
	 * @param boolean               $errorRequired Indicates whether an error
	 *                                             field is expected in the
	 *                                             response or not.
	 *
	 * @return mixed
	 */
	public function testApiEndPoint(
		string $method,
		string $endPoint,
		null | array | string $data,
		bool $multiPartData = false,
		bool $isError = false,
		bool $errorRequired = true) : mixed
	{
		$responseContent = null;

		try
		{
			if ($this->requestDataType === 'application/json')
			{
				$options = ['body' => $data];
			}
			elseif ($multiPartData === false)
			{
				// Normal form data.
				$options = ['form_params' => $data];
			}
			else
			{
				$options = $data;
			}

			if ($isError === true)
			{
				// Disable throwing exceptions on an HTTP protocol errors.
				$options['http_errors'] = false;
			}

			$request = new Request($method, $endPoint);
			$response = $this->client->send($request, $options);

			self::CheckStatus($response, $isError);
			$responseContent =
				self::CheckBody($response, $isError, $errorRequired);
		}
		// Guzzle high level super class exception.
		catch (BadResponseException $exception)
		{
			$this->displayException($exception, $isError);
		}
		// Guzzle low level super class exception.
		catch (RequestException $exception)
		{
			$this->displayException($exception, $isError);
		}
		catch (\Exception $exception)
		{
			// Not expecting any general exceptions.
			$this->displayException($exception, false);
		}

		return $responseContent;
	}

	/**
	 * Check body method.
	 *
	 * @param \Psr\Http\Message\ResponseInterface $response      The PSR7
	 *                                                           response
	 *                                                           object.
	 * @param boolean                             $isError       Indicates
	 *                                                           whether an
	 *                                                           error is
	 *                                                           expected or
	 *                                                           not.
	 * @param boolean                             $errorRequired Indicates
	 *                                                           whether an
	 *                                                           error field is
	 *                                                           expected in the
	 *                                                           response or
	 *                                                           not.
	 *
	 * @return mixed
	 */
	private static function CheckBody(
		ResponseInterface $response,
		bool $isError = false,
		bool $errorRequired = true) : mixed
	{
		$stream = $response->getBody();
		$body = $stream->getContents();

		assertJson($body);
		$data = json_decode($body);

		assertNotEmpty($data);

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

		return $data;
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
	private static function CheckStatus(
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
		echo "exception class: $class\r\n";

		$response = $exception->getResponse();

		$exists = !empty($response);

		if ($exists === true)
		{
			$body = $response->getBody();
			$contents = $body->getContents();
			print_r($contents);
		}
		else
		{
			echo "WARNING: Empty exception object\r\n";
		}

		assertTrue($isError);
	}
}