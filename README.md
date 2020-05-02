Fork of https://github.com/albanafmeti/rocket-chat

Rocket-chat is a rest client package for Laravel that you can use to consume Rocket.Chat REST API.

## Install

This package is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "noisim/rocket-chat": "dev-master"
    }
}
```

and run composer to update the dependencies `composer update`.

Then open your Laravel config file config/app.php and in the `$providers` array add the service provider for this package.

```php
\Noisim\RocketChat\RocketChatServiceProvider::class
```

Finally generate the configuration file running in the console:
```
php artisan vendor:publish --tag=config
```

## How to use 

```php

## app/Http/Auth/RegisterController.php
//sync rocketchat user at main laravel registration

    protected function create(array $data)
    {
        $password = Hash::make($data['password']);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $password,
        ]);

        $chatUser = new ChatUser($user->name, $password, $user->name, $user->email);
        $chatUser->store();
        $chatUser->login();

        $user->chat_id = $chatUser->id();
        $user->chat_token = $chatUser->authToken();
        $user->save();
        
        return $user;
    }
}


## app/User.php
//add shortcuts in your user class

use Noisim\RocketChat\Entities\User as ChatUser;
use Noisim\RocketChat\Entities\Im as ChatIm;

class User extends Authenticatable
{
    //..
    
    public function chatUser(){
        return (new ChatUser())->loginByToken($this->chat_token, $this->chat_id);
    }

    public function chatIm(){
        return (new ChatIm())->loginByToken($this->chat_token, $this->chat_id);
    }
}


## app/Http/Controllers/ChatController.php
//samples

    public function im($id)
    {
        //create im (from current auth user to another one)
        $user = auth()->user(); 
        $profile = User::find($id); 
        
        $user->chatIm()->create($profile->name);
        //...

    }
```
