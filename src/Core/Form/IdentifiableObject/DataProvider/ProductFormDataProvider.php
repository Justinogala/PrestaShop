<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\LocalizedTags;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;

/**
 * Provides the data that is used to prefill the Product form
 */
final class ProductFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @param CommandBusInterface $queryBus
     * @param int $defaultCurrencyId
     */
    public function __construct(
        CommandBusInterface $queryBus,
        int $defaultCurrencyId
    ) {
        $this->queryBus = $queryBus;
        $this->defaultCurrencyId = $defaultCurrencyId;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        /** @var ProductForEditing $productForEditing */
        $productForEditing = $this->queryBus->handle(new GetProductForEditing((int) $id));

        return [
            'id' => $id,
            'basic' => $this->extractBasicData($productForEditing),
            'stock' => $this->extractStockData($productForEditing),
            'price' => $this->extractPriceData($productForEditing),
            'seo' => $this->extractSEOData($productForEditing),
            'redirect_option' => $this->extractRedirectOptionData($productForEditing),
            'shipping' => $this->extractShippingData($productForEditing),
            'options' => $this->extractOptionsData($productForEditing),
            'suppliers' => $this->extractSuppliersData($productForEditing),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'basic' => [
                'type' => ProductType::TYPE_STANDARD,
            ],
            'price' => [
                'price_tax_excluded' => 0,
                'price_tax_included' => 0,
                'wholesale_price' => 0,
                'unit_price' => 0,
            ],
            'shipping' => [
                'width' => 0,
                'height' => 0,
                'depth' => 0,
                'weight' => 0,
            ],
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractBasicData(ProductForEditing $productForEditing): array
    {
        return [
            'name' => $productForEditing->getBasicInformation()->getLocalizedNames(),
            'type' => $productForEditing->getBasicInformation()->getType()->getValue(),
            'description' => $productForEditing->getBasicInformation()->getLocalizedDescriptions(),
            'description_short' => $productForEditing->getBasicInformation()->getLocalizedShortDescriptions(),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractStockData(ProductForEditing $productForEditing): array
    {
        $stockInformation = $productForEditing->getStockInformation();
        $availableDate = $stockInformation->getAvailableDate();

        return [
            'quantity' => $stockInformation->getQuantity(),
            'minimal_quantity' => $stockInformation->getMinimalQuantity(),
            'stock_location' => $stockInformation->getLocation(),
            'low_stock_threshold' => $stockInformation->getLowStockThreshold(),
            'low_stock_alert' => $stockInformation->isLowStockAlertEnabled(),
            'pack_stock_type' => $stockInformation->getPackStockType(),
            'out_of_stock_type' => $stockInformation->getOutOfStockType(),
            'available_now_label' => $stockInformation->getLocalizedAvailableNowLabels(),
            'available_later_label' => $stockInformation->getLocalizedAvailableLaterLabels(),
            'available_date' => $availableDate ? $availableDate->format(DateTime::DEFAULT_DATE_FORMAT) : '',
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractPriceData(ProductForEditing $productForEditing): array
    {
        return [
            'price_tax_excluded' => (float) (string) $productForEditing->getPricesInformation()->getPrice(),
            // @todo: we don't have the price tax included for now This should be computed by GetProductForEditing
            'price_tax_included' => (float) (string) $productForEditing->getPricesInformation()->getPrice(),
            'ecotax' => (float) (string) $productForEditing->getPricesInformation()->getEcotax(),
            'tax_rules_group_id' => $productForEditing->getPricesInformation()->getTaxRulesGroupId(),
            'on_sale' => $productForEditing->getPricesInformation()->isOnSale(),
            'wholesale_price' => (float) (string) $productForEditing->getPricesInformation()->getWholesalePrice(),
            'unit_price' => (float) (string) $productForEditing->getPricesInformation()->getUnitPrice(),
            'unity' => $productForEditing->getPricesInformation()->getUnity(),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array
     */
    private function extractSEOData(ProductForEditing $productForEditing): array
    {
        $seoOptions = $productForEditing->getProductSeoOptions();

        return [
            'meta_title' => $seoOptions->getLocalizedMetaTitles(),
            'meta_description' => $seoOptions->getLocalizedMetaDescriptions(),
            'link_rewrite' => $seoOptions->getLocalizedLinkRewrites(),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array
     */
    private function extractRedirectOptionData(ProductForEditing $productForEditing): array
    {
        $seoOptions = $productForEditing->getProductSeoOptions();

        return [
            'type' => $seoOptions->getRedirectType(),
            'target' => $seoOptions->getRedirectTargetId(),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractShippingData(ProductForEditing $productForEditing): array
    {
        $shipping = $productForEditing->getShippingInformation();

        return [
            'width' => (string) $shipping->getWidth(),
            'height' => (string) $shipping->getHeight(),
            'depth' => (string) $shipping->getDepth(),
            'weight' => (string) $shipping->getWeight(),
            'additional_shipping_cost' => (string) $shipping->getAdditionalShippingCost(),
            'delivery_time_note_type' => $shipping->getDeliveryTimeNoteType(),
            'delivery_time_in_stock_note' => $shipping->getLocalizedDeliveryTimeInStockNotes(),
            'delivery_time_out_stock_note' => $shipping->getLocalizedDeliveryTimeOutOfStockNotes(),
            'carriers' => $shipping->getCarrierReferences(),
        ];
    }

    private function extractOptionsData(ProductForEditing $productForEditing): array
    {
        $options = $productForEditing->getOptions();
        $details = $productForEditing->getDetails();

        return [
            'active' => $options->isActive(),
            'visibility' => $options->getVisibility(),
            'available_for_order' => $options->isAvailableForOrder(),
            'show_price' => $options->showPrice(),
            'online_only' => $options->isOnlineOnly(),
            'show_condition' => $options->showCondition(),
            'condition' => $options->getCondition(),
            'tags' => $this->presentTags($productForEditing->getBasicInformation()->getLocalizedTags()),
            'mpn' => $details->getMpn(),
            'upc' => $details->getUpc(),
            'ean_13' => $details->getEan13(),
            'isbn' => $details->getIsbn(),
            'reference' => $details->getReference(),
        ];
    }

    /**
     * @param LocalizedTags[] $localizedTagsList
     *
     * @return array<int, string>
     */
    private function presentTags(array $localizedTagsList): array
    {
        $tags = [];
        foreach ($localizedTagsList as $localizedTags) {
            $tags[$localizedTags->getLanguageId()] = implode(',', $localizedTags->getTags());
        }

        return $tags;
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array
     */
    private function extractSuppliersData(ProductForEditing $productForEditing): array
    {
        /** @var ProductSupplierOptions $productSupplierOptions */
        $productSupplierOptions = $this->queryBus->handle(new GetProductSupplierOptions($productForEditing->getProductId()));
        $defaultSupplierId = $productSupplierOptions->getDefaultSupplierId();

        $suppliersData = [
            'default_supplier_id' => $defaultSupplierId,
        ];

        foreach ($productSupplierOptions->getSuppliersInfo() as $supplierOption) {
            $supplierForEditing = $supplierOption->getProductSupplierForEditing();
            $supplierId = $supplierOption->getSupplierId();

            $suppliersData['supplier_ids'][$supplierId] = $supplierId;
            $suppliersData['supplier_references'][$supplierId]['supplier_id'] = $supplierId;
            $suppliersData['supplier_references'][$supplierId]['supplier_name'] = $supplierOption->getSupplierName();

            $suppliersData['supplier_references'][$supplierId]['product_supplier'] = [
                'product_supplier_id' => $supplierForEditing->getProductSupplierId(),
                'supplier_price_tax_excluded' => $supplierForEditing->getPriceTaxExcluded(),
                'supplier_reference' => $supplierForEditing->getReference(),
                'currency_id' => $supplierForEditing->getCurrencyId(),
                'combination_id' => $supplierForEditing->getCombinationId(),
            ];
        }

        return $suppliersData;
    }
}
