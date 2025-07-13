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

namespace DigitalZenWorks\ApiTest\Tests;

$root = dirname(__DIR__, 1);

require_once $root . '/vendor/autoload.php';
require_once 'AbstractTestBase.php';

use DigitalZenWorks\ApiTest\ApiOptions;
use DigitalZenWorks\ApiTest\APITester;

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
	public function SanityCheck()
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
	public function GetSuccess()
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
	 * Post success test.
	 * 
	 * @return void
	 */
	#[Group('post')]
	#[Test]
	public function PostSuccess()
	{
		$endPoint = 'https://httpbin.org/post';

		$data =
		[
			'name' => 'James',
			'email' => 'james@example.com'
		];

		$options = new ApiOptions();

		$options->dataType = 'json';

		$response = $this->apiTester->apiEndPointTest(
			'POST', $endPoint, $data, $options);

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
	public function PostSuccessAdditionalData()
	{
		$endPoint = 'https://httpbin.org/post';

		$jsonData = $this->formatRequestJsonBody();

		$options = new ApiOptions();

		$options->dataType = 'body';

		$response = $this->apiTester->apiEndPointTest(
			'POST', $endPoint, $jsonData, $options);

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
	 * Format request body method.
	 *
	 * @return array<string>
	 */
	private function formatRequestBody() : array
	{
		$postData =
		[
			'userName'    => 'JamesMc',
			'email' => 'james@example.com',
			'title'       => 'New anime office'
		];

		return $postData;
	}

	/**
	 * Format Request JSON body method.
	 *
	 * @return string
	 */
	private function formatRequestJsonBody() : string
	{
		$postData = $this->formatRequestBody();

		$jsonBody = json_encode($postData);

		return $jsonBody;
	}
}
