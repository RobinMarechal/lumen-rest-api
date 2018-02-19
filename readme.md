# Lumen Rest API

A simple way to create PHP RESTful APIs

## Usage example
The REST API is easy to use since it respects Laravel/Lumen's name  conventions and RESTful API's standards.

 For example, imagine you have a `posts` table in your database, then you'll want to get all the posts from your API, running on the server `myserver.com`. To do that, you'll simply have to request the following URL:
 ```http
 http://myserver.com/api/posts
```

If you only want the post with id 4:
```http
http://myserver.com/api/posts/4
```

If you want the comments of the 4th post:

```http
http://myserver.com/api/posts/4/comments
```

_Remark: In this case, you need to specify that the `posts` table (the `Post` model) has a relation named `comments`. We will speak about that later._

Finally, if you want the 3rd comment of the 4th post:

```http
http://myserver.com/api/posts/4/comments/3
```

Of course, it's also possible to add (`POST`), update (`PUT/PATCH`) and delete (`DELETE`) data. See the documentation.

## Installation

Simply clone the repository:
```bash
git clone https://github.com/RobinMarechal/lumen-rest-api.git
```

##  Prepare your API
Once you've cloned the repo, you just need to create the required structure.
For each of your database's tables (except the pivots), you need to create a **Controller** and a **Model**.

The API controllers are located in `app/Http/Controllers/Rest/`, and the models are in `app/`.

The controller's names should be you your table's names, in camel case and in plural form.
The model's names should be your table's name, in camel case and in singular form.

For example, if you have a `posts` table, you should have a **controller** named `PostsController` in `app/Http/Controllers/Rest/`, and a **model** named `Post` in `app/`.

### But there's a command for this!

I've created an _Artisan_'s command that helps us to create these controllers and models.
To create the controller and the model for a database table, simply execute the following command:
```bash
php artisan api:table [table_name|model_name] 
``` 

You can also specify your *fillables* fields with the `--fillables` option, and the relations with the `--relations` options.

- `--fillables` option is just a list of fields, seperated by a comma (`,`),
- `--relations` option is a list of relations, also seperated by a comma (`,`).
A "relation" is a string, that can take 2 forms:
    - `<hasMany|hasOne|belongsTo|belongsToMany> <related_model> [<function_name>]`, where `function_name` allows you to define a custom function name.
    - `<function_name>`. This form creates an empty function. This can be useful if you want, for example, to use another relation method than the supported ones here.

_**Note**:  If you don't specify the function name option (`<function_name>`), the name will be your related model (`<related_model>`) in snake case, in plural form for `hasMany` and `belongsToMany` methods, and in singular form for `hasOne` and `belongsTo` methods._

#### Example:

```bash
php artisan api:table posts --fillables=title,content,user_id --relations="belongsTo User author, hasMany Comment"
```
<u>**Important**: Don't forget the quotes for the `--relations` options!</u>

This example will create the following files:

##### Controller:
`app/Http/Controllers/Rest/PostsController.php`
```php
<?php

namespace App\Http\Controllers\Rest;

class PostsController extends ApiController
{

}
```
##### Model:
`app/Post.php`

```php
<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $timestamps = true;
    protected $fillable = ['title', 'content', 'user_id'];

    
    public function author(){
        return $this->belongsTo('App\User');
    }


    public function comments(){
        return $this->hasMany('App\Comment');
    }
}
```

*<u>Upcoming features</u>: `--timestamps`, `--softdeletes`, `--hidden`, `--dates`*.

Once you've done the same for `users` and `comments` table, you're ready to use your REST API. For example, you can call these URLs:

```http
http://myserver.com/api/users
http://myserver.com/api/posts
http://myserver.com/api/comments

http://myserver.com/api/posts/4
http://myserver.com/api/posts/4/author
http://myserver.com/api/posts/4/comments
http://myserver.com/api/posts/4/comments/3
```