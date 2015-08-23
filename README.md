# Extensions for RoboTask

## Installation

    composer require jasny\robo-extensions

## Usage

```php
class RoboFile extends Robo\Tasks
{
    use Jasny\Robo\loadTasks;
    
    ...
}
```

#### LessTask

A version of `Robo\Task\Assets\Less` that supports passing options and using `uri_root` for less.php.

```php
  $this->taskLess(['www/less/main.less' => 'www/css/style.css'])
    ->compiler('less', [
       'base' => 'www',
       'strictMath' => true
    ])
    ->run();
```

#### BumpVersionTask

Bump the version in a json file.

```php
  $this->taskBumpVersion('composer.json')
    ->inc('minor')
    ->run();

  $this->taskBumpVersion('composer.json')
    ->to('1.2.6')
    ->run();

  // `to` also works for 'major', 'minor' and 'patch'
  $this->taskBumpVersion('composer.json')
    ->to('minor')
    ->run();
```

