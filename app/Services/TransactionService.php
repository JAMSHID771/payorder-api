<?php

namespace App\Services;

use App\Interfaces\TransactionServiceInterface;
use App\Interfaces\Repositories\TransactionRepositoryInterface;
use App\Models\Transaction;

class TransactionService implements TransactionServiceInterface
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository
    ) {}

    public function index()
    {
        return $this->transactionRepository->all();
    }

    public function show($id)
    {
        return $this->transactionRepository->findOrFail($id);
    }

    public function create($data)
    {
        return $this->transactionRepository->create($data);
    }

    public function update(Transaction $transaction, $data)
    {
        $this->transactionRepository->update($transaction->id, $data);
        return $this->transactionRepository->find($transaction->id);
    }

    public function delete(Transaction $transaction)
    {
        return $this->transactionRepository->delete($transaction->id);
    }

    public function getByTimeRange($from, $to)
    {
        return $this->transactionRepository->where('paycom_time', 'between', [$from, $to]);
    }

    public function findByPaycomId($paycomId)
    {
        return $this->transactionRepository->whereFirst('paycom_transaction_id', $paycomId);
    }

    public function checkPerformTransaction($orderId, $amount)
    {
        $order = $this->transactionRepository->findOrderById($orderId);
        
        if (!$order) {
            return [
                'success' => false,
                'error' => [
                    'code' => -31050,
                    'message' => 'Buyurtma topilmadi'
                ]
            ];
        }

        if ($order->price * 100 != $amount) {
            return [
                'success' => false,
                'error' => [
                    'code' => -31001,
                    'message' => 'Summada xatolik'
                ]
            ];
        }

        return [
            'success' => true,
            'result' => [
                'allow' => true,
                'detail' => [
                    'receipt_type' => 0,
                    'items' => [[
                        'title' => $order->product->title,
                        'price' => $order->price * 100,
                        'count' => 1,
                        'code' => $order->product_id,
                        'package_code' => $order->product_id,
                        'vat_percent' => 0
                    ]]
                ]
            ]
        ];
    }

    public function createPaymeTransaction($data)
    {
        $existing = $this->findByPaycomId($data['paycom_transaction_id']);
        if ($existing) {
            return [
                'success' => true,
                'result' => [
                    'create_time' => $existing->create_time->timestamp * 1000,
                    'transaction' => $existing->id,
                    'state' => $existing->state
                ]
            ];
        }

        $transactionData = [
            'paycom_transaction_id' => $data['paycom_transaction_id'],
            'paycom_time' => $data['time'] ?? null,
            'paycom_time_datetime' => isset($data['time']) ? date('Y-m-d H:i:s', $data['time'] / 1000) : null,
            'create_time' => now(),
            'amount' => $data['amount'],
            'state' => 1,
            'order_id' => $data['order_id']
        ];

        $transaction = $this->transactionRepository->create($transactionData);

        return [
            'success' => true,
            'result' => [
                'create_time' => $transaction->create_time->timestamp * 1000,
                'transaction' => $transaction->id,
                'state' => $transaction->state
            ]
        ];
    }

    public function checkTransaction($paycomId)
    {
        $transaction = $this->findByPaycomId($paycomId);
        
        if (!$transaction) {
            return [
                'success' => false,
                'error' => [
                    'code' => -31003,
                    'message' => 'Tranzaksiya topilmadi'
                ]
            ];
        }

        return [
            'success' => true,
            'result' => [
                'create_time' => $transaction->create_time->timestamp * 1000,
                'perform_time' => $transaction->perform_time ? $transaction->perform_time->timestamp * 1000 : 0,
                'cancel_time' => $transaction->cancel_time,
                'transaction' => $transaction->id,
                'state' => $transaction->state,
                'reason' => $transaction->reason
            ]
        ];
    }

    public function performTransaction($paycomId)
    {
        $transaction = $this->findByPaycomId($paycomId);
        
        if (!$transaction) {
            return [
                'success' => false,
                'error' => [
                    'code' => -31003,
                    'message' => 'Tranzaksiya topilmadi'
                ]
            ];
        }

        if ($transaction->state == 2) {
            return [
                'success' => true,
                'result' => [
                    'transaction' => $transaction->id,
                    'perform_time' => $transaction->perform_time->timestamp * 1000,
                    'state' => $transaction->state
                ]
            ];
        }

        if ($transaction->state != 1) {
            return [
                'success' => false,
                'error' => [
                    'code' => -31008,
                    'message' => 'Tranzaksiya holati notogri'
                ]
            ];
        }

        $this->transactionRepository->performTransaction($transaction->id);

        return [
            'success' => true,
            'result' => [
                'transaction' => $transaction->id,
                'perform_time' => now()->timestamp * 1000,
                'state' => 2
            ]
        ];
    }

    public function cancelTransaction($paycomId, $reason)
    {
        $transaction = $this->findByPaycomId($paycomId);
        
        if (!$transaction) {
            return [
                'success' => false,
                'error' => [
                    'code' => -31003,
                    'message' => 'Tranzaksiya topilmadi'
                ]
            ];
        }

        if ($transaction->state == -1) {
            return [
                'success' => true,
                'result' => [
                    'transaction' => $transaction->id,
                    'cancel_time' => $transaction->cancel_time,
                    'state' => $transaction->state
                ]
            ];
        }

        if ($transaction->state == 2) {
            return [
                'success' => false,
                'error' => [
                    'code' => -31007,
                    'message' => 'Tranzaksiya allaqachon bajarilgan'
                ]
            ];
        }

        $this->transactionRepository->cancelTransaction($transaction->id, $reason);

        return [
            'success' => true,
            'result' => [
                'transaction' => $transaction->id,
                'cancel_time' => now()->timestamp * 1000,
                'state' => -1
            ]
        ];
    }

    public function getStatement($from, $to)
    {
        $transactions = $this->transactionRepository->getStatement($from, $to);

        $transactionsData = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->paycom_transaction_id,
                'time' => $transaction->paycom_time,
                'amount' => $transaction->amount,
                'account' => [
                    'order_id' => $transaction->order_id
                ],
                'create_time' => $transaction->create_time->timestamp * 1000,
                'perform_time' => $transaction->perform_time->timestamp * 1000,
                'cancel_time' => $transaction->cancel_time,
                'transaction' => $transaction->id,
                'state' => $transaction->state,
                'reason' => $transaction->reason,
                'receivers' => $transaction->receivers
            ];
        });

        return [
            'success' => true,
            'result' => [
                'transactions' => $transactionsData
            ]
        ];
    }
}
