# 条件系统 Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-787CB5?style=flat-square)](https://packagist.org/packages/tourze/condition-system-bundle)
[![License](https://img.shields.io/badge/license-MIT-brightgreen?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen?style=flat-square)](#)
[![Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen?style=flat-square)](#)

一个灵活的 Symfony 条件评估系统，提供了定义、管理和评估各种类型条件的框架。

## 功能特性

- **灵活的条件系统**：通过自定义处理器创建和管理各种类型的条件
- **评估引擎**：针对特定上下文和参与者评估条件
- **可扩展架构**：通过实现处理器轻松添加新的条件类型
- **触发器支持**：内置对不同条件触发器的支持
- **验证功能**：条件配置的全面验证
- **批量评估**：一次评估多个条件

## 安装

您可以通过 Composer 安装此 bundle：

```bash
composer require tourze/condition-system-bundle
```

### 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM 3.0 或更高版本

## 快速开始

1. 在 `config/bundles.php` 中注册 bundle：

```php
return [
    // ...
    Tourze\ConditionSystemBundle\ConditionSystemBundle::class => ['all' => true],
];
```

2. 创建自定义条件处理器：

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
        // 你的评估逻辑
        $passed = true; // 根据你的逻辑判断
        
        return $passed 
            ? EvaluationResult::pass()
            : EvaluationResult::fail(['条件不满足']);
    }
}
```

3. 使用条件管理器：

```php
use Tourze\ConditionSystemBundle\Service\ConditionManagerService;

// 创建条件
$condition = $conditionManager->createCondition($subject, 'my_custom_condition', [
    'threshold' => 100,
    'operator' => '>='
]);

// 评估条件
$context = new EvaluationContext($actor, ['value' => 150]);
$result = $conditionManager->evaluateCondition($condition, $context);

if ($result->isPassed()) {
    // 条件通过
} else {
    // 条件失败
    $errors = $result->getMessages();
}
```

## 配置

### 依赖关系

此 bundle 依赖于：

- `tourze/doctrine-timestamp-bundle`：为实体提供时间戳功能
- `tourze/enum-extra`：增强的枚举支持

### 高级用法

#### 条件触发器

Bundle 通过 `ConditionTrigger` 枚举支持不同的条件触发器。
您可以按触发器过滤可用的条件类型：

```php
$availableTypes = $conditionManager->getAvailableConditionTypes(ConditionTrigger::BEFORE_ACTION);
```

#### 批量评估

一次评估多个条件：

```php
$results = $conditionManager->evaluateConditions($conditions, $context);
```

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/condition-system-bundle/tests
```

## 贡献

请查看 [CONTRIBUTING.md](CONTRIBUTING.md) 了解如何为此项目做出贡献的详细信息。

## 许可证

此 bundle 采用 MIT 许可证。查看完整的许可证：
[LICENSE](LICENSE)