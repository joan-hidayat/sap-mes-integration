<?php

class SapEtlPipeline {

    protected $sapClient;
    protected $validator;
    protected $orderService;

    public function __construct($sapClient, $validator, $orderService) {
        $this->sapClient = $sapClient;
        $this->validator = $validator;
        $this->orderService = $orderService;
    }

    public function run() {
        try {

            // Step 1 — Extract
            $sapData = $this->sapClient->fetchOrdersXML();

            if (empty($sapData)) {
                throw new Exception("No data received from SAP.");
            }

            // Step 2 — Transform
            $orders = $this->transformXMLToArray($sapData);

            // Step 3 — Load (via service)
            foreach ($orders as $order) {

                if (!$this->validator->checkFields($order, [
                    'order_id',
                    'product_code',
                    'quantity'
                ])) {

                    $this->handleInvalidData($order);
                    continue;
                }

                $this->orderService->processOrder($order);
            }

        } catch (Exception $e) {

            // centralized logging
            error_log("ETL Pipeline Error: " . $e->getMessage());

            // optional alerting hook
            // $this->notifyTeam($e);

        }
    }

    private function transformXMLToArray($xml) {

        $parsed = simplexml_load_string($xml);
        $orders = [];

        foreach ($parsed->Order as $order) {

            $orders[] = [
                'order_id' => (string) $order->OrderID,
                'product_code' => (string) $order->Product,
                'quantity' => (int) $order->Qty,
            ];
        }

        return $orders;
    }

    private function handleInvalidData($order) {

        // fallback strategy
        error_log("Invalid SAP order detected: " . json_encode($order));

        // Example:
        // - send alert
        // - mark for manual review
        // - insert into quarantine table
    }
}
