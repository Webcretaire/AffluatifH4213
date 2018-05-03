# Affluatif - Site Web #

Ce repo contient le site web qui fait office d'interface pour le projet, codé en PHP. La documentation du code se trouve dans le dossier [html/docs](html/docs) au format HTML.

## Installation ##

La configuration recommandée est un serveur LAMP (linux, apache, mysql, php), dont la procédure d'installation est disponible [ici](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-16-04#step-2-install-mysql)

Le site nécessite impérativement [PHP](http://php.net/) de version >= 7, une base de données [MySQL](https://www.mysql.com/fr/), [Composer](https://getcomposer.org/) (installé globalement ou téléchargé localement) et [RabbitMQ](https://www.digitalocean.com/community/tutorials/how-to-install-and-manage-rabbitmq#managing-rabbitmq)

Pour installer en développement :

```bash
git pull
composer install
```

Pour déployer en production :

```bash
git pull
composer install --no-dev --optimize-autoloader
rm -rf Resources_Do_Not_Deploy
```

Les fichiers de configuration Apache (virtualhost) se trouvent sur le dépôt git, dans le dossier 
[Resources_Do_Not_Deploy/Apache](Resources_Do_Not_Deploy/Apache).

Ces fichiers doivent être déployés dans le dossier /etc/apache2/sites-available et activés à l’aide des commandes :
```bash
a2ensite rabbitmq.conf
a2ensite smart.conf
a2ensite smart-le-ssl.conf
service apache2 restart
```

Une fois le virtualhost correctement configuré, utiliser [certbot](https://certbot.eff.org/lets-encrypt/ubuntuxenial-apache) pour obtenir un certificat SSL 

Pour mettre en place l'InterpreterManager qui relance les flux qui se sont arrêtés, initialiser la tâche cron : 

```bash
crontab crontab.txt
```

## Création de la Base de Donnée ##

La base de donnée peut être créée avec le script suivant : [DB_Creation_Script_Affluatif.sql]( Resources_Do_Not_Deploy/DB_Creation_Script_Affluatif.sql)
