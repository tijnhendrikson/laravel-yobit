# Laravel-Yobit

Start trading on Yobit right away using your favorite PHP framework.

### Installation

`composer require pepijnolivier/laravel-yobit`.

Add the service provider to your `config/app.php`:
 
 ``` 
 'providers' => [
 
     Pepijnolivier\Yobit\YobitServiceProvider::class,
     
 ],
 ```
 
...run `php artisan vendor:publish` to copy the config file.

Edit the `config/yobit.php` or add Yobit api and secret in your `.env` file

```
YOBIT_KEY={YOUR_API_KEY}
YOBIT_SECRET={YOUR_API_SECRET}

```

Add the alias to your `config/app.php`:

```    
'aliases' => [
           
    'Yobit' => Pepijnolivier\Yobit\Yobit::class,
           
],
```

### Usage

Please refer to the [Api Documentation](https://yobit.net/en/api/) for more info, or read the docblocks.


Tips are appreciated 
`1N5ET46r5Z4HdfhRjGMp7SpEMPes9S1H9n`
