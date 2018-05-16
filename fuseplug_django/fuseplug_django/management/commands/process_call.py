import json
import pprint
import re
import requests

from django.core.management.base import BaseCommand, CommandError
import pika

from fuseplug_django.models.models import Call

class Command(BaseCommand):
    help = 'processes a fuseplug call'

    def add_arguments(self, parser):
        parser.add_argument(
            '--run_once',
            help='run this once, rather than all the time',
        )

    def print_body(self, body):
        pprint.pprint('-------------------- new message --------------')
        body_obj = json.loads(body)
        pprint.pprint(body_obj)
        pprint.pprint('--------data ------')
        command_obj = body_obj['data']['command']
        pprint.pprint(command_obj)

    def get_super_call_id(self, body):
        body_obj = json.loads(body)
        command_obj = body_obj['data']['command']
        id_regex = 'super_call_id\"\;i\:(\d+)(.*)$'
        match_object = re.search(id_regex, command_obj)
        if match_object:
            super_call_id = match_object.group(1)
            print('super_call_id is {0}'.format(super_call_id))
            return super_call_id

    def get_latest_call(self, super_call_id):
        calls = Call.objects.filter(super_call_id=super_call_id).order_by('-created_at')
        if calls:
            call = calls[0]
            return call

    def do_the_call(self, call):
        target_url = 'http://foaas.com/cool/'
        try:
            initial_payload = json.loads(call.request_data)
            from_name = 'Dave'
            if 'from' in initial_payload:
                from_name = initial_payload['from']
            target_url += from_name
            headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
            response = requests.get(target_url, headers=headers)
            call.response_data = response.json() 
            call.status_code='COMPLETE'
            call.save()
        except Exception as no_way:
            call.status_code='ERROR'
            call.save()

    def enqueue_next_call_or_close_out_supercall(self, call):
        print('building the next call or closing out the supercall')

    def handle(self, *args, **options):
        connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
        channel = connection.channel()
        channel.queue_declare(queue='fuseplug_python', durable=True)
        def callback(ch, method, properties, body):
            """docstring for callback"""
            self.print_body(body)
            super_call_id = self.get_super_call_id(body)
            call = self.get_latest_call(super_call_id)
            self.do_the_call(call)
            if call.status_code == 'COMPLETE':
                self.enqueue_next_call_or_close_out_supercall(call)
            if options['run_once'] == 'true':
                exit()
        channel.basic_consume(callback, queue='fuseplug_python', no_ack=True)
        channel.start_consuming()
