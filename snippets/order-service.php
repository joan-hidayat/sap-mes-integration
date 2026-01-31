<?php
class OrderService {
    protected $validation;
    protected $mesClient;

    public function __construct($validation, $mesClient) {
        $this->validation = $validation;
        $this->mesClient = $mesClient;
    }

    public function processOrder(array $orderData) {
        // Step 1: Validate incoming SAP data
        if (!$this->validation->checkFields($orderData, ['order_id', 'product_code', 'quantity'])) {
            // fallback: log missing fields, generate default, notify stakeholders
            $this->fallback($orderData);
            return false;
        }

        // Step 2: Transform data to MES format
        $mesPayload = $this->transformToMES($orderData);

        // Step 3: Send to MES system
        $response = $this->mesClient->sendOrder($mesPayload);

        // Step 4: Handle MES response
        if (!$response['success']) {
            $this->fallback($orderData);
            return false;
        }

        return true;
    }

    protected function transformToMES(array $orderData) {
        // Example transformation logic
        return [
            'MES_OrderID' => $orderData['order_id'],
            'MES_Product' => strtoupper($orderData['product_code']),
            'MES_Qty' => (int)$orderData['quantity'],
        ];
    }

    protected function fallback(array $orderData) {
        // Log & notify stakeholders
        error_log("Order fallback triggered for: " . json_encode($orderData));
        // Additional fallback logic could be added here
    }
}
