# Présentation
Ce composant est en charge 
*  du "fitting" de modèles de prédiction d'affluence sur réception d'évènements sur une queue RabbitMQ 
*  de la prédiction d'affluence sur appel à une API Rest

L'API Rest est codée avec le framework Flask et les modèles sont écrits dans le langage R.

# Procédure d'installation
1. installer apache2 avec mod_wsgi: sudo apt-get install libapache2-mod-wsgi-py3
2. cloner le dépôt et se déplacer dans le dossier analyseur
3. créer un dossier ici [R/Engine/data](./R/Engine/data)
```bash 
mkdir model
```
4. installer anaconda, créer un environnement (à partir du dossier [Python](./Python)) :
```bash 
conda env create -f environment.yml
```
5. installer R et les librairies forecast, anytime , lubridate et plotly
6. Pour la partie fitting du modèle
   1. lancer une session screen
   2. se placer dans le dossier  [Python](./Python)
   3. activer l’environnement: 
   ```bash 
   source activate analyseur
   ```
   4. Adapter les logins dans le [script](./Python/RMQ_receiver.py)
   5. lancer le consomateur RabbitMQ : 
   ```bash 
   python RMQ_receiver.py
   ```
7. Pour la partie prédiction 
   1. copier les fichiers du dossier [/devops/apache2](./devops/apache2) dans /etc/apache2/sites-available/ et modifier les chemins spécifiés dans ces fichiers
   2. lancer un script : 
   ```bash 
   bash fix_perm.sh
   ```
   3. Adapter les logins dans le [script](./Python/DataLoader.py) et [ici](./R/Engine/src/forecasterService.R) pour plotly 
   4. relancer apache : 
   ```bash 
   sudo service apache2 restart
   ```

# Installation d'anaconda :  
pour windows :
https://conda.io/docs/user-guide/install/windows.html
https://docs.anaconda.com/anaconda/install/windows

pour linux :
https://gist.github.com/mGalarnyk/05e4147b7fdfe4f94863e693644b43d9

