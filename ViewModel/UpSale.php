<?php

namespace Pointeger\UpSale\ViewModel;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Session;
use Magento\Framework\Pricing\Helper\Data as PriceFormatter;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Pointeger\UpSale\Model\Configuration;

/**
 * Class UpSale
 * @package Pointeger\UpSale\ViewModel
 */
class UpSale implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var PriceFormatter
     */
    protected $priceFormatter;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var CartHelper
     */
    protected $cartHelper;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @param Configuration $configuration
     * @param ProductRepositoryInterface $productRepository
     * @param PriceFormatter $priceFormatter
     * @param Image $imageHelper
     * @param EncoderInterface $encoder
     * @param UrlInterface $urlBuilder
     * @param CartHelper $cartHelper
     * @param Session $checkoutSession
     */
    public function __construct(
        Configuration              $configuration,
        ProductRepositoryInterface $productRepository,
        PriceFormatter             $priceFormatter,
        Image                      $imageHelper,
        EncoderInterface           $encoder,
        UrlInterface               $urlBuilder,
        CartHelper                 $cartHelper,
        Session                    $checkoutSession
    ) {
        $this->productRepository = $productRepository;
        $this->configuration = $configuration;
        $this->priceFormatter = $priceFormatter;
        $this->imageHelper = $imageHelper;
        $this->urlEncoder = $encoder;
        $this->urlBuilder = $urlBuilder;
        $this->cartHelper = $cartHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return array|false
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductData()
    {
        $product = $this->getProduct();
        if ($product) {
            return [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'image' => $this->getProductImage($product),
                'price' => $this->getPrice($product),
                'submitUrl' => $this->getAddToCartUrl($product),
                'sku' => '',
                'upSaleSku' => $this->configuration->getUpSaleProductSku()
            ];
        }
        return false;
    }

    /**
     * @return false|\Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getProduct()
    {
        if ($this->configuration->isEnabled()) {
            $sku = $this->configuration->getUpSaleProductSku();
            $product = $this->productRepository->get($sku);
            if ($product) {
                $quoteItem = $this->checkoutSession->getQuote()->getItemsCollection()->addFieldToFilter(
                    'sku',
                    ['eq' => $product->getSku()]
                );
                if (!$quoteItem->getSize()) {
                    return $product;
                }
            }
        }
        return false;
    }

    /**
     * @param $product
     * @return string
     */
    protected function getProductImage($product)
    {
        $url = $this->imageHelper->init(
            $product,
            'product_small_image'
        )->setImageFile($product->getSmallImage())->getUrl();
        return $url;
    }

    /**
     * @param $product
     * @return float|string
     */
    protected function getPrice($product)
    {
        $price = $this->priceFormatter->currency($product->getFinalPrice(), true, false);
        return $price;
    }

    /**
     * @param $product
     * @return string
     */
    protected function getAddToCartUrl($product)
    {
        $addUrlKey = \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED;
        $addUrlValue = $this->urlBuilder->getUrl('checkout/cart/index', ['_use_rewrite' => true, '_current' => true]);
        $additional[$addUrlKey] = $this->urlEncoder->encode($addUrlValue);

        return $this->cartHelper->getAddUrl($product, $additional);
    }
}
