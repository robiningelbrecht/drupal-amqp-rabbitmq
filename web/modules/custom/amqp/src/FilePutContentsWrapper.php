<?php

namespace Drupal\amqp;

class FilePutContentsWrapper
{
  public function filePutContents(string $filename, string $contents): void
  {
    file_put_contents($filename, $contents);
  }
}
