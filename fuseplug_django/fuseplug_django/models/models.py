# This is an auto-generated Django model module.
# You'll have to do the following manually to clean this up:
#   * Rearrange models' order
#   * Make sure each model has one field with primary_key=True
#   * Make sure each ForeignKey has `on_delete` set to the desired behavior.
#   * Remove `managed = False` lines if you wish to allow Django to create, modify, and delete the table
# Feel free to rename the models, but don't rename db_table values or field names.
from django.db import models


class Brand(models.Model):
    name = models.CharField(unique=True, max_length=255)

    class Meta:
        managed = False
        db_table = 'brands'


class Call(models.Model):
    operation_action = models.ForeignKey('OperationAction', models.DO_NOTHING)
    super_call = models.ForeignKey('SuperCall', models.DO_NOTHING)
    request_data = models.CharField(max_length=255)
    response_data = models.CharField(max_length=255, blank=True, null=True)
    error_messages = models.CharField(max_length=255, blank=True, null=True)
    status_code = models.CharField(max_length=255)
    created_at = models.DateTimeField(blank=True, null=True)
    updated_at = models.DateTimeField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'calls'

    @classmethod
    def create(cls, super_call, operation_action, input_data):
        call = cls(operation_action_id=operation_action.id,
            super_call_id=super_call.id,
            request_data=input_data,
            status_code='CREATED')
        call.save()
        return call
        

class Cronjob(models.Model):
    operation = models.ForeignKey('Operation', models.DO_NOTHING)
    schedule = models.CharField(max_length=255)
    created_at = models.DateTimeField(blank=True, null=True)
    updated_at = models.DateTimeField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'cronjobs'


class DataMappingDetail(models.Model):
    data_mapping = models.ForeignKey('DataMapping', models.DO_NOTHING)
    order = models.IntegerField()
    source_field = models.CharField(max_length=255)
    source_field_type = models.CharField(max_length=255)
    target_field = models.CharField(max_length=255)
    target_data_type = models.CharField(max_length=255)
    target_format_string = models.CharField(max_length=255)
    transform = models.CharField(max_length=255)
    skip_if_empty = models.IntegerField()
    default_value = models.CharField(max_length=255)

    class Meta:
        managed = False
        db_table = 'data_mapping_details'


class DataMapping(models.Model):
    operation_action = models.ForeignKey('OperationAction', models.DO_NOTHING)
    brand_versions = models.CharField(max_length=255)
    fuse_versions = models.CharField(max_length=255)
    template = models.CharField(max_length=4096)

    class Meta:
        managed = False
        db_table = 'data_mappings'


class Migration(models.Model):
    migration = models.CharField(max_length=255)
    batch = models.IntegerField()

    class Meta:
        managed = False
        db_table = 'migrations'


class OperationAction(models.Model):
    operation_rule = models.ForeignKey('OperationRule', models.DO_NOTHING)
    order = models.IntegerField()
    name = models.CharField(unique=True, max_length=255)
    operation_type = models.CharField(max_length=255)
    operation_source = models.CharField(max_length=255)
    extra_parameters = models.CharField(max_length=1024)
    http_verb = models.CharField(max_length=255)

    class Meta:
        managed = False
        db_table = 'operation_actions'


class OperationRule(models.Model):
    operation = models.ForeignKey('Operation', models.DO_NOTHING)
    brand_version = models.CharField(max_length=255)
    fuse_version = models.CharField(max_length=255)
    order = models.IntegerField()
    acting_on = models.CharField(max_length=255)
    do_always = models.IntegerField()
    input_selector = models.CharField(max_length=255)
    operator = models.CharField(max_length=255)
    allowed_value = models.CharField(max_length=255)

    class Meta:
        managed = False
        db_table = 'operation_rules'

    def should_be_called(self, super_call, calls):
        if self.do_always:
            return True
        # TODO: remove this hack soon
        return True


class Operation(models.Model):
    brand = models.ForeignKey(Brand, models.DO_NOTHING)
    name = models.CharField(unique=True, max_length=255)
    queue = models.CharField(unique=True, max_length=255)

    class Meta:
        managed = False
        db_table = 'operations'


class PasswordReset(models.Model):
    email = models.CharField(max_length=255)
    token = models.CharField(max_length=255)
    created_at = models.DateTimeField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'password_resets'


class SampleRequest(models.Model):
    operation_action = models.ForeignKey(OperationAction, models.DO_NOTHING)
    request_body = models.CharField(max_length=255)
    created_at = models.DateTimeField(blank=True, null=True)
    updated_at = models.DateTimeField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'sample_requests'


class SampleResponseCriteria(models.Model):
    sample_request = models.ForeignKey(SampleRequest, models.DO_NOTHING)
    order = models.IntegerField()
    response_selector = models.CharField(max_length=255)
    expected_value = models.CharField(max_length=255)
    created_at = models.DateTimeField(blank=True, null=True)
    updated_at = models.DateTimeField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'sample_response_criteria'


class SuperCall(models.Model):
    operation = models.ForeignKey(Operation, models.DO_NOTHING)
    initial_payload = models.CharField(max_length=255)
    final_response = models.CharField(max_length=255, blank=True, null=True)
    status = models.CharField(max_length=255)
    created_at = models.DateTimeField(blank=True, null=True)
    updated_at = models.DateTimeField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'super_calls'

    def get_next_call(self):
        calls = Call.objects.filter(super_call_id=self.id).order_by('-updated_at')
        if calls:
            last_response = calls[0].response_data
            called_action_ids = [x.operation_action_id for x in calls]
        rules = OperationRule.objects.filter(operation_id=self.operation_id).order_by('order')
        for rule in rules:
            if rule.should_be_called(self, calls):
                actions = OperationAction.objects.filter(operation_rule_id=rule.id).order_by('order')
                for action in actions:
                    if action.id in called_action_ids:
                        continue
                    call = Call.create(self, action, last_response)
                    return call.id
        self.final_response = last_response
        self.status = 'COMPLETE'
        self.save()


class User(models.Model):
    name = models.CharField(max_length=255)
    email = models.CharField(unique=True, max_length=255)
    password = models.CharField(max_length=255)
    remember_token = models.CharField(max_length=100, blank=True, null=True)
    created_at = models.DateTimeField(blank=True, null=True)
    updated_at = models.DateTimeField(blank=True, null=True)

    class Meta:
        managed = False
        db_table = 'users'
