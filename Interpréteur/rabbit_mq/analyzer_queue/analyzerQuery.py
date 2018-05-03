
#!/usr/bin/env python
from time import sleep

import pika
import json
from itertools import cycle

# Class used to push analyzer model update queries
class AnalyzerQuery:
    # Constructor
    def __init__(self, fluxId):
        self.fluxId = fluxId
        self.credentials = pika.PlainCredentials('UTILISATEUR_RABBITMQ', 'MOT_DE_PASSE_RABBITMQ')
        self.connection = pika.BlockingConnection(pika.ConnectionParameters(
            host='HOSTNAME_RABBITMQ',
            port=3306,       #default 3306
            credentials=self.credentials))
        self.channel = self.connection.channel()

    # Pushes model update query to rabbitMQ queue
    def pushQueue(self):
        channel = self.connection.channel()
        channel.queue_declare(queue='analyzer_stream')
        data = {
                'action':'update_model',
                'donnees':{
                    "fluxid" : str(self.fluxId)
                }
        }
        message = json.dumps(data)
        channel.basic_publish(exchange='',
                        routing_key='analyzer_stream',
                        body=message)
    
    # Destructor - closes current connection
    def __del__(self):
        self.connection.close()
