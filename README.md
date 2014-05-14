what-to-eat
===========

Symfony project to help decide what to eat

To run first add "192.168.56.103  symfony.dev" to hosts file on host machine.

Setup vm by running "vagrant up" from root directory, then:

 - ssh into vm
 - cd to "/var/www/symfony.dev"
 - run composer update
 - then clear cache files owned by ssh user by running "sudo rm -rfv /tmp/symfony.dev/"

To use site go to http://symfony.dev/

To run tests cd to "/var/www/symfony.dev"
then run "bin/phpunit -c app/"
