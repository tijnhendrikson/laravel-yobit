<?php
namespace Pepijnolivier\Yobit;

interface ClientContract
{

    /**
     * Provides statistic data for the last 24 hours.
     *
     * @param string $currencyPair
     * @return mixed
     */
    public function getTicker(string $currencyPair);

    /**
     * Provides statistic data for the last 24 hours.
     *
     * @param array $currencyPairs
     * @param bool $ignoreInvalid
     * @return mixed
     */
    public function getTickers(array $currencyPairs=[], $ignoreInvalid=false);

    /**
     * Returns information about lists of active orders for selected pairs
     * parameter limit stipulates size of withdrawal (on default 150 to 2000 maximum).
     *
     * @param string $currencyPair
     * @param int $limit
     * @return mixed
     */
    public function getDepth(string $currencyPair, $limit=150);

    /**
     * Returns information about lists of active orders for selected pairs
     * parameter limit stipulates size of withdrawal (on default 150 to 2000 maximum).
     *
     * @param string $currencyPair
     * @return mixed
     */
    public function getDepths(array $currencyPairs=[], $ignoreInvalid=false, $limit=null);


    /**
     * Returns information about the last transactions of selected pairs.
     * parameter limit stipulates size of withdrawal (on default 150 to 2000 maximum).
     *
     * @param string $currencyPair
     * @param int $limit
     * @return mixed
     */
    public function getTrade(string $currencyPair, $limit);
    public function getTrades(array $currencyPairs=[], $ignoreInvalid=false, $limit=null);


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
     * @return mixed
     */
    public function getPublicInfo();


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
    public function getTradeInfo();


    /**
     * Method that allows creating new orders for stock exchange trading
     *
     * @param $pair
     * @param $type
     * @param $rate
     * @param $amount
     * @return mixed
     */
    public function trade($pair, $type, $rate, $amount);

    /**
     * @param $pair
     * @param $rate
     * @param $amount
     * @return mixed
     */
    public function buy($pair, $rate, $amount);

    /**
     * @param $pair
     * @param $rate
     * @param $amount
     * @return mixed
     */
    public function sell($pair, $rate, $amount);


    /**
     * Method returns list of user's active orders
     * Requirements: priviledges of key info
     *
     * @param $pair
     * @return mixed
     */
    public function getActiveOrders($pair);


    /**
     * Method returns detailed information about the chosen order
     *
     * @param $orderId
     * @return mixed
     */
    public function getOrderInfo($orderId);


    /**
     * Method cancels the chosen order
     *
     * @param $orderId
     * @return mixed
     */
    public function cancelOrder($orderId);


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
    public function getTradeHistory(array $options=[]);


    /**
     * Method returns deposit address.
     *
     * @param $coinName
     * @param bool $needNew
     * @return mixed
     */
    public function getDepositAddress($coinName, $needNew=false);


    /**
     * Method creates withdrawal request.
     *
     * @param $coinName
     * @param $amount
     * @param $address
     * @return mixed
     */
    public function withdraw($coinName, $amount, $address);
}
