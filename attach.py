'''
auth-- (Bhupesh Pandey)
hook test
Automated Code to Check For Deleted Systems in Cloud
File should be located at /www/cronsjobs/ in virginia Root server'''
import boto3
import time
import os
import datetime
import telepot

bot = telepot.Bot('759947770:AAHYG4mclhJhXu97Bkvx2SRYwBGfu5g8B4g')

user = 'appamplify'
tags = []
tagsvol = []
tagsinst = []
arraysubzone = []
regionarr = []
newvolarray = []
iparray = []
startupregion = []
zonearr = ['us-east-1','us-west-1','us-west-2','ap-southeast-1','ap-northeast-1','eu-west-1']
regarr = ['virginia','california','oregon','singapore','tokyo','ireland']


def auth():
    cnt = 0
    for x in range(len(iparray)):
        print('Authenticating {} in {} Region'.format(iparray[x], startupregion[x]))
        regn = startupregion[x]
        ip = iparray[x]
        if regn == 'virginia':
            a = os.system('ssh-keygen -f "/home/ubuntu/.ssh/known_hosts" -R ' + ip)
            b = os.system('ssh -oStrictHostKeyChecking=no ' + ip + ' rm config.sh')
            for k in range(3):
                if a != 0 or b != 0 and b != 256:
                    time.sleep(10)
                    a = os.system('ssh-keygen -f "/home/ubuntu/.ssh/known_hosts" -R ' + ip)
                    b = os.system('ssh -oStrictHostKeyChecking=no ' + ip + ' rm config.sh')
                    print(a, b)
            os.system('scp ~/bhupesh/startup.sh ubuntu@' + ip + ':/home/ubuntu/')
        else:
            a = os.system(
                'ssh ' + regn + ' ssh-keygen -f "/home/ubuntu/.ssh/known_hosts" -R ' + ip)
            b = os.system(
                'ssh ' + regn + ' ssh -oStrictHostKeyChecking=no ' + ip + ' rm config.sh')
            for k in range(3):
                if a != 0 or b != 0 and b != 256:
                    time.sleep(10)
                    a = os.system('ssh-keygen -f "/home/ubuntu/.ssh/known_hosts" -R ' + ip)
                    b = os.system('ssh -oStrictHostKeyChecking=no ' + ip + ' rm config.sh')
                    print(a, b)
            os.system('ssh ' + region + ' scp ~/bhupesh/startup.sh ubuntu@' + ip + ':/home/ubuntu/')


def cred(region):
    randarr = []
    if user == 'appamplify':
        abc = {u'virginia' : {u'zone' : 'us-east-1' , u'subzone' : 'us-east-1e' , u'ami' : 'ami-01fbe1f6044838716' , u'sgroup' : 'app-installer'},
u'california' : {u'zone' : 'us-west-1' , u'subzone' : 'us-west-1b' , u'ami' : 'ami-0aa9e7deec1d97b0d' , u'sgroup' : 'app-installer'},
u'oregon' : {u'zone' : 'us-west-2' , u'subzone' : 'us-west-2c' , u'ami' : 'ami-0c0eaa41dcd41c90b' , u'sgroup' : 'app-installer'},
u'singapore' : {u'zone' : 'ap-southeast-1' , u'subzone' : 'ap-southeast-1a' , u'ami' : 'ami-0a0757455a6466013' , u'sgroup' : 'app-installer'},
u'tokyo' : {u'zone' : 'ap-northeast-1' , u'subzone' : 'ap-northeast-1c' , u'ami' : 'ami-0e1fb61191948573b' , u'sgroup' : 'app-installer'},
u'ireland' : {u'zone' : 'eu-west-1' , u'subzone' : 'eu-west-1c' , u'ami' : 'ami-04fab4e8d1577d38b' , u'sgroup' : 'app-installler'}}
        zone = abc.get(region).get('zone')
        ami = abc.get(region).get('ami')
        sgroup = abc.get(region).get('sgroup')
        launch(ami,sgroup,zone,region)
    if user == 'bhupesh':
        zone = 'us-east-1'
        regionarr.append(region)
        ami = "ami-0212cfa751a07dde6"
        sgroup = "bhupesh"
        launch(ami, sgroup, zone, region)


