# Metabase Laravel
**First [Metabase](https://www.metabase.com/ "Metabase") should be installed  . You can get more info [Here](https://www.metabase.com/docs/latest/).**

# Installation

## Step 1 - Install the package

  You can install ehsan9/metabase-laravel with Composer directly in your project:

```sh
$ composer require ehsan9/metabase-laravel
```
## Step 2 - Publish

Run this command in your project directory:
```sh
php artisan vendor:publish --provider="Ehsan9\MetabaseLaravel\MetabaseServiceProvider"
```

## Step 3 - Set config

Now you must define your [Metabase Url, Username and Password](https://www.metabase.com/learn/administration/metabase-api) to project. for this head to **config/metabase-api.php** then put your metabase info in the code:
```php
return [
    'url' => 'https://yoursmetabase.com',
    'username' => 'your_metabase_username',
    'password' => 'your_metabase_pass'
];
```

# Usage

You can use the package where ever you want.
- **Method 1**:
  
    - First use the class:
  ```php
    use Ehsan9\MetabaseLaravel\MetabaseApi;
  ```
  - Then use this pattern to connect Metabase api:
  ```php
    $metabaseApi = new \Ehsan9\MetabaseLaravel\MetabaseApi(
        config('metabase-api.url'), config('metabase-api.username'), config('metabase-api.password')
    );
            $parameters = [
                [
                    "type" => "category",
                    "value" => "YOUR_VALUE",
                    "target" => [
                        "variable",
                        [
                            "template-tag",
                            "member_id"
                        ]
                    ]
                ]
            ];
    
    $result = $metabaseApi->getQuestion('questionId', 'json', $parameters);
  ```

- **Method 2**:
    - use MetabaseApi Facade in ServiceProvider
    ```php
  use Ehsan9\MetabaseLaravel\Facades\MetabaseApi;
    ```
  - then use this pattern
  ```php
    MetabaseApi::getQuestion('questionId');
  ```