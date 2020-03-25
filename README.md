# PROJECT SETUP

The first thing we need is to install Docker on your computer.

You will need:

Docker &&
Docker-compose

See https://docs.docker.com/install/ & https://docs.docker.com/compose/install/ for full documentation.

After those are installed you can open up terminal

cd into your favorite directory where you like to store your projects and run the following:

    git clone git@github.com:joscraw/vehicle-history.git
    
    copy .env.dist to .env or (.env.local for local env file) and modify any params specific to your local machine.
    You may need to modify the LOCAL_USER depending on your OS. Run id -u && id -g to determine your user id and group id.

    docker-compose build 
    
    docker-compose up -d
    
    (to stop containers: docker-compose stop)
    
    docker-compose exec php composer install 
    
    docker-compose exec php php bin/console doctrine:schema:create
    