# DKAN Project Template

This is a Composer project template for the DKAN open data platform based on
Drupal.

Use Composer to create a project:

    composer create-project getdkan/recommended-project --stability dev

You can also specify a Drupal core version:

    composer create-project getdkan/recommended-project:11.0.x-dev dkan

Need more information? Head over to the DKAN site: [https://getdkan.org/](https://getdkan.org/)

## Drupal 11 notes

Drupal 11 compatibility notes. 

1. Update to PHP 8.3 and by editing .ddev/config.dkan.yaml and `ddev restart`
2. You will not be able to install dependencies on first try. After failure,
remove `drupal/select1` and `getdkan/dkan` from composer.json temporarily. Run
`ddev composer install`. Restore those two lines to the `"require"` section, then
run `ddev composer update`.
