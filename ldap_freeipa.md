# Environment of the practice

The working environment consists of the following structure for each user:

![structuredocker](https://sites.google.com/site/manuparra/home/structuredocker.png)

Two containers and a few ports per user.

![dockerserver](https://sites.google.com/site/manuparra/home/dockerserver.png)

Connect to **Docker Server** and manage containers.

# Connecting and starting with docker server and docker system:

First of all read about how to manage docker container [here!](README.md)

# Creating a LDAP with TLS/SSL service:

We will create a LDAP over TLS/SSL (389 Directory Server, MIT Kerberos).

We will use two containers for this deployment, one for LDAP server and the second for clients (and other apps that will connect to LDAP).

- For the first we will use an initial container with CentOS 7 and from which we will install everything necessary to serve LDAP.

- In the other container we will use an initial with CentOS 7 and install the LDAP  client and other connection clients from different applications.

## Downloading CentOS7 base container

Download image CentOS7 container

```
docker pull centos
```

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

## Installing SLDAP service

```
yum -y install *openldap* migrationtools
```

## Create a LDAP root passwd for administration purpose.

```
slappasswd
```

## Edit the OpenLDAP Server Configuration

```
cd /etc/openldap/slapd.d/cn=config
```

```
vi olcDatabase={2}hdb.ldif
```

Change the variables of "olcSuffix" and "olcRootDN" according to your domain as below.

```
olcSuffix: dc=learnitguide,dc=net
olcRootDN: cn=Manager,dc=learnitguide,dc=net
```

Add the below three lines additionally in the same configuration file.

```
olcRootPW: <PASSWORD STRING GENERATED with slappasswd>
olcTLSCertificateFile: /etc/pki/tls/certs/learnitguideldap.pem
olcTLSCertificateKeyFile: /etc/pki/tls/certs/learnitguideldapkey.pem
```

## Change monitor privileges

```
vi /etc/openldap/slapd.d/cn=config/olcDatabase={1}monitor.ldif
```

Go to line starting with `olcAccess`:

```
olcAccess: {0}to * by dn.base="gidNumber=0+uidNumber=0,cn=peercred,cn=external, cn=auth" read by dn.base="cn=Manager,dc=learnitguide,dc=net" read by * none
```

## Check configuration

```
slaptest -u
```

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

## Base 

```
touch /root/base.ldif
```


and add:

```
dn: dc=learnitguide,dc=net
objectClass: top
objectClass: dcObject
objectclass: organization
o: learnitguide net
dc: learnitguide

dn: cn=Manager,dc=learnitguide,dc=net
objectClass: organizationalRole
cn: Manager
description: Directory Manager

dn: ou=People,dc=learnitguide,dc=net
objectClass: organizationalUnit
ou: People

dn: ou=Group,dc=learnitguide,dc=net
objectClass: organizationalUnit
ou: Group
```

and:


```
ldapadd -x -W -D "cn=Manager,dc=learnitguide,dc=net" -f /root/base.ldif
```


## Create a simple user:

`users.ldif` -> fill data from https://github.com/manuparra/docker_ldap#training-with-ldap

```
ldapadd -x -W -D "cn=Manager,dc=learnitguide,dc=net" -f /root/users.ldif
```

## Test LDAP configuration:

```
ldapsearch -x cn=<your user> -b dc=learnitguide,dc=net
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
13. Check if IPA works. Exit of the server and try to connect: `ssh manuparra@192.168.10.220` If it is working, ssh ask to you about change your password and retype it twice. If you can access to the server, IPA server now is Working.



