# Podcastwala
Your very own Podcast web app built with Laravel 5. This web app enables you to manage RSS feeds for your favorite podcasts and listen to your favorite episodes in a seemless UI. New episodes published by podcasts are automatically updated on a daily basis. Mark the items you have listened to as read and much more. 

### Screenshots
![alt tag](https://raw.githubusercontent.com/modestkdr/Podcastwala/master/screenshots/prototype.png)

### Install Instructions
To install Podcastwala you can clone the repository:

```
$ git clone https://github.com/modestkdr/Podcastwala.git.
```


Next, enter the project's root directory and install the project dependencies:

```
$ composer install
```

Next, configure your .env file (root directory) and database (config/database.php). Subsequently, create the database and then run the migrations:

```
$ php artisan migrate
```

### License
Podcastwala is licensed under the MIT license. If you find something wrong with the code or think it could be improved, I welcome you to create an <a href="https://github.com/modestkdr/Podcastwala/issues">issue</a> or submit a pull request!

