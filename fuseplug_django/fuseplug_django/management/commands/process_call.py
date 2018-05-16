from django.core.management.base import BaseCommand, CommandError

import json
import pika
import pprint
#from polls.models import Question as Poll

class Command(BaseCommand):
    help = 'Closes the specified poll for voting'

    def add_arguments(self, parser):
        parser.add_argument(
            '--run_once',
            help='run this once, rather than all the time',
        )

    def handle(self, *args, **options):

        connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
        channel = connection.channel()
        channel.queue_declare(queue='fuseplug_python', durable=True)
        def callback(ch, method, properties, body):
            """docstring for callback"""
            pprint.pprint('-------------------- new message --------------')
            body_obj = json.loads(body)
            pprint.pprint(body_obj)
            pprint.pprint('--------data ------')
            command_obj = body_obj['data']['command']
            pprint.pprint(command_obj)
            if options['run_once'] == 'true':
                exit()
        channel.basic_consume(callback, queue='fuseplug_python', no_ack=True)
        channel.start_consuming()
