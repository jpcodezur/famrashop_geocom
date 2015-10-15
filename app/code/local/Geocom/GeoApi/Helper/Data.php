<?php

require_once(Mage::getBaseDir().DS.'includes'.DS.'config.php');
class Geocom_GeoApi_Helper_Data extends Mage_Core_Helper_Abstract {

    public function changeOrderStatus($orderId, $status, $cajero, $cajaPos) {
        $order = Mage::getModel("sales/order")->loadByIncrementId($orderId);
        $lowerStatus = strtolower($status);
        if (!$order->getId()) {
            Mage::throwException('No existe una orden con ese identificador');
            return;
        }

        if (strtolower($order->getStatus()) == 'complete' || strtolower($order->getStatus()) == 'canceled') {
            Mage::throwException('No es posible modificar el estado de esta orden');
            return;
        }

        if ($lowerStatus == 'complete') {
            $this->createInvoice($order);
            $this->createShipment($order);
        }

        //Magento canceled status is spelled with l
        $lowerStatus = $lowerStatus == 'cancelled' ? 'canceled' : $lowerStatus;
        //$order->setState($this->getValidState($status), true);
        $order->setCajero($cajero);
        $order->setCajaGeopos($cajaPos);
        $order->setStatus($lowerStatus);
        $order->save();

        return $this->buildOrderResponseData($orderId);
    }

