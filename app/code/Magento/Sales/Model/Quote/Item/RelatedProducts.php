<?php
/**
 *
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Magento\Sales\Model\Quote\Item;

class RelatedProducts
{
    /**
     * List of related product types
     *
     * @var array
     */
    protected $_relatedProductTypes;

    /**
     * @param array $relatedProductTypes
     */
    public function __construct($relatedProductTypes = array())
    {
        $this->_relatedProductTypes = $relatedProductTypes;
    }

    /**
     * Retrieve Array of product ids which have special relation with products in Cart
     *
     * @param \Magento\Sales\Model\Quote\Item[] $quoteItems
     * @return int[]
     */
    public function getRelatedProductIds(array $quoteItems)
    {
        $productIds = array();
        /** @var $quoteItems \Magento\Sales\Model\Quote\Item[] */
        foreach ($quoteItems as $quoteItem) {
            $productTypeOpt = $quoteItem->getOptionByCode('product_type');
            if ($productTypeOpt instanceof \Magento\Sales\Model\Quote\Item\Option) {
                if (in_array($productTypeOpt->getValue(), $this->_relatedProductTypes)
                    && $productTypeOpt->getProductId()
                ) {
                    $productIds[] = $productTypeOpt->getProductId();
                }
            }
        }
        return $productIds;
    }
} 
