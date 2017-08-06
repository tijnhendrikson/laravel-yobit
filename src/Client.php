<?php
namespace Pepijnolivier\Yobit;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Client implements ClientContract
{
    protected $tradingUrl;
    protected $publicUrlV2;
    protected $publicUrlV3;

    private $key;
    private $secret;

    public function __construct(array $auth, array $urls)
    {
        $this->tradingUrl = array_get($urls, 'trade');
        $this->publicUrlV2 = array_get($urls, 'publicv2');
        $this->publicUrlV3 = array_get($urls, 'publicv3');

        $this->key = array_get($auth, 'key');
        $this->secret = array_get($auth, 'secret');
    }

    /**
     * Provides statistic data for the last 24 hours.
     *
     * @param string $currencyPair
     * @return mixed
     */
    public function getTicker(string $currencyPair)
    {
        return $this->public('v3', "ticker/$currencyPair");
    }

    /**
     * Provides statistic data for the last 24 hours.
     *
     * @param array $currencyPairs
     * @param bool $ignoreInvalid
     * @return mixed
     */
    public function getTickers(array $currencyPairs = [], $ignoreInvalid = false)
    {
        $impl = implode('-', $currencyPairs);
        $urlFragment = "ticker/$impl";
        return $this->public('v3', $urlFragment, [
           'ignore_invalid' => (int) $ignoreInvalid,
        ]);
    }

    /**
     * Returns information about lists of active orders for selected pairs
     * parameter limit stipulates size of withdrawal (on default 150 to 2000 maximum).
     *
     * @param string $currencyPair
     * @param int $limit
     * @return mixed
     */
    public function getDepth(string $currencyPair, $limit = 150)
    {
        return $this->public('v3', "depth/$currencyPair", [
            'limit' => $limit,
        ]);
    }

    /**
     * Returns information about lists of active orders for selected pairs
     * parameter limit stipulates size of withdrawal (on default 150 to 2000 maximum).
     *
     * @param string $currencyPairs
     * @param bool $ignoreInvalid
     * @return mixed
     */
    public function getDepths(array $currencyPairs = [], $ignoreInvalid = false, $limit=null)
    {
        $impl = implode('-', $currencyPairs);
        return $this->public('v3', "depth/$impl", [
            'ignore_invalid' => (int) $ignoreInvalid,
            'limit' => $limit,
        ]);
    }

    /**
     * Returns information about the last transactions of selected pairs.
     * parameter limit stipulates size of withdrawal (on default 150 to 2000 maximum).
     *
     * @param string $currencyPair
     * @param int $limit
     * @return mixed
     */
    public function getTrade(string $currencyPair, $limit)
    {
        return $this->public('v3', "trade/$currencyPair", [
            'limit' => $limit,
        ]);
    }

    public function getTrades(array $currencyPairs = [], $ignoreInvalid = false, $limit=null)
    {
        $impl = implode('-', $currencyPairs);
        return $this->public('v3', "trade/$impl", [
            'ignore_invalid' => (int) $ignoreInvalid,
            'limit' => $limit,
        ]);
    }

    /**
     * Returns all currency information including:
     *      decimal_places: Quantity of permitted numbers after decimal point
     *      min_price: minimal permitted price
     *      max_price: maximal permitted price
     *      min_amount: minimal permitted buy or sell amount
     *      hidden: pair is hidden (0 or 1)
     *      fee: pair commission
     *
     * Hidden pairs are not shown in the list at Stock Exchange home page, but exchange transactions continue.
     * In case if any pair is disabled it disappears from the list.
     *
     * @param string $currencyPair
     * @return mixed
     */
    public function getPublicInfo()
    {
        return $this->public('v3', "info", []);
    }

    /**
     * Method returns information about user's balances and privileges of API-key as well as server time.
     * Requirements: privilege of key info
     *
     *     funds: available account balance (does not include money on open orders)
     *     funds_incl_orders: available account balance (include money on open orders)
     *     rights: priviledges of key. withdraw is not used (reserved)
     *     transaction_count: always 0 (outdated)
     *     open_orders: always 0 (outdated)
     *     server_time: server time
     *
     * @return mixed
     */
    public function getTradeInfo()
    {
        return $this->tradeRequest('getInfo', []);
    }

