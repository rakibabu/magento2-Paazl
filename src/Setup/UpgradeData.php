<?php
/**
 * Copyright (c) 2017 H&O E-commerce specialisten B.V. (http://www.h-o.nl/)
 * See LICENSE.txt for license details.
 */

namespace Paazl\Shipping\Setup;

use Magento\Catalog\Model\Product\Exception;
use Paazl\Shipping\Setup\PaazlSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Serialize\Serializer\Json;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $eavAttribute;
    /**
     * EAV setup factory
     *
     * @var PaazlSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Customer setup factory
     *
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $attributeSetFactory;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * UpgradeData constructor.
     *
     * @param \Paazl\Shipping\Setup\PaazlSetupFactory            $eavSetupFactory
     * @param \Magento\Customer\Setup\CustomerSetupFactory       $customerSetupFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory     $attributeSetFactory
     * @param \Magento\Eav\Api\AttributeRepositoryInterface      $attributeRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Json                                               $serializer
     */
    public function __construct(
        PaazlSetupFactory $eavSetupFactory,
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Json $serializer,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeRepository = $attributeRepository;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->eavAttribute = $eavAttribute;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.1') < 0) {
            /** @var PaazlSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'matrix', 'backend_type', 'int');
            $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'matrix', 'frontend_input', 'select');
            $eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'matrix', 'source_model', 'Paazl\Shipping\Model\Attribute\Source\Matrix');
        }
        if (version_compare($context->getVersion(), '1.3.0') < 0) {
            // create Customer attributes
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerEntity = $customerSetup->getEavConfig()->getEntityType(
                'customer_address'
            );
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);


            if ($this->isAttributeAllowedForImport($customerEntity, 'street_name')) {
                $attribute = $customerSetup->getEavConfig()->getAttribute(
                    $customerEntity,
                    'street_name'
                )
                    ->addData(
                        [
                            'attribute_set_id'   => $attributeSetId,
                            'attribute_group_id' => $attributeGroupId,
                            'used_in_forms'      => [
                                'adminhtml_customer_address',
                                'customer_address_edit',
                                'customer_register_address'
                            ],
                        ]
                    );
                $attribute->save();
                $customerSetup->addAttribute(
                    'customer_address',
                    'street_name',
                    [
                        'type'             => 'varchar',
                        'label'            => 'Street Name',
                        'input'            => 'text',
                        'required'         => true,
                        'visible'          => true,
                        'visible_on_front' => true,
                        'user_defined'     => true,
                        'position'       => 76,
                        'system'           => 0,
                    ]
                );
            }


            if ($this->isAttributeAllowedForImport($customerEntity, 'house_number')) {
                $attribute = $customerSetup->getEavConfig()
                    ->getAttribute(
                        'customer_address',
                        'house_number'
                    )
                    ->addData(
                        [
                            'attribute_set_id'   => $attributeSetId,
                            'attribute_group_id' => $attributeGroupId,
                            'used_in_forms'      => [
                                'adminhtml_customer_address',
                                'customer_address_edit',
                                'customer_register_address'
                            ],
                        ]
                    );
                $attribute->save();
                $customerSetup->addAttribute(
                    'customer_address',
                    'house_number',
                    [
                        'type'             => 'varchar',
                        'label'            => 'House Number',
                        'input'            => 'text',
                        'required'         => true,
                        'visible'          => true,
                        'visible_on_front' => true,
                        'user_defined'     => true,
                        'position'       => 74,
                        'system'           => 0,
                    ]
                );
            }


            if ($this->isAttributeAllowedForImport($customerEntity, 'house_number_addition')) {
                $attribute = $customerSetup->getEavConfig()->getAttribute(
                    'customer_address',
                    'house_number_addition'
                )
                    ->addData(
                        [
                            'attribute_set_id'   => $attributeSetId,
                            'attribute_group_id' => $attributeGroupId,
                            'used_in_forms'      => [
                                'adminhtml_customer_address',
                                'customer_address_edit',
                                'customer_register_address'
                            ],
                        ]
                    );
                $attribute->save();
                $customerSetup->addAttribute(
                    'customer_address',
                    'house_number_addition',
                    [
                        'type'             => 'varchar',
                        'label'            => 'House Number Addition',
                        'input'            => 'text',
                        'required'         => false,
                        'visible'          => true,
                        'visible_on_front' => true,
                        'user_defined'     => true,
                        'position'       => 75,
                        'system'           => 0,
                    ]
                );
            }

            if ($this->isAttributeAllowedForImport($customerEntity, 'house_number', true)) {
                $attribute = $customerSetup->getEavConfig()
                    ->getAttribute(
                        'customer_address',
                        'house_number'
                    )
                    ->addData(
                        [
                            'validate_rules'   => serialize([
                                'input_validation' => 'numeric',
                            ]),
                        ]
                    );
                $attribute->save();
            }
        }

        if (version_compare($context->getVersion(), '1.3.4') < 0) {
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerEntity = $customerSetup->getEavConfig()->getEntityType(
                'customer_address'
            );

            foreach (['street_name', 'house_number', 'house_number_addition'] as $field) {
                if ($this->isAttributeAllowedForImport($customerEntity, $field, true)) {
                    $attribute = $customerSetup->getEavConfig()
                        ->getAttribute(
                            'customer_address',
                            $field
                        )
                        ->addData(
                            [
                                'is_user_defined'   => true,
                            ]
                        );
                    $attribute->save();
                }
            }
        }

        if (version_compare($context->getVersion(), '1.3.8', '<')) {
            // create Customer attributes
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerSetup->updateAttribute(
                'customer_address',
                $this->eavAttribute->getIdByCode('customer_address', 'house_number'),
                'validate_rules',
                $this->serializer->serialize([
                    'input_validation' => 'numeric',
                ])
            );
        }

        $setup->endSetup();
    }


    /**
     * @param $customerEntity
     * @param $attributeCode
     * @param $existingAllowed
     *
     * @return bool
     */
    protected function isAttributeAllowedForImport($customerEntity, $attributeCode, $existingAllowed = false)
    {
        try {
            $this->attributeRepository->get($customerEntity, $attributeCode);
            if ($existingAllowed) {
                return true;
            }
            return false;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $allowed = true;
        }
        foreach (explode(',', $this->scopeConfig->getValue('exclude/' . $attributeCode)) as $v) {
            try {
                $this->attributeRepository->get($customerEntity, trim($v));
                $allowed = false;
                break;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $allowed = true;
            }
        }
        return $allowed;
    }
}
