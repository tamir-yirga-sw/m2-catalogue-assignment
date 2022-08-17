<?php

namespace Scandiweb\Test\Setup\Patch\Data;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\CatalogInventory\Model\Stock;
use Magento\Framework\App\State;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

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
     * @var SourceItemInterfaceFactory
     */
    protected SourceItemInterfaceFactory $sourceItemFactory;

    /**
     * @var SourceItemsSaveInterface
     */
    protected SourceItemsSaveInterface $sourceItemsSaveInterface;

    /**
     * CreateSimpleProduct Constructor
     *
     * @param ProductInterfaceFactory         $productFactory
     * @param ProductRepositoryInterface      $productRepository
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param State                           $appState
     * @param SourceItemInterfaceFactory      $sourceItemFactory
     * @param SourceItemsSaveInterface        $sourceItemsSaveInterface
     */
    public function __construct(
        ProductInterfaceFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        CategoryLinkManagementInterface $categoryLinkManagement,
        State $appState,
        SourceItemInterfaceFactory $sourceItemFactory,
        SourceItemsSaveInterface $sourceItemsSaveInterface
    ) {
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->appState = $appState;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): CreateSimpleProduct
    {
        $this->appState->emulateAreaCode(
            'adminhtml',
            [$this, 'createSimpleProduct']
        );

        return $this;
    }

    public function createSimpleProduct(): void
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

        // Update the stock level
        $sourceItem = $this->sourceItemFactory->create();
        $sourceItem->setSourceCode('default');
        $sourceItem->setSku('SCANDIWEB-TEST-SKU');
        $sourceItem->setQuantity(16);
        $sourceItem->setStatus(Stock::STOCK_IN_STOCK);
        $this->sourceItemsSaveInterface->execute([$sourceItem]);

        $categoryIds = [2]; // Default category id in store
        $this->categoryLinkManagement->assignProductToCategories(
            $product->getSku(),
            $categoryIds
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }
}
