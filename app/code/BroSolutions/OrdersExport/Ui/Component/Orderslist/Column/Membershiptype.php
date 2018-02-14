<?php
namespace BroSolutions\OrdersExport\Ui\Component\Orderslist\Column;

class Membershiptype extends \Magento\Ui\Component\Listing\Columns\Column
{

    protected $_customerInterface;
    protected $_eavAttributeRepository;

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
        \Magento\Eav\Api\AttributeRepositoryInterface $eavAttributeRepository
    )
    {
        $this->_customerInterface = $customerInterface;
        $this->_eavAttributeRepository = $eavAttributeRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['customer_id'])) {
                    $item['membership_type'] = $this->_getMembershipType($item['customer_id']);
                }
            }
        }

        return $dataSource;
    }

    protected function _getMembershipType($customerId)
    {
        $customer = $this->_customerInterface->getById($customerId);
        if ($customer) {
            $customAttribute = $customer->getCustomAttribute('membership_type');
            if ($customAttribute) {
                $optionId = $customAttribute->getValue();
                $label = $this->_getMembershipOptions($optionId);
                return $label;

            }
        }
        return '';
    }

    protected function _getMembershipOptions($valueId)
    {
        $attributes = $this->_eavAttributeRepository->get(\Magento\Customer\Model\Customer::ENTITY, 'membership_type');
        $options = $attributes->getSource()->getAllOptions(false);
        foreach($options as $option){
            if(isset($option['value']) && isset($option['label']) && $option['value'] == $valueId){
                return $option['label'];
            }
        }
        return '';
    }
}