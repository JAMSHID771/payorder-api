<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymeController extends Controller
{
    public function payme(Request $req)
    {
        if ($req->input('method') == 'CheckPerformTransaction') {
            $order_id = $req->params['account']['order_id'];
            $order = Order::find($order_id);
            if (!$order) {
                $errorResponse = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -31050,
                        'message' => [
                            "uz" => "Buyurtma topilmadi",
                            "ru" => "Пользователь не найден",
                            "en" => "User not found",
                        ]
                    ]
                ];
                return response()->json($errorResponse);
            }
            if ($order->price != $req->params['amount']) {
                $errorResponse = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -31001,
                        'message' => [
                            "uz" => "Narxi xato topilmadi",
                            "ru" => "Пользователь не найден",
                            "en" => "User not found",
                        ]
                    ]
                ];
                return response()->json($errorResponse);
            }
            return response()->json([
                'id' => $req->id,
                'result' => [
                    'allow' => true
                ]
            ]);
        }

        if ($req->input('method') == "CreateTransaction") {
            Log::info('CreateTransaction called', [
                'request_id' => $req->id,
                'params' => $req->params
            ]);
            
            if (empty($req->params['account'])) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -32504,
                        'message' => "Bajarish usuli uchun imtiyozlar etarli emas."
                    ]
                ];
                return response()->json($response);
            } else {
                $account = $req->params['account'];
                $order = Order::where('id', $account['order_id'])->first();
                $order_id = $req->params['account']['order_id'];
                $transaction = Transaction::where('order_id', $order_id)->where('state', 1)->get();

                if (!$order) {
                    $response = [
                        'id' => $req->id,
                        'error' => [
                            'code' => -31050,
                            'message' => [
                                "uz" => "Buyurtma topilmadi",
                                "ru" => "Заказ не найден",
                                "en" => "Order not found"
                            ]
                        ]
                    ];
                    return response()->json($response);
                } else if ($order->price != $req->params['amount']) {
                    $response = [
                        'id' => $req->id,
                        'error' => [
                            'code' => -31001,
                            'message' => [
                                "uz" => "Notogri summa",
                                "ru" => "Неверная сумма",
                                "en" => "Incorrect amount"
                            ]
                        ]
                    ];
                    return response()->json($response);
                } elseif (count($transaction) == 0) {

                    $transaction = new Transaction();
                    $transaction->paycom_transaction_id = $req->params['id'];
                    $transaction->paycom_time = $req->params['time'];
                    $transaction->paycom_time_datetime = now();
                    $transaction->amount = $req->params['amount'];
                    $transaction->state = 1;
                    $transaction->order_id = $account['order_id'];
                    $transaction->save();

                    return response()->json([
                        "id" => $req->id,
                        "result" => [
                            'create_time' => $req->params['time'],
                            'transaction' => strval($transaction->id),
                            'state' => $transaction->state
                        ]
                    ]);
                } elseif ((count($transaction) == 1) and ($transaction->first()->paycom_time == $req->params['time']) and ($transaction->first()->paycom_transaction_id == $req->params['id'])) {
                    $response = [
                        'id' => $req->id,
                        'result' => [
                            "create_time" => $req->params['time'],
                            "transaction" => "{$transaction[0]->id}",
                            "state" => intval($transaction[0]->state)
                        ]
                    ];

                    return response()->json($response);
                } else {
                    $response = [
                        'id' => $req->id,
                        'error' => [
                            'code' => -31099,
                            'message' => [
                                "uz" => "Buyurtma tolovi hozirda amalga oshrilmoqda",
                                "ru" => "Оплата заказа в данный момент обрабатывается",
                                "en" => "Order payment is currently being processed"
                            ]
                        ]
                    ];
                    return response()->json($response);
                }
            }
        }

        if ($req->input('method') == "CheckTransaction") {
            $ldate = date('Y-m-d H:i:s');
            $transaction = Transaction::where('paycom_transaction_id', $req->params['id'])->first();
            Log::info($transaction);
            if (empty($transaction)) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -31003,
                        'message' => "Транзакция не найдена."
                    ]
                ];
                return response()->json($response);
            } else if ($transaction->state == 1) {
                Log::info('Test');
                $response = [
                    "id" => $req->id,
                    "result" => [
                        'create_time' => intval($transaction->paycom_time),
                        'perform_time' => intval($transaction->perform_time_unix),
                        'cancel_time' => 0,
                        'transaction' => strval($transaction->id),
                        "state" => $transaction->state,
                        "reason" => $transaction->reason
                    ]
                ];
                return response()->json($response);
            } else if ($transaction->state == 2) {
                // Log::info('Test');
                $response = [
                    "id" => $req->id,
                    "result" => [
                        'create_time' => intval($transaction->paycom_time),
                        'perform_time' => intval($transaction->perform_time_unix),
                        'cancel_time' => 0,
                        'transaction' => strval($transaction->id),
                        "state" => $transaction->state,
                        "reason" => $transaction->reason
                    ]
                ];
                return response()->json($response);
            } else if ($transaction->state == -1) {
                Log::info('Test');
                $response = [
                    "id" => $req->id,
                    "result" => [
                        'create_time' => intval($transaction->paycom_time),
                        'perform_time' => 0,
                        'cancel_time' => intval($transaction->cancel_time),
                        'transaction' => strval($transaction->id),
                        "state" => $transaction->state,
                        "reason" => $transaction->reason
                    ]
                ];
                return response()->json($response);
            }
        }
        
        if ($req->input('method') == "PerformTransaction") {
            $ldate = date('Y-m-d H:i:s');
            $transaction = Transaction::where('paycom_transaction_id', $req->params['id'])->first();
            if (empty($transaction)) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        'code' => -31003,
                        'message' => "Транзакция не найдена "
                    ]
                ];
                return response()->json($response);
            } else if ($transaction->state == 1) {
                $currentMillis = intval(microtime(true) * 1000);
                $transaction = Transaction::where('paycom_transaction_id', $req->params['id'])->first();
                $transaction->state = 2;
                $transaction->perform_time = $ldate;
                $transaction->perform_time_unix = str_replace('.', '', $currentMillis);
                $transaction->update();

                $completed_order = Order::where('id', $transaction->order_id)->first();
                $completed_order->status = 'yakunlandi';
                $completed_order->update();

                $response = [
                    'id' => $req->id,
                    'result' => [
                        'transaction' => "{$transaction->id}",
                        'perform_time' => intval($transaction->perform_time_unix),
                        'state' => intval($transaction->state)
                    ]
                ];
                return response()->json($response);
            } else if ($transaction->state == 2) {
                $response = [
                    'id' => $req->id,
                    'result' => [
                        'transaction' => strval($transaction->id),
                        'perform_time' => intval($transaction->perform_time_unix),
                        'state' => intval($transaction->state)
                    ]
                ];
                return response()->json($response);
            }
        }
        
        if ($req->input('method') == "CancelTransaction") {
            $ldate = date('Y-m-d H:i:s');
            $transaction = Transaction::where('paycom_transaction_id', $req->params['id'])->first();
            if (empty($transaction)) {
                $response = [
                    'id' => $req->id,
                    'error' => [
                        "code" => -31003,
                        "message" => "Транзакция не найдена"
                    ]
                ];
                return response()->json($response);
            } else if ($transaction->state == 1) {
                $currentMillis = intval(microtime(true) * 1000);
                $transaction = Transaction::where('paycom_transaction_id', $req->params['id'])->first();
                $transaction->reason = $req->params['reason'];
                $transaction->cancel_time = str_replace('.', '', $currentMillis);
                $transaction->state = -1;
                $transaction->update();

                $order = Order::find($transaction->order_id);
                $order->update(['status' => 'bekor qilindi']);
                $response = [
                    'id' => $req->id,
                    'result' => [
                        "state" => intval($transaction->state),
                        "cancel_time" => intval($transaction->cancel_time),
                        "transaction" => strval($transaction->id)
                    ]
                ];
                return response()->json($response);
            } else if ($transaction->state == 2) {
                $currentMillis = intval(microtime(true) * 1000);
                $transaction = Transaction::where('paycom_transaction_id', $req->params['id'])->first();
                $transaction->reason = $req->params['reason'];
                $transaction->cancel_time = str_replace('.', '', $currentMillis);
                $transaction->state = -2;
                $transaction->update();

                $order = Order::find($transaction->order_id);
                $order->update(['status' => 'bekor qilindi']);
                $response = [
                    'id' => $req->id,
                    'result' => [
                        "state" => intval($transaction->state),
                        "cancel_time" => intval($transaction->cancel_time),
                        "transaction" => strval($transaction->id)
                    ]
                ];
                return response()->json($response);
            } elseif (($transaction->state == -1) or ($transaction->state == -2)) {
                $response = [
                    'id' => $req->id,
                    'result' => [
                        "state" => intval($transaction->state),
                        "cancel_time" => intval($transaction->cancel_time),
                        "transaction" => strval($transaction->id)
                    ]
                ];

                return response()->json($response);
            }
        }

        if ($req->input('method') == "GetStatement") {
            $from = $req->params['from'];
            $to = $req->params['to'];
            $transactions = Transaction::getTransactionsByTimeRange($from, $to);

            return response()->json([
                'id' => $req->id,
                'result' => [
                    'transactions' => TransactionResource::collection($transactions),
                ],
            ]);
        }

        // If method not found
        return response()->json([
            'id' => $req->id,
            'error' => [
                'code' => -32601,
                'message' => 'Method topilmadi'
            ]
        ]);
    }
}