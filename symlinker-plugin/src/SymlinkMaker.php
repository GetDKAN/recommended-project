<?php

namespace Dkan\Composer\Plugin\Symlinker;

use Symfony\Component\Filesystem\Filesystem;
use Composer\IO\IOInterface;

class SymlinkMaker {

  protected $source;

  protected $destination;

  protected $io;

  public function __construct(IOInterface $io, $source, $destination) {
    $this->source = $source;
    $this->destination = $destination;
    $this->io = $io;
  }

  public function execute() {
    $exists = FALSE;
    $fs = new Filesystem();
    $source = realpath($this->source);
    // If the symlink already exists and is correct, don't do any work.
    if ($fs->exists($this->destination) && $fs->exists($source)) {
      $exists = TRUE;
      if (is_link($this->destination) && readlink($this->destination) == $source) {
        return;
      }
    }
    if ($exists) {

    }
    $this->io->write('Symlinking: ' . $this->source . ' to ' . $this->destination);
    $fs->symlink($source, $this->destination);
  }

}
