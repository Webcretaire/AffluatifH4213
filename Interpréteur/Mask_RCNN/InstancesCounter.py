
import os
import sys
import random
import skimage.io

# Root directory of the project
import time

ROOT_DIR = os.path.abspath("../../Mask_RCNN")  # ne PAS changer ce chemin

# Import Mask RCNN
sys.path.append(ROOT_DIR)  # To find local version of the library
from mrcnn import utils
import mrcnn.model as modellib
from mrcnn import visualize
# Import COCO config
sys.path.append(os.path.join(ROOT_DIR, "samples/coco/"))  # To find local version
import coco
from pympler.tracker import SummaryTracker
tracker = SummaryTracker()
# Directory to save logs and trained model
MODEL_DIR = os.path.join(ROOT_DIR, "logs")

# Local path to trained weights file
COCO_MODEL_PATH = os.path.join(ROOT_DIR, "mask_rcnn_coco.h5")
# Download COCO trained weights from Releases if needed
if not os.path.exists(COCO_MODEL_PATH):
    utils.download_trained_weights(COCO_MODEL_PATH)

class InferenceConfig(coco.CocoConfig):
    # Set batch size to 1 since we'll be running inference on
    # one image at a time. Batch size = GPU_COUNT * IMAGES_PER_GPU
    GPU_COUNT = 1
    IMAGES_PER_GPU = 1

config = InferenceConfig()
config.display()

instanceCounterGlobalInstance = None

# Class used to count instances on an image
class InstanceCounter:

    def __init__(self):
        # Create model object in inference mode.
        self.model = modellib.MaskRCNN(mode="inference", model_dir=MODEL_DIR, config=config)

        # Load weights trained on MS-COCO
        self.model.load_weights(COCO_MODEL_PATH, by_name=True)

        # COCO Class names
        # Index of the class in the list is its ID. For example, to get ID of
        # the teddy bear class, use: class_names.index('teddy bear')
        self.class_names = ['BG', 'person', 'bicycle', 'car', 'motorcycle', 'airplane',
                       'bus', 'train', 'truck', 'boat', 'traffic light',
                       'fire hydrant', 'stop sign', 'parking meter', 'bench', 'bird',
                       'cat', 'dog', 'horse', 'sheep', 'cow', 'elephant', 'bear',
                       'zebra', 'giraffe', 'backpack', 'umbrella', 'handbag', 'tie',
                       'suitcase', 'frisbee', 'skis', 'snowboard', 'sports ball',
                       'kite', 'baseball bat', 'baseball glove', 'skateboard',
                       'surfboard', 'tennis racket', 'bottle', 'wine glass', 'cup',
                       'fork', 'knife', 'spoon', 'bowl', 'banana', 'apple',
                       'sandwich', 'orange', 'broccoli', 'carrot', 'hot dog', 'pizza',
                       'donut', 'cake', 'chair', 'couch', 'potted plant', 'bed',
                       'dining table', 'toilet', 'tv', 'laptop', 'mouse', 'remote',
                       'keyboard', 'cell phone', 'microwave', 'oven', 'toaster',
                       'sink', 'refrigerator', 'book', 'clock', 'vase', 'scissors',
                       'teddy bear', 'hair drier', 'toothbrush']
                       
    # Returns number of all instances specified in class_list
    def getNumberOfInstances(self, image, class_list):
        """
        :param image: ndarray
            The different colour bands/channels are stored in the
            third dimension, such that a grey-image is MxN, an
            RGB-image MxNx3 and an RGBA-image MxNx4.
        :param classToCount: ndarray
            List of all the classes to search in the image
        :return: map
            return a map (dictionary) containing the number of instances for each classes
        """
        results = self.model.detect([image], verbose=1)
        r = results[0]

        # [plt, img2] = visualize.getAnalyzedImage(image, r['rois'], r['masks'], r['class_ids'], 
        #                     self.class_names, r['scores'])

        self.class_names, r['scores']
        class_counts = {}

        for i in class_list:
            class_counts[i] = 0

        for i in r['class_ids'] :
            for j in class_list :
                if j == i :
                    class_counts[j] += 1

        return class_counts

    @staticmethod
    def getGlobalInstance():
        global instanceCounterGlobalInstance
        if instanceCounterGlobalInstance is None:
            instanceCounterGlobalInstance = InstanceCounter()
        return instanceCounterGlobalInstance

    def get_classes_names(self):
        return self.class_names



