# Présentation
Le logiciel d’interprétation des flux vidéo est responsable du comptage des affluences et des notifications mail en cas d’alerte programmée par l’utilisateur. Ce logiciel est écrit en python, et peut tourner sur n’importe quelle machine disposant des technologies mentionnées ci-dessous.

# Liste des technologies nécessaires
1. Python 3.6, environnement Anaconda conseillé (à télécharger sur https://docs.anaconda.com/anaconda/install/windows). En cas d’utilisation d’anaconda, créer un environnement virtuel ainsi : ```conda create --no-default-packages -n <name> pip python=3.6 ``` 

et l’activer via la commande activate ```bash <name>```.

2. Gestionnaire de paquets PIP, version 10 minimum

3. API Google TensorFlow (pour CPU ou GPU). Pour la version GPU, il faut disposer d’une carte graphique NVIDIA possédant une compute capability >= 3.0, de l’API CUDA 9.0 de NVIDIA et des outils CUDNN mis à disposition par NVIDIA (version 7.0 pour CUDA 9.0)

4. Il faut également suivre les instructions d’installation de l’API MS COCO utilisée pour la reconnaissance de formes : 
https://github.com/matterport/Mask_RCNN#user-content-installation


Une fois tous les outils installés, l’interpréteur peut à présent être lancé en exécutant le script python receiveStream.py localisé dans le répertoire rabbit_mq\interpreter_queue du projet.
Ceci peut être fait à partir de la commande suivante : 
```python receiveStream.py``` 

Le projet dispose également d’un fichier .INI de configuration permettant de spécifier diverses options (nombre maximal de flux différents à traiter pour l’interpréteur, fréquence de mise à jour des modèles statistiques utilisés par les analyseurs pour chaque flux).

## Installation d'anaconda :  
pour windows :
https://conda.io/docs/user-guide/install/windows.html
https://docs.anaconda.com/anaconda/install/windows

pour linux :
https://gist.github.com/mGalarnyk/05e4147b7fdfe4f94863e693644b43d9

Si vous pouvez, passer par le sous-système linux pour pouvoir utiliser un bash, zshell ou autre mais attention, vous n'aurez pas de support GPU (as stated here https://askubuntu.com/questions/935735/does-ubuntu-for-windows-have-gpu-support)