    protected function createInvoice($order) {
        $comment = 'Generado mediante ws de cambio de estado.';
        $invoice = $order->prepareInvoice()
            ->setTransactionId($order->getId())
            ->addComment($comment)
            ->register()
            ->pay();

        $transaction_save = Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());
        $transaction_save->save();
    }

    protected function createShipment($order) {
        $itemQty =  $order->getItemsCollection()->count();
        $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($itemQty);
        $shipment = new Mage_Sales_Model_Order_Shipment_Api();
        $shipmentId = $shipment->create($order->getIncrementId());
    }

    public function buildRemainingDispatchOrderResponseData($local) {
        //$expireDate = date("F j, Y, H:i", strtotime('+1 hour'));
        $response = array();

        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('commerce_local', array('eq'=>$local))
            //->addAttributeToFilter('updated_at', array('lteq'=>$expireDate))
            ->addFieldToFilter(array('status', 'status'), array(array('eq' => 'pending'), array('eq' => 'in_process')));
        $response['cantidad'] = $orders->getSize();

        return $response;
    }

    public function unlockOrders() {
        $response = array();
        $time = (is_numeric(GEOAPI_TIEMPO_EXPIRACION) && GEOAPI_TIEMPO_EXPIRACION > 0) ? GEOAPI_TIEMPO_EXPIRACION : 720;
        $formattedTime = "-$time minutes";
        $expireDate = date('Y-m-d H:i:s', strtotime($formattedTime));

        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('updated_at', array('lteq'=>$expireDate))
            ->addFieldToFilter(array('status', 'status'), array(array('eq' => 'locked')));

        $response['cantidad'] = $orders->getSize();

        foreach ($orders as $order) {
            $order->setStatus("pending");
            $order->save();
        }

        return $response;
    }
	
	public function buildOrdersResponseDataFilters($filters) {
        
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('farmashop_local', array('eq'=>$filters->local))
            ->addAttributeToFilter('created_at', array('gt'=>$filters->fechadesde . " " . $filters->horadesde))
            ->addAttributeToFilter('created_at', array('lt'=>$filters->fechahasta . " ". $filters->horahasta));
                
            /*->addFieldToFilter(array('status', 'status'), array(
                array('eq' => 'pending'),
                array('eq' => 'in_process')));*/

        return $this->buildOrdersData($orders);
    }

    public function buildOrderResponseData($orderId) {
        $order = Mage::getModel("sales/order")->loadByIncrementId($orderId);

        if (!$order->getId()) {
            Mage::throwException('No existe una orden con ese identificador');
            return;
        }

        return $this->buildOrderData($order);
    }

    protected function buildOrdersData($orders) {
        $data = array();

        foreach ($orders as $order) {
            $data[] = $this->buildOrderData($order);
        }

        return $data;
    }

    protected function buildOrderData($order) {
        $data = array();
        $rut_number = $order->getRutNumber();

        $data['header'] = $this->buildHeader($order);
        $data['items'] = $this->buildItems($order);
        $data['payment'] = $this->buildPayment($order);
        if (!empty($rut_number)) {
            $data['invoiceclient'] = $this->buildInvoiceClient($order);
        }
        $data['dispatchdata'] = $this->buildDispatchData($order);
        $data['agreement'] = $this->buildAgreements($order);

        return $data;
    }

    protected function buildHeader($order) {
        $header = array();
        $rut_number = $order->getRutNumber();
        $contextoPromocion = unserialize($order->getContextoPromocion());
        $header['nro_pedido'] = $order->getIncrementId();
        $header['fecha_creacion'] = $order->getCreatedAt();
        $header['fecha_actualizacion'] = $order->getUpdatedAt();
        $header['total'] = $order->getGrandTotal();
        $header['estado'] = $this->buildPrettyStatus($order->getStatus());
        $header['tipo_documento'] = (!empty($rut_number) ? 'INVOICE' : 'TICKET');
        $header['codigo_barras'] = $this->buildBarcode($order->getIncrementId());
        $header['cajero'] = $order->getCajero();
        $header['local_web'] = GEOAPI_LOCAL_WEB;
        $header['terminal_web'] = GEOAPI_TERMINAL_WEB;
        $header['caja_pos'] = $order->getCajaGeopos();
        $header['local_pos'] = $order->getCommerceLocal();
        $header['contexto_promocion'] = $contextoPromocion;

        return $header;
    }

    protected function buildBarcode($incrementId) {
        $barcode = new Zend_Barcode_Object_Ean13();
        $barcode->setText($incrementId);
        return $barcode->getTextToDisplay();
    }

    protected function buildItems($order) {
        $items = array();
        $rowIndex = 0;
        foreach($order->getAllItems() as $index=>$orderItem) {
            $item = array();
            $item['nro_linea'] = $index;
            $item['sku'] = $orderItem->getSku();
            $item['descripcion'] = $orderItem->getName();
            $item['cantidad'] = (int) $orderItem->getQtyOrdered();
            //$item['descuentos'] = $this->buildPromos($orderItem);
            $item['precio_unitario'] = $orderItem->getPrice();
            $item['precio_total'] = $orderItem->getRowTotal();
            $item['timbre'] = false;
            $item['nro_convenio'] = null;
            $item['id_receta'] = null;
            $rowIndex = $index;
            $items[] = $item;
        }
        $timbres = $this->buildTimbreItems($order, $rowIndex+1);
        $items = array_merge($items, $timbres);
        return $items;
    }

    protected function buildTimbreItems($order, $index) {
        $items = array();
        $agreements = json_decode($order->getAgreements());
        foreach ($agreements as $agreementIndex => $agreement) {
            $item = array();
            $item['nro_linea'] = $index;
            $item['sku'] = '';
            $item['descripcion'] = $agreement->descripcion;
            $item['cantidad'] = 1;
            //$item['descuentos'] = $this->buildPromos($orderItem);
            $item['precio_unitario'] = $agreement->precio_timbre;
            $item['precio_total'] = $agreement->precio_timbre;
            $item['timbre'] = true;
            $item['nro_convenio'] = $agreement->type;
            $item['id_receta'] = $agreement->number;
            $items[] = $item;
            $index++;
        }
        return $items;
    }

    protected function buildPromos($item) {
        $discounts = array();
        $promotions = json_decode($item->getOrder()->getPromotions());
        if(!is_null($promotions)) {
            $product_promo = $promotions->{$item->getProductId()};
            foreach ($product_promo as $promotion){
                $discounts[] = array("id_promo" => $promotion->{'@attributes'}->{'promotion-id'}, "descripcion" => $promotion->{'@attributes'}->{'general-name'}, "monto" => $promotion->{'@attributes'}->{'amount'});
            }
        }
        return $discounts;
    }

    protected function buildPayment($order) {
        $method = $order->getPayment()->getMethod();
        $payment = array();
        $payment['tipo_pago'] = $this->buildPrettyPayment($order->getPayment());
        $payment['monto'] = $order->getGrandTotal();
        $payment['card_bin'] = $order->getPayment()->getCreditCardIin();
        if ($method == "PointsExchangePayment") {
            $payment['puntos_canjeados'] = $order->getPuntosCanjeados();
        }

        //$payment['total_deuda'] = $order->getTotalDue();
        return $payment;
    }

    protected function buildPrettyPayment($payment) {
        $method = $payment->getMethod();
        switch($method) {
            case "cualit_credit_card":
                $type = $payment->getCreditCardType();
                return "CREDIT";
            case "cashpayment":
                return "CASH";
            case "cardpospayment":
                return "CARD";
            case "PointsExchangePayment":
                return "POINTS";
            default:
                return null;
        }
    }

    protected function buildPrettyStatus($status) {
        $status = ($status == 'canceled') ? 'cancelled' : $status;
        return strtoupper($status);
    }

    protected function buildDispatchData($order) {
        $client = array();
        $client['documento'] = $this->buildPrettyValues($order->getCustomerDocumentNumber());
        $client['nombre'] = $order->getCustomerName();
        $client['direccion'] = $order->getShippingAddress()->getCity();
        $client['direccion'] .= " ".$order->getShippingAddress()->getStreet()[0];
        $client['direccion'] .= " ".$order->getShippingAddress()->getAddressCorner();
        $client['direccion'] .= " ".$order->getShippingAddress()->getAddressNumber();
        $client['direccion'] .= " ".$order->getShippingAddress()->getTelephone();
        $client['telefono'] = $order->getShippingAddress()->getTelephone();
        $client['tipo_documento'] = $this->buildPrettyDocument($order->getCustomerDocumentType());
        $client['codigo_pais_documento'] = $order->getShippingAddress()->getCountry();
        return $client;
    }

    protected function buildPrettyDocument($type) {
        switch(strtolower($type)) {
            case "ci":
                return 3;
            case "otro":
                return 4;
            case "rut":
                return 2;
            default:
                return null;
        }
    }

    protected function buildPrettyValues($value) {
        if (is_null($value) || empty($value) || $value == 'N/A') {
            return null;
        }

        return $value;
    }

    protected function buildInvoiceClient($order) {
        $client = array();
        $client['ruc'] = $order->getRutNumber();
        $client['nombre'] = $order->getCustomerName();
        $billingAdd = $order->getBillingAddress();
        $client['direccion'] = $billingAdd->getCity();
        $client['direccion'] .= " ".$billingAdd->getStreet()[0];
        $client['direccion'] .= " ".$billingAdd->getAddressCorner();
        $client['direccion'] .= " ".$billingAdd->getAddressNumber();
        $client['direccion'] .= " ".$billingAdd->getTelephone();
        $client['ciudad'] = $order->getBillingAddress()->getCity();
        return $client;
    }

    protected function buildAgreements($order) {
        $items = array();
        $agreements = json_decode($order->getAgreements());
        foreach ($agreements as $agreementIndex => $agreement) {
            foreach ($agreement->items as $itemIndex => $item) {
                $tempitem = array();
                $tempitem['sku'] = $this->getProductSkuByItemId($order, $item->id);
                $tempitem['cantidad_receta'] = $item->qty;
                $tempitem['nro_convenio'] = $agreement->type;
                $tempitem['id_receta'] =  $agreement->number;
                $items[] = $tempitem;
            }
        }
        return $items;
    }

    protected function getProductSkuByItemId($order, $productId) {
        foreach($order->getAllItems() as $index=>$orderItem) {
            if ($orderItem->getProduct()->getId() == $productId) {
                return $orderItem->getSku();
            }
        }
        return null;
    }


}