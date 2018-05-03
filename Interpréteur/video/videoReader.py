import cv2
import urllib
import numpy as np
import urllib.request
import time
# Class used to read video streams and write images
class VideoReader:
    # Constructors
    def __init__(self, url):
        self.url = url
    # Reads image from video stream and return it as a numpy array
    def readImage(self):
        try:
            print("[*] Fetching image from " + self.url)
            a = b = -1
            req = urllib.request.Request(
                self.url, 
                data=None, 
                headers={
                    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.47 Safari/537.36'
                }
            )
            stream = urllib.request.urlopen(req)
            bytes=b""
            while a==-1 or b==-1 :
                bytes += stream.read(1024)
                a = bytes.find(b'\xff\xd8')
                b = bytes.find(b'\xff\xd9')
                if a != -1 and b != -1:
                    jpg = bytes[a:b + 2]
                    bytes = bytes[b + 2:]
                    return np.asarray(cv2.imdecode(np.fromstring(jpg, dtype=np.uint8), cv2.IMREAD_COLOR)[:,:])
            return None
        except Exception as e:
            print("[*] WARNING Camera unavailable")
            return None

    # Reads image from video stream and write it to a file (preferably JPG or PNG)
    def writeImage(self, imageName):
        try:
            a = b = -1
            stream = urllib.request.urlopen(self.url)
            bytes=b""
            while a==-1 or b==-1 :
                bytes += stream.read(1024)
                a = bytes.find(b'\xff\xd8')
                b = bytes.find(b'\xff\xd9')
                if a != -1 and b != -1:
                    jpg = bytes[a:b + 2]
                    bytes = bytes[b + 2:]
                    cv2.imwrite(imageName, cv2.imdecode(np.fromstring(jpg, dtype=np.uint8), cv2.IMREAD_COLOR))
            return None
        except:
            print("[*] WARNING Camera unavailable")
            return None
