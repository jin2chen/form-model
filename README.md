# Form Model

The package helps with implementing data entry forms.

[![Build status](https://github.com/jin2chen/form-model/workflows/build/badge.svg)](https://github.com/jin2chen/form-model/actions?query=workflow%3Abuild)
[![static analysis](https://github.com/jin2chen/form-model/workflows/static%20analysis/badge.svg)](https://github.com/jin2chen/form-model/actions?query=workflow%3A%22static+analysis%22)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jin2chen/form-model/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jin2chen/form-model/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jin2chen/form-model/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jin2chen/form-model/?branch=master)

### Installation

The package could be installed via composer:

```shell
composer require jin2chen/form-model --prefer-dist
```

### Usage

You must create your form model by extending the abstract form class, defining all the private properties with their
respective typehint.

Example: LoginForm.php

```php
<?php

declare(strict_types=1);

namespace App\Form;

use jin2chen\FormModel\FormModel;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Validator;

class LoginForm extends FormModel
{
    public string $username = '';
    public string $password = '';
    public bool $rememberMe = false;

    /** Add rules */
    public function getRules(): array
    {
        return [
            'login' => $this->loginRules()
        ];
    }

    private function loginRules(): array
    {
        return [
            Required::rule(),
            HasLength::rule()
                ->min(4)
                ->max(40)
                ->tooShortMessage('Is too short.')
                ->tooLongMessage('Is too long.'),
            Email::rule()
        ];
    }
}

$form = new LoginForm();
$validator = new Validator();
$results = $validator->validate($form);
$results->isValid();
```

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```
