<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Result;
use App\Exceptions\SystemException;
use App\Models\DeviceToken;

class DeviceTokenService extends BaseService
{
    /**
     * Register iOS device token to Amazon SNS.
     *
     * @param int    $userId
     * @param string $deviceToken
     * @param object $awsSnsClient
     * @return null|object
     */
    public function registerIosDeviceTokenToSns(int $userId, string $deviceToken, object $awsSnsClient): DeviceToken
    {
        try {
            // Check if the device token is already registered
            $registeredDeviceTokenEntity = DeviceToken::whereDeviceToken($deviceToken)->first();

            if (empty($registeredDeviceTokenEntity)) {
                // In case that the device token is not registered yet

                // Create platform endpoint
                $endpointArn = $this->createEndpoint($deviceToken, env('IOS_APPLICATION_ARN'), $awsSnsClient);

                // Register the device token to topic to subscribe
                $subscriptionArn = $this->subscribeToTopic($endpointArn, env('TOPIC_IOS'), $awsSnsClient);

                // Insert the record
                $deviceTokenEntity = new DeviceToken();
                $deviceTokenEntity->user_id = $userId;
                $deviceTokenEntity->platform = DeviceToken::IOS;
                $deviceTokenEntity->device_token = $deviceToken;
                $deviceTokenEntity->arn = $endpointArn;
                $deviceTokenEntity->subscription_arn = $subscriptionArn;
                $deviceTokenEntity->save();

                return $deviceTokenEntity;
            }
            // In case that the device token is already registered

            // Enable endpoint ARN again If it's disabled
            $this->enableEndpoint($registeredDeviceTokenEntity->arn, $awsSnsClient);

            return $registeredDeviceTokenEntity;
        } catch (SnsException $e) {
            $this->logInfo($e->getMessage());

            throw new SystemException();
        }
    }

    /**
     * Register Android device token to Amazon SNS.
     *
     * @param int    $userId
     * @param string $deviceToken
     * @param object $awsSnsClient
     * @return null|object
     */
    public function registerAndroidDeviceTokenToSns(int $userId, string $deviceToken, object $awsSnsClient): DeviceToken
    {
        try {
            // Check if the device token is already registered
            $registeredDeviceTokenEntity = DeviceToken::whereDeviceToken($deviceToken)->first();

            if (empty($registeredDeviceTokenEntity)) {
                // In case that the device token is not registered yet

                // Create platform endpoint
                $endpointArn = $this->createEndpoint($deviceToken, env('ANDROID_APPLICATION_ARN'), $awsSnsClient);

                // Register the device token to topic to subscribe
                $subscriptionArn = $this->subscribeToTopic($endpointArn, env('TOPIC_ANDROID'), $awsSnsClient);

                // Insert the record
                $deviceTokenEntity = new DeviceToken();
                $deviceTokenEntity->user_id = $userId;
                $deviceTokenEntity->platform = DeviceToken::ANDROID;
                $deviceTokenEntity->device_token = $deviceToken;
                $deviceTokenEntity->arn = $endpointArn;
                $deviceTokenEntity->subscription_arn = $subscriptionArn;
                $deviceTokenEntity->save();

                return $deviceTokenEntity;
            }
            // In case that the device token is already registered

            // Enable endpoint ARN again If it's disabled
            $this->enableEndpoint($registeredDeviceTokenEntity->arn, $awsSnsClient);

            return $registeredDeviceTokenEntity;
        } catch (SnsException $e) {
            $this->logInfo($e->getMessage());

            throw new SystemException();
        }
    }

    /**
     * Create platform endpoint.
     *
     * @param string $deviceToken
     * @param string $platformApplicationArn
     * @param object $awsSnsClient
     * @return string
     */
    private function createEndpoint(string $deviceToken, string $platformApplicationArn, object $awsSnsClient): string
    {
        $result = $awsSnsClient->createPlatformEndpoint([
            'PlatformApplicationArn' => $platformApplicationArn,
            'Token' => $deviceToken,
        ]);

        return $result['EndpointArn'];
    }

    /**
     * Subscribe to topic.
     *
     * @param string $endpointArn
     * @param string $topicName
     * @param object $awsSnsClient
     * @return string
     */
    private function subscribeToTopic(string $endpointArn, string $topicName, object $awsSnsClient): string
    {
        $result = $awsSnsClient->subscribe([
            'Endpoint' => $endpointArn,
            'Protocol' => 'application',
            'TopicArn' => $topicName,
        ]);

        return $result['SubscriptionArn'];
    }

    /**
     * Unsubscribe to topic.
     *
     * @param string $subscriptionArn
     * @param object $awsSnsClient
     * @return int
     */
    private function unsubscribeToTopic(string $subscriptionArn, object $awsSnsClient): int
    {
        try {
            $awsSnsClient->unsubscribe([
                'SubscriptionArn' => $subscriptionArn,
            ]);

            return Result::SUCCESS;
        } catch (SnsException $e) {
            $this->logInfo($e->getMessage());

            return Result::FAILURE;
        }
    }

    /**
     * Enable Amazon SNS endpoint ARN.
     *
     * @param string $endpointArn
     * @param object $awsSnsClient
     */
    private function enableEndpoint(string $endpointArn, object $awsSnsClient): void
    {
        $endpointAtt = $awsSnsClient->getEndpointAttributes(['EndpointArn' => $endpointArn]);
        if ($endpointAtt != 'failed' && $endpointAtt['Attributes']['Enabled'] == 'false') {
            $awsSnsClient->setEndpointAttributes([
                'Attributes' => [
                    'Enabled' => 'true',
                ],
                'EndpointArn' => $endpointArn,
            ]);
        }
    }
}
