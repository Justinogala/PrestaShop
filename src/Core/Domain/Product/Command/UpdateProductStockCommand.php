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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Class UpdateProductStatusCommand update a given product stock
 */
class UpdateProductStockCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var bool|null
     */
    private $useAdvancedStockManagement;

    /**
     * @var bool|null
     */
    private $dependsOnStock;

    /**
     * @var PackStockType|null
     */
    private $packStockType;

    /**
     * @var int|null
     */
    private $quantity;

    /**
     * @var OutOfStockType|null
     */
    private $outOfStockType;

    /**
     * @var int|null
     */
    private $minimalQuantity;

    /**
     * @var string|null
     */
    private $location;

    /**
     * @var int|null
     */
    private $lowStockThreshold;

    /**
     * @var bool|null
     */
    private $lowStockAlert;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedAvailableNowLabels;

    /**
     * @var string[]|null key value pairs where key is the id of language
     */
    private $localizedAvailableLaterLabels;

    /**
     * @var DateTime|null
     */
    private $availableDate;

    /**
     * @var bool
     */
    private $addMovement = true;

    /**
     * @param int $productId
     */
    public function __construct(int $productId)
    {
        $this->productId = new ProductId($productId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return bool|null
     */
    public function useAdvancedStockManagement(): ?bool
    {
        return $this->useAdvancedStockManagement;
    }

    /**
     * @param bool $useAdvancedStockManagement
     *
     * @return UpdateProductStockCommand
     */
    public function setUseAdvancedStockManagement(bool $useAdvancedStockManagement): UpdateProductStockCommand
    {
        $this->useAdvancedStockManagement = $useAdvancedStockManagement;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function dependsOnStock(): ?bool
    {
        return $this->dependsOnStock;
    }

    /**
     * @param bool $dependsOnStock
     *
     * @return UpdateProductStockCommand
     */
    public function setDependsOnStock(bool $dependsOnStock): UpdateProductStockCommand
    {
        $this->dependsOnStock = $dependsOnStock;

        return $this;
    }

    /**
     * @return PackStockType|null
     */
    public function getPackStockType(): ?PackStockType
    {
        return $this->packStockType;
    }

    /**
     * @param int $packStockType
     *
     * @return UpdateProductStockCommand
     *
     * @throws ProductPackConstraintException
     */
    public function setPackStockType(int $packStockType): UpdateProductStockCommand
    {
        $this->packStockType = new PackStockType($packStockType);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     *
     * @return UpdateProductStockCommand
     */
    public function setQuantity(int $quantity): UpdateProductStockCommand
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return OutOfStockType|null
     */
    public function getOutOfStockType(): ?OutOfStockType
    {
        return $this->outOfStockType;
    }

    /**
     * @param int $outOfStockType
     *
     * @return $this
     *
     * @throws ProductStockConstraintException
     */
    public function setOutOfStockType(int $outOfStockType): UpdateProductStockCommand
    {
        $this->outOfStockType = new OutOfStockType($outOfStockType);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinimalQuantity(): ?int
    {
        return $this->minimalQuantity;
    }

    /**
     * @param int $minimalQuantity
     *
     * @return UpdateProductStockCommand
     */
    public function setMinimalQuantity(int $minimalQuantity): UpdateProductStockCommand
    {
        $this->minimalQuantity = $minimalQuantity;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string $location
     *
     * @return UpdateProductStockCommand
     */
    public function setLocation(string $location): UpdateProductStockCommand
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLowStockThreshold(): ?int
    {
        return $this->lowStockThreshold;
    }

    /**
     * @param int $lowStockThreshold
     *
     * @return UpdateProductStockCommand
     */
    public function setLowStockThreshold(int $lowStockThreshold): UpdateProductStockCommand
    {
        $this->lowStockThreshold = $lowStockThreshold;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getLowStockAlert(): ?bool
    {
        return $this->lowStockAlert;
    }

    /**
     * @param bool $lowStockAlert
     *
     * @return UpdateProductStockCommand
     */
    public function setLowStockAlert(bool $lowStockAlert): UpdateProductStockCommand
    {
        $this->lowStockAlert = $lowStockAlert;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedAvailableNowLabels(): ?array
    {
        return $this->localizedAvailableNowLabels;
    }

    /**
     * @param string[] $localizedAvailableNowLabels
     *
     * @return UpdateProductStockCommand
     */
    public function setLocalizedAvailableNowLabels(array $localizedAvailableNowLabels): UpdateProductStockCommand
    {
        $this->localizedAvailableNowLabels = $localizedAvailableNowLabels;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedAvailableLaterLabels(): ?array
    {
        return $this->localizedAvailableLaterLabels;
    }

    /**
     * @param string[] $localizedAvailableLaterLabels
     *
     * @return UpdateProductStockCommand
     */
    public function setLocalizedAvailableLaterLabels(array $localizedAvailableLaterLabels): UpdateProductStockCommand
    {
        $this->localizedAvailableLaterLabels = $localizedAvailableLaterLabels;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getAvailableDate(): ?DateTime
    {
        return $this->availableDate;
    }

    /**
     * @param DateTime $availableDate
     *
     * @return UpdateProductStockCommand
     */
    public function setAvailableDate(DateTime $availableDate): UpdateProductStockCommand
    {
        $this->availableDate = $availableDate;

        return $this;
    }

    /**
     * @return bool
     */
    public function addMovement(): bool
    {
        return $this->addMovement;
    }

    /**
     * @param bool $addMovement
     *
     * @return UpdateProductStockCommand
     */
    public function setAddMovement(bool $addMovement): UpdateProductStockCommand
    {
        $this->addMovement = $addMovement;

        return $this;
    }
}
