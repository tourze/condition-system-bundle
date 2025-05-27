<?php

namespace Tourze\ConditionSystemBundle\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\ValueObject\FormField;
use Tourze\ConditionSystemBundle\ValueObject\FormFieldFactory;

class FormFieldFactoryTest extends TestCase
{
    public function test_text_creates_text_field(): void
    {
        $field = FormFieldFactory::text('username', '用户名');
        
        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('username', $field->getName());
        $this->assertEquals('text', $field->getType());
        $this->assertEquals('用户名', $field->getLabel());
    }

    public function test_integer_creates_integer_field(): void
    {
        $field = FormFieldFactory::integer('age', '年龄');
        
        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('age', $field->getName());
        $this->assertEquals('integer', $field->getType());
        $this->assertEquals('年龄', $field->getLabel());
    }

    public function test_decimal_creates_decimal_field_with_default_scale(): void
    {
        $field = FormFieldFactory::decimal('price', '价格');
        
        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('price', $field->getName());
        $this->assertEquals('decimal', $field->getType());
        $this->assertEquals('价格', $field->getLabel());
        $this->assertEquals(['scale' => 2], $field->getOptions());
    }

    public function test_decimal_creates_decimal_field_with_custom_scale(): void
    {
        $field = FormFieldFactory::decimal('rate', '汇率', 4);
        
        $this->assertEquals(['scale' => 4], $field->getOptions());
    }

    public function test_boolean_creates_boolean_field(): void
    {
        $field = FormFieldFactory::boolean('active', '是否激活');
        
        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('active', $field->getName());
        $this->assertEquals('boolean', $field->getType());
        $this->assertEquals('是否激活', $field->getLabel());
    }

    public function test_choice_creates_choice_field_without_choices(): void
    {
        $field = FormFieldFactory::choice('status', '状态');
        
        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('status', $field->getName());
        $this->assertEquals('choice', $field->getType());
        $this->assertEquals('状态', $field->getLabel());
        $this->assertEquals(['choices' => []], $field->getOptions());
    }

    public function test_choice_creates_choice_field_with_choices(): void
    {
        $choices = ['active' => '激活', 'inactive' => '禁用'];
        $field = FormFieldFactory::choice('status', '状态', $choices);
        
        $this->assertEquals(['choices' => $choices], $field->getOptions());
    }

    public function test_array_creates_array_field(): void
    {
        $field = FormFieldFactory::array('tags', '标签');
        
        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('tags', $field->getName());
        $this->assertEquals('array', $field->getType());
        $this->assertEquals('标签', $field->getLabel());
    }

    public function test_textarea_creates_textarea_field(): void
    {
        $field = FormFieldFactory::textarea('description', '描述');
        
        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('description', $field->getName());
        $this->assertEquals('textarea', $field->getType());
        $this->assertEquals('描述', $field->getLabel());
    }

    public function test_date_creates_date_field(): void
    {
        $field = FormFieldFactory::date('birthday', '生日');
        
        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('birthday', $field->getName());
        $this->assertEquals('date', $field->getType());
        $this->assertEquals('生日', $field->getLabel());
    }

    public function test_datetime_creates_datetime_field(): void
    {
        $field = FormFieldFactory::datetime('created_at', '创建时间');
        
        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('created_at', $field->getName());
        $this->assertEquals('datetime', $field->getType());
        $this->assertEquals('创建时间', $field->getLabel());
    }

    public function test_email_creates_email_field(): void
    {
        $field = FormFieldFactory::email('email', '邮箱');
        
        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('email', $field->getName());
        $this->assertEquals('email', $field->getType());
        $this->assertEquals('邮箱', $field->getLabel());
    }

    public function test_url_creates_url_field(): void
    {
        $field = FormFieldFactory::url('website', '网站');
        
        $this->assertInstanceOf(FormField::class, $field);
        $this->assertEquals('website', $field->getName());
        $this->assertEquals('url', $field->getType());
        $this->assertEquals('网站', $field->getLabel());
    }

    public function test_factory_methods_return_configurable_fields(): void
    {
        $field = FormFieldFactory::text('username', '用户名')
            ->required()
            ->help('请输入用户名')
            ->min(3)
            ->max(20);
        
        $this->assertTrue($field->isRequired());
        $this->assertEquals('请输入用户名', $field->getHelp());
        $this->assertEquals(['min' => 3, 'max' => 20], $field->getConstraints());
    }

    public function test_all_factory_methods_exist(): void
    {
        $methods = [
            'text', 'integer', 'decimal', 'boolean', 'choice',
            'array', 'textarea', 'date', 'datetime', 'email', 'url'
        ];
        
        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists(FormFieldFactory::class, $method),
                "FormFieldFactory 应该有 {$method} 方法"
            );
        }
    }

    public function test_factory_methods_create_different_field_types(): void
    {
        $fields = [
            FormFieldFactory::text('text_field', 'Text'),
            FormFieldFactory::integer('int_field', 'Integer'),
            FormFieldFactory::decimal('decimal_field', 'Decimal'),
            FormFieldFactory::boolean('bool_field', 'Boolean'),
            FormFieldFactory::choice('choice_field', 'Choice'),
            FormFieldFactory::array('array_field', 'Array'),
            FormFieldFactory::textarea('textarea_field', 'Textarea'),
            FormFieldFactory::date('date_field', 'Date'),
            FormFieldFactory::datetime('datetime_field', 'Datetime'),
            FormFieldFactory::email('email_field', 'Email'),
            FormFieldFactory::url('url_field', 'URL'),
        ];
        
        $expectedTypes = [
            'text', 'integer', 'decimal', 'boolean', 'choice',
            'array', 'textarea', 'date', 'datetime', 'email', 'url'
        ];
        
        foreach ($fields as $index => $field) {
            $this->assertEquals($expectedTypes[$index], $field->getType());
        }
    }
} 