def launch(ami, sgroup, zone, region):
    ec2 = boto3.resource('ec2', region_name=zone)
    print('Trying to launch in {} Region'.format(region))
    user_data_script = '''#!/bin/bash
            sleep 100
            sudo mount /dev/xvdf /home/ubuntu/hdd
            sleep 100
            su - ubuntu -c 'bash /home/ubuntu/startup.sh &' '''
    bot.sendMessage(572080754,str(len(arraysubzone))+' spot is terminated in '+region+' region trying to relaunch new spot')
    bot.sendMessage(704323667,str(len(arraysubzone))+' spot is terminated in '+region+' region trying to relaunch new spot')
    bot.sendMessage(731796588,str(len(arraysubzone))+' spot is terminated in '+region+' region trying to relaunch new spot')
    for i in range(len(arraysubzone)):
        itr = 0
        x = tags[i]
        try:
            r = ec2.create_instances(ImageId=ami, MinCount=1, MaxCount=1,
                                     SecurityGroups=[sgroup], UserData=user_data_script,
                                     InstanceType='m3.medium', DryRun=False,
                                     InstanceMarketOptions=dict(MarketType='spot', SpotOptions={'MaxPrice': '0.99'}),
                                     Placement={'AvailabilityZone': arraysubzone[i]},
                                     TagSpecifications=[{'ResourceType': 'instance',
                                                         'Tags': [{'Key': 'Name',
                                                                   'Value': '{}'.format(x)}, ]},
                                                        {'ResourceType': 'volume',
                                                         'Tags': [{'Key': 'Name',
                                                                   'Value': '{}'.format(x)}, ]}
                                                        ],)
            itr = +1
            bot.sendMessage(572080754,'Successfully Launched '+tags[i])
            bot.sendMessage(704323667,'Successfully Launched '+tags[i])
            bot.sendMessage(731796588,'Successfully Launched '+tags[i])
            tagsinst.append(r[0].instance_id)
            iparray.append(r[0].private_ip_address)
            startupregion.append(region)
            newvolarray.append(tagsvol[i])
        except Exception as e:
            print(e)
            if itr == 0:
                bot.sendMessage(572080754,'Error Occoured during Launching new spot in '+region+e)
                bot.sendMessage(704323667,'Error Occoured during Launching new spot in'+region+e)
                bot.sendMessage(731796588,'Error Occoured during Launching new spot in'+region+e)

    if len(newvolarray) != 0:
        attach(region)


def attach (region):
    time.sleep(10)
    ec2 = boto3.resource('ec2', region_name=zone)
    for i in range(len(newvolarray)):
        volid = newvolarray[i]
        instid = tagsinst[i]
        count = 0
        while count < 4:
            count += 1
            instance = ec2.Instance(instid)
            inststate = instance.state.get('Name')
            if count == 4:
                break
            elif inststate == 'running':
                print(instid, '=====>', inststate)
                response = instance.attach_volume(
                    Device='/dev/sdf',
                    VolumeId=volid,
                )
                a = response.get('ResponseMetadata').get('HTTPStatusCode')
                if a == 200:
                    break
                else:
                    print('Error Attaching Volume')


def check(zone, region):
    print('Searching in {} Region'.format(region))
    ec2 = boto3.resource('ec2', region_name=zone)
    volumes = ec2.volumes.filter(Filters=[{'Name': 'status', 'Values': ['available']}])
    count = 0
    validc = 0
    for vol in volumes:
        x = vol.tags
        if vol.volume_type == 'standard':
            if x[0].get('Value') == '':
                count += 1
            else:
                tagsvol.append(vol.volume_id)
                subzone = vol.availability_zone
                y = vol.tags[0].get('Value')
                if y:
                    tags.append(y)
                    arraysubzone.append(subzone)
                    validc += 1
    if len(tags) != 0:
        if count != 0 and validc == 0:
            print('Volumes were Found Without tags')
        elif validc != 0 and count == 0:
            cred(region)
        elif validc != 0 and count != 0:
            cred(region)
    else:
        if count == 0 and validc == 0:
            print('No Available Magnetic Volume Found')


boto3.setup_default_session(profile_name=user)
for i in range(len(regarr)):
    zone = zonearr[i]
    region = regarr[i]
    check(zone, region)

if len(arraysubzone) >= 1:
    auth()

date = datetime.datetime.now().strftime("%d/%m/%Y")
time = datetime.datetime.now().time()
print('#############################################')
print('         Date     ', date)
print('         Time     ', time.hour, time.minute)
print('##############################################')

