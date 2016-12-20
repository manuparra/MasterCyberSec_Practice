# Starting with SELINUX 

![LogoHeadMasterCES](https://sites.google.com/site/manuparra/home/logo_master_ciber.png)


[UGR](http://www.ugr.es) | [DICITS](http://dicits.ugr.es) | [SCI2S](http://sci2s.ugr.es) | [DECSAI](http://decsai.ugr.es)

Manuel J. Parra Royón (manuelparra@decsai.ugr.es) & José. M. Benítez Sánchez (j.m.benitez@decsai.ugr.es)


Table of Contents
=================
   
   * [What is SELinux?](#what-is-selinux)
   * [SELinux Policy](#selinux-policy)
   * [Installing SELinux](#installing-selinux)
      * [Setting up SELinux](#setting-up-selinux)
         * [Checking yout SELinux status and modes](#checking-yout-selinux-status-and-modes)
         * [Configuration file](#configuration-file)
         * [Enabling / disabling SELinux](#enabling--disabling-selinux)
   * [Troubleshooting with SELinux](#troubleshooting-with-selinux)
      * [Checking SELinux logs](#checking-selinux-logs)
   * [Enable Apache Public HTML folder with SELinux](#enable-apache-public-html-folder-with-selinux)
      * [Persistent context changes](#persistent-context-changes)
      * [SELinux and ports security](#selinux-and-ports-security)
   * [References and more information](#references-and-more-information)




# What is SELinux?

**Definition:**

> Security Enhanced Linux or SELinux is an advanced access control mechanism built into most modern Linux distributions. It was initially developed by the US National Security Agency to protect computer systems from malicious intrusion and tampering. Over time, SELinux was released in the public domain and various distributions have since incorporated it in their code.

SELinux is a powerful tool for controlling what applications are allowed to do on your system. SELinux is a labeling system where every process and every object (files, directories, devices, network ports, etc.) gets a label. Then a large rules database, called policy, is loaded into the kernel. The kernel, based on the policy, controls what each process can do based on its label, and the label of the object it is trying to access. 

For example SELinux allows a process with the Apache label (httpd_t) to share data labeled as "read/only Apache content" (httpd_sys_content_thttpd_sys_content_rw_t). SELinux will block Apache processes from reading data labeled as user's home content (user_home_t) or database data (mysql_db_t). Apache processes can listen on ports labeled as the Apache port (http_port_t) but can not connect to the ports labeled as the mail port (smtp_port_t).

SELinux provides confinement on an application if the application has been hacked, even if the application is running as root. If policy says the (for example) Apache process is only supposed to read Apache content, then even if a hacker gets uid = 0 (the root user), he will not be able to turn it into a spam bot; he will not be able to read credit card data in your home directory; and he will not be able to destroy log files. The hacked process will only be able to act as an Apache process.

![SELinuxExample](https://www.centos.org/docs/5/html/Deployment_Guide-en-US/images/selinux/SELinux_Decision_Process.png)

# SELinux Policy

SELinux allows different policies to be written that are interchangeable. The default policy in CentOS is the targeted policy which "targets" and confines selected system processes including httpd, named, dhcpd, mysqld, etc.


# Installing SELinux

```
yum install policycoreutils policycoreutils-python selinux-policy selinux-policy-targeted libselinux-utils setroubleshoot-server setools setools-console mcstrans
```

Now we should have a system that's loaded with all the SELinux packages



## Setting up SELinux

**SELinux Modes**

Three possible modes:

- Enforcing: In enforcing mode SELinux will enforce its policy on the Linux system and make sure any unauthorized access attempts by users and processes are denied
- Permissive: Permissive mode is like a semi-enabled state.
- Disabled (not recommended):  the system won't be running with enhanced security.

### Checking yout SELinux status and modes

We can run the getenforce command:

```
getenforce
```

or

```
sestatus
```

What is your SELinux status? 

### Configuration file

The configuration file for SELinux is ``/etc/selinux/config``.

```
vi /etc/selinux/config
```

The output:

```
# This file controls the state of SELinux on the system.
# SELINUX= can take one of these three values:
#     enforcing - SELinux security policy is enforced.
#     permissive - SELinux prints warnings instead of enforcing.
#     disabled - No SELinux policy is loaded.
SELINUX=enforcing
# SELINUXTYPE= can take one of three two values:
#     targeted - Targeted processes are protected,
#     minimum - Modification of targeted policy. Only selected processes are protected.
#     mls - Multi Level Security protection.
SELINUXTYPE=targeted
```

So, here SELinux is ``enforcing``. And SELINUXTYPE directive is the policy that will be used. Here is targeted, so SELinux allows you to customize and fine tune access control permissions.

### Enabling / disabling SELinux

Modify file 

```
vi /etc/sysconfig/selinux
```

and modify:

```
...
SELINUX=permissive 
...
```

**permisive** will print warnings, but is not enforcing

with ``enforcing`` 

```
...
SELINUX=enforcing 
...
```

Setting the status to ``permissive`` first is necessary because every file in the system needs to have its context labelled before SELinux can be enforced.


Due to SELinux is loaded into the kernel :

```
reboot
```

Log as root and search for the string "SELinux is preventing" from the contents of the ``/var/log/messages file``.


```
cat /var/log/messages | grep "SELinux is preventing"
```

If there are no errors, we can safely move to the next step. However, it would still be a good idea to search for text containing "SELinux" in ``/var/log/messages` file. In our system, we ran the following command:

```
cat /var/log/messages | grep "SELinux"
```

And if everything went fine, change the SELINUX directive from permissive to enforcing in the `/etc/sysconfig/selinux` file:

```
...
SELINUX=enforcing
...
```

Reboot again: ``reboot``. After reboot check SELinux Status:

```
sestatus
```

or 

```
getenforce
```

# Troubleshooting with SELinux

There are a several reasons why SELinux may deny access to a file, process or resource:

- A mislabeled file.
- A process running under the wrong SELinux security context.
- A bug in policy. An application requires access to a file that wasn't anticipated when the policy was written and generates an error.
- An intrusion attempt.

## Checking SELinux logs

By default SELinux log messages are written to ```/var/log/audit/audit.log``` via the Linux Auditing System ``auditd``, which is started by default. If the auditd daemon is not running, then messages are written to ``/var/log/messages`` .


# Enable Apache Public HTML folder with SELinux


![SELinuxApache](https://sites.google.com/site/manuparra/home/selinux.png)

```
vi -w /etc/httpd/conf.d/userdir.conf
```

Change and include:

```
<IfModule mod_userdir.c>
    #
    # UserDir is disabled by default since it can confirm the presence
    # of a username on the system (depending on home directory
    # permissions).
    #
    UserDir enabled manuparra

    #
    # To enable requests to /~user/ to serve the user's public_html
    # directory, remove the "UserDir disabled" line above, and uncomment
    # the following line instead:
    #
    UserDir public_html

</IfModule>

<Directory /home/*/public_html>
        ## Apache 2.4 users use following ##
        AllowOverride FileInfo AuthConfig Limit Indexes
        Options MultiViews Indexes SymLinksIfOwnerMatch IncludesNoExec
        Require method GET POST OPTIONS

        ## Apache 2.2 users use following ##
        Options Indexes Includes FollowSymLinks        
        AllowOverride All
        Allow from all
        Order deny,allow
</Directory>
```


To allow a few users to have UserDir directories, but not anyone else, use the following:

```
UserDir disabled
UserDir enabled testuser1 testuser2 testuser3
```


To allow most users to have UserDir directories, but deny this to a few, use the following:

```
UserDir enabled
UserDir disabled testuser4 testuser5 testuser6
```

Restart Apache ``service httpd restart`` .


Create a public_html folder for the user :

```
mkdir /home/manuparra/public_html
```

Change permissions to home:

```
chmod 711 /home/manuparra

chown manuparra:manuparra /home/manuparra/public_html
chmod 755 /home/manuparra/public_html
```

Set-up SELinux and Apache:

```
setsebool -P httpd_enable_homedirs true
chcon -R -t httpd_sys_content_t /home/manuparra/public_html
```

With ``chcon`` change the SELinux security context of each FILE to CONTEXT.

The ``chcon`` command changes the SELinux context for files. Changes made with the chcon command **do not survive** a file system relabel, or the execution of the restorecon command. SELinux policy controls whether users are able to modify the SELinux context for any given file. When using chcon, users provide all or part of the SELinux context to change. An incorrect file type is a common cause of SELinux denying access.

## Persistent context changes 

The ``semanage fcontext`` command is used to change the SELinux context of files. To show contexts to newly created files and directories, run the following command as root:

```
semanage fcontext -C -l
```


Changes made by ``semanage fcontext`` are used by the following utilities. The setfiles utility is used when a file system is relabeled and the restorecon utility restores the default SELinux contexts. This means that changes made by semanage fcontext are persistent, even if the file system is relabeled. SELinux policy controls whether users are able to modify the SELinux context for any given file.


```
semanage fcontext -a -t httpd_sys_content_rw_t '/home/manuparra/public_html(/.*)?'

restorecon -R -v /home/manuparra/public_html
```

The first command uses ``semanage`` (SELinux Manage) with the ``fcontext`` command (File Context). We tell the system to add the SELinux type ``httpd_sys_content_rw_t`` type to the ``/home/manuparra/public_html`` directory and all of its children using the regular expression ``'/home/manuparra/public_html(/.*)?'``. Then running restorecon will actually change the labels on disk on all existing files and directories.

## SELinux and ports security

SELinux also controls network access. By default the Apache process is allowed to bind the ``http_port_t`` type. This type is defined for the following tcp ports:

``80,443, ...``

If you wanted to allow Apache to bind to tcp port 81, you would execute the following command:

```
semanage port -a -t http_port_t -p tcp 81
```

You can use the semanage port -l command to list all port definitions, or system-config-selinux.




# References and more information

- https://linuxacademy.com/blog/linux/exploring-selinux-context/
- https://www.digitalocean.com/community/tutorials/an-introduction-to-selinux-on-centos-7-part-1-basic-concepts
- https://www.drupalwatchdog.com/volume-2/issue-2/using-apache-and-selinux-together

