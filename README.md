# DKAN Recommended Project Template

Note: This repo is currently under development. Do not use in production!

This is a recommended starting point for a DKAN project.

The suggested set of commands thus far are:
```shell
# composer create-project getdkan/recommended-project my-project
# Since this isn't yet on packagist, do this instead:
git clone [this repository]
composer install
ddev start
ddev dktl make
ddev config
# Change the name if you'd like, but the important thing here is that ddev
# will write out settings.php stuff.
ddev dktl install
ddev drush uli
```

Note also that it's possible to run dktl commands locally without spinning up a bunch of containers like this:
```shell
DKTL_MODE=HOST ./vendor/bin/dktl [your commmand]
```

You can now develop a DKAN site locally, and much like the workflow with dkan-tools, you'd commit the `src/` directory to your project repo.

On deploy, you'd issue `composer install` and `DKTL_MODE=HOST ./vendor/bin/dktl make`

All of this is subject to improvement.

If you experiment with this repo, you will likely encounter stuff that breaks. Thank you and please let us know. :-)

The version of dkan-tools which is a directory in this repo is the bespoke version that fixes some issues for `DKTL_MODE=HOST`, among other issues. We'll need to work on adding those changes to dkan-tools.
