# Starting with SELINUX 

![LogoHeadMasterCES](https://sites.google.com/site/manuparra/home/logo_master_ciber.png)


[UGR](http://www.ugr.es) | [DICITS](http://dicits.ugr.es) | [SCI2S](http://sci2s.ugr.es) | [DECSAI](http://decsai.ugr.es)

Manuel J. Parra Royón (manuelparra@decsai.ugr.es) & José. M. Benítez Sánchez (j.m.benitez@decsai.ugr.es)


# What is SELinux?

**Definition:**

> Security Enhanced Linux or SELinux is an advanced access control mechanism built into most modern Linux distributions. It was initially developed by the US National Security Agency to protect computer systems from malicious intrusion and tampering. Over time, SELinux was released in the public domain and various distributions have since incorporated it in their code.

SELinux is a powerful tool for controlling what applications are allowed to do on your system. SELinux is a labeling system where every process and every object (files, directories, devices, network ports, etc.) gets a label. Then a large rules database, called policy, is loaded into the kernel. The kernel, based on the policy, controls what each process can do based on its label, and the label of the object it is trying to access. 

For example SELinux allows a process with the Apache label (httpd_t) to share data labeled as "read/only Apache content" (httpd_sys_content_thttpd_sys_content_rw_t). SELinux will block Apache processes from reading data labeled as user's home content (user_home_t) or database data (mysql_db_t). Apache processes can listen on ports labeled as the Apache port (http_port_t) but can not connect to the ports labeled as the mail port (smtp_port_t).

SELinux provides confinement on an application if the application has been hacked, even if the application is running as root. If policy says the (for example) Apache process is only supposed to read Apache content, then even if a hacker gets uid = 0 (the root user), he will not be able to turn it into a spam bot; he will not be able to read credit card data in your home directory; and he will not be able to destroy log files. The hacked process will only be able to act as an Apache process.

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

And if everything went fine, change the SELINUX directive from permissive to enforcing in the ``/etc/sysconfig/selinux` file:

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




# References and more information

- https://www.digitalocean.com/community/tutorials/an-introduction-to-selinux-on-centos-7-part-1-basic-concepts
- https://www.drupalwatchdog.com/volume-2/issue-2/using-apache-and-selinux-together

