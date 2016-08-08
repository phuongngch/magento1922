<?php
/**
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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * New products block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_Special extends Mage_Catalog_Block_Product_Abstract
{
    protected $_productsCount = null;

    protected $_defaultToolbarBlock = "catalog/product_list_toolbar";
     
    const DEFAULT_PRODUCTS_COUNT = "";

    protected function _beforeToHtml()
    {    
        $toolbar = $this->getToolbarBlock();
         
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        
        $collection = Mage::getResourceModel("catalog/product_collection");
        Mage::getSingleton("catalog/product_status")->addVisibleFilterToCollection($collection);
        Mage::getSingleton("catalog/product_visibility")->addVisibleInCatalogFilterToCollection($collection);
        
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()

            ->setPageSize($this->getProductsCount())
            ->setCurPage(1)
        ;
        $this->setProductCollection($collection);
        
         if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to tollbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild("toolbar", $toolbar);
        Mage::dispatchEvent("catalog_block_product_list_collection", array(
            "collection"=>$collection,
        ));
        
        return parent::_beforeToHtml();
    }
    
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }
    
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }
    
    
     public function getMode()
    {
        return $this->getChild("toolbar")->getCurrentMode();
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml("toolbar");
    }
} 