<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\FilePutContentsWrapper;
use Drupal\Tests\UnitTestCase;

class FilePutContentsWrapperTest extends UnitTestCase
{
  public function testFilePutContents(): void{
    $filePutContentsWrapper = new FilePutContentsWrapper();

    $filePutContentsWrapper->filePutContents('file.tmp', 'content');
    $this->assertEquals('content', file_get_contents('file.tmp'));
  }
}
