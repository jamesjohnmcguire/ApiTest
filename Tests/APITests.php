<?php

/**
 * An end to end API tester.
 *
 * @package   APITest
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2023 - 2026 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @version   1.7.35
 * @link      https://github.com/jamesjohnmcguire/ApiTest
 */

declare(strict_types=1);

namespace DigitalZenWorks\ApiTest\Tests;

use DigitalZenWorks\ApiTest\TestOptions;
use DigitalZenWorks\ApiTest\APITester;
use DigitalZenWorks\ApiTest\PageTester;
use GuzzleHttp\Cookie\CookieJar;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * APITests class.
 *
 * Contains all the automated API tests.
 */
final class APITests extends AbstractTestBase
{
	/**
	 * API Tester object.
	 *
	 * @var APITester
	 */
	private APITester $apiTester;

	/**
	 * Host.
	 *
	 * @var string
	 */
	private string $host;

	/**
	 * Page Tester object.
	 *
	 * @var PageTester
	 */
	private PageTester $pageTester;

	/**
	 * Set up before class method.
	 *
	 * @return void
	 */
	public static function setUpBeforeClass() : void
	{
	}

	/**
	 * Set up method.
	 *
	 * @return void
	 */
	protected function setUp() : void
	{
		parent::setUp();

		$this->host = 'https://httpbin.org';

		$this->apiTester = new APITester($this->host);
		$this->pageTester =
			new PageTester($this->host, 'text/html', 'text/html');
	}

	/**
	 * Tear down method.
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
	}

	/**
	 * Sanity check test.
	 *
	 * @return void
	 */
	#[Group('basic')]
	#[Test]
	public function sanityCheck()
	{
		$tester = 18;

		$this->assertEquals(18, $tester);
	}

	/**
	 * Get success test.
	 *
	 * @return void
	 */
	#[Group('get')]
	#[Test]
	public function getSuccess()
	{
		$endPoint = 'https://httpbin.org/get';

		$response = $this->apiTester->apiEndPointTest('GET', $endPoint);

		$this->assertNotEmpty($response);

		$this->assertStringContainsString('application/json', $response);

		$decodedResponse = json_decode($response, true);
		$this->assertIsArray($decodedResponse);

		$this->assertArrayHasKey('url', $decodedResponse);
		$url = $decodedResponse['url'];
		$this->assertNotEmpty($url);
		$this->assertIsString($url);
		$this->assertEquals('https://httpbin.org/get', $url);
	}

	/**
	 * Not found status test.
	 *
	 * @return void
	 */
	#[Group('not-found')]
	#[Test]
	public function notFound()
	{
		$endPoint = 'https://httpbin.org/status/404';

		$options = new TestOptions();
		$options->contentRequired = false;
		$options->errorExpected = true;

		$this->pageTester->webPageTest('GET', $endPoint, null, $options);

		$response = $this->pageTester->response;
		$status = $response->getStatusCode();
		$this->assertEquals(404, $status);
	}

	/**
	 * Post success test.
	 *
	 * @return void
	 */
	#[Group('post')]
	#[Test]
	public function postFormParamsSuccess()
	{
		$endPoint = 'https://httpbin.org/post';

		$data =
		[
			'name'  => 'SomeUser',
			'email' => 'somebody@example.com'
		];

		$options = new TestOptions();

		$options->requestDataType = 'form_params';

		$response = $this->apiTester->apiEndPointTest(
			'POST',
			$endPoint,
			$data,
			$options);

		$this->assertNotEmpty($response);

		$this->assertStringContainsString('somebody@example.com', $response);

		$decodedResponse = json_decode($response, true);
		$this->assertIsArray($decodedResponse);

		$this->assertArrayHasKey('url', $decodedResponse);
		$url = $decodedResponse['url'];
		$this->assertNotEmpty($url);
		$this->assertIsString($url);
		$this->assertEquals('https://httpbin.org/post', $url);
	}

