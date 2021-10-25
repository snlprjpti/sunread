<?php

namespace Modules\Tax\Traits;

trait HasClientIp {

	public function getClientIp(): ?string
    {
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$clientIP = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			$clientIP = $_SERVER['HTTP_CF_CONNECTING_IP']; 
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
			$clientIP = $_SERVER['HTTP_X_FORWARDED'];
		} elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$clientIP = $_SERVER['HTTP_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_FORWARDED'])) {
			$clientIP = $_SERVER['HTTP_FORWARDED'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$clientIP = $_SERVER['REMOTE_ADDR'];
		}
        return $clientIP;
    }

}
