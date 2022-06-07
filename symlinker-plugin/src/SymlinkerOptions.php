<?php

namespace Dkan\Composer\Plugin\Symlinker;

/**
 * Per-project options from the 'extras' section of the composer.json file.
 *
 * Projects that describe scaffold files do so via their scaffold options. This
 * data is pulled from the 'file-system-Symlink' portion of the extras section
 * of the project data.
 *
 * @internal
 */
class SymlinkerOptions {

  /**
   * The raw data from the 'extras' section of the top-level composer.json file.
   *
   * @var array
   */
  protected $options = [];

  /**
   * ScaffoldOptions constructor.
   *
   * @param array $options
   *   The options taken from the 'symlinker-plugin' section.
   * @param array $scaffoldOptions
   *   The scaffold options used to configure Drupal's scaffolding plugin.
   */
  protected function __construct(array $options, array $scaffoldOptions = []) {
    // Some defaults.
    $project_root = FALSE;
    $web_root = FALSE;

    // Grab from symlinker.
    if ($value = $options['locations']['project-root'] ?? FALSE) {
      $project_root = $value;
    }
    if ($value = $options['locations']['web-root'] ?? FALSE) {
      $web_root = $value;
    }

    // Grab from scaffold.
    if ($value = $scaffoldOptions['locations']['project-root'] ?? FALSE) {
      if ($project_root !== FALSE && $project_root !== $value) {
        throw new \Exception('symlinker plugin configuration locations:project-root can not override Drupal scaffold plugin.');
      }
      $project_root = $value;
    }
    if ($value = $scaffoldOptions['locations']['web-root'] ?? FALSE) {
      if ($web_root !== FALSE && $web_root !== $value) {
        throw new \Exception('symlinker plugin configuration locations:web-root can not override Drupal scaffold plugin.');
      }
      $web_root = $value;
    }

    $this->options = $options + [
        "locations" => [],
        "file-mapping" => [],
      ];

    // Define any default locations.
    $this->options['locations'] += [
      'project-root' => $project_root ?? '.',
      'web-root' => $web_root ?? '.',
    ];
  }

  /**
   * Creates a scaffold options object.
   *
   * @param array $extras
   *   The contents of the 'extras' section.
   *
   * @return self
   *   The scaffold options object representing the provided scaffold options
   */
  public static function create(array $extras) {
    return new self(
      $extras['symlinker-plugin'] ?? [],
      $extras['drupal-scaffold'] ?? []
    );
  }

  public function notSymlinkedMessage() {
    if ($message = $this->options['not-processed-message'] ?? FALSE) {
      return $message;
    }
    return [
      "The symlinker plugin never performed the symlinks.",
      "Please run: composer makesymlinks",
    ];
  }

  /**
   * Creates a new scaffold options object with some values overridden.
   *
   * @param array $options
   *   Override values.
   *
   * @return self
   *   The scaffold options object representing the provided scaffold options
   */
  protected function override(array $options) {
    return new self($options + $this->options);
  }

  /**
   * Creates a new scaffold options object with an overridden 'symlink' value.
   *
   * @param bool $symlink
   *   Whether symlinking should be enabled or not.
   *
   * @return self
   *   The scaffold options object representing the provided scaffold options
   */
  public function overrideSymlink($symlink) {
    return $this->override(['symlink' => $symlink]);
  }

  /**
   * Determines whether any allowed packages were defined.
   *
   * @return bool
   *   Whether there are allowed packages
   */
  public function hasAllowedPackages() {
    return !empty($this->allowedPackages());
  }

  /**
   * Gets allowed packages from these options.
   *
   * @return array
   *   The list of allowed packages
   */
  public function allowedPackages() {
    return $this->options['allowed-packages'];
  }

  /**
   * Gets the location mapping table, e.g. 'webroot' => './'.
   *
   * @return array
   *   A map of name : location values
   */
  public function locations() {
    return $this->options['locations'];
  }

  /**
   * Determines whether a given named location is defined.
   *
   * @param string $name
   *   The location name to search for.
   *
   * @return bool
   *   True if the specified named location exist.
   */
  protected function hasLocation($name) {
    return array_key_exists($name, $this->locations());
  }

  /**
   * Gets a specific named location.
   *
   * @param string $name
   *   The name of the location to fetch.
   *
   * @return string
   *   The value of the provided named location
   */
  public function getLocation($name) {
    return $this->hasLocation($name) ? $this->locations()[$name] : FALSE;
  }

  /**
   * Determines if symlink mode is set.
   *
   * @return bool
   *   Whether or not 'symlink' mode
   */
  public function symlink() {
    return $this->options['symlink'];
  }

  /**
   * Determines if there are file mappings.
   *
   * @return bool
   *   Whether or not the scaffold options contain any file mappings
   */
  public function hasFileMapping() {
    return !empty($this->fileMapping());
  }

  /**
   * Returns the actual file mappings.
   *
   * @return array
   *   File mappings for just this config type.
   */
  public function fileMapping() {
    return $this->options['file-mapping'];
  }

  /**
   * Determines if there is defined a value for the 'gitignore' option.
   *
   * @return bool
   *   Whether or not there is a 'gitignore' option setting
   */
  public function hasGitIgnore() {
    return isset($this->options['gitignore']);
  }

  /**
   * Gets the value of the 'gitignore' option.
   *
   * @return bool
   *   The 'gitignore' option, or TRUE if undefined.
   */
  public function gitIgnore() {
    return $this->hasGitIgnore() ? $this->options['gitignore'] : TRUE;
  }

}
