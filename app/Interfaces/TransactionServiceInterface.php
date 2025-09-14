<?php

namespace App\Interfaces;

use App\Models\Transaction;

interface TransactionServiceInterface
{
    public function index();

    public function show($id);

    public function create($data);

    public function update(Transaction $transaction, $data);

    public function delete(Transaction $transaction);

    public function getByTimeRange($from, $to);

    public function findByPaycomId($paycomId);

    public function checkPerformTransaction($orderId, $amount);
    public function createPaymeTransaction($data);
    public function checkTransaction($paycomId);
    public function performTransaction($paycomId);
    public function cancelTransaction($paycomId, $reason);
    public function getStatement($from, $to);
}
