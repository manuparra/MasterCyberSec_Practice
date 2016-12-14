

# Environment of the practice

Máster en CiberSeguridad 2016-2017. Universidad de Granada.

Manuel J. Parra Royón & José. M. Benítez Sánchez

Soft Computing and Intelligent Information Systems

University of Granada


Table of Contents
=================

   * [Connecting and starting with docker server and docker system:](#connecting-and-starting-with-docker-server-and-docker-system)
   * [Creating a LDAP with TLS/SSL service:](#creating-a-ldap-with-tlsssl-service)
      * [Downloading CentOS7 base container](#downloading-centos7-base-container)
      * [Run your docker container with CentOS 7](#run-your-docker-container-with-centos-7)
      * [Open a bash shell inside the created container:](#open-a-bash-shell-inside-the-created-container)
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
      * [Create a simple user:](#create-a-simple-user)
      * [Test LDAP configuration:](#test-ldap-configuration)
   * [Creating a freeIPA service:](#creating-a-freeipa-service)
      * [Downloading CentOS7 base container](#downloading-centos7-base-container-1)
      * [Run your docker container with CentOS 7](#run-your-docker-container-with-centos-7-1)
      * [Open a bash shell inside the created container:](#open-a-bash-shell-inside-the-created-container-1)
      * [Install freeIPA service](#install-freeipa-service)

# Environment of the practice

The working environment consists of the following structure for each user:

- 2 Virtual Machines per user:
   * 1 for  LDAP server
   * 1 for freeIPA server

- 2 containers, 5 TCP ports and 5 UDP ports per user.


## Connecting to Virtual Machines:

1. Log in docker ugr server with your credentials
2. Connect to your Virtual Machines with ``ssh`` using specific IP assigned to you.
3. NOTE: Virtual Machine A: for LDAP server and Virtual Machine B: for freeIPA server

![SchemaVM](https://sites.google.com/site/manuparra/home/as.jpg)
*Diagram of the connection: 1. Log in docker ugr. 2. Log in VMachine.*


## Connecting to Docker Containers:

Docker system will be used in this practice to deploy LDAP and freeIPA clients. That is, installing applications that connect to LDAP and FreeIPA.

![structuredocker](https://sites.google.com/site/manuparra/home/structuredocker.png)

**REMEMBER: Each container could require more than one port**.  

To work in this practice is mandatory to connect to **Docker Server** in order to manage **Virtual Machines** and **containers**.

## Provided infraestructure 

The complete structure of the infraestructure for the practice is the next:

![CompleteStruct](https://sites.google.com/site/manuparra/home/dockervm.jpg)

As you see, inside docker you will work with Virtual Machines for Services LDAP and FreeIPA, and by the other hand with Docker Containers for applications that connect to FreeIPA in different ways.



# Connecting and starting with docker server and docker system:

First of all read about how to manage docker container [here!](README.md). It will be used for LDAP and FreeIPA clients.

# Creating a LDAP with TLS/SSL service:

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

## Create a LDAP root passwd for administration purpose.

```
slappasswd
```


This command will ask you about your LDAP admin password. It will be used for each elevated operation (admin operations).


Copy the hashed password returned by last command.


## Edit the OpenLDAP Server Configuration

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

## Change monitor privileges

```
vi /etc/openldap/slapd.d/cn=config/olcDatabase={1}monitor.ldif
```

Go to line starting with `olcAccess` and change values with ``cn=Manager,dc=ugr,dc=es``: 

```
olcAccess: {0}to * by dn.base="gidNumber=0+uidNumber=0,cn=peercred,cn=external, cn=auth" read by dn.base="cn=Manager,dc=ugr,dc=es" read by * none
```

## Check configuration

```
slaptest -u
```

NOTE: Don't mind warnings

## Enable services

```
systemctl start slapd
systemctl enable slapd
```

## Configure Database

```
cp /usr/share/openldap-servers/DB_CONFIG.example /var/lib/ldap/DB_CONFIG
chown -R ldap:ldap /var/lib/ldap/
```

## Add default schemas

```
ldapadd -Y EXTERNAL -H ldapi:/// -f /etc/openldap/schema/nis.ldif
ldapadd -Y EXTERNAL -H ldapi:/// -f /etc/openldap/schema/cosine.ldif
ldapadd -Y EXTERNAL -H ldapi:/// -f /etc/openldap/schema/inetorgperson.ldif
```

## Create certificates for LDAP

```
openssl req -new -x509 -nodes -out /etc/pki/tls/certs/learnitguideldap.pem -keyout /etc/pki/tls/certs/learnitguideldapkey.pem -days 365
```
Provide your details to generate the certificate.

Common name will be: ugr.es

## Base 

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


## Create a simple user or migrate local user:

`users.ldif` -> fill data from https://github.com/manuparra/docker_ldap#training-with-ldap

```
ldapadd -x -W -D "cn=Manager,dc=ugr,dc=es" -f /root/users.ldif
```

## Test LDAP configuration:

```
ldapsearch -x cn=<your user> -b dc=ugr,dc=es
```


# Creating a freeIPA service:

We will create a FreeIPA service (integrated security information management solution combining Linux (Fedora), 389 Directory Server, MIT Kerberos, NTP, DNS, Dogtag (Certificate System)). 


![FreeIPA_docker](https://sites.google.com/site/manuparra/home/docker_freeipa.png)

We will use two containers for this deployment, one for freeIPA and the second for clients (and other apps that will connect to FreeIPA).

- For the first we will use an initial container with CentOS 7 and from which we will install everything necessary to serve freeIPA.

- In the other container we will use an initial with CentOS 7 and install the FreeIPA client and other connection clients from different applications.


## Downloading CentOS7 base container

Download image CentOS7 container

´´´
docker pull centos
´´´

## Run your docker container with CentOS 7

```
docker run -d -i -t --name <mycontainername> docker.io/centos 
```

## Open a bash shell inside the created container:

First, show the running containers:

```
docker ps
```

Connect to your docker:

```
docker exec -i -t <containername> /bin/bash
```

## Install freeIPA service

Once inside the container with last command, install the FreeIPA service following the next instructions:

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
13. Check if IPA works. Exit of the server and try to connect: `ssh -p <port_ssh_dockercontainer> manuparra@localhost` If it is working, ssh ask to you about change your password and retype it twice. If you can access to the server, IPA server now is Working.




