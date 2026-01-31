<?php
class BillingService {
    protected $mesClient;

    public function __construct($mesClient) {
        $this->mesClient = $mesClient;
    }

    public function generateInvoice(array $orderData) {
        // Basic check
        if (empty($orderData['order_id']) || empty($orderData['total'])) {
            return false; // fallback could be triggered
        }

        // Prepare invoice payload for MES or ERP
        $invoice = [
            'InvoiceID' => 'INV-' . $orderData['order_id'],
            'Amount' => $orderData['total'],
            'Date' => date('Y-m-d'),
        ];

        // Send to billing system
        return $this->mesClient->createInvoice($invoice);
    }
}
