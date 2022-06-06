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
    $this->io->write('Symlinking: ' . $this->source . ' to ' . $this->destination);
    $fs = new Filesystem();
    $fs->symlink($this->source, $this->destination);
  }

}
