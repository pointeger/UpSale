<?php

namespace Pointeger\UpSale\Model;

/**
 * Class Configuration
 * @package Pointeger\UpSale\Model
 */
class Configuration
{
    const ENABLED = 'pointeger_upsale/upsale/enabled';
    const PRODUCT_SKU = 'pointeger_upsale/upsale/product_sku';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Configuration constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getUpSaleProductSku()
    {
        return $this->scopeConfig->getValue($this::PRODUCT_SKU, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue($this::ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
