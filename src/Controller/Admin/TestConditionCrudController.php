<?php

declare(strict_types=1);

namespace Tourze\ConditionSystemBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\ConditionSystemBundle\Entity\TestCondition;
use Tourze\ConditionSystemBundle\Enum\ConditionTrigger;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

/**
 * 测试条件CRUD控制器
 *
 * @extends AbstractCrudController<TestCondition>
 */
#[AdminCrud(routePath: '/condition-system/test-condition', routeName: 'condition_system_test_condition')]
final class TestConditionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TestCondition::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('测试条件')
            ->setEntityLabelInPlural('测试条件管理')
            ->setSearchFields(['type', 'label'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setPaginatorPageSize(20)
            ->setHelp('index', '管理测试条件，用于系统的条件判断逻辑')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield TextField::new('type', '条件类型')
            ->setRequired(true)
            ->setMaxLength(50)
            ->setHelp('条件的分类标识')
        ;

        yield TextField::new('label', '条件标签')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('条件的显示名称')
        ;

        $triggerTypeField = EnumField::new('triggerType', '触发器类型');
        $triggerTypeField->setEnumCases(ConditionTrigger::cases());
        $triggerTypeField->setRequired(true);
        $triggerTypeField->setHelp('条件触发的时机');
        yield $triggerTypeField;

        yield BooleanField::new('enabled', '是否启用')
            ->setHelp('控制条件是否生效')
        ;

        yield TextField::new('remark', '备注')
            ->setMaxLength(65535)
            ->hideOnIndex()
            ->setHelp('条件的详细说明（可选）')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('type', '条件类型'))
            ->add(TextFilter::new('label', '条件标签'))
            ->add(BooleanFilter::new('enabled', '是否启用'))
        ;
    }
}
