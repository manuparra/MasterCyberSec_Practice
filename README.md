![LogoHeadMasterCES](https://sites.google.com/site/manuparra/home/logo_master_ciber.png)


[UGR](http://www.ugr.es) | [DICITS](http://dicits.ugr.es) | [SCI2S](http://sci2s.ugr.es) | [DECSAI](http://decsai.ugr.es)

Manuel J. Parra Royón (manuelparra@decsai.ugr.es) & José. M. Benítez Sánchez (j.m.benitez@decsai.ugr.es)


Table of Contents
=================


   * [1. Environment of the practice](#environment-of-the-practice)
      * [Connecting to Docker Server UGR](#connecting-to-docker-server-ugr)
      * [Connecting to Docker Containers](#connecting-to-docker-containers)
      * [Infrastructure](#infrastructure)
      * [Connecting and starting with docker server and docker system](#connecting-and-starting-with-docker-server-and-docker-system)
   * [2. Creating a LDAP with TLS/SSL service](#creating-a-ldap-with-tlsssl-service)
      * [Create a container](#create-a-container)
      * [Connect to the container](#connect-to-the-container)
      * [Important notes](#important-notes)
      * [Installing SLDAP service](#installing-sldap-service)
         * [Edit the OpenLDAP Server Configuration](#edit-the-openldap-server-configuration)
         * [Check configuration](#check-configuration)
         * [Creating certificates for LDAP](#creating-certificates-for-ldap)
         * [Creating Base Directory and schema](#creating-base-directory-and-schema)
      * [LDAP User Management](#ldap-user-management)
         * [Understanding properties and objectClass of a user in LDAP](#understanding-properties-and-objectclass-of-a-user-in-ldap)
         * [Creating LDAP users from local users (migration tools)](#creating-ldap-users-from-local-users-migration-tools)
         * [Creating users manually from a .ldif file](#creating-users-manually-from-a-ldif-file)
         * [Changing password LDAP user](#changing-password-ldap-user)
         * [Modifying LDAP user account data: DELETE, MODIFY.](#modifying-ldap-user-account-data-delete-modify)
      * [Testing LDAP configuration](#testing-ldap-configuration)
   * [3. Installing clients for LDAP](#installing-clients-for-ldap)
      * [Authentication with with PHP on HTTPS/SSL](#authentication-with-with-php-on-httpsssl)
      * [Installing PHPLDAPMYADMIN](#installing-phpldapmyadmin)
   * [5. Working with OpenNebula](#working-with-opennebula)
   * [6. Creating a freeIPA service](#creating-a-freeipa-service)
      * [Create a container](#create-a-container-1)
      * [Connect to the container](#connect-to-the-container-1)
      * [Install freeIPA service](#install-freeipa-service)
      * [Installing clients](#installing-clients)

# Environment of the practice

The working environment consists of the following structure for each user:

- 2 Containers per user + 5 TCP ports and 5 UDP ports per user:
   * 1 for LDAP server
   * 1 for freeIPA server

- 2 containers + 5 TCP ports and 5 UDP ports per user for LDAP and freeIPA Clients.


## Connecting to Docker Server UGR

Log in docker ugr server with your credentials

```
ssh manuparra@docker...
```

## Connecting to Docker Containers

Docker system will be used in this practice to deploy LDAP and freeIPA server and clients. That is, installing applications that serve and connect to LDAP and FreeIPA.

Everything running on docker containers.

![structuredocker](https://sites.google.com/site/manuparra/home/docker_freeipa.png)

**REMEMBER: Each container could require more than one port**.  

To work in this practice is mandatory to connect to **Docker Server** in order to manage  **containers**.

## Infrastructure

The complete infraestructure for the practice is the next:

![CompleteStruct](https://sites.google.com/site/manuparra/home/dockerstrcut.jpg)

As you see, inside docker you will work with multiple Docker containers for Services LDAP and FreeIPA, and by the other hand with Docker Containers for applications that connect to FreeIPA in different ways.

*IMPORTANT*: Ports assignment


## Connecting and starting with docker server and docker system

First of all read about how to manage docker container [here!](starting_docker.md). It will be used for LDAP and FreeIPA servers and clients.

# Creating a LDAP with TLS/SSL service

We will create a LDAP over TLS/SSL (389 Directory Server, MIT Kerberos).

We will use one docker container for this deployment following the next:

- For the first we will use an docker container with UbuntuServer minimal and from which we will install everything necessary to serve LDAP service.

- Then using a docker container we will use an initial with CentOS 7 (or https/php container) and install the LDAP client and other clients in order to authenticate from different applications (HTTP, PHP, etc.)


## Create a container

Connect to docker ugr server:

```
ssh manuparra@docker...
```

Create a container with Ubuntu


```
docker pull ubuntu
```

Run the docker container with ubuntu image:

```
docker run -d -i -t --name <nameofcontainer> docker.io/ubuntu:16.04
```

Check if container is running (check column ``NAMES``)

```
docker ps
```


## Connect to the container

Connect to your created docker <nameofcontainer>:

```
docker exec -i -t <containername> /bin/bash
```

It will provide of access to the container.


## Important notes

You must to call to `docker run` with `-p` option in order to redirect ports for LDAP:  

- 389 TCP
- 636 TCP

So, delete your last container and re-run with this parameters:

`-p 14XXX:389 -p 14XXX:636`

It will redirect from outside of the container: 

- from external 14XXX to 389 internal (container).
- from external 14XXX to 636 internal (container).


## Installing SLDAP service

Install OpenLDAP application and services. 

More info and details: https://www.centos.org/docs/5/html/Deployment_Guide-en-US/s1-ldap-quickstart.html 

```
apt-get update
```

After this, execute:


```
apt-get install slapd ldap-utils migrationtools
```

It will install openldap packages and migrationtool (migrate local users to LDAP).

**NOTE:Installation ask you about admin password, write a admin password.**




### Edit the OpenLDAP Server Configuration

Once configured, the first thing to do is to enable systemclt and services execution (privileged):


```
vim /usr/sbin/policy-rc.d
```

or 

```
nano /usr/sbin/policy-rc.d
```

and change ``101`` by ``0``.

Then start this command to configure the Directory parameters

```
dpkg-reconfigure slapd
```


This ask you the next:

```
Configuring slapd
-----------------

If you enable this option, no initial configuration or database will be created for you.

Omit OpenLDAP server configuration? [yes/no] no

The DNS domain name is used to construct the base DN of the LDAP directory. For example, 'foo.example.org' will create the directory with 'dc=foo,
dc=example, dc=org' as base DN.

DNS domain name: ugr.es

Please enter the name of the organization to use in the base DN of your LDAP directory.

Organization name: ugr

Please enter the password for the admin entry in your LDAP directory.

Administrator password: <youradminpassword>

HDB and BDB use similar storage formats, but HDB adds support for subtree renames. Both support the same configuration options.

The MDB backend is recommended. MDB uses a new storage format and requires less configuration than BDB or HDB.

In any case, you should review the resulting database configuration for your needs. See /usr/share/doc/slapd/README.Debian.gz for more details.

  1. BDB  2. HDB  3. MDB
Database backend to use: 2


Do you want the database to be removed when slapd is purged? [yes/no] no

There are still files in /var/lib/ldap which will probably break the configuration process. If you enable this option, the maintainer scripts will move the
old database files out of the way before creating a new database.

Move old database? [yes/no] yes

The obsolete LDAPv2 protocol is disabled by default in slapd. Programs and users should upgrade to LDAPv3.  If you have old programs which can't use LDAPv3,
you should select this option and 'allow bind_v2' will be added to your slapd.conf file.

Allow LDAPv2 protocol? [yes/no] no 

```


### Check configuration

```
slaptest -u
```

NOTE: Don't mind warnings


### Creating certificates for LDAP

```
openssl req -new -x509 -nodes -out /etc/pki/tls/certs/ugr.pem -keyout /etc/pki/tls/certs/ugrkey.pem -days 365
```
Provide your details to generate the certificate.

Common name will be: ugr.es

### Creating Base Directory and schema

This is the tree that will be use:

![ldaptree](https://sites.google.com/site/manuparra/home/tree.jpg)

To do this:


```
touch /root/base.ldif
```


and add:

```
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
ldapadd -x -W -D "cn=admin,dc=ugr,dc=es" -f /root/base.ldif
```

This command will add OU=People, OU=Group, etc.


## LDAP User Management 




### Understanding properties and objectClass of a user in LDAP

If when we create a user from a ldif file we indicate as attributes of the user registry:

```
...
objectClass: person
objectClass: organizationalPerson
objectClass: inetOrgPerson
objectClass: posixAccount
objectClass: top
objectClass: shadowAccount
...
```


With this example we will be defining that the user ``myuser`` is part of the class of objects:

``person, organizationalPerson, inetOrgPerson, posixAccount, top, shadowAccount``

So you have all the properties of the specified classes associated. Each objectClass adds multiple properties and characteristics to the new register entry ``myuser``.

For example ``posixAccount`` will provide (https://tools.ietf.org/html/rfc4519):

```
uidNumber 
gidNumber
homeDirectory 
...
```

For example ``shadowAccount`` will provide (https://tools.ietf.org/html/rfc4519) :

```
...
shadowLastChange
shadowExpire
shadowFlag
...
```

For example ``inetOrgPerson`` attributes: https://tools.ietf.org/html/rfc2798 :

```
...
description
givenName
departmentNumber
title
telephoneNumber
jpegPhoto
...
certificates+
...
```

If you include some attribute and this attribute is not defined in any of the ``objectClass``, the insertion, modification and deletion will show error.



### Creating LDAP users from local users (migration tools)

We can create an local user:

```
useradd myuser

```

add the password:


```
passwd myuser
```

Copy created user to ``/root/passwd``:

```
grep ":10[0-9][0-9]" /etc/passwd > /root/passwd
```

Copy created groups to ``/root/group``:


```
grep ":10[0-9][0-9]" /etc/group > /root/group
```

Now, edit migration tools: 

```
cd /etc/migrationtools/
```

In this file, we create a template for LDAP user with out Base schema

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
cd /usr/share/migrationtools
```

```
./migrate_passwd.pl /root/passwd /root/users.ldif
```

```
./migrate_group.pl /root/group /root/groups.ldif
```

Import users from local:

```
ldapadd -x -W -D "cn=admin,dc=ugr,dc=es" -f /root/users.ldif
```

Import groups from local:

```
ldapadd -x -W -D "cn=admin,dc=ugr,dc=es" -f /root/groups.ldif
```


### Creating users manually from a `.ldif` file


```
ldapadd -x -W -D "cn=admin,dc=ugr,dc=es" -f /root/users.ldif
```

For example file ``users.ldif`` :

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


### Changing password LDAP user

```
ldappasswd -s <new_user_password> -W -D "cn=admin,dc=ugr,dc=es" -x "cn=mparra,ou=Users,dc=ugr,dc=es"
```

In this case, using -W option, ``ldappasswd`` ask for LDAP admin password.

Syntax:

```
ldappasswd [ options ] [ user ]
```

Please check: https://www.centos.org/docs/5/html/CDS/cli/8.0/Configuration_Command_File_Reference-Command_Line_Utilities-ldappasswd.html for more information about this command.

Now, try out:

```
ldapsearch -H ldap://localhost -LL -b ou=Users,dc=ugr,dc=es -x
```

It returns LDAP directory with the last user added.


### Modifying LDAP user account data: DELETE, MODIFY.

The syntax of the ldapmodify tool on the command-line can take any of these forms:

```
ldapmodify [ options ]

ldapmodify [ options ] < LDIFfile

ldapmodify [ options ] -f LDIFfile
```

LDIF text file containing new entries or updates to existing entries on LDAP directory.

When modifying the contents of a directory, you must satisfy several prerequisite conditions. 

First, the bind DN and password used for authentication must have the appropriate permissions for the operations being performed.

Create an example LDIF Modify and save the file as i.e. ``mparra_modify.ldif``

```
dn: cn=mparra,ou=Users,dc=ugr,dc=es
changetype: modify
replace: loginShell
loginShell: /bin/csh
```

Then execute:

```
ldapmodify -x -D "cn=admin,dc=ugr,dc=es" -w password -H ldap:// -f mparra_modify.ldif
```

It will update ``cn=mparra`` with a new ``loginShell``, in this case ``/bin/csh``

Check if the change has been done:

```
...
dn: cn=mparra,ou=Users,dc=ugr,dc=es
objectClass: top
objectClass: account
objectClass: posixAccount
objectClass: shadowAccount
cn: mparra
uid: mparra
uidNumber: 16859
gidNumber: 100
homeDirectory: /home/mparra
gecos: mparra
shadowMax: 0
shadowWarning: 0
loginShell: /bin/csh
...

```

Adding a entry of LDAP user. Create a new file ``manu_add_descrip.ldif`` and add: 

```
dn: cn=mparra,ou=Users,dc=ugr,dc=es
changetype: modify
add: description
description: Manuel Parra
```

Execute next command:

```
ldapmodify -x -D "cn=admin,dc=ugr,dc=es" -w password -H ldap:// -f manu_add_descrip.ldif
```

Now, check changes:


```
ldapsearch -H ldap://localhost -LL -b ou=Users,dc=ugr,dc=es -x
```

And finally delete description. Create a new file i.e. ``manu_del_descr.ldif``

```
dn: cn=mparra,ou=Users,dc=ugr,dc=es
changetype: modify
delete: description
``` 

Then execute: 

```
ldapmodify -x -D "cn=admin,dc=ugr,dc=es" -w password -H ldap:// -f manu_del_descr.ldif
```

Now, check out:

```
ldapsearch -H ldap://localhost -LL -b ou=Users,dc=ugr,dc=es -x
```

Verify if entity description is not set.


## Testing LDAP configuration

```
ldapsearch -x cn=<your user> -b dc=ugr,dc=es
```

```
ldapsearch -x cn=myuser -b dc=ugr,dc=es
```



# Installing and configuring clients for LDAP

For this part will be necessary to use the docker containers. So, you must to use in this case docker container (Not virtual machines).

You must to use one or two containers in which the ldap clients will be installed, to validate that the installation of the SLDAP service is correct.

## Authentication with with PHP on HTTPS/SSL

**NOTE: ALL IN YOUR CONTAINER**

You will need a docker image with SSL, APACHE and PHP:

Use the next *docker.io/eboraas/apache-php*. Remember port redirection to the container:


```
docker run -p 14XXX:80 -p 14XXX:443 --name <yourcontainername> -d eboraas/apache-php
```


```
docker run -p 14001:80 -p 14002:443 --name ContOfManu -d eboraas/apache-php
```

Now if you go to SSL web page of the container created: ``https://docker.ugr.es:14002``, it's working !.



Go inside your container:

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

Now create a file in /var/www/html/ i.e.: authentication.php:

- You must create a simple webpage to authenticate against our LDAP server
- Remember: 
   - Your LDAP IP server 
   - And you Admin Password to do LDAP BIND


Copy the next piece of code and test it (customize with your specific values for your LDAP server):


```
<?php

// example of authentication
$ldaprdn  = 'username';     // ldap rdn or dn
$ldappass = 'password';  // associated password

// Connecting to your Server
$ldapconn = ldap_connect("ldap.example.com")
    or die("Could not connect to LDAP server.");

if ($ldapconn) {

    // realizando la autenticación
    $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

    // verificación del enlace
    if ($ldapbind) {
        echo "LDAP bind successful...";
    } else {
        echo "LDAP bind failed...";
    }

}

?>
```




## Installing PHPLDAPMYADMIN

phpLDAPadmin is a web-based LDAP client. It provides easy, anywhere-accessible, multi-language administration for your LDAP server.
Its hierarchical tree-viewer and advanced search functionality make it intuitive to browse and administer your LDAP directory. Since it is a web application, this LDAP browser works on many platforms, making your LDAP server easily manageable from any location.

It can be installed into a container in different ways:

1 With a specific container with phpLDAPadmin:
   - Remember that you must to redirect ports (-p option), and you can use your browser (``http://docker.ugr.es:14XXX/``) and you can manage it.
   - This is: https://github.com/osixia/docker-phpLDAPadmin

2 With a container with apache, php and MySQL and install phpLDAPadmin from the scratch.



# Working with OpenNebula

Read our manual about OpenNebula [here](starting_OpenNebula.md), to learn how to work with Virtual Machines within the OpenNebula environment.


# Creating a freeIPA service

We will create a FreeIPA service (integrated security information management solution combining Linux (Fedora), 389 Directory Server, MIT Kerberos, NTP, DNS, Dogtag (Certificate System)). 


![FreeIPA_docker](https://sites.google.com/site/manuparra/home/docker_freeipa.png)

We will use a docker container for this deployment, one container for freeIPA and the another docker container for clients (and other apps that will connect to FreeIPA).

- For the first we will use an docker container with Ubuntu or CentOS 7 and from which we will install everything necessary to serve freeIPA.

- In the container we will use Ubuntu or CentOS 7 or other container and install the FreeIPA client and other connection clients from different applications.


## Create a container

Connect to docker ugr server:

```
ssh manuparra@docker...
```

Create a container with Ubuntu or centos /


```
docker pull ubuntu
docker pull centos
```

Run the docker container with ubuntu image:

```
docker run -d -i -t --name <nameofcontainer> docker.io/ubuntu:16.04
```

or

```
docker run -d -i -t --name <nameofcontainer> centos
```


Check if container is running (check column ``NAMES``)

```
docker ps
```


## Connect to the container

Connect to your created docker <nameofcontainer>:

```
docker exec -i -t <containername> /bin/bash
```

It will provide of access to the container.



## Install freeIPA service

Follow the next instructions  for CentOS7 (+info: https://github.com/manuparra/freeipa):

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


