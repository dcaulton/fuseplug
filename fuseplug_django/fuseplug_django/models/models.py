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
    function_name = models.IntegerField()
    source_field = models.IntegerField()
    target_field = models.IntegerField()
    target_data_type = models.IntegerField()
    target_format_string = models.IntegerField()
    transform = models.IntegerField()

    class Meta:
        managed = False
        db_table = 'data_mapping_details'


class DataMapping(models.Model):
    operation_action = models.ForeignKey('OperationAction', models.DO_NOTHING)
    brand_versions = models.CharField(max_length=255)
    fuse_versions = models.CharField(max_length=255)
    template = models.CharField(max_length=255)

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
    input = models.CharField(max_length=255)
    operation_type = models.CharField(max_length=255)
    operation_source = models.CharField(max_length=255)
    source_data = models.CharField(max_length=255)
    brand_url = models.CharField(max_length=255)
    fuse_url = models.CharField(max_length=255)
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
