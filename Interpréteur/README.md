# Anaconda
## Installer anaconda 
pour windows :
https://conda.io/docs/user-guide/install/windows.html
https://docs.anaconda.com/anaconda/install/windows

pour linux :
https://gist.github.com/mGalarnyk/05e4147b7fdfe4f94863e693644b43d9

Si vous pouvez, passer par le sous-système linux pour pouvoir utiliser un bash, zshell ou autre mais attention, vous n'aurez pas de support GPU (as stated here https://askubuntu.com/questions/935735/does-ubuntu-for-windows-have-gpu-support)

## Env Conda
1. créer un env conda pour le projet

```bash
conda create --no-default-packages -n <name> pip python=3.6
```

2. activer l'env (à faire à chaque fois qu'on ouvre un shell)
https://conda.io/docs/user-guide/tasks/manage-environments.html#activating-an-environment

# Mask RCNN

Suivre les instructions de leur [README](https://github.com/matterport/Mask_RCNN) (ne pas oublier d'installer COCO et ne pas faire le 4.)

Attention, par défaut, tensorflow (version cpu) est installé.

Si vous voulez la version gpu:
1. désinstaller tensorflow
```bash
pip uninstall tensorflow
```

2. installer cuda, cudnn
https://www.tensorflow.org/install/install_windows

3. installer la version gpu (ne pas oublier d'activer l'env)
```bash
pip install tensorflow-gpu
```

# Demo
lancer [demo.py](./Mask_RCNN/samples/demo.py)
```bash
python demo.py
```
ou le notebook
```bash
jupyter notebook
```
# Me poser des questions !!
