<?php

declare(strict_types=1);

namespace App\Service;

class ZaleyCashService
{
    public const URL_DOMAIN = 'https://zaleycash.com';

    /**
     * Генерация токена по секретному ключу.
     */
    public const URL_TOKEN_AUTHORIZATION = '/api/v2/token';

    /**
     * Список внешних рекламных сетей.
     */
    public const URL_SERVICES = '/api/v2/services/';

    /**
     * Список алиасов стран.
     */
    public const URL_COUNTRIES = '/api/v2/countries/';

    /**
     * Информация о пользователе по email.
     */
    public const URL_USER_EMAIL = '/api/v2/user?email=';

    /**
     * Регистрация нового пользователя.
     */
    public const URL_USER = '/api/v2/user';

    /**
     * Данные финансовых балансов пользователя.
     */
    public const URL_USER_BALANCE = '/api/v2/user/balance';

    /**
     * Подробные данные финансовых балансов пользователя.
     */
    public const URL_USER_BALANCE_DETAILED = '/api/v2/user/balance/detailed';

    /**
     * Секретный ключ.
     */
    private string $secretKey = 'xxxxxxx-xxxxxx';

    private string $accessToken;

    /**
     * Отправка запроса.
     *
     * @param string      $url        [адресная строка запроса]
     * @param array       $headers    [заголовки]
     * @param array       $postFields [тело запроса]
     * @param string|null $type       [тип запроса]
     *
     * @throws \JsonException
     */
    private function sendCurl(
        string $url,
        array $headers,
        ?string $type = 'GET',
        array $postFields = [],
    ): mixed {
        $curl = curl_init();

        $curlOptions = [
            CURLOPT_URL => self::URL_DOMAIN . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_POSTFIELDS => 'POST' === $type ? json_encode($postFields, JSON_THROW_ON_ERROR) : http_build_query($postFields),
            CURLOPT_HTTPHEADER => $headers,
        ];
        curl_setopt_array($curl, $curlOptions);
        $response = curl_exec($curl);
        $result = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        curl_close($curl);

        return $result;
    }

    /**
     * Получение токена.
     *
     * @throws \JsonException
     */
    public function getToken(): void
    {
        $headers = [
            'accept: application/json',
            'authorization: '.$this->getAuthorizationHeader(),
        ];

        $response = $this->sendCurl(self::URL_TOKEN_AUTHORIZATION, $headers, 'POST');

        $this->accessToken = $response['access_token'];
    }

    /**
     * Формирование header запроса.
     */
    private function getAuthorizationHeader(): string
    {
        return 'Bearer '.$this->accessToken;
    }
}
