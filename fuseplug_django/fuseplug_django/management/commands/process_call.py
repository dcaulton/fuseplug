from django.core.management.base import BaseCommand, CommandError

import pika
#from polls.models import Question as Poll

class Command(BaseCommand):
    help = 'Closes the specified poll for voting'

    def add_arguments(self, parser):
#        parser.add_argument('poll_id', nargs='+', type=int)
        pass

    def handle(self, *args, **options):
        connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
        channel = connection.channel()
        channel.queue_declare(queue='fuseplug_python', durable=True)
        def callback(ch, method, properties, body):
            """docstring for callback"""
            print("[x] Received: %r" % (body,))
        channel.basic_consume(callback, queue='fuseplug_python', no_ack=True)
        channel.start_consuming()
