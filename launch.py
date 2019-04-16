'''
auth-- (Bhupesh Pandey)
Location: /www/		in virginia root
used to launch New systems
first input asks for name of region in which u wish to launch new systems
second input prompts for serial number to create tags
thirdly it prompts for numbers of systems to be launched
sucessive tags for multiple systems will be automatically created  in ascending order'''
import boto3
import time
import os
instanceids = []
volumeids = []
name = []
privateip = []
regionarr = []
state = []
spotarr = []
def data():
    print('Please Wait Creating Complete Data Set For Launched Instances')
    time.sleep(30)
    ec2 = boto3.resource('ec2', region_name=zone)
    for i in range(len(instanceids)):
        xy = ec2.Instance(instanceids[i])
        name.append(xy.tags[0].get('Value'))
        privateip.append(xy.private_ip_address)
        regionarr.append(region)
        if xy.state.get('Name') == 'running':
            state.append(xy.state.get('Name'))
        elif xy.state.get('Name') == 'terminated':
            print('Instance Terminated', xy.tags[0].get('Value'))
        else:
            print('Instance Not In Running State Sleeping For 10 sec')
            time.sleep(10)
    volumes = ec2.volumes.filter(Filters=[{'Name': 'attachment.instance-id', 'Values': instanceids}])
    for i in name:
        for x in volumes:
            if x.volume_type == 'standard' and i == x.tags[0].get('Value'):
                volumeids.append(x.volume_id)


def cred(region):
    abc = {u'virginia' : {u'zone' : 'us-east-1' , u'subzone' : 'us-east-1e' , u'ami' : 'ami-01666eac4abbb1d06' , u'sgroup' : 'app-installer'},
u'california' : {u'zone' : 'us-west-1' , u'subzone' : 'us-west-1b' , u'ami' : 'ami-07b4ce3e322700c59' , u'sgroup' : 'app-installer'},
u'oregon' : {u'zone' : 'us-west-2' , u'subzone' : 'us-west-2c' , u'ami' : 'ami-0b680c417a4c94b73' , u'sgroup' : 'app-installer'},
u'singapore' : {u'zone' : 'ap-southeast-1' , u'subzone' : 'ap-southeast-1a' , u'ami' : 'ami-0ee83e60417bfdf18' , u'sgroup' : 'app-installer'},
u'tokyo' : {u'zone' : 'ap-northeast-1' , u'subzone' : 'ap-northeast-1c' , u'ami' : 'ami-045af10fcded62282' , u'sgroup' : 'app-installer'},
u'ireland' : {u'zone' : 'eu-west-1' , u'subzone' : 'eu-west-1c' , u'ami' : 'ami-0b88079f9bec2e986' , u'sgroup' : 'app-installler'}}

    for i in range(1):
        zone = abc.get(region).get('zone')
        subzone = abc.get(region).get('subzone')
        ami = abc.get(region).get('ami')
        sgroup = abc.get(region).get('sgroup')
        spotarr.append(zone)
        spotarr.append(subzone)
        spotarr.append(ami)
        spotarr.append(sgroup)
user = 'appamplify'
region = raw_input("Enter Region\n")
if user == 'appamplify':
    cred(region)

starttag = input('Tag Number To Start From\n')
if not starttag or starttag == '0':
    print('Enter Valid Start Tag & Try Again')
    exit()
boto3.setup_default_session(profile_name=user)
zone = spotarr[0]
subzone = spotarr[1]
ami = spotarr[2]
sgroup = spotarr[3]
ec2 = boto3.resource('ec2', region_name=zone)
num = input('No Of Instances To Launch\n')
count = 0
while num > 0:
    user_data_script = '''#!/bin/bash
        sleep 100
        touch /home/ubuntu/config.sh
        echo "idstart=''' + str(starttag) + '''&&for x in {0..49};do cd ~/work/main\$x;sed -i 's/AGENTID = 1/AGENTID = '\$idstart'/' Config.py ;sudo mkdir ~/hdd/data\$x;sudo cp Config.py ~/hdd/data\$x/;sudo chmod u=rw ~/hdd/data\$x/Config.py;sudo chmod g=rw ~/hdd/data\$x/Config.py;sudo chmod o=r ~/hdd/data\$x/Config.py;cd ../;((idstart+=1));done" > /home/ubuntu/config.sh
    sudo chown ubuntu:ubuntu /home/ubuntu/config.sh
    sudo mkfs -t ext4 /dev/xvdf
    sudo mount /dev/xvdf /home/ubuntu/hdd
    su - ubuntu -c 'bash /home/ubuntu/config.sh &' '''
    endtag = starttag
    endtag += 49
    newtag = ('Multi({}-{})'.format(starttag, endtag))
    status = 'False'
    try:
        r = ec2.create_instances(ImageId=ami, MinCount=1, MaxCount=1,
                             SecurityGroups=[sgroup],UserData=user_data_script,
                             InstanceType='m3.medium', DryRun=False,
                             InstanceMarketOptions={'MarketType': 'spot', 'SpotOptions': {'MaxPrice': '0.99'}},
                             Placement={'AvailabilityZone': subzone},
                             TagSpecifications=[{'ResourceType': 'instance',
                                                 'Tags': [{'Key': 'Name',
                                                          'Value': newtag}, ]},
                                                {'ResourceType': 'volume',
                                                 'Tags': [{'Key': 'Name',
                                                           'Value': newtag}, ]}
                                                ],
                             )
        status = True
    except Exception as e:
        print(e)
    if status:
        instanceids.append(r[0].instance_id)
        count += 1
        num -= 1
        starttag += 50


data()

count = 0
time.sleep(30)
for x in range(len(instanceids)):
    i = privateip[x]
    if region == 'virginia':
        a = os.system('ssh-keygen -f "/home/ubuntu/.ssh/known_hosts" -R '+i)
        b = os.system('ssh -oStrictHostKeyChecking=no '+i+' rm Config.sh')
        for x in range(3):
            if a != 0 or b != 0 and b != 256:
                a = os.system('ssh-keygen -f "/home/ubuntu/.ssh/known_hosts" -R ' + i)
                b = os.system('ssh -oStrictHostKeyChecking=no ' + i + ' rm Config.sh')
                print(a, b)
        os.system('scp ~/bhupesh/startup.sh ubuntu@' + i + ':/home/ubuntu/')
        if a == 0 and b == 0:
            count += 1
    else:
        a = os.system('ssh '+region+' ssh-keygen -f "/home/ubuntu/.ssh/known_hosts" -R ' + i)
        b = os.system('ssh '+region+' ssh -oStrictHostKeyChecking=no ' + i + ' rm Config.sh')
        for x in range(3):
            if a != 0 or b != 0 and b != 256:
                a = os.system(
                    'ssh ' + region + ' ssh-keygen -f "/home/ubuntu/.ssh/known_hosts" -R ' + i)
                b = os.system(
                    'ssh ' + region + ' ssh -oStrictHostKeyChecking=no ' + i + ' rm Config.sh')
                print(a, b)
        os.system('ssh ' + region + ' scp ~/bhupesh/startup.sh ubuntu@' + i + ':/home/ubuntu/')
        if a == 0 and b == 0:
            count += 1
print('Total Number of Authenticated Systems is     ', count)
print("{:22}{:22}{:22}{:15}{:11}".format('Name', 'InstanceID', 'PrivateIP', 'Region', 'VolumeID'))
for i in range(len(instanceids)):
    print("{:22}{:22}{:22}{:15}{:11}".format(name[i], instanceids[i], privateip[i], regionarr[i], volumeids[i]))