    /**
     * Method that allows creating new orders for stock exchange trading
     *
     * @param $pair
     * @param $type
     * @param $rate
     * @param $amount
     * @return mixed
     */
    public function trade($pair, $type, $rate, $amount)
    {
        return $this->tradeRequest('Trade', [
            'pair' => $pair,
            'type' => $type,
            'rate' => $rate,
            'amount' => $amount,
        ]);
    }

    /**
     * @param $pair
     * @param $rate
     * @param $amount
     * @return mixed
     */
    public function buy($pair, $rate, $amount)
    {
        return $this->trade($pair, 'buy', $rate, $amount);
    }

    /**
     * @param $pair
     * @param $rate
     * @param $amount
     * @return mixed
     */
    public function sell($pair, $rate, $amount)
    {
        return $this->trade($pair, 'sell', $rate, $amount);
    }

    /**
     * Method returns list of user's active orders
     * Requirements: priviledges of key info
     *
     * @param $pair
     * @return mixed
     */
    public function getActiveOrders($pair)
    {
        return $this->tradeRequest('ActiveOrders', [
            'pair' => $pair,
        ]);
    }

    /**
     * Method returns detailed information about the chosen order
     *
     * @param $orderId
     * @return mixed
     */
    public function getOrderInfo($orderId)
    {
        return $this->tradeRequest('OrderInfo', [
            'order_id' => $orderId,
        ]);
    }

    /**
     * Method cancels the chosen order
     *
     * @param $orderId
     * @return mixed
     */
    public function cancelOrder($orderId)
    {
        return $this->tradeRequest('CancelOrder', [
            'order_id' => $orderId,
        ]);
    }

    /**
     * Returns transaction history.
     * While using option since or end parameter `order` automatically takes the value ASC.
     * While using option since the earliest date available to get transaction history is one week ago.
     *
     * options:
     *
     * from: No. of transaction from which withdrawal starts (value: numeral, on default: 0)
     * count: quantity of withrawal transactions (value: numeral, on default: 1000)
     * from_id: ID of transaction from which withdrawal starts (value: numeral, on default: 0)
     * end_id: ID of transaction at which withdrawal finishes (value: numeral, on default: ∞)
     * order: sorting at withdrawal (value: ASC or DESC, on default: DESC)
     * since: the time to start the display (value: unix time, on default: 0)
     * end: the time to end the display (value: unix time, on default: ∞)
     * pair: pair (example: ltc_btc)
     *
     * @return mixed
     */
    public function getTradeHistory(array $options = [])
    {
        return $this->tradeRequest('TradeHistory', [
            'from' => $options['from'] ?? null,
            'count' => $options['count'] ?? null,
            'from_id' => $options['from_id'] ?? null,
            'end_id' => $options['end_id'] ?? null,
            'order' => $options['order'] ?? null,
            'since' => $options['since'] ?? null,
            'end' => $options['end'] ?? null,
            'pair' => $options['pair'] ?? null,
        ]);
    }

    /**
     * Method returns deposit address.
     *
     * @param $coinName
     * @param bool $needNew
     * @return mixed
     */
    public function getDepositAddress($coinName, $needNew = false)
    {
        return $this->tradeRequest('GetDepositAddress', [
            'coinName' => $coinName,
            'need_new' => (int) $needNew,
        ]);
    }

    /**
     * Method creates withdrawal request.
     *
     * @param $coinName
     * @param $amount
     * @param $address
     * @return mixed
     */
    public function withdraw($coinName, $amount, $address)
    {
        return $this->tradeRequest('WithdrawCoinsToAddress', [
            'coinName' => $coinName,
            'amount' => $amount,
            'address' => $address,
        ]);
    }

    private function public ($version, $segments, array $parameters=[])
    {
        $options = [
            'http' => [
                'method'  => 'GET',
                'timeout' => 10
            ]
        ];

        $publicUrl = $this->getPublicUrl($version);

        $url = $publicUrl . $segments . '?' . http_build_query(array_filter($parameters));

        $feed = file_get_contents(
            $url, false, stream_context_create($options)
        );

        $response = json_decode($feed, true);
        if(isset($response['error'])) {
            Log::error($response['error']);
        }
        return $response;
    }

