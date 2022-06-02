<?php

use DkanTools\Util\Util;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{

  private static $docroot = '/var/www/html/docroot';

  /**
   * Perform Drupal/DKAN database installation
   *
   * @option bool existing-config
   *   Use drush site:install --existing-config option.
   */
  public function install($opts = ['existing-config' => false])
  {
    if ($opts['existing-config']) {
      $this->taskExec('drush si -y --existing-config')
        ->dir(static::$docroot)
        ->run();
    } else {
      $this->standardInstallation();
    }

    $result = $this->taskExecStack()
      // Ensure resources directories exists and are writable.
      ->exec('mkdir -p sites/default/files/uploaded_resources')
      ->exec('mkdir -p sites/default/files/resources')
      ->exec('chmod -R 777 sites/default/files')
      // Workaround for https://www.drupal.org/project/drupal/issues/3091285.
      ->exec('chmod u+w sites/default')
      ->dir(static::$docroot)
      ->run();

    return $result;
  }

  private function standardInstallation()
  {
    $this->taskExecStack()
      ->stopOnFail()
      ->exec('drush site:install standard --site-name "DKAN" -y')
      ->exec("drush en dkan config_update_ui -y")
      ->exec("drush config-set system.performance css.preprocess 0 -y")
      ->exec("drush config-set system.performance js.preprocess 0 -y")
      ->dir(static::$docroot)
      ->run();
  }

  /**
   * Install DKAN sample content.
   */
  public function installSample()
  {
    $this->taskExecStack()
      ->stopOnFail()
      ->exec('drush en sample_content -y')
      ->exec('drush  dkan:sample-content:create')
      ->exec('drush  queue:run datastore_import')
      ->exec('drush  dkan:metastore-search:rebuild-tracker')
      ->exec('drush  sapi-i')
      ->dir(static::$docroot)
      ->run();
  }
}
