<?php

declare(strict_types=1);

namespace Itribe\TyposcriptCode\Tests\Functional\Controller;

use PHPUnit\Framework\Attributes\Test;
use SBUERK\TYPO3\Testing\SiteHandling\SiteBasedTestTrait;
use TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Functional test for TypoScript content element rendering
 */
final class ContentRenderingTest extends FunctionalTestCase
{
    use SiteBasedTestTrait;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/typoscript_code',
    ];


    protected const LANGUAGE_PRESETS = [
        'EN' => ['id' => 0, 'title' => 'English', 'locale' => 'en_US.UTF8'],
    ];

    protected array $configurationToUseInTestInstance = [
        'SYS' => [
            'caching' => [
                'cacheConfigurations' => [
                    'pages' => [
                        'backend' => Typo3DatabaseBackend::class,
                    ],
                ],
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/content_element_rendering.csv');

        $this->writeSiteConfiguration(
            'test',
            $this->buildSiteConfiguration(1, '/'),
            [
                $this->buildDefaultLanguageConfiguration('EN', '/'),
            ],
            $this->buildErrorHandlingConfiguration('Fluid', [404])
        );
    }

    #[Test]
    public function regularTypoScriptIsCachedButCoaIntIsNot(): void
    {
        $body1 = $this->fetchTestPageBody();

        preg_match('/Cached:(\d+)/', $body1, $cachedMatches1);
        preg_match('/Timestamp:(\d+)/', $body1, $uncachedMatches1);
        $cachedTimestamp1 = $cachedMatches1[1] ?? null;
        $uncachedTimestamp1 = $uncachedMatches1[1] ?? null;

        sleep(2);
        $this->resetExecutionTime();

        $body2 = $this->fetchTestPageBody();

        preg_match('/Cached:(\d+)/', $body2, $cachedMatches2);
        preg_match('/Timestamp:(\d+)/', $body2, $uncachedMatches2);
        $cachedTimestamp2 = $cachedMatches2[1] ?? null;
        $uncachedTimestamp2 = $uncachedMatches2[1] ?? null;

        // Verify timestamps are present
        self::assertNotNull($cachedTimestamp1, 'First cached timestamp should be present');
        self::assertNotNull($cachedTimestamp2, 'Second cached timestamp should be present');
        self::assertNotNull($uncachedTimestamp1, 'First uncached timestamp should be present');
        self::assertNotNull($uncachedTimestamp2, 'Second uncached timestamp should be present');

        // Cached timestamp should be the SAME (proves caching works)
        self::assertEquals(
            $cachedTimestamp1,
            $cachedTimestamp2,
            'Regular TypoScript timestamps should be identical as content is cached'
        );

        // COA_INT timestamp should be DIFFERENT (uncached)
        $timestampDiff = abs((int)$uncachedTimestamp2 - (int)$uncachedTimestamp1);
        self::assertGreaterThanOrEqual(
            2,
            $timestampDiff,
            'COA_INT timestamps should differ by at least 2 seconds as it is uncached content.'
        );
    }

    #[Test]
    public function simpleTextRendersCorrectly(): void
    {
        $body = $this->fetchTestPageBody();

        self::assertStringContainsString('Hello from TypoScript!', $body);
    }

    #[Test]
    public function libReferencingWorks(): void
    {
        $body = $this->fetchTestPageBody();

        self::assertStringContainsString('TEST Lib value', $body, 'lib.test should render its content');
    }

    #[Test]
    public function conditionsWorkCorrectly(): void
    {
        $body = $this->fetchTestPageBody();

        self::assertStringContainsString('Condition matched for page 1', $body);
        self::assertStringNotContainsString('This should not appear', $body);
    }

    #[Test]
    public function complexCoaRendersAllParts(): void
    {
        $body = $this->fetchTestPageBody();

        self::assertStringContainsString('<h2>Header</h2>', $body);
        self::assertStringContainsString('Page title:Test Page', $body);
        self::assertStringContainsString('<footer>Footer</footer>', $body);
    }

    #[Test]
    public function dataProcessingWorksCorrectly(): void
    {
        $body = $this->fetchTestPageBody();

        self::assertStringContainsString('Page UID:1', $body);
        self::assertStringContainsString('TSFE ID:1', $body);
    }

    /**
     * Executes a frontend request to the test page and returns the response body.
     */
    private function fetchTestPageBody(): string
    {
        $response = $this->executeFrontendSubRequest(
            new InternalRequest('https://example.com/test-page')
        );
        self::assertEquals(200, $response->getStatusCode());

        return (string)$response->getBody();
    }

    /**
     * Resets TYPO3 execution time globals to current time.
     *
     * In functional tests, $GLOBALS['EXEC_TIME'] is set once during setUp and not
     * updated between executeFrontendSubRequest() calls. This simulates a new
     * HTTP request by updating the timestamp, which is necessary for testing
     * time-dependent features like COA_INT uncached content.
     *
     * @see SystemEnvironmentBuilder::initializeGlobalTimeTrackingVariables()
     */
    private function resetExecutionTime(): void
    {
        // EXEC_TIME is set so that the rest of the script has a common value for the script execution time
        $GLOBALS['EXEC_TIME'] = time();
        // $ACCESS_TIME is a common time in minutes for access control
        $GLOBALS['ACCESS_TIME'] = $GLOBALS['EXEC_TIME'] - $GLOBALS['EXEC_TIME'] % 60;
        // $SIM_EXEC_TIME is set to $EXEC_TIME but can be altered later in the script if we want to
        // simulate another execution-time when selecting from eg. a database
        $GLOBALS['SIM_EXEC_TIME'] = $GLOBALS['EXEC_TIME'];
        // If $SIM_EXEC_TIME is changed this value must be set accordingly
        $GLOBALS['SIM_ACCESS_TIME'] = $GLOBALS['ACCESS_TIME'];
    }
}