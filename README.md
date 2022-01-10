<p align="center">
  <img src="https://github.com/moonmoon/moonmoon/raw/master/custom/img/moonmoon%40128w.png">
</p>


moonmoon [![Build Status](https://travis-ci.org/moonmoon/moonmoon.svg?branch=master)](https://travis-ci.org/moonmoon/moonmoon)
========

Moonmoon is a web based aggregator similar to planetplanet.
It can be used to blend articles from different blogs with same interests into a single page.

Moonmoon is simple: it only aggregates feeds and spits them out in one single page.
It does not archive articles, it does not do comments nor votes.

Requirements
------------
You will need a web hosting with at least PHP 7.4 (8.0 and 8.1 are supported too).

If you are installing moonmoon on a Linux private server (VPS, dedicated host),
please note that you will need to have installed the following packages:
`php-curl`, `php-mbstring`, `php-xml`, `php-xmlreader`.

Installing
----------

Installation steps (shared hosting or virtual / dedicated server) can be found
[in the wiki](https://github.com/moonmoon/moonmoon/wiki/How-to-install).

Docker images are also available in [moonmoon/docker-images](https://github.com/moonmoon/docker-images).
Theses images are probably not production-ready but should work for manual testing.

Contributing
------------

You want to contribute to moonmoon? Perfect! [We wrote some guidelines to help you
craft the best Issue / Pull Request possible](https://github.com/moonmoon/moonmoon/blob/master/CONTRIBUTING.md),
don't hesitate to take a look at it :-)

License
-------

Moonmoon is free software and is released under the [BSD license](https://github.com/moonmoon/moonmoon/blob/master/LICENSE).
Third-party code differently licensed is included in this project, in which case mention is always made of
the applicable license.

[The logo](https://github.com/moonmoon/moonmoon/raw/master/custom/img/moonmoon.png) was designed by [@rakujira](https://twitter.com/rakujira).

Configuration options
---------------------
After installation, configuration is kept in a YAML formatted `custom/config.yml`:

```%yaml
url: http://planet.example.net  # your planet base URL
name: My Planet                 # your planet front page name
locale: en                      # front page locale
items: 10                       # how many items to show
refresh: 240                    # feeds cache timeout (in seconds)
cache: 10                       # front page cache timeout (in seconds)
cachedir: ./cache               # where is cache stored
postmaxlength: 0                # deprecated
shuffle: 0                      # deprecated
nohtml: 0                       # deprecated
categories:                     # only list posts that have one
                                # of these (tag or category)
debug: false                    # debug mode (dangerous in production!)
checkcerts: true                # check feeds certificates
```
