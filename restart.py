'''
auth-- (Bhupesh Pandey)
Location: /www		Virginia root
fully automated for restarting all running systems in all regions we curretly work on'''
import boto3
import datetime
import time
import os
varr = []
carr = []
oarr = []
sarr = []
tarr = []
iarr = []
xyz = [varr,carr,oarr,sarr,tarr,iarr]
user = 'appamplify'
boto3.setup_default_session(profile_name=user)
regarr = ['virginia', 'california', 'oregon', 'singapore', 'tokyo', 'ireland']
zone = ''
region = ''
def collect(zone, region):
    ec2 = boto3.resource('ec2', region_name=zone)
    for instance in ec2.instances.all():
        if region == 'virginia':
            if instance.state.get(
                    'Name') == 'running' and instance.instance_type == 'm3.medium' and instance.instance_lifecycle == 'spot':
                varr.append(instance.private_ip_address)
        elif region == 'california':
            if instance.state.get(
                    'Name') == 'running' and instance.instance_type == 'm3.medium' and instance.instance_lifecycle == 'spot':
                carr.append(instance.private_ip_address)
        elif region == 'oregon':
            if instance.state.get(
                    'Name') == 'running' and instance.instance_type == 'm3.medium' and instance.instance_lifecycle == 'spot':
                oarr.append(instance.private_ip_address)
        elif region == 'singapore':
            if instance.state.get(
                    'Name') == 'running' and instance.instance_type == 'm3.medium' and instance.instance_lifecycle == 'spot':
                sarr.append(instance.private_ip_address)
        elif region == 'tokyo':
            if instance.state.get(
                    'Name') == 'running' and instance.instance_type == 'm3.medium' and instance.instance_lifecycle == 'spot':
                tarr.append(instance.private_ip_address)
        elif region == 'ireland':
            if instance.state.get(
                    'Name') == 'running' and instance.instance_type == 'm3.medium' and instance.instance_lifecycle == 'spot':
                iarr.append(instance.private_ip_address)


def start():
    for region in regarr:
        print(region)
        if user == 'appamplify':
            abc = {u'virginia': {u'zone': 'us-east-1', u'subzone': 'us-east-1e', u'ami': 'ami-01cb2165dc19ec9ba',
                                 u'sgroup': 'app-installer'},
                   u'california': {u'zone': 'us-west-1', u'subzone': 'us-west-1b', u'ami': 'ami-078b6357ce771cac1',
                                   u'sgroup': 'app-installer'},
                   u'oregon': {u'zone': 'us-west-2', u'subzone': 'us-west-2c', u'ami': 'ami-0e3df614db908653e',
                               u'sgroup': 'app-installer'},
                   u'singapore': {u'zone': 'ap-southeast-1', u'subzone': 'ap-southeast-1a',
                                  u'ami': 'ami-0471fe89b3f738ad9', u'sgroup': 'app-installer'},
                   u'tokyo': {u'zone': 'ap-northeast-1', u'subzone': 'ap-northeast-1c', u'ami': 'ami-0b37791c94dee5be7',
                              u'sgroup': 'app-installer'},
                   u'ireland': {u'zone': 'eu-west-1', u'subzone': 'eu-west-1c', u'ami': 'ami-05a0e7b78858200cb',
                                u'sgroup': 'app-installler'}}
            zone = abc.get(region).get('zone')


start()
for x in regarr:
    if x == 'virginia' and len(varr) >= 1:
        for i in varr:
            a = os.system('ssh ' + i + ' nohup sh startup.sh &')
            time.sleep(5)
    elif x == 'california' and len(carr) >= 1:
        for i in carr:
            a = os.system('ssh california ssh ' + i + ' nohup sh startup.sh &')
            time.sleep(5)
    elif x == 'oregon' and len(oarr) >= 1:
        for i in oarr:
            a = os.system('ssh oregon ssh ' + i + ' nohup sh startup.sh &')
            time.sleep(5)
    elif x == 'singapore' and len(sarr) >= 1:
        for i in sarr:
            a = os.system('ssh ' + x + ' ssh ' + i + ' nohup sh startup.sh &')
            time.sleep(5)
    elif x == 'tokyo' and len(tarr) >= 1:
        for i in tarr:
            a = os.system('ssh ' + x + ' ssh ' + i + ' nohup sh startup.sh &')
            time.sleep(5)
    elif x == 'ireland' and len(iarr) >= 1:
        for i in iarr:
            a = os.system('ssh ' + x + ' ssh ' + i + ' nohup sh startup.sh &')
            time.sleep(5)

date = datetime.datetime.now().strftime("%d/%m/%Y")
time = datetime.datetime.now().time()
print('#############################################')
print('         Date     ', date)
print('         Time     ', time.hour, time.minute)
print('##############################################')


