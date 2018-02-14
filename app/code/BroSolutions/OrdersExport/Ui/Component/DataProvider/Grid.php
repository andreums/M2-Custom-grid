<?php
namespace BroSolutions\OrdersExport\Ui\Component\DataProvider;
use Magento\Ui\DataProvider\AbstractDataProvider;
class Grid
    extends AbstractDataProvider
{
    const REGISTRATION_CATEGORY_ID = 4;
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemsCollectionFactory,
        \Magento\Framework\UrlInterface $url
    ) {
        $meta = array();
        $updateUrl = $url->getUrl('mui/index/render');
        $data = [
            'config' => [
                'component' => 'Magento_Ui/js/grid/provider',
                'update_url' => $updateUrl
            ]
        ];
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $orderItemsCollectionFactory->create();
        $this->collection->getSelect()->joinLeft(
            array('o' => 'sales_order'),
            'main_table.order_id = o.entity_id',
            [
                /*'customer_email' => 'customer_email',
                'customer_firstname' => 'customer_firstname',
                'customer_lastname' => 'customer_lastname',
                'o.status' => 'status',
                'o.entity_id' => 'order_id',
                'o.customer_id' => 'customer_id',
                'o.shipping_address_id' => 'shipping_address_id',
                'o.customer_id' => 'customer_id',*/
                '*'
            ]
        );
        $this->collection->getSelect()->joinLeft(
            array('s' => 'membership'),
            'o.customer_id = s.customer_id',
            ['member_expiration' => 'member_expiration']
        );

        $this->collection->getSelect()->joinLeft(
            array('a' => 'sales_order_address'),
            'o.shipping_address_id = a.entity_id AND a.address_type = \'shipping\'',
            ['telephone' => 'telephone', 'street' => 'street', 'city' => 'city', 'region' => 'region', 'postcode' => 'postcode', 'country_id' => 'country_id']
        );
        /*$this->collection->getSelect()->joinLeft(
            array('oi' => 'sales_order_item'),
            'main_table.entity_id = oi.order_id',
            ['product_id' => 'product_id',
                'product_name' => 'oi.name',
                'product_sku' => 'oi.sku',
                'info_buy_request' => 'oi.product_options',
                'product_original_price' => 'oi.base_original_price',
                'product_discount' => 'oi.base_discount_amount',
                'product_price' => 'oi.price'
            ]
        );*/
        $this->collection->getSelect()->joinLeft(
            array('pc' => 'catalog_category_product'),
            'main_table.product_id = pc.product_id AND pc.category_id = '.self::REGISTRATION_CATEGORY_ID,
            ['category_id' => 'category_id']
        );
        $this->collection->getSelect()->where('pc.category_id IS NOT NULL');
        $this->collection->getSelect()->columns('o.created_at as order_date');
        $this->collection->getSelect()->columns('o.entity_id as purchased_order_id');
        $this->collection->getSelect()->columns('main_table.name as product_name');
        $this->collection->getSelect()->columns('main_table.sku as product_sku');
        $this->collection->getSelect()->columns('main_table.product_options as info_buy_request');
        $this->collection->getSelect()->columns('main_table.base_original_price as product_original_price');
        $this->collection->getSelect()->columns('main_table.base_discount_amount as product_discount');
        $this->collection->getSelect()->columns('main_table.price as product_price');
        $this->collection->getSelect()->group('main_table.item_id');

        //$this->collection->getSelect()->group('main_table.entity_id');
        /*$this->collection->getSelect()->group('s.member_expiration');
        $this->collection->getSelect()->group('a.customer_address_id');*/
        //print_r($this->collection->getSelect()->__toString()); die;
    }
}