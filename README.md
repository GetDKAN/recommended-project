# DKAN Recommended Project Template

Note: This repo is currently under development. Do not use in production!

This is a recommended starting point for a DKAN project.

The suggested set of commands thus far are:
```shell
ddev config --project-type=drupal9 --docroot=docroot --create-docroot
# Alternately, just ddev config and hit return, being sure you're making a drupal9 site.
ddev start
ddev composer install
ddev composer makesymlinks
ddev drush site:install -y
ddev drush uli
ddev launch
```

You can now develop a DKAN site locally, and much like the workflow with dkan-tools, you'd commit the `src/` directory to your project repo.

On deploy, you'd issue `composer install` and use Drush for updates or whatever.

All of this is subject to improvement.

If you experiment with this repo, you will likely encounter stuff that breaks. Thank you and please let us know. :-)

The version of dkan-tools which is a directory in this repo is the bespoke version that fixes some issues for `DKTL_MODE=HOST`, among other issues. We'll need to work on adding those changes to dkan-tools.
