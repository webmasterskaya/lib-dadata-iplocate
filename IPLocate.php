<?php
/*
 * @package    Joomla.Library - DaData\IPLocate
 * @version    __DEPLOYMENT_VERSION__
 * @author     Artem Vasilev - webmasterskaya.xyz
 * @copyright  Copyright (c) 2018 - 2021 Webmasterskaya. All rights reserved.
 * @license    GNU/GPL license: https://www.gnu.org/copyleft/gpl.html
 * @link       https://webmasterskaya.xyz/
 */

namespace DaData\IPLocate;

use Exception;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * Класс определяет местоположение пользователя по IP и возвращает информацию,
 * полученную из сервиса DaData.ru
 *
 * @since __DEPLOYMENT_VERSION__
 */
class IPLocate
{
	/**
	 * Метод возвращает массив данных, о место опложении IP адреса
	 * или NULL, если не удалось определить местоположение
	 *
	 * @documentation Описание параметров массива https://dadata.ru/api/iplocate/#response
	 *
	 * @param   string  $token  API-ключ сервиса DaData.ru
	 * @param   string  $ip     IP-дрес, местоположение которого нужно вычеслить.
	 *                          При пустом значении IP будет определён автоматически
	 *
	 * @return array | null
	 * @throws Exception
	 *
	 * @since         __DEPLOYMENT_VERSION__
	 */
	public static function address(string $token, string $ip = ''): ?array
	{
		// Проверка токена
		if (empty($token))
		{
			throw new Exception(
				Text::_('LIB_DADATA_IPLOCATE_ERROR_API_KEY_NOT_SET')
			);
		}

		if (empty($ip))
		{
			try
			{
				$ip = self::getClientIp();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
		else
		{
			if (!($ip = filter_var($ip, FILTER_VALIDATE_IP)))
			{
				throw new Exception(
					Text::_('LIB_DADATA_IPLOCATE_ERROR_WRONG_IP_FORMAT')
				);
			}
		}

		try
		{
			$addresses = json_decode(self::makeRequest($token, $ip), true);

			return $addresses['location'];
		}
		catch (Exception $e)
		{
			throw new Exception(
				Text::_('LIB_DADATA_IPLOCATE_ERROR_JSON_PARSE_ERROR')
			);
		}
	}

	/**
	 * Метод возвращает IP пользователя, определённый на основании переданных
	 * клиентом HTTP-заголовков
	 *
	 * @return string|bool
	 *
	 * @since __DEPLOYMENT_VERSION__
	 */
	public static function getClientIp()
	{
		$keys = [
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'REMOTE_ADDR',
		];
		foreach ($keys as $key)
		{
			if (!empty($_SERVER[$key]))
			{
				$ipResult = explode(',', $_SERVER[$key]);
				$ipResult = end($ipResult);
				$ip       = trim($ipResult);
				if (filter_var($ip, FILTER_VALIDATE_IP))
				{
					return $ip;
				}
			}
		}

		return false;
	}

	/**
	 * Возвращает результат запроса к сервису https://dadata.ru/api/iplocate
	 *
	 * @param   string  $token  API-ключ сервиса DaData.ru
	 * @param   string  $ip     IP-дрес, местоположение которого нужно вычеслить.
	 *                          При пустом значении будет передан IP сервера
	 *
	 * @return string|bool
	 * @throws Exception
	 *
	 * @since __DEPLOYMENT_VERSION__
	 */
	protected static function makeRequest(
		string $token,
		string $ip = ''
	): string {
		$ch = curl_init();

		curl_setopt(
			$ch,
			CURLOPT_URL,
			'https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address?ip='
			.$ip
		);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


		$headers   = array();
		$headers[] = 'Accept: application/json';
		$headers[]
		           = 'Authorization: Token '.$token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch))
		{
			throw new Exception(
				Text::sprintf(
					'LIB_DADATA_IPLOCATE_ERROR_HTTP_ERROR',
					curl_error($ch)
				)
			);
		}
		curl_close($ch);

		return $result;
	}
}
