<?php

namespace Scandiweb\Test\Setup\Patch\Data;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\App\State;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class CreateSimpleProduct implements DataPatchInterface
{
    /**
     * @var ProductInterfaceFactory
     */
    protected ProductInterfaceFactory $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * @var CategoryLinkManagementInterface
     */
    protected CategoryLinkManagementInterface $categoryLinkManagement;

    /**
     * @var State
     */
    protected State $appState;

    /**
     * CreateSimpleProduct Constructor
     *
     * @param ProductInterfaceFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param State $appState
     */
    public function __construct(
        ProductInterfaceFactory         $productFactory,
        ProductRepositoryInterface      $productRepository,
        CategoryLinkManagementInterface $categoryLinkManagement,
        State                           $appState
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->appState = $appState;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->appState->emulateAreaCode('adminhtml', [$this, 'createSimpleProduct']);
        return $this;
    }

    public function createSimpleProduct()
    {
        $product = $this->productFactory->create();
        $product->setSku('SCANDIWEB-TEST-SKU')
            ->setName('Scandiweb Test Item')
            ->setAttributeSetId(4) //Default attr. set for products
            ->setStatus(Status::STATUS_ENABLED)
            ->setWeight(2)
            ->setPrice(18)
            ->setVisibility(4)
            ->setTypeId(Type::TYPE_SIMPLE);
        $product = $this->productRepository->save($product);
        $categoryIds = [2]; // Default category id in store
        $this->categoryLinkManagement->assignProductToCategories($product->getSku(), $categoryIds);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
