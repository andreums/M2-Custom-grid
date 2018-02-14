<?php

namespace BroSolutions\OrdersExport\Ui\Component\Orderslist\Column;

class Options extends \Magento\Ui\Component\Listing\Columns\Column {

    protected $_customerInterface;
    protected $_productModel;
    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = [],
        \Magento\Customer\Api\CustomerRepositoryInterface $customerInterface,
        \Magento\Catalog\Model\Product $productModel
    ){
        $this->_customerInterface = $customerInterface;
        $this->_productModel = $productModel;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['custom_option_value'] = '';
                if(isset($item['product_options']) && isset($item['product_options']['info_buyRequest']) && isset($item['product_options']['info_buyRequest']['product'])  && isset($item['product_options']['info_buyRequest']['options'])){
                    $product = $this->_productModel->load($item['product_options']['info_buyRequest']['product']);
                    if($product && $product->getId()){
                        $productOptions = $product->getOptions();
                        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
                        $logger = new \Zend\Log\Logger();
                        $logger->addWriter($writer);
                        $logger->info(print_r($productOptions, true));*/
                        if($productOptions && count($productOptions)){
                            foreach($productOptions as $productOption){
                                $productOptionValues = $productOption->getValues();
                                if($productOptionValues && count($productOptionValues)){
                                    foreach($productOptionValues as $productOptionValueId => $productOptionValue){
                                        if(in_array($productOptionValueId, $item['product_options']['info_buyRequest']['options']) !== false){
                                            $storeTitle = $productOptionValue->getStoreTitle();
                                            $item['custom_option_value'] .= $storeTitle;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $dataSource;
    }
}