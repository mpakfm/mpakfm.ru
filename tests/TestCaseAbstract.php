<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 11.07.2020
 * Time: 19:20.
 */

namespace App\Tests;

use App\Tests\Utils\DatabaseReloader;
use Doctrine\ORM\EntityManager;
use Mpakfm\Printu;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestAssertionsTrait;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Throwable;

abstract class TestCaseAbstract extends KernelTestCase
{
    use WebTestAssertionsTrait;

    public static $execCounter = 0;

    /**
     * @var EntityManager
     */
    protected $em;

    /** @var DatabaseReloader */
    private static $databaseReloader;

    /** @var AbstractBrowser */
    private static $clientBrowser;

    protected function setUp(): void
    {
        parent::setUp();
        if ('test' !== getenv('APP_ENV')) {
            static::markTestSkipped('Тесты должны запускаться с APP_ENV=test');
        }

        try {
            self::bootKernel();
            $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();

            if (!self::$execCounter) {
                $this->getDatabaseReloader($this->em)->cleanDatabase($this);
            }
        } catch (Throwable $exception) {
            Printu::obj($exception->getMessage())->error('TestCaseAbstract::setUp Exception in file ' . $exception->getFile() . ' in line ' . $exception->getLine());
        }

        self::$execCounter++;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::getClient(null);
    }

    /**
     * Creates a KernelBrowser.
     */
    protected static function createClient(array $options = [], array $server = [])
    {
        if (static::$booted) {
            $kernel = static::$kernel;
        } else {
            $kernel = static::bootKernel($options);
        }

        try {
            self::$clientBrowser = $kernel->getContainer()->get('test.client');
        } catch (ServiceNotFoundException $e) {
            if (class_exists(KernelBrowser::class)) {
                throw new \LogicException('You cannot create the client used in functional tests if the "framework.test" config is not set to true.');
            }

            throw new \LogicException('You cannot create the client used in functional tests if the BrowserKit component is not available. Try running "composer require symfony/browser-kit".');
        }

        self::$clientBrowser->setServerParameters($server);

        return self::getClient(self::$clientBrowser);
    }

    private function getDatabaseReloader(EntityManager $em): DatabaseReloader
    {
        if (!self::$databaseReloader) {
            self::$databaseReloader = new DatabaseReloader($em);
        }

        return self::$databaseReloader;
    }
}
