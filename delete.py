'''
auth-- (Bhupesh Pandey)
Location: /www/		virginia root System
Can be Used To Delete Systems on amazon Cloud
recommended to Delete Using Systems Tags OR filename as comma seperated list'''
import boto3
instanceid = []
tags = []
print('''1)      Delete Using Tag\'s
2)      Delete Using InstanceID\'s\n''')
inp = raw_input('Enter Desired Opreation\n')
if not inp :
    exit('No Input')
else:
    inp = int(inp)
    if inp == 2:
        arr = raw_input('Enter InstanceId\'s\n')
        instanceid = arr.split(',')
    elif inp == 1:
        arr = raw_input('Enter Tag\'s\n')
        tags = arr.split(',')
    else:
        exit('Invalid Input Try Again')
user = 'appamplify'
boto3.setup_default_session(profile_name=user)


def delete(zone, abc):
    ec2 = boto3.resource('ec2', region_name=zone)
    if tags:
        del instanceid[:]
        p = ec2.instances.all()
        for q in p:
            try:
                r = q.tags[0].get('Value')
                for l in tags:
                    if l == r:
                        instanceid.append(q.instance_id)
            except Exception as e:
                if q.state.get('Name') == 'running':
                    continue
    for i in instanceid:
        r = ec2.Instance(i)
        if not r:
            continue
        else:
            try:
                y = r.block_device_mappings
                for device in r.block_device_mappings:
                    volume = device.get('Ebs')
                    x = volume.get('VolumeId')
                    vol = ec2.Volume(x)
                    if vol.volume_type == 'standard':
                        r.modify_attribute(DryRun=False,
                                           BlockDeviceMappings=[{'DeviceName': '/dev/sdf',
                                                                 'Ebs': {'DeleteOnTermination': True, 'VolumeId': x}}])
                        print('Deleting     ', r.tags[0])
                        r.delete_tags(DryRun=False)
                        xyz = r.terminate(DryRun=False)
                        x = xyz.get('TerminatingInstances')
                        y = x[0].get('CurrentState')
                        c = y.get('Name')
                        z = x[0].get('PreviousState')
                        p = z.get('Name')
                        print('{} State Changed From {} To {}'.format(i, p, c))
            except Exception as e:
                print(e)
                continue

region = ['us-east-1','us-west-1','us-west-2','ap-southeast-1','ap-northeast-1','eu-west-1']
rname = ['Virginia','California','Oregon','Singapore','Tokyo','Ireland']
if inp:
    for i in range(len(region)):
        zone = region[i]
        abc = rname[i]
        delete(zone, abc)
