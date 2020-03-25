# PROJECT SETUP

The first thing we need is to install Docker on your computer.

You will need:

Docker &&
Docker-compose

See https://docs.docker.com/install/ & https://docs.docker.com/compose/install/ for full documentation.

After those are installed you can open up terminal

cd into your favorite directory where you like to store your projects and run the following:

    git clone git@github.com:joscraw/vehicle-history-test.git
    
    docker-compose build 
    
    docker-compose up -d
    
    docker-compose exec php composer install 
    
    docker-compose exec php php bin/console doctrine:schema:create
    
    (to stop containers if needed: docker-compose stop)
    