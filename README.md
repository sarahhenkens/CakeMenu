# CakeMenu plugin

## Requirements

TODO: Write this section

## Installation

_[Manual]_

* Download this: http://github.com/jellehenkens/CakeMenu/zipball/master
* Unzip that download.
* Copy the resulting folder to app/Plugin
* Rename the folder you just copied to CakeMenu

_[GIT Submodule]_

In your app directory type:
```
git submodule add git://github.com/jellehenkens/CakeMenu.git Plugin/CakeMenu
git submodule init
git submodule update
```

_[GIT Clone]_

In your plugin directory type
`git clone git://github.com/jellehenkens/CakeMenu.git CakeMenu`

## Usage

In `app/Config/bootstrap.php` add: `CakePlugin::load('CakeMenu')`;

## Show me how to print a basic menu

```php
$this->CakeMenu->create('nav');

$this->CakeMenu->add('nav', 'home', 'Homepage', array('controller' => 'pages', 'action' => 'home'));
$this->CakeMenu->add('nav', 'blog', 'Blog', array('controller' => 'blog_posts', 'action' => 'index'));
$this->CakeMenu->add('nav', 'support', 'Support', 'http://help.me');

$this->CakeMenu->setActive('nav', 'blog');

echo $this->CakeMenu->render('nav');
```

Output:
```html
<ul class="menu">
    <li>
        <a href="/pages/home">Homepage</a>
    </li>
    <li class="active">
        <a href="/blog_posts">Blog</a>
    </li>
    <li>
        <a href="http://help.me">Support</a>
    </li>
</ul>
```