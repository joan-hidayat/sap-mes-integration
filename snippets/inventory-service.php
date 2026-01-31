<?php
class InventoryService {
    protected $mesClient;

    public function __construct($mesClient) {
        $this->mesClient = $mesClient;
    }

    public function syncInventory(array $inventoryData) {
        foreach ($inventoryData as $item) {
            if (!isset($item['sku'], $item['stock'])) {
                // fallback or skip
                continue;
            }
            $payload = $this->prepareMESPayload($item);
            $this->mesClient->updateInventory($payload);
        }
    }

    protected function prepareMESPayload(array $item) {
        return [
            'MES_SKU' => $item['sku'],
            'MES_Stock' => (int)$item['stock'],
        ];
    }
}
