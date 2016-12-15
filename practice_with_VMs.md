![LogoHeadMasterCES](https://sites.google.com/site/manuparra/home/logo_master_ciber.png)


[UGR](http://www.ugr.es) | [DICITS](http://dicits.ugr.es) | [SCI2S](http://sci2s.ugr.es) | [DECSAI](http://decsai.ugr.es)

Manuel J. Parra Royón (manuelparra@decsai.ugr.es) & José. M. Benítez Sánchez (j.m.benitez@decsai.ugr.es)




Table of Contents
=================
   
   * [Environment of the practice](#environment-of-the-practice)
      * [Connecting to Virtual Machines](#connecting-to-virtual-machines)
      * [Connecting to Docker Containers](#connecting-to-docker-containers)
      * [Provided infraestructure](#provided-infraestructure)
   * [Connecting and starting with docker server, Virtual Machines and docker system](#connecting-and-starting-with-docker-server-virtual-machines-and-docker-system)
   * [Creating a LDAP with TLS/SSL service](#creating-a-ldap-with-tlsssl-service)
      * [Connecting to your Virtual Machine](#connecting-to-your-virtual-machine)
      * [Installing SLDAP service](#installing-sldap-service)
         * [Create a LDAP root passwd for administration purpose.](#create-a-ldap-root-passwd-for-administration-purpose)
         * [Edit the OpenLDAP Server Configuration](#edit-the-openldap-server-configuration)
         * [Change monitor privileges](#change-monitor-privileges)
         * [Check configuration](#check-configuration)
         * [Enable services](#enable-services)
         * [Configure Database](#configure-database)
         * [Add default schemas](#add-default-schemas)
         * [Create certificates for LDAP](#create-certificates-for-ldap)
         * [Base](#base)
         * [Create a simple user or migrate local user:](#create-a-simple-user-or-migrate-local-user)
         * [Creating from local users (migrating)](#creating-from-local-users-migrating)
         * [Creating manually](#creating-manually)
      * [Test LDAP configuration:](#test-ldap-configuration)
      * [Installing clients for LDAP](#installing-clients-for-ldap)
         * [Authentication with with PHP on HTTPS/SSL](#authentication-with-with-php-on-httpsssl)
         * [Authentication on LDAP server with with SSH](#authentication-on-ldap-server-with-with-ssh)
   * [Creating a freeIPA service](#creating-a-freeipa-service)
      * [Connecting to your Virtual Machine](#connecting-to-your-virtual-machine-1)
      * [Install freeIPA service](#install-freeipa-service)
      * [Installing clients](#installing-clients)


# Environment of the practice

The working environment consists of the following structure for each user:

- 2 Virtual Machines per user:
   * 1 for  LDAP server
   * 1 for freeIPA server

- 2 containers, 5 TCP ports and 5 UDP ports per user.


## Connecting to Virtual Machines

1. Log in docker ugr server with your credentials
2. Connect to your Virtual Machines with ``ssh`` using specific IP assigned to you.
3. NOTE: Virtual Machine A: for LDAP server and Virtual Machine B: for freeIPA server

![SchemaVM](https://sites.google.com/site/manuparra/home/as.jpg)
*Diagram of the connection: 1. Log in docker ugr. 2. Log in VMachine.*


## Connecting to Docker Containers

Docker system will be used in this practice to deploy LDAP and freeIPA clients. That is, installing applications that connect to LDAP and FreeIPA.

![structuredocker](https://sites.google.com/site/manuparra/home/structuredocker.png)

**REMEMBER: Each container could require more than one port**.  

To work in this practice is mandatory to connect to **Docker Server** in order to manage **Virtual Machines** and **containers**.

## Provided infraestructure 

The complete structure of the infraestructure for the practice is the next:

![CompleteStruct](https://sites.google.com/site/manuparra/home/dockervm.jpg)

As you see, inside docker you will work with Virtual Machines for Services LDAP and FreeIPA, and by the other hand with Docker Containers for applications that connect to FreeIPA in different ways.



# Connecting and starting with docker server, Virtual Machines and docker system

First of all read about how to manage docker container [here!](README.md). It will be used for LDAP and FreeIPA clients.

# Creating a LDAP with TLS/SSL service

We will create a LDAP over TLS/SSL (389 Directory Server, MIT Kerberos).

We will use one Virtual Machine for this deployment. Virtual Machine for LDAP server and docker containers for clients (and other apps that will connect to LDAP).

- For the first we will use an Virtual Machine with CentOS 7 and from which we will install everything necessary to serve LDAP service.

- Then using a docker container we will use an initial with CentOS 7 (or https/php container) and install the LDAP client and other clients in order to authenticate from different applications (HTTP, PHP, etc.)


## Connecting to your Virtual Machine

First connect to docker ugr server

```
ssh myuser@docker...
```

Then connect to your first Virtual Machine for LDAP service

```
ssh root@192.168.10.XXX
```

NOTE: XXX is your assigned IP for LDAP.


## Installing SLDAP service

Install OpenLDAP application and services. More info and details: https://www.centos.org/docs/5/html/Deployment_Guide-en-US/s1-ldap-quickstart.html

```
yum -y install *openldap* migrationtools
```

It will install openldap packages and migrationtool (migrate local users to LDAP).

### Create a LDAP root passwd for administration purpose.

```
slappasswd
```


This command will ask you about your LDAP admin password. It will be used for each elevated operation (admin operations).


Copy the hashed password returned by last command.


### Edit the OpenLDAP Server Configuration

```
cd /etc/openldap/slapd.d/cn=config
```

```
vi olcDatabase={2}hdb.ldif
```

Change the variables of "olcSuffix" and "olcRootDN" according to our domain as below.

```
olcSuffix: dc=ugr,dc=es
olcRootDN: cn=Manager,dc=ugr,dc=es
```

Add the below three lines additionally in the same configuration file.

```
olcRootPW: <PASSWORD STRING GENERATED with slappasswd>
olcTLSCertificateFile: /etc/pki/tls/certs/ugr.pem
olcTLSCertificateKeyFile: /etc/pki/tls/certs/ugrkey.pem
```

NOTE: ``<PASSWORD STRING GENERATED with slappasswd>`` must be the hashed password.

### Change monitor privileges

```
vi /etc/openldap/slapd.d/cn=config/olcDatabase={1}monitor.ldif
```

Go to line starting with `olcAccess` and change values with ``cn=Manager,dc=ugr,dc=es``: 

```
olcAccess: {0}to * by dn.base="gidNumber=0+uidNumber=0,cn=peercred,cn=external, cn=auth" read by dn.base="cn=Manager,dc=ugr,dc=es" read by * none
```

### Check configuration

```
slaptest -u
```

NOTE: Don't mind warnings

### Enable services

```
systemctl start slapd
systemctl enable slapd
```

### Configure Database

```
cp /usr/share/openldap-servers/DB_CONFIG.example /var/lib/ldap/DB_CONFIG
chown -R ldap:ldap /var/lib/ldap/
```

### Add default schemas

Those schemas are required:

```
ldapadd -Y EXTERNAL -H ldapi:/// -f /etc/openldap/schema/cosine.ldif
ldapadd -Y EXTERNAL -H ldapi:/// -f /etc/openldap/schema/nis.ldif
ldapadd -Y EXTERNAL -H ldapi:/// -f /etc/openldap/schema/inetorgperson.ldif
```

### Create certificates for LDAP

```
openssl req -new -x509 -nodes -out /etc/pki/tls/certs/learnitguideldap.pem -keyout /etc/pki/tls/certs/learnitguideldapkey.pem -days 365
```
Provide your details to generate the certificate.

Common name will be: ugr.es

### Base 

```
touch /root/base.ldif
```


and add:

```
dn: dc=ugr,dc=es
objectClass: top
objectClass: dcObject
objectclass: organization
o: ugr es
dc: ugr

dn: cn=Manager,dc=ugr,dc=es
objectClass: organizationalRole
cn: Manager
description: Directory Manager

dn: ou=People,dc=ugr,dc=es
objectClass: organizationalUnit
ou: People

dn: ou=Group,dc=ugr,dc=es
objectClass: organizationalUnit
ou: Group
```

and then execute:


```
ldapadd -x -W -D "cn=Manager,dc=ugr,dc=es" -f /root/base.ldif
```

This command will add OU=People, OU=Group, etc.


### Create a simple user or migrate local user:



### Creating from local users (migrating)

```
useradd myuser

```

```
passwd myuser
```

```
grep ":10[0-9][0-9]" /etc/passwd > /root/passwd
```


```
grep ":10[0-9][0-9]" /etc/group > /root/group
```

Now, edit migration tools: 

```
cd /usr/share/migrationtools/
```


```
vi migrate_common.ph
```

Change to our ldap domain:

```
LINE 71: $DEFAULT_MAIL_DOMAIN = "ugr.es";
```

```
LINE 74: $DEFAULT_BASE = "dc=ugr,dc=es";
```

```
LINE 90: $EXTENDED_SCHEMA = 0;
```

Execute migration tool:

```
./migrate_passwd.pl /root/passwd /root/users.ldif
```

```
./migrate_group.pl /root/group /root/groups.ldif
```

Import users:

```
ldapadd -x -W -D "cn=Manager,dc=ugr,dc=es" -f /root/users.ldif
```

Import groups:

```
ldapadd -x -W -D "cn=Manager,dc=ugr,dc=es" -f /root/groups.ldif
```


### Creating manually

`users.ldif` -> fill data from out last tutorial: https://github.com/manuparra/docker_ldap#training-with-ldap

```
ldapadd -x -W -D "cn=Manager,dc=ugr,dc=es" -f /root/users.ldif
```

For instance:

```
dn: uid=myuser,ou=People,dc=ugr,dc=es
uid: myuser
cn: myuser
sn: myuser
mail: myuser@ugr.es
objectClass: person
objectClass: organizationalPerson
objectClass: inetOrgPerson
objectClass: posixAccount
objectClass: top
objectClass: shadowAccount
userPassword: {crypt}$6$aRkYTYsY$7FcXPmehiisAhbTiF3kDVo7g.EOKfpvhkrYExK5Y7wNq0rh0JJehbKDZKbUVxoF2hO0KfP1bmWPhXvq9BIxJT/
shadowLastChange: 17149
shadowMin: 0
shadowMax: 99999
shadowWarning: 7
loginShell: /bin/bash
uidNumber: 1000
gidNumber: 1000
homeDirectory: /home/myuser
```

NOTE: Remember change user password with LDAP.


## Test LDAP configuration:

```
ldapsearch -x cn=<your user> -b dc=ugr,dc=es
```

```
ldapsearch -x cn=myuser -b dc=ugr,dc=es
```



## Installing clients for LDAP

For this part will be necessary to use the docker containers. So, you must to use in this case docker container (Not virtual machines).

You must to use one or two containers in which the ldap clients will be installed, to validate that the installation of the SLDAP service is correct.

### Authentication with with PHP on HTTPS/SSL

**NOTE: ALL IN YOUR CONTAINER**

You will need a docker image with SSL, APACHE and PHP:

Use the next *docker.io/eboraas/apache-php*. Remember port redirection to the container:

```
docker run -p 14001:80 -p 14002:443 --name ContOfManu -d eboraas/apache-php
```

Now if you go to SSL web page of the container created: https://docker.ugr.es:14002 

Go inside your container 
```
docker exec -i -t ContOfManu /bin/bash
```

You need to install php5-ldap extension:

```
apt-get install php5-ldap
```

And reboot APACHE:

```
apachectl2 restart
```

And again (because it produce an good-bye of the container): 

```
docker exec -i -t ContOfManu /bin/bash
```

Now create a file in ``/var/www/html/`` i.e.: ``authentication.php``:

- You must create a simple webpage to authenticate against our LDAP server
- Remember: 
   - Your LDAP IP server 
   - And you Admin Password to do LDAP BIND


### Authentication on LDAP server with with SSH

**NOTE: ALL IN YOUR CONTAINER**

Create another docker container with ubuntu (follow the steps to create on docker):

Install client packages:

```
apt-get update
apt-get install libpam-ldap nscd
```

And follow the next questions:

```
LDAP server Uniform Resource Identifier: ldap://LDAP-server-IP-Address

Change the initial string from "ldapi:///" to "ldap://" before inputing your server's information
Distinguished name of the search base:

   This should match the value you put in your LDAP service
   
   Our example was "dc=ugr,dc=es"

LDAP version to use: 3

Make local root Database admin: Yes

Does the LDAP database require login? No

LDAP account for root:

   This should also match the value in your LDAP SERVER
   Our example was "cn=Manager,dc=ugr,dc=es"

LDAP root account password: Your-LDAP-root-password
``

if you make a mistake:

```
sudo dpkg-reconfigure ldap-auth-config
```

Edit ``/etc/nsswitch.conf``

```
passwd:         ldap compat
group:          ldap compat
shadow:         ldap compat
```

Edit ``/etc/pam.d/common-session``

Add to the bottom:

```
session required    pam_mkhomedir.so skel=/etc/skel umask=0022
```

And then:

```
/etc/init.d/nscd restart
```

Exit from the container and try log in with ssh:

```
ssh -p <PORTcontainer> LDAP_user@localhost
```

# Creating a freeIPA service

We will create a FreeIPA service (integrated security information management solution combining Linux (Fedora), 389 Directory Server, MIT Kerberos, NTP, DNS, Dogtag (Certificate System)). 


![FreeIPA_docker](https://sites.google.com/site/manuparra/home/docker_freeipa.png)

We will use another virtual machine for this deployment, one VM for freeIPA and the a docker container for clients (and other apps that will connect to FreeIPA).

- For the first we will use an Virtual Machine with CentOS 7 and from which we will install everything necessary to serve freeIPA.

- In the container we will use an initial with CentOS 7 or other container and install the FreeIPA client and other connection clients from different applications.


## Connecting to your Virtual Machine

First connect to docker ugr server

```
ssh myuser@docker...
```

Then connect to your first Virtual Machine for LDAP service

```
ssh root@192.168.10.XXX
```

NOTE: XXX is your assigned IP for LDAP.



## Install freeIPA service

Follow the next instructions (+info: https://github.com/manuparra/freeipa):

1. First of all, execute command: 
`yum -y update`
2. Set the name of the IPA Server: 
`hostnamectl set-hostname ipa.centos.local` or `vi /etc/hostname` and add ipa.centos.local
3. Edit `/etc/hosts` and add: 
`<ContainerIP> ipa.centos.local ipa`
4. Download and install freeIPA packages with: 
`yum install ipa-server bind-dyndb-ldap ipa-server-dns`
5. Install and set freeIPA services: 
`ipa-server-install --setup-dns`
6. Follow the steps of previous item : [here!](https://github.com/manuparra/FreeIPA/blob/master/questions.txt)
7. Next steps:
	1. You must make sure these network ports are open TCP Ports:
		  * 80, 443: HTTP/HTTPS
		  * 389, 636: LDAP/LDAPS
		  * 88, 464: kerberos
		  * 53: bind
	2. UDP Ports:
		  * 88, 464: kerberos
		  * 53: bind
		  * 123: ntp
8. Start authentication with Kerberos ticket: 
`kinit admin`
9. Set default shell for the users:
`ipa config-mod --defaultshell=/bin/bash`
10. Create a few users: `ipa user-add manuparra --first=Manuel --last=Parra --password` . Create the home folder for the user created:`mkdir -m0750 -p /home/mparra` and set permissions for the user: `chown XXXXXXXX:XXXXXXXX /home/mparra/` where `XXXXXXXX` is the UID returned by ``ipa user-add manuparra ...``.
11. (Mandatory) Create home folder for admin user, execute: `mkdir -m0750 -p /home/admin/` 
12. Execute `ipa user-show admin` and copy UID number. Then execute: `chown XXXXXX:XXXXXX /home/admin` where XXXXXXX is the UID number of admin user. This is mandatory due to in replica server it try to connect with this user and it needs the home be created.
13. Check if IPA works. Exit of the server and try to connect: `ssh -p <port_ssh_dockercontainer> manuparra@<IP>` If it is working, ssh ask to you about change your password and retype it twice. If you can access to the server, IPA server now is Working.



## Installing clients

For this part will be necessary to use the docker containers. So, you must to use in this case docker container (Not virtual machines).

You must to use one or two containers in which the ldap clients will be installed, to validate that the installation of the FreeIPA service is correct.

Here you have a pair of clients to authenticate: https://github.com/manuparra/freeipa#authentication-from-anywhere  


