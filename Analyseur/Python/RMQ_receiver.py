#!/usr/bin/env python
import pika
import json

from DataLoader import DataLoader
from REngineRunner import REngineRunner

R_ANALYSER_FILE = "calculatorService.R"

credentials = pika.PlainCredentials('xxx', 'yyy')

def callback(ch, method, properties, body):
    data = json.loads(body)
    if data['action'] == 'update_model':
        print("[x] Received {}".format(data['donnees']['fluxid']))
        ch.basic_ack(delivery_tag=method.delivery_tag)
        DataLoader.writeJson(int(data['donnees']['fluxid']))
        r = REngineRunner(R_ANALYSER_FILE, False)
        r.run_update(int(data['donnees']['fluxid']))
    else:
        ch.basic_nack(delivery_tag=method.delivery_tag, requeue=True)


while True:
    try:
        connection = pika.BlockingConnection(pika.ConnectionParameters(
            host='zzz',
            port=3306,
            credentials=credentials,
            blocked_connection_timeout=60 * 30))

        channel = connection.channel()
        channel.queue_declare(queue='analyzer_stream')
        channel.basic_consume(callback,
                              queue='analyzer_stream',
                              no_ack=False)
        channel.start_consuming()
    except Exception as e:
        print(e)
