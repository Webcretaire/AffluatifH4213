#!/usr/bin/env python

import pika
import json
import sys

sys.path.insert(0, '../../video')
sys.path.insert(0, '../../Mask_RCNN')
sys.path.insert(0, '../../utils')
import videoReader
import InstancesCounter
import dbInterract
import threading
from itertools import cycle
import configparser
import analyzerThread
from mrcnn import visualize
import datetime
import time
from mailsender import send_mail_alert

# RabbitMQ interpreter queue listener script

# RABBIT_MQ CREDENTIALS
credentials = pika.PlainCredentials('UTILISATEUR_RABBITMQ', 'MOT_DE_PASSE_RABBITMQ')
connection = pika.BlockingConnection(pika.ConnectionParameters(
       host='HOSTNAME_RABBITMQ',
       port=3306,       #default 3306
       credentials=credentials))

# Globals
channel = connection.channel()
count = 0
channel.queue_declare(queue='interpreter_stream')
streamList = []
lock = threading.Lock()
config = configparser.ConfigParser()
config.read('../../config.ini')
print("MAX STREAM IS " + str(config["GENERAL"]["MAX_STREAMS"]))
container = threading.Semaphore(int(config["GENERAL"]["MAX_STREAMS"]))

# Adds a new stream to the stream list, if not already included
def addStream(stream, stream_id):
    global streamList
    global count
    if [stream, stream_id] not in streamList :
        streamList.append([stream, stream_id])
        print("[*] Stream " + stream + " detected")
        count = count + 1
    else:
        container.release()

# Processes all streams in the stream list, until it is empty
def imageProcessing():
    print("[*] Waiting for new Streams")
    global streamList
    global count
    global instance_counter
    db_interract = dbInterract.DBInterract()
    db_interract.open_connection()
    instance_counter = InstancesCounter.InstanceCounter.getGlobalInstance()
    currentElement = 0
    while 1:
        try:
            lock.acquire()
            if len(streamList) == 0:        # Stops if the streamlist becomes empty
                break
            if currentElement + 1 >= len(streamList):
                currentElement = 0
            else:
                currentElement = currentElement + 1
            nextStream = streamList[currentElement][0]
            nextStreamId = streamList[currentElement][1]
            if nextStream:
                db_interract.set_stream_not_waiting_for_interpreter(nextStreamId)
                isActif = db_interract.get_flux_actif(nextStreamId)     
                if releaseIfNone(isActif, currentElement, not isActif) == -1:
                    continue
                vr = videoReader.VideoReader(nextStream)
                img = vr.readImage()        
                if releaseIfNone(img, currentElement, img is None) == -1:   # the stream is down
                    continue
                class_list = db_interract.get_classes_from_stream(nextStreamId)
                print(class_list)
                if releaseIfNone(class_list, currentElement, not class_list ) == -1:
                    continue
                affluence_dict = instance_counter.getNumberOfInstances(img, class_list)
                # calculate sum of affluences
                affluenceSum = getAffluenceSum(affluence_dict)
                checkAdvancedAlert(nextStreamId, affluenceSum, db_interract, vr)
                sendImgDataIfNeeded(vr, db_interract, nextStreamId, affluenceSum)
                sendAffluenceDictToDb(affluence_dict, nextStreamId, db_interract)
        finally:
            lock.release()

# Checks if the stream is linked to alerts, and sends an email to all concerned users if an intruder is detected
def checkAdvancedAlert(streamId, sommeAffluence, db_interract, vr):
    alerts = db_interract.getAlertsFromStream(streamId)
    streamDescription = db_interract.getStreamDescription(streamId)
    emails = db_interract.getStreamUsersEmails(streamId)
    for i in range(len(alerts)):
        alert = alerts[i]
        alertBeginning = (datetime.datetime.min + alert[2]).time()
        alertEnd = (datetime.datetime.min + alert[3]).time()
        currentDate = datetime.datetime.now()
        currentTime = datetime.datetime.time(currentDate)
        if currentTime >= alertBeginning and currentTime <= alertEnd and sommeAffluence > 0:
            dateLastAlert = db_interract.getLastDateAlert(alert[0])
            if (dateLastAlert is None) or (dateLastAlert < currentDate.date()):
                vr.writeImage("img.png")
                for j in range(len(emails)):
                    print(" *** ALERT *** INTRUDER DETECTED IN " + streamDescription + " *** SENDING EMAIL TO " + emails[j][0])
                    send_mail_alert(emails[j][0], currentDate, "img.png", streamDescription)
                db_interract.setLastDateAlert(alert[0])

# Sends an image to DB if a maximum of affluence is detected
def sendImgDataIfNeeded(vr, db_interract, streamId, sommeAffluence):
    bestAffluenceRow = db_interract.getMaxAffluenceInfos(streamId)
    if (bestAffluenceRow is None) or (sommeAffluence >= bestAffluenceRow[1]) or (bestAffluenceRow[3] is None):
        vr.writeImage("img.png")
        with open("img.png", "rb") as binary_file:
            db_interract.send_image_data(streamId, binary_file.read())

# Calculate sum of all detected affluence classes
def getAffluenceSum(affluence_dict):
    affluenceSum = 0
    for key in affluence_dict:
        affluenceSum += affluence_dict[key]
    return affluenceSum

# Sends affluence data to DB
def sendAffluenceDictToDb(affluence_dict, nextStreamId, db_interract):
    for key in affluence_dict:
        db_interract.send_affluence_data(nextStreamId, key[0], affluence_dict[key])
        classe_affluence = 'unknown'
        try:
            classe_affluence = instance_counter.get_classes_names()[key[0]]
        except:
            classe_affluence = 'unknown'
        print("[*] Affluence of " + str(classe_affluence) + " is " + str(affluence_dict[key]))     

# Checks if input data is valid
def releaseIfNone(item, currentElement, boolean):
    global count
    global streamList
    if boolean :
        streamList.remove(streamList[currentElement])
        count -= 1
        try:
            container.release()
            print("[*] Current stream number increased.")
        except ValueError:
            print("[*] Current stream number fulled, skipping.")
        return -1
    return 1

# RabbitMQ receive stream callback
def callback(ch, method, properties, body):
    """
        RabbitMQ callback
    """
    data = json.loads(body)
    if data['action'] == 'ajout_source':
        print("[x] Received {} and {} ".format(data['donnees']['url'], data['donnees']['id']))
        try:
            lock.acquire()
            print("count : " + str(count))
            if container._value + 1 > int(config["GENERAL"]["MAX_STREAMS"]) :
                 ch.basic_nack(delivery_tag=method.delivery_tag, requeue=True)
            else:
                ch.basic_ack(delivery_tag=method.delivery_tag)
        finally:
            lock.release()
        if container.acquire():
            print("[*] Current stream number decreased.")
        try:
            lock.acquire()   
            if count == 0:
                t = threading.Thread(target=imageProcessing, args=())
                t.start()               
                addStream(data['donnees']['url'], data['donnees']['id'])
                print("[*] Thread created")
                t2 = threading.Thread(target=analyzerThread.updateModels, args=(lock, streamList, config,))
                t2.start()
            else:
                addStream(data['donnees']['url'], data['donnees']['id'])
        finally:
            lock.release()
    else:
        ch.basic_nack(delivery_tag=method.delivery_tag, requeue=True)


channel.basic_consume(callback,
                      queue='interpreter_stream',
                      no_ack=False)

print(' [*] Waiting for messages. To exit press CTRL+C')
channel.start_consuming()
