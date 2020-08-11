<?php

declare(strict_types=1);

namespace muqsit\tebex\api\user;

use muqsit\tebex\api\TebexGETRequest;
use muqsit\tebex\api\TebexResponse;

/**
 * @phpstan-extends TebexGETRequest<TebexUser>
 */
final class TebexUserLookupRequest extends TebexGETRequest{

	/** @var string */
	private $username_or_uuid;

	public function __construct(string $username_or_uuid){
		$this->username_or_uuid = $username_or_uuid;
	}

	public function getEndpoint() : string{
		return "/user/{$this->username_or_uuid}";
	}

	public function getExpectedResponseCode() : int{
		return 200;
	}

	public function createResponse(array $response) : TebexResponse{
		$payments = [];
		foreach($response["payments"] as $payment){
			$payments[] = new TebexPayment($payment["txn_id"], $payment["time"], $payment["price"], $payment["currency"], $payment["status"]);
		}

		$player = $response["player"];

		return new TebexUser(
			new TebexPlayer(
				$player["id"],
				$player["created_at"],
				$player["updated_at"],
				$player["cache_expire"],
				$player["username"],
				$player["meta"],
				$player["plugin_username_id"]
			),
			$response["banCount"],
			$response["chargebackRate"],
			$payments,
			new TebexPurchaseTotals($response["purchaseTotals"])
		);
	}
}