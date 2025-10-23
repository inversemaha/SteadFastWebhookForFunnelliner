<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Routing\Controller;

class SteadfastWebhookController extends Controller
{
    /**
     * Handle incoming Steadfast webhook
     */
    public function handleSteadFastWebhook(Request $request)
    {
        $payload = $request->all();
        $token = $request->header('Authorization');

        // Check if Bearer token is valid
        if ($token !== 'Bearer '. config('steadfast.webhook_token')) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $validatedPayloadData = $this->validatePayload($request);
            $this->processPayload($validatedPayloadData);

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook received successfully.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ], 400);
        }
    }

    private function validatePayload(Request $request)
    {
        $notificationType = $request->input('notification_type');
        
        // Base validation rules
        $baseRules = [
            'notification_type' => 'required|string|in:delivery_status,tracking_update',
            'consignment_id' => 'required|integer',
            'invoice' => 'required|string',
        ];
        
        // Notification-specific rules
        $specificRules = [];
        if ($notificationType === 'delivery_status') {
            $specificRules = [
                'status' => 'required|string',
                'cod_amount' => 'required|numeric|min:0',
                'delivery_charge' => 'required|numeric|min:0',
                'tracking_message' => 'required|string',
                'updated_at' => 'required|date_format:Y-m-d H:i:s',
            ];
        } elseif ($notificationType === 'tracking_update') {
            $specificRules = [
                'tracking_message' => 'required|string',
                'updated_at' => 'required|date_format:Y-m-d H:i:s',
            ];
        }
        
        return $request->validate(array_merge($baseRules, $specificRules));
    }

    private function processPayload($validatedPayloadData)
    {
        $consignment_id = $validatedPayloadData['consignment_id'];
        $notificationType = $validatedPayloadData['notification_type'];

        $order = Order::where('consignment_id', $consignment_id)->first();
        
        if (!$order) {
            abort(404, json_encode([
                'status' => 'error',
                'message' => 'Order not found for consignment_id: ' . $consignment_id
            ]));
        }

        // Update common fields
        $order->notification_type = $notificationType;
        $order->invoice = $validatedPayloadData['invoice'];
        
        // Update notification-specific fields
        if ($notificationType === 'delivery_status') {
            $order->status = $validatedPayloadData['status'];
            $order->cod_amount = $validatedPayloadData['cod_amount'];
            $order->delivery_charge = $validatedPayloadData['delivery_charge'];
            $order->tracking_message = $validatedPayloadData['tracking_message'];
            $order->updated_at = $validatedPayloadData['updated_at'];
        } elseif ($notificationType === 'tracking_update') {
            $order->tracking_message = $validatedPayloadData['tracking_message'];
            $order->updated_at = $validatedPayloadData['updated_at'];
        }
        
        $order->save();
    }
}
