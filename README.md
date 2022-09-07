# sf6m2iexo
Anthony PARIS Formateur M2I

/cours/ (partie cours SF6)

Vous reprenez le dossier project à placer dans votre dossier wamp/www ou mamp/htdocs
modifier le fichier .env la ligne database avec votre base de données à créer.
DATABASE_URL="mysql://root:@localhost:3306/sf6m2iexo?serverVersion=5.7&charset=utf8mb4"
lancer les commandes suivantes dans l'ordre 
- php bin/console doctrine:database:create
- composer install
- yarn install
- yarn encore dev
- php bin/console doctrine:migrations:migrate
- php bin/console doctrine:fixtures:load
- php bin/console cache:clear


Si vous avez un souci avec les migrations
Vous reprennez mes entity
vous supprimez l'ensemble des fichiers dans le dossier migrations
vous videz la table migrations de la base
vous relancez la commande php bin/console make:migration
et la commande php bin/console doctrine:migrations:migrate
puis php bin/console doctrine:fixtures:load