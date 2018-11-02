<?php

namespace Tests\UrlUtils;

use App\UrlUtils\Model\Url;
use App\UrlUtils\Model\UrlContext;
use App\UrlUtils\UrlScanner;
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
    $urls = $scanner->scanText($val, $this->getContext());

    // Assert
    $this->assertEquals($expected, $urls);
  }

  private function getContext(bool $inline = false): UrlContext
  {
    $context = new UrlContext(self::class);

    return $inline ? $context->asInline() : $context;
  }

  public function scanTextProvider()
  {
    return [
        [
            'Simple: http://google.com',
            [
                new Url('http://google.com', false, $this->getContext(true)),
            ],
        ],

        [
            'Multiple: http://google.com, www.google.com & drenso.nl',
            [
                new Url('http://google.com', false, $this->getContext(true)),
                new Url('www.google.com', false, $this->getContext(true)),
                new Url('drenso.nl', false, $this->getContext(true)),
            ],
        ],

        [
            'Duplicates: http://google.com, http://google.com en http://google.com',
            [
                new Url('http://google.com', false, $this->getContext(true)),
            ],
        ],

        [
            'Internal: http://localhost/test',
            [
                new Url('http://localhost/test', true, $this->getContext(true)),
            ],
        ],

        [
            'Internal with external: http://localhost/test, www.google.com',
            [
                new Url('http://localhost/test', true, $this->getContext(true)),
                new Url('www.google.com', false, $this->getContext(true)),
            ],
        ],

        [
            'Img tag <img src="google.com/test2">',
            [
                new Url('google.com/test2', false, $this->getContext()),
            ],
        ],

        [
            'Img tags <img src=\'google.com/test\'> <img src="google.com/test2">',
            [
                new Url('google.com/test', false, $this->getContext()),
                new Url('google.com/test2', false, $this->getContext()),
            ],
        ],

        [
            'Img tags with internal <img src=\'/test\'> <img src="google.com/test2">',
            [
                new Url('google.com/test2', false, $this->getContext()),
                new Url('/test', true, $this->getContext()),
            ],
        ],

        [
            'A tag <a href="google.com/test2">',
            [
                new Url('google.com/test2', false, $this->getContext()),
            ],
        ],

        [
            'A tags <a href="http://localhost/test"> <a href="https://google.com/test2">',
            [
                new Url('http://localhost/test', true, $this->getContext()),
                new Url('https://google.com/test2', false, $this->getContext()),
            ],
        ],

        [
            'A tags with internal <a href=\'/test\'> <a href="google.com/test2">',
            [
                new Url('google.com/test2', false, $this->getContext()),
                new Url('/test', true, $this->getContext()),
            ],
        ],

        [
            'A tag with duplicate <a href="http://localhost/test"> <a href=\'http://localhost/test\'>',
            [
                new Url('http://localhost/test', true, $this->getContext()),
            ],
        ],

        [
            'A and inline <a href="http://localhost/test"> & http://localhost/test',
            [
                new Url('http://localhost/test', true, $this->getContext()),
                new Url('http://localhost/test', true, $this->getContext(true)),
            ],
        ],

        [
            'Long text: <a href="http://localhost/test"> <img src="http://localhost/test"> & http://localhost/test & http://localhost/test ' .
            '<a href="/test"> <img src="/test"> & /test & /test ' .
            '<a href="http://google.com/test"> <img src="http://google.com/test"> & http://google.com/test & http://google.com/test',
            [
                new Url('http://localhost/test', true, $this->getContext()),
                new Url('http://localhost/test', true, $this->getContext(true)),
                new Url('http://google.com/test', false, $this->getContext()),
                new Url('http://google.com/test', false, $this->getContext(true)),
                new Url('/test', true, $this->getContext()),
            ],
        ],
    ];
  }
}
