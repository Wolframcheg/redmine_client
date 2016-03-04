#!/bin/sh
APACHEUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data' | grep -v root | head -1 | cut -d\  -f1`

setfacl -R -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX app/cache app/logs web/
setfacl -dR -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX app/cache app/logs web/


#echo "$1 parameters";

action="$1"

if [ -z "$1" ]; then
    action="-help"
fi

install_run()
{
    composer install
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate -n
    php bin/console cache:clear
    php bin/console cache:clear -e prod
    npm install
    ./node_modules/.bin/bower install --allow-root
    ./node_modules/.bin/gulp
}


update_run ()
{
    composer install
    php bin/console doctrine:database:drop --force
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate -n
    php bin/console cache:clear
    php bin/console cache:clear -e prod
    npm install
    ./node_modules/.bin/bower install --allow-root
    ./node_modules/.bin/gulp
}

case "$action" in
    -install)
        install_run
        ;;
    -update)
        update_run
        ;;
    -help)
        echo "Available options"
        echo "-install : Install project"
        echo "-update : Update project"
        ;;
    *)
        echo "undefined action"
        ;;
esac