<?php

namespace Dkan\Composer\Plugin\Symlinker;

use Composer\Composer;
use Composer\EventDispatcher\EventDispatcher;
use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;
use Drupal\Composer\Plugin\Scaffold\Operations\OperationData;
use Drupal\Composer\Plugin\Scaffold\Operations\OperationFactory;
use Drupal\Composer\Plugin\Scaffold\Operations\ScaffoldFileCollection;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

/**
 * Core class of the plugin.
 *
 * Contains the primary logic which determines the files to be fetched and
 * processed.
 *
 * @internal
 */
class Handler {

  /**
   * Composer hook called before scaffolding begins.
   */
  //  const PRE_DRUPAL_SCAFFOLD_CMD = 'pre-drupal-scaffold-cmd';

  /**
   * Composer hook called after scaffolding completes.
   */
  //  const POST_DRUPAL_SCAFFOLD_CMD = 'post-drupal-scaffold-cmd';

  /**
   * The Composer service.
   *
   * @var \Composer\Composer
   */
  protected $composer;

  /**
   * Composer's I/O service.
   *
   * @var \Composer\IO\IOInterface
   */
  protected $io;

  /**
   * The scaffold options in the top-level composer.json's 'extra' section.
   *
   * @var \Dkan\Composer\Plugin\Symlinker\SymlinkerOptions
   */
  protected $manageOptions;

  /**
   * The manager that keeps track of which packages are allowed to scaffold.
   *
   * @var \Drupal\Composer\Plugin\Scaffold\AllowedPackages
   */
  protected $manageAllowedPackages;

  /**
   * The list of listeners that are notified after a package event.
   *
   * @var \Drupal\Composer\Plugin\Scaffold\PostPackageEventListenerInterface[]
   */
  protected $postPackageListeners = [];

  /**
   * Handler constructor.
   *
   * @param \Composer\Composer $composer
   *   The Composer service.
   * @param \Composer\IO\IOInterface $io
   *   The Composer I/O service.
   */
  public function __construct(Composer $composer, IOInterface $io) {
    $this->composer = $composer;
    $this->io = $io;
    $this->manageOptions = new ManageOptions($composer);
  }

  public function makesymlinks() {
    $symlink_makers = [];
    $symlinker_options = $this->manageOptions->getOptions();
    if ($mappings = $symlinker_options->fileMapping()) {
      foreach ($mappings as $destination => $source) {
        $dest_path = $this->locationSubtitution($destination, $symlinker_options->locations());
        $src_path = $this->locationSubtitution($source, $symlinker_options->locations());
        $symlink_makers[] = new SymlinkMaker($this->io, realpath($src_path), $dest_path);
      }
    }
    foreach ($symlink_makers as $maker) {
      $maker->execute();
    }
  }

  protected function locationSubtitution($path, $locations) {
    foreach ($locations as $name => $location_path) {
      $path = str_replace("[$name]", $location_path, $path);
    }
    // Handle empty path elements.
    $path_items = array_filter(explode('/', $path));
    // Normalize to current directory.
    if ($path_items[0] !== '.') {
      array_unshift($path_items, '.');
    }
    return implode('/', $path_items);
  }

}
