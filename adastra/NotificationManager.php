<?php

use App\Model\Entity\Device;

class NotificationManager
{

	public $countryCode;
	public $client;

	public function __construct($client, $countryCode)
	{
		$this->client = $client;
		$this->countryCode = $countryCode;
	}

	public function notifyUsers($text, $metadata, $userId = null)
	{
        if (isset($text)) {
            if (empty($metadata)) {
                $metadata = array(
                    'icon' => 'default.ico',
                );
            }
			$messages = array();
			if ($userId) {
                $devices = Device::whereCountryCode($this->countryCode)->whereUserId($userId)->get();
            } else {
                $devices = Device::whereCountryCode($this->countryCode)->get();
            }
			foreach ($devices as $device) {
				if ($device->state != 'ACTIVE') {
					$metadata['is_active'] = false;
				} else {
					$metadata['is_active'] = true;
				}

				$messages[] = array(
					'message' => $text,
					'registration_id' => $device->registration_id,
					'data' => $metadata
				);
			}
			foreach ($messages as $payload) {
				$this->client->send($payload);
			}
			if (count($messages) > 0) {
				return true;
            } else {
				return false;
            }
        } else {
            return false;
        }
	}
}