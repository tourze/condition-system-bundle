# Condition System Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-787CB5?style=flat-square)](https://packagist.org/packages/tourze/condition-system-bundle)
[![License](https://img.shields.io/badge/license-MIT-brightgreen?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen?style=flat-square)](#)
[![Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen?style=flat-square)](#)

A flexible condition evaluation system for Symfony applications, providing a framework for
defining, managing, and evaluating various types of conditions.

## Features

- **Flexible Condition System**: Create and manage various types of conditions with custom handlers
- **Evaluation Engine**: Evaluate conditions against specific contexts and actors
- **Extensible Architecture**: Easy to add new condition types by implementing handlers
- **Trigger Support**: Built-in support for different condition triggers
- **Validation**: Comprehensive configuration validation for conditions
- **Batch Evaluation**: Evaluate multiple conditions at once

## Installation

You can install this bundle via Composer:

```bash
composer require tourze/condition-system-bundle
```

### Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM 3.0 or higher

## Quick Start

1. Register the bundle in your `config/bundles.php`:

```php
return [
    // ...
    Tourze\ConditionSystemBundle\ConditionSystemBundle::class => ['all' => true],
];
```

2. Create a custom condition handler:

```php
use Tourze\ConditionSystemBundle\Handler\AbstractConditionHandler;
use Tourze\ConditionSystemBundle\Interface\ConditionInterface;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationContext;
use Tourze\ConditionSystemBundle\ValueObject\EvaluationResult;

class MyCustomConditionHandler extends AbstractConditionHandler
{
    public function getType(): string
    {
        return 'my_custom_condition';
    }

    public function evaluate(ConditionInterface $condition, EvaluationContext $context): EvaluationResult
    {
        // Your evaluation logic here
        $passed = true; // Determine based on your logic
        
        return $passed 
            ? EvaluationResult::pass()
            : EvaluationResult::fail(['Condition not met']);
    }
}
```

3. Use the condition manager:

```php
use Tourze\ConditionSystemBundle\Service\ConditionManagerService;

// Create a condition
$condition = $conditionManager->createCondition($subject, 'my_custom_condition', [
    'threshold' => 100,
    'operator' => '>='
]);

// Evaluate a condition
$context = new EvaluationContext($actor, ['value' => 150]);
$result = $conditionManager->evaluateCondition($condition, $context);

if ($result->isPassed()) {
    // Condition passed
} else {
    // Condition failed
    $errors = $result->getMessages();
}
```

## Configuration

### Dependencies

This bundle depends on:

- `tourze/doctrine-timestamp-bundle`: Provides timestamp functionality for entities
- `tourze/enum-extra`: Enhanced enum support

### Advanced Usage

#### Condition Triggers

The bundle supports different condition triggers through the `ConditionTrigger` enum.
You can filter available condition types by trigger:

```php
$availableTypes = $conditionManager->getAvailableConditionTypes(ConditionTrigger::BEFORE_ACTION);
```

#### Batch Evaluation

Evaluate multiple conditions at once:

```php
$results = $conditionManager->evaluateConditions($conditions, $context);
```

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/condition-system-bundle/tests
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute to this project.

## License

This bundle is under the MIT license. See the complete license in the bundle:
[LICENSE](LICENSE)