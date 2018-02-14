<?php

namespace BroSolutions\OrdersExport\Ui\Component\Orderslist\Column;

class Company extends \Magento\Ui\Component\Listing\Columns\Column {

    protected $_customerInterface;
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
        \Magento\Customer\Api\CustomerRepositoryInterface $customerInterface
    ){
        $this->_customerInterface = $customerInterface;
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
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if(isset($item['customer_id'])){
                    $item['company'] = $this->_getCustomerCompany($item['customer_id']);
                }
            }
        }

        return $dataSource;
    }

    protected function _getCustomerCompany($customerId)
    {
        $customer = $this->_customerInterface->getById($customerId);
        if($customer){
            $customAttribute = $customer->getCustomAttribute('company');
            if($customAttribute){
                return $customAttribute->getValue();
            }
        }
        return '';
    }
}