    private function getPublicUrl($version)
    {
        switch($version) {
            case 'v2':
                return $this->publicUrlV2;
            case 'v3':
                return $this->publicUrlV3;
        }

        throw new \Exception("Unsupported Yobit API version: $version");
    }

    private function tradeRequest($method, array $parameters = [], $isRetry=false)
    {
        $url = $this->tradingUrl;

        $apiKey = $this->key;
        $apiSecret = $this->secret;

        if(empty($apiKey) || empty($apiSecret)) {
            throw new \Exception("Cannot execute Yobit trade request - invalid key/secret");
        }

        $authHash = sha1("$apiKey $apiSecret");
        $nonce = $this->getNextNonce($authHash);
        $parameters['nonce'] = $nonce;
        $parameters['method'] = $method;

        $post = http_build_query(array_filter($parameters), '', '&');
        $sign = hash_hmac('sha512', $post, $apiSecret);

        $headers = [
            'Key: ' . $apiKey,
            'Sign: ' . $sign,
        ];

        static $ch = null;

        if (is_null($ch)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT,
                'Mozilla/4.0 (compatible; Yobit PHP-Laravel Client; '.php_uname('a').'; PHP/'.phpversion().')'
            );
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);

        if ($response === false) {
            throw new \Exception('Curl error: '.curl_error($ch));
        }

        $response = json_decode($response, true);

        // if the nonce was too low, we should correct the given nonce to the current time, and retry
        $error = isset($response['error']) ? strtolower($response['error']) : '';
        if(str_contains($error, 'invalid nonce')) {
            $currentTimeNonce = floor(microtime(true)); // current time
            if(!$isRetry && ($nonce < $currentTimeNonce)) {
                $this->correctNonce($authHash, $currentTimeNonce);
                Log::warning("Yobit nonce $nonce was too low. Corrected to current unixtime, trying again...");
                return $this->tradeRequest($method, $parameters, true);
            }

            $error = "Yobit nonce is too low: $nonce";
            throw new \Exception($error);
        }

        return $response;
    }

    /**
     * @param string $authHash
     * @param int $nonce
     * @return bool
     */
    private function correctNonce(string $authHash, int $nonce) {
        $currentNonceModel = YobitNonce::where('auth_hash', $authHash)->first();
        return $currentNonceModel->update([
            'nonce' => $nonce,
        ]);
    }

    /**
     * @param string $authHash
     * @return int
     */
    private function getNextNonce(string $authHash) {
        $currentNonceModel = YobitNonce::where('auth_hash', $authHash)->first();
        if(empty($currentNonceModel)) {
            $nonce = floor(microtime(true));
            $currentNonceModel = YobitNonce::create([
                'auth_hash' => $authHash,
                'nonce'     => $nonce,
            ]);
        }

        $currentNonce = $currentNonceModel->nonce;
        $nextNonce = $currentNonce + 1;

        $currentNonceModel->update([
            'nonce' => $nextNonce,
        ]);
        return $nextNonce;
    }

    /**
     * Yobit allows nonces starting from 1 up till 2147483646, which is the end of unix epoch
     * 2147483646 = somewhere in the year 2038
     *
     * We don't want to limit ourselves to max. 1 request per minute, so
     * let's start at the current unix timestamp time,
     * and manually add 1 on each request.
     *
     * For now, just saving this in the storage folder
     */
    private function getNonceOld()
    {
        try {
            $dir = storage_path('exchange/yobit/');
            if(!file_exists($dir)) {
                mkdir($dir, '0777', true);
            }

            $file = $dir . 'nonce.txt';

            $nonce = (int) file_get_contents($file);
            if(empty($nonce)) {
                $nonce = floor(microtime(true));
            }

            $newNonce = $nonce+1;
            file_put_contents($file, $newNonce);

            return $nonce;
        } catch(\Exception $e) {
            Log::critical($e);

            if(config('app.debug')) {
                throw $e;
            }
        }
    }
}
