import json
import pprint
import re
import requests

from django.core.management.base import BaseCommand, CommandError
import pika

from fuseplug_django.models.models import Call, SuperCall

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
            debug_data = {'called_url': target_url, 'payload_to_url': ''}
            response = requests.get(target_url, headers=headers)
            call.response_data = response.json() 
            debug_data['response_code'] = response.status_code
            call.debug_info = json.dumps(debug_data)
            call.status_code='COMPLETE'
            call.save()
        except Exception as no_way:
            error_data = {'message': str(no_way), 'error_class': no_way.__class__.__name__}
            call.error_messages = json.dumps(error_data)
            call.status_code = 'FAILED'
            call.save()

    def handle(self, *args, **options):
        connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
        channel = connection.channel()
        channel.queue_declare(queue='fuseplug_python', durable=True)
        def callback(ch, method, properties, body):
            message_body = body
            super_call_id = self.get_super_call_id(body)
            call = self.get_latest_call(super_call_id)
            self.do_the_call(call)
            if call.status_code == 'COMPLETE':
                super_call = SuperCall.objects.get(pk=super_call_id)
                call = super_call.get_next_call()
                if call:
                    print('get the queue info and dispatch the next call')
########### here's a thought: if we keep the thing we dequeued, we can just enqueue that again, then even laravel could service it if we want.  Actually, that should work for celery too.  We might need to hack date fields and sequence numbers or something
            if options['run_once'] == 'true':
                exit()
        channel.basic_consume(callback, queue='fuseplug_python', no_ack=True)
        channel.start_consuming()
