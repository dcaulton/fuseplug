import json
import pprint
import re
import requests
import traceback

from django.core.management.base import BaseCommand, CommandError
import pika

from fuseplug_django.models.models import *

class Command(BaseCommand):
    help = 'processes a fuseplug call'

    def do_get_request(self, action, call):
        data_mapping = DataMapping.objects.filter(operation_action_id=action.id, object_type_being_created='url')[0]
        target_url = data_mapping.transform(call.request_data, action)
        headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
        debug_data = {}
        debug_data['called_url'] = target_url
        debug_data['payload_to_url'] = ''

        response = requests.get(target_url, headers=headers)

        debug_data['response_code'] = response.status_code
        call.debug_info = json.dumps(debug_data)
 
        return response.text

    def do_post_request(self, action, call):
        print('doing post request')
        return 'did a post request'

    def get_queue_name(self, options):
        return options['queue_name']
        
    def add_arguments(self, parser):
        parser.add_argument(
            '--run_once',
            help='run this once, rather than all the time',
        )
        parser.add_argument(
            '--queue_name',
            help='the queue name to use',
            default='fuseplug_python',
        )

    def process_request(self, super_call_id):
        super_call = SuperCall.objects.get(pk=super_call_id)
        call = Call.objects.filter(super_call_id=super_call.id).order_by('-created_at')[0]
        action = OperationAction.objects.get(pk=call.operation_action_id)
        rule = OperationRule.objects.get(pk=action.operation_rule_id)
        try:
            if action.http_verb == 'GET':
                data = self.do_get_request(action, call)
            elif action.http_verb == 'POST':
                data = self.do_post_request(action, call)

            if len(data) > 4096:
                data = data[0:4095]
            call.response_data = data
            call.status_code = 'COMPLETE'
            call.save()
        except Exception as unfortunate:
            error_data = {'message': str(unfortunate), 'error_class': unfortunate.__class__.__name__}
            error_string = json.dumps(error_data) 
            error_string += '------ TRACEBACK: ' + traceback.format_exc()
            call.error_messages = error_string
            call.status_code = 'FAILED'
            call.save()
            super_call.status = 'FAILED'
            super_call.save()
        
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
            if 'from' in initial_payload['payload']:
                from_name = initial_payload['payload']['from']
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
        queue_name = self.get_queue_name(options)
        connection = pika.BlockingConnection(pika.ConnectionParameters('localhost'))
        channel = connection.channel()
        channel.queue_declare(queue=queue_name, durable=True)
        def callback(ch, method, properties, body):
            super_call_id = self.get_super_call_id(body)
# NEW LOGIC
            self.process_request(super_call_id)
            super_call = SuperCall.objects.get(pk=super_call_id)
            call = super_call.get_next_call()
            if call:
                print("right nere we will be requeueing the body on queue {0}".format(queue_name))
            exit()
#### END NEW LOGIC
# OLD LOGIC, REMOVE SOON
#            call = self.get_latest_call(super_call_id)
#            self.do_the_call(call)
#
#            if call.status_code == 'COMPLETE':
#                super_call = SuperCall.objects.get(pk=super_call_id)
#                call = super_call.get_next_call()
#                if call:
#                    print('get the queue info and dispatch the next call')
##### END OLD LOGIC
            if options['run_once'] == 'true':
                exit()
        channel.basic_consume(callback, queue=queue_name, no_ack=True)
        channel.start_consuming()
