#!/usr/bin/env python
from time import sleep

import pika
import json
from itertools import cycle

# Manual script to feed database during development process

credentials = pika.PlainCredentials('UTILISATEUR_RABBITMQ', 'MOT_DE_PASSE_RABBITMQ')
connection = pika.BlockingConnection(pika.ConnectionParameters(
       host='HOSTNAME_RABBITMQ',
       port=3306,       #default 3306
       credentials=credentials))

channel = connection.channel()
# static DB streams
DB_STREAMS = [
    {
        "id" : "5",
        "url":"http://213.193.89.202/mjpg/video.mjpg"
    },
    {
        "id" : "4",
        "url":"http://mail.bekescsaba.hu:8080/mjpg/video.mjpg?webcam.jpg"
    },
    {
        "id" : "8",
        "url":"http://213.123.128.37/mjpg/video.mjpg"
    },
    {
        "id" : "7",
        "url":"http://195.196.36.242/mjpg/video.mjpg"
    },
    {
        "id" : "6",
        "url":"http://192.171.163.3/mjpg/video.mjpg"
    },
    {
        "id" : "1",
        "url":"http://62.152.76.62:8080/cgi-bin/viewer/video.jpg?r=1523463764"
    },
    {
        "id" : "12",
        "url":"http://213.157.112.2:8081/video2.mjpg"
    },
    {
        "id" : "14",
        "url":"http://213.157.112.2/ipcam/mjpeg.cgi"
    },
    {
        "id" : "15",
        "url":"http://213.157.112.66:8080/mjpg/video.mjpg"
    },
	{
		"id":"16",
		"url":"http://200.36.58.250/mjpg/video.mjpg"
	}
]
channel.queue_declare(queue='interpreter_stream')

for i in range(len(DB_STREAMS)):
    data = {
        'action':'ajout_source',
        'donnees':{}
    }
    data["donnees"]["id"] = DB_STREAMS[i]["id"]
    data["donnees"]["url"] = DB_STREAMS[i]["url"]
    message = json.dumps(data)
    channel.basic_publish(exchange='',
                      routing_key='interpreter_stream',
                      body=message)

connection.close()
