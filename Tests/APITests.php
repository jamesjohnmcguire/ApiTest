<?php

/**
 * An end to end API tester.
 *
 * @package   APITest
 * @author    James John McGuire <jamesjohnmcguire@gmail.com>
 * @copyright 2023 - 2024 James John McGuire <jamesjohnmcguire@gmail.com>
 * @license   MIT https://opensource.org/licenses/MIT
 * @version   1.3.11
 * @link      https://github.com/jamesjohnmcguire/ApiTest
 */

declare(strict_types=1);

namespace DigitalZenWorks\ApiTest\Tests;

$root = dirname(__DIR__, 1);

require_once $root . '/vendor/autoload.php';
require_once 'AbstractTestBase.php';

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
	private const LOCAL = 'http://pix.localhost:8080/';

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
	 * Server.
	 *
	 * @var string
	 */
	private string $server = 'local';

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

		$this->host = self::getHost();

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

	#[Group('local')]
	#[Group('post')]
	#[Test]
	public function testApiEndPointMailListAddAccount()
	{
		$data =
		[
			'userName'       => 'Curly',
			'passWord'       => 'LetMeIn2!',
			'mailGunApiKey'  => 'api:key-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
			'mailGunSendurl' =>
				'https://api.mailgun.net/v3/mg.example.com/messages',
			'smtpHost'       => 'smtp.gmail.com',
			'smtpUserName'   => 'somebody@gmail.com',
			'smtpPassWord'   => 'xxxxxxxxxxxxxxxx',
			'smtpProtocol'   => 'tls',
			'smtpPort'       => 587
		];

		$endPoint = self::LOCAL . 'add_account';

		$response =
			$this->apiTester->testApiEndPoint('POST', $endPoint, $data, 'json');

		// Clean up.
		$data =
		[
			'userName' => 'Curly'
		];

		$endPoint = self::LOCAL . 'delete_account';
		$this->apiTester->testApiEndPoint('DELETE', $endPoint, $data, 'json');

		$this->assertIsArray($response);
		$this->assertContains('Curly account created successfully.', $response);
	}

	#[Group('local')]
	#[Group('post')]
	#[Test]
	public function testApiEndPointMailListSend()
	{
		$jsonData = $this->formatRequestJsonBody();

		$endPoint = self::LOCAL . 'maillist_send';

		$this->apiTester->testApiEndPoint('POST', $endPoint, $jsonData, 'body');
	}

	#[Group('local')]
	#[Group('post')]
	#[Test]
	public function testApiEndPointMailListSendNoOptionalValues()
	{
		$jsonData = $this->formatRequestJsonBody();

		$endPoint = self::LOCAL . 'maillist_send';

		$this->apiTester->testApiEndPoint('POST', $endPoint, $jsonData, 'body');
	}

	#[Group('local')]
	#[Group('post')]
	#[Test]
	public function testApiEndPointMailListSendProduction()
	{
		$jsonData = $this->formatRequestJsonBody(true, false, 'production');

		$endPoint = self::LOCAL . 'maillist_send';

		$this->apiTester->testApiEndPoint('POST', $endPoint, $jsonData, 'body');
	}

	#[Group('local')]
	#[Group('post')]
	#[Test]
	public function testApiEndPointMailListSendProductionWithFrom()
	{
		$jsonData = $this->formatRequestJsonBody(false, true);

		$endPoint = self::LOCAL . 'maillist_send';

		$this->apiTester->testApiEndPoint('POST', $endPoint, $jsonData, 'body');
	}

	#[Group('local')]
	#[Group('post')]
	#[Test]
	public function testApiEndPointMailListSendProductionWithOptionalValues()
	{
		$jsonData = $this->formatRequestJsonBody(false, true, 'production');

		$endPoint = self::LOCAL . 'maillist_send';

		$this->apiTester->testApiEndPoint('POST', $endPoint, $jsonData, 'body');
	}

	#[Group('local')]
	#[Group('post')]
	#[Test]
	public function testApiEndPointMailListSendWithFrom()
	{
		$jsonData = $this->formatRequestJsonBody(true, true, 'production');

		$endPoint = self::LOCAL . 'maillist_send';

		$this->apiTester->testApiEndPoint('POST', $endPoint, $jsonData, 'body');
	}

	#[Group('local')]
	#[Group('post')]
	#[Test]
	public function testApiEndPointMailListSendWithOptionalValues()
	{
		$jsonData = $this->formatRequestJsonBody();

		$endPoint = self::LOCAL . 'maillist_send';

		$this->apiTester->testApiEndPoint('POST', $endPoint, $jsonData, 'body');
	}

	#[Group('local')]
	#[Group('post')]
	#[Test]
	public function testApiEndPointQueueGetCount()
	{
		$endPoint = self::LOCAL . 'queue_count';

		$response = $this->apiTester->testApiEndPoint('POST', $endPoint, null);
		$this->assertIsInt($response);
	}

	#[Group('get')]
	#[Group('local')]
	#[Test]
	public function testApiEndPointQueueGetList()
	{
		$endPoint = self::LOCAL . 'queue_list';

		$response = $this->apiTester->testApiEndPoint('GET', $endPoint, null);
		$this->assertIsArray($response);
	}

	/**
	 * Get host method.
	 *
	 * @return string
	 */
	private static function getHost() : string
	{
		$argv = $GLOBALS['argv'];

		$host = self::LOCAL;

		// Specifying host on the command line would be the fifth item.
		$isEmpty = empty($argv[5]);

		if ($isEmpty === false)
		{
			$host = $argv[5];
		}

		return $host;
	}

	/**
	 * Format request body method.
	 *
	 * @return string
	 */
	private function formatRequestBody() : array
	{
		$postData =
		[
			'userName'    => 'JamesMc',
			'passWord'    => 'LetMeIn2!',
			'title'       => 'New anime office',
			'body'        => '<b>This is a bold title</b><br><br>' .
				'<p>This is the email content</p>',
			'attachments' => [],
			'recipients'  => $this->recipients
		];

		$postData['from'] = $this->fromAddress;

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
