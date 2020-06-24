<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Helper\LogToChannels;
use App\Models\Contract;
use App\Models\DeviceToken;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use ReceiptValidator\iTunes\Validator as iTunesValidator;

class ConstractService
{
    public $logger;

    public function __construct(LogToChannels $logger)
    {
        $this->logger = $logger;
    }

    public function createConstractIOS($app_store_receipt = '', $user_id = '', $is_subscription = false)
    {
        $constract = null;
        if ($app_store_receipt && $user_id) {
            // Payment with app store
            if (app()->environment('local', 'staging')) {
                // Or iTunesValidator::ENDPOINT_SANDBOX if sandbox testing
                $validator = new iTunesValidator(iTunesValidator::ENDPOINT_SANDBOX);
            } else {
                $validator = new iTunesValidator(iTunesValidator::ENDPOINT_PRODUCTION);
            }

            try {
                if ($is_subscription) {
                    $sharedSecret = env('ITUNE_SHARE_SECRET', '1234...'); // Generated in iTunes Connect's In-App Purchase menu
                    $response = $validator->setSharedSecret($sharedSecret)->setReceiptData($app_store_receipt)->validate(); // use setSharedSecret() if for recurring subscriptions
                } else {
                    $response = $validator->setReceiptData($app_store_receipt)->validate();
                }
            } catch (\Exception $e) {
                $this->writeLogPayment('Validate app store receipt error', $e->getMessage());
            }

            if ($response->isValid()) {
                $this->writeLogPayment('Validate app store receipt success', $response->getReceipt());

                // Get last product was buy for create a constract
                $purchases = $response->getPurchases();
                $purchase = null;
                if (count($purchases)) {
                    $purchase = last($purchases);
                }
                if ($purchase) {
                    $plan = null;
                    if ($purchase->getProductId() == Plan::IOS_PLAN_TYPE_1) {
                        $plan = Plan::where('contract_period', 1)->first();
                    }
                    if ($purchase->getProductId() == Plan::IOS_PLAN_TYPE_3) {
                        $plan = Plan::where('contract_period', 3)->first();
                    }
                    if ($purchase->getProductId() == Plan::IOS_PLAN_TYPE_6) {
                        $plan = Plan::where('contract_period', 6)->first();
                    }
                    if ($purchase->getProductId() == Plan::IOS_PLAN_TYPE_12) {
                        $plan = Plan::where('contract_period', 12)->first();
                    }

                    // If find a plan to create a new constract
                    if ($plan) {
                        $constract = new Contract();
                        $constract->user_id = $user_id;
                        $constract->plan_id = $plan->id;
                        // $purchase->getPurchaseDate();
                        $constract->plan_start_date = Carbon::now()->format('Y-m-d');
                        $constract->plan_end_date = Carbon::now()->addMonths($plan->contract_period)->format('Y-m-d');
                        $constract->price = $plan->total;
                        $constract->purchase_device = DeviceToken::IOS;
                        $constract->app_store_receipt = $app_store_receipt;
                        $constract->save();
                    }
                }
            } else {
                $this->writeLogPayment('Validate app store receipt is not valid.', $response->getResultCode());
            }
        }

        return $constract;
    }

    private function writeLogPayment($error = '', $errorMessages = ''): void
    {
        // Write log when error
        $contextLog = [
            'user_login' => Auth::check() ? Auth::user()->id : null,
            'input' => request()->all(),
            'errors' => $errorMessages,
        ];
        $this->logger->error('payment_log', $error, $contextLog);
    }
}
