<?php

namespace Tests\UrlScanner;

use App\UrlScanner\Model\Url;
use App\UrlScanner\UrlScanner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class UrlScannerTest extends TestCase
{

  /**
   * @dataProvider scanTextProvider
   *
   * @param string $val
   * @param array  $expected
   */
  public function test_scanText(string $val, array $expected)
  {
    // Arrange
    $contextMock = new RequestContext();
    $routerMock  = $this->createMock(RouterInterface::class);
    $routerMock
        ->method('getContext')
        ->willReturn($contextMock);

    $scanner = new UrlScanner($routerMock);

    // Act
    $urls = $scanner->scanText($val);

    // Assert
    $this->assertEquals($expected, $urls);
  }

  public function scanTextProvider()
  {
    return [
        [
            "Text with simple http://google.com url",
            [
                new Url("http://google.com", false),
            ],
        ],

        [
            "Text with multiple: http://google.com urls, www.google.com en drenso.nl",
            [
                new Url("http://google.com", false),
                new Url("www.google.com", false),
                new Url("drenso.nl", false),
            ],
        ],

        [
            "Text with doubles: http://google.com urls, http://google.com en http://google.com",
            [
                new Url("http://google.com", false),
            ],
        ],

        [
            "Text with internal: http://localhost/test urls, www.google.com",
            [
                new Url("http://localhost/test", true),
                new Url("www.google.com", false),
            ],
        ],

        [
            "Text with img <img src='/test'> <img src=\"google.com/test2\">",
            [
                new Url("google.com/test2", false),
                new Url("/test", true),
            ],
        ],

        [
            "Text with a <a href='/test'> <a href=\"google.com/test2\">",
            [
                new Url("google.com/test2", false),
                new Url("/test", true),
            ],
        ],

        [
            "Text with a <a href='http://localhost/test'> <a href=\"https://google.com/test2\">",
            [
                new Url("http://localhost/test", true),
                new Url("https://google.com/test2", false),
            ],
        ],

        [
            "Text with a and doubles <a href='http://localhost/test'> <a href=\"http://localhost/test\">",
            [
                new Url("http://localhost/test", true),
            ],
        ],
    ];
  }
}