	/**
	 * Post success test.
	 *
	 * @return void
	 */
	#[Group('post')]
	#[Test]
	public function postJsonSuccess()
	{
		$endPoint = 'https://httpbin.org/post';

		$data =
		[
			'name'  => 'SomeUser',
			'email' => 'somebody@example.com'
		];

		$options = new TestOptions();

		$options->requestDataType = 'json';

		$response = $this->apiTester->apiEndPointTest(
			'POST',
			$endPoint,
			$data,
			$options);

		$this->assertNotEmpty($response);

		$this->assertStringContainsString('somebody@example.com', $response);

		$decodedResponse = json_decode($response, true);
		$this->assertIsArray($decodedResponse);

		$this->assertArrayHasKey('url', $decodedResponse);
		$url = $decodedResponse['url'];
		$this->assertNotEmpty($url);
		$this->assertIsString($url);
		$this->assertEquals('https://httpbin.org/post', $url);
	}

	/**
	 * Post success additional data test.
	 *
	 * @return void
	 */
	#[Group('post')]
	#[Test]
	public function postSuccessAdditionalData()
	{
		$endPoint = 'https://httpbin.org/post';

		$jsonData = $this->formatRequestJsonBody();

		$options = new TestOptions();

		$options->requestDataType = 'body';

		$response = $this->apiTester->apiEndPointTest(
			'POST',
			$endPoint,
			$jsonData,
			$options);

		$this->assertNotEmpty($response);

		$this->assertStringContainsString('james@example.com', $response);

		$decodedResponse = json_decode($response, true);
		$this->assertIsArray($decodedResponse);

		$this->assertArrayHasKey('url', $decodedResponse);
		$url = $decodedResponse['url'];
		$this->assertNotEmpty($url);
		$this->assertIsString($url);
		$this->assertEquals('https://httpbin.org/post', $url);
	}

	/**
	 * Post success additional data test.
	 *
	 * @return void
	 */
	#[Group('post')]
	#[Test]
	public function postWithJarSuccess()
	{
		$endPoint = 'https://httpbin.org/post';

		$jsonData = $this->formatRequestJsonBody();

		$options = new TestOptions();

		$options->requestDataType = 'body';

		$cookies =
		[
			'some_id' => 123
		];

		$jar = CookieJar::fromArray($cookies, 'httpbin.org');

		$guzzleAdditionalOptions =
		[
			'cookies' => $jar
		];

		$options->guzzleAdditionalOptions = $guzzleAdditionalOptions;

		$response = $this->apiTester->apiEndPointTest(
			'POST',
			$endPoint,
			$jsonData,
			$options);

		$this->assertNotEmpty($response);

		$this->assertStringContainsString('james@example.com', $response);

		$decodedResponse = json_decode($response, true);
		$this->assertIsArray($decodedResponse);

		$this->assertArrayHasKey('url', $decodedResponse);
		$url = $decodedResponse['url'];
		$this->assertNotEmpty($url);
		$this->assertIsString($url);
		$this->assertEquals('https://httpbin.org/post', $url);
	}

	/**
	 * Post success additional data test.
	 *
	 * @return void
	 */
	#[Group('post')]
	#[Test]
	public function optionsSetUserAgent()
	{
		$userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) ' .
			'AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 ' .
			'Safari/537.36';
		$expected = $userAgent;

		$options = new TestOptions();
		$options->userAgent = $userAgent;

		$this->assertEquals($expected, $options->userAgent);
	}

	/**
	 * Format request body method.
	 *
	 * @return array<string>
	 */
	private function formatRequestBody() : array
	{
		$postData =
		[
			'userName' => 'JamesMc',
			'email'    => 'james@example.com',
			'title'    => 'New Example'
		];

		return $postData;
	}

	/**
	 * Format Request JSON body method.
	 *
	 * @return null|string
	 */
	private function formatRequestJsonBody() : ?string
	{
		$jsonData = null;
		$postData = $this->formatRequestBody();

		$jsonBody = json_encode($postData);

		if ($jsonBody !== false)
		{
			$jsonData = $jsonBody;
		}

		return $jsonData;
	}
}
