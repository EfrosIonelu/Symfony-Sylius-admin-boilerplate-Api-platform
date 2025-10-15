## 1. Creating the Symfony project

If the directory is not empty, create a temporary directory and move the files:

```bash
mkdir temp_symfony
composer create-project symfony/skeleton temp_symfony
cp -r temp_symfony/* . && cp temp_symfony/.* . 2>/dev/null || true
rm -rf temp_symfony
```

## 2. Installing basic Symfony packages

```bash
composer require doctrine/orm doctrine/doctrine-bundle
composer require symfony/webpack-encore-bundle symfony/stimulus-bundle
composer require symfony/translation
```

### Translation Configuration

Create `translations/messages.en.yaml`:
```yaml
app.ui.cms: "CMS"
app.ui.configs: "Configs"
```

Documentation: https://symfony.com/doc/current/translation.html

## 3. Development tools

Install Web Profiler Bundle for development:
```bash
composer require --dev symfony/web-profiler-bundle
```

Install code quality tools:
```bash
composer require --dev friendsofphp/php-cs-fixer phpstan/phpstan
```

### Code Quality Commands

Static analysis with PHPStan:
```bash
vendor/bin/phpstan analyse
```
Analyzes PHP code for potential bugs, dead code, and type errors without executing it.

Code style fixing with PHP-CS-Fixer:
```bash
./vendor/bin/php-cs-fixer fix src
```
Automatically fixes coding standards violations according to PSR-12 and other rules.

Check `config/bundles.php` contains:
```php
Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
```

Add `config/routes/dev/web_profiler.yaml`:
```yaml
web_profiler_wdt:
    resource: '@WebProfilerBundle/Resources/config/routing/wdt.xml'
    prefix: /_wdt

web_profiler_profiler:
    resource: '@WebProfilerBundle/Resources/config/routing/profiler.xml'
    prefix: /_profiler
```

Test profiler access at: `/_profiler`

## 4. Basic frontend setup

Install JavaScript dependencies:
```bash
npm install --force
```

## 5. Starting the server

```bash
symfony serve -d
```

The server will be available at: https://127.0.0.1:8000

## 6. Icons

Visit: https://fonts.google.com/icons

```bash
php bin/console ux:icons:import material-symbols:favorite
```
```twig
{{ ux_icon('material-symbols:upload') }}
```

## Next Step

Continue with [Step 1.1: Installing and Configuring Sylius Bundles](step_1.1.md) to add Sylius functionality to your project.


