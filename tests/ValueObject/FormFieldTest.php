<?php

namespace Tourze\ConditionSystemBundle\Tests\ValueObject;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\ConditionSystemBundle\ValueObject\FormField;

/**
 * @internal
 */
#[CoversClass(FormField::class)]
final class FormFieldTest extends TestCase
{
    public function testCreateBasicField(): void
    {
        $field = FormField::create('username', 'text', '用户名');

        $this->assertEquals('username', $field->getName());
        $this->assertEquals('text', $field->getType());
        $this->assertEquals('用户名', $field->getLabel());
        $this->assertFalse($field->isRequired());
        $this->assertNull($field->getHelp());
        $this->assertEmpty($field->getOptions());
        $this->assertEmpty($field->getConstraints());
    }

    public function testRequiredMethodReturnsNewInstance(): void
    {
        $originalField = FormField::create('email', 'email', '邮箱');
        $requiredField = $originalField->required();

        // 验证不可变性
        $this->assertNotSame($originalField, $requiredField);
        $this->assertFalse($originalField->isRequired());
        $this->assertTrue($requiredField->isRequired());

        // 其他属性保持不变
        $this->assertEquals('email', $requiredField->getName());
        $this->assertEquals('email', $requiredField->getType());
        $this->assertEquals('邮箱', $requiredField->getLabel());
    }

    public function testRequiredWithFalseParameter(): void
    {
        $field = FormField::create('password', 'password', '密码')
            ->required(false)
        ;

        $this->assertFalse($field->isRequired());
    }

    public function testHelpMethodSetsHelpText(): void
    {
        $helpText = '请输入有效的邮箱地址';
        $field = FormField::create('email', 'email', '邮箱')
            ->help($helpText)
        ;

        $this->assertEquals($helpText, $field->getHelp());
    }

    public function testOptionsMethodMergesOptions(): void
    {
        $field = FormField::create('country', 'choice', '国家')
            ->options(['placeholder' => '请选择国家'])
            ->options(['multiple' => true])
        ;

        $expectedOptions = ['placeholder' => '请选择国家', 'multiple' => true];
        $this->assertEquals($expectedOptions, $field->getOptions());
    }

    public function testConstraintsMethodMergesConstraints(): void
    {
        $field = FormField::create('age', 'integer', '年龄')
            ->constraints(['min' => 18])
            ->constraints(['max' => 100])
        ;

        $expectedConstraints = ['min' => 18, 'max' => 100];
        $this->assertEquals($expectedConstraints, $field->getConstraints());
    }

    public function testMinConstraintShortcut(): void
    {
        $field = FormField::create('price', 'decimal', '价格')
            ->min(0.01)
        ;

        $this->assertEquals(['min' => 0.01], $field->getConstraints());
    }

    public function testMaxConstraintShortcut(): void
    {
        $field = FormField::create('quantity', 'integer', '数量')
            ->max(999)
        ;

        $this->assertEquals(['max' => 999], $field->getConstraints());
    }

    public function testChoicesOptionShortcut(): void
    {
        $choices = ['male' => '男', 'female' => '女'];
        $field = FormField::create('gender', 'choice', '性别')
            ->choices($choices)
        ;

        $this->assertEquals(['choices' => $choices], $field->getOptions());
    }

    public function testMethodChaining(): void
    {
        $choices = ['active' => '激活', 'inactive' => '禁用'];
        $field = FormField::create('status', 'choice', '状态')
            ->required()
            ->help('选择用户状态')
            ->choices($choices)
            ->options(['expanded' => true])
            ->min(1)
            ->max(10)
        ;

        $this->assertEquals('status', $field->getName());
        $this->assertEquals('choice', $field->getType());
        $this->assertEquals('状态', $field->getLabel());
        $this->assertTrue($field->isRequired());
        $this->assertEquals('选择用户状态', $field->getHelp());
        $this->assertEquals(['choices' => $choices, 'expanded' => true], $field->getOptions());
        $this->assertEquals(['min' => 1, 'max' => 10], $field->getConstraints());
    }

    public function testToArrayReturnsCompleteStructure(): void
    {
        $field = FormField::create('description', 'textarea', '描述')
            ->required()
            ->help('请输入详细描述')
            ->options(['rows' => 5])
            ->constraints(['maxLength' => 500])
        ;

        $expected = [
            'name' => 'description',
            'type' => 'textarea',
            'label' => '描述',
            'required' => true,
            'help' => '请输入详细描述',
            'options' => ['rows' => 5],
            'constraints' => ['maxLength' => 500],
        ];

        $this->assertEquals($expected, $field->toArray());
    }

    public function testToArrayWithMinimalField(): void
    {
        $field = FormField::create('name', 'text', '姓名');

        $expected = [
            'name' => 'name',
            'type' => 'text',
            'label' => '姓名',
            'required' => false,
            'help' => null,
            'options' => [],
            'constraints' => [],
        ];

        $this->assertEquals($expected, $field->toArray());
    }

    public function testImmutabilityWithMultipleOperations(): void
    {
        $originalField = FormField::create('test', 'text', 'Test');

        $field1 = $originalField->required();
        $field2 = $field1->help('Help text');
        $field3 = $field2->min(5);

        // 验证每个字段都是独立的
        $this->assertFalse($originalField->isRequired());
        $this->assertNull($originalField->getHelp());
        $this->assertEmpty($originalField->getConstraints());

        $this->assertTrue($field1->isRequired());
        $this->assertNull($field1->getHelp());
        $this->assertEmpty($field1->getConstraints());

        $this->assertTrue($field2->isRequired());
        $this->assertEquals('Help text', $field2->getHelp());
        $this->assertEmpty($field2->getConstraints());

        $this->assertTrue($field3->isRequired());
        $this->assertEquals('Help text', $field3->getHelp());
        $this->assertEquals(['min' => 5], $field3->getConstraints());
    }
}
