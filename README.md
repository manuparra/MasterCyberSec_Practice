# Starting with docker (Primeros pasos con docker)
Master en CiberSeguridad 2016-2017. Universidad de Granada.

Manuel J. Parra Royón & José. M. Benítez Sánchez

Soft Computing and Intelligent Information Systems

University of Granada


# What is docker?

Docker containers wrap a piece of software in a complete filesystem that contains everything needed to run: code, runtime, system tools, system libraries, anything that can be installed on a server. This guarantees that the software will always run the same, regardless of its environment.

## What is the difference between Docker and Virtual Machines?

Containers and virtual machines have similar resource isolation and allocation benefits, but a different architectural approach allows containers to be more portable and efficient.

### VIRTUAL MACHINES

Virtual machines include the application, the necessary binaries and libraries, and an **entire guest operating system**,  all of which can amount to tens of GBs.

![VMsDiff](https://www.docker.com/sites/default/files/WhatIsDocker_2_VMs_0-2_2.png)

### CONTAINERS

Containers include the application and all of its dependencies, but **share the kernel with other containers, running as isolated processes in user space on the host operating system**. Docker containers are not tied to any specific infrastructure: they run on any computer, on any infrastructure, and in any cloud.

![DockersDiff](https://www.docker.com/sites/default/files/WhatIsDocker_3_Containers_2_0.png)

## Advantages of Docker

**Rapid development**

- Stop wasting hours setting up developer environments, spinning up new instances, and making copies of production code to run locally. With Docker, you simply take copies of your live environment and run them on any new endpoint running a Docker engine.

**Work comfortably**

- The isolation capabilities of Docker containers free developers from constraints: they can use the best language and tools for their application services without worrying about causing internal tooling conflicts.

**Forget inconsistences**

- Packaging an application in a container with its configs and dependencies guarantees that the application will always work as designed in any environment: locally, on another machine, in test or production. No more worries about having to install the same configurations into different environments.

**Share your containers**

- Store, distribute, and manage Docker images in Docker Hub with your team. Image updates, changes, and history are automatically shared across your organization.

**Scale**

- Docker allows you to dynamically change your application from adding new capabilities and scaling services, to quickly changing problem areas.

- Docker containers spin up and down in seconds, making it easy to scale application services to satisfy peak customer demand, and then reduce running containers.

- Docker makes it easy to identify issues, isolate the problem container, quickly roll back to make the necessary changes, and then push the updated container into production. Isolation between containers makes these changes less disruptive than in traditional software models.


**Multiplataform**

- Docker runs on Windows, Mac and Linux.



# Starting

Log and connect to our system with:

```
ssh manuparra@.........es
```

First of all, check that you have access to the docker system, try this command:

```
docker run hello-world
```

And it will return a message where it shows that your installation appears to be working correctly and you are allow use it.


# First container

To create a new container in docker, it can be done in two ways: 

- on the one hand by doing it by creating a docker file (link) or 

- by downloading / using a container that is already created by other users.

Containers already created available to be used are stored in a kind of container market at https://hub.docker.com/. Virtually anything you think will already be dockerized. 

But you can also build your own container with everything you need. For example a container having your complete application with all its dependencies, mixing i.e. php, mysql, nginx, etc. on the same container or on different containers.

## A simple web server with NGINX

The first thing we need is to download the docker image from nginx, for them we check whether or not the image is in the list of available images:

```
docker images
```

If the image is not found locally, Docker will pull it from Docker Hub and you will use it:

```
docker pull nginx
```

It will download the image of nginx container. 

And now you can see if image is on images repository using:

```
docker images
```

```
REPOSITORY                        TAG                 IMAGE ID            CREATED             SIZE
docker.io/nginx                   latest              abf312888d13        12 days ago         181.5 MB
...
```

Run the container, using the next syntax:

```
docker run -d -p <yourport>:<containerport> --name <mynameofcontainer> <container>
```

Options:

``-d Run container in background and print container ID``

``-p Publish a container's port(s) to the host``

``--name Name of your contaniner i.e. 'containerofmanuparra'``

``<container> This is the container that will be executed`` 

So, we execute:

```
docker run -d -p <yourport>:80 --name testnginx nginx
```

In ``<yourport>`` write your individually assigned port.

To check if your container is runnig and see the status of all your container:

```
docker ps
```

And it returns:

```
CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                            NAMES
52ad2efb9fff        nginx               "nginx -g 'daemon off"   12 minutes ago      Up 12 minutes       443/tcp, 0.0.0.0:14000->80/tcp   testnginx
...
```

Where ``container ID`` is the unique ID of your Container. You can use Container ID or NAMES to refer to your container. ``IMAGE`` is the name of the container image. ``PORTS`` show what is the correspondence of the ports between server and docker container.


And now, go to your browser and write:

```
http://docker.ugr.es:<yourport>/
```

![nginxDocker](https://sites.google.com/site/manuparra/home/docker_nginx.png)

### Enter in the nginx container:

```
docker exec -i -t testnginx /bin/bash
```

With this command your are inside of the container and you can modify things, for instance change the main website exposed by NGINX:

``
vim /usr/share/nginx/html/index.html 
``

ERROR: command ´´vi´´ is not in container. 

***Why***? -> Container has been built with minimal software! ... , so you need to install it:

```
apt-get install vim
```

and try again:

``
vim /usr/share/nginx/html/index.html 
``

Edit the file and change something ``<H1>Hello Manuel Parra</H1>``. Save and close and try again in your browser: 

```
http://docker.ugr.es:<yourport>/
```

# Review of docker commands


## Show docker Images

With:

```
docker images
```

The default docker images will show all top level images, their repository and tags, and their size, and it will return:

```
REPOSITORY                        TAG                 IMAGE ID            CREATED             SIZE
docker.io/tomcat                  latest              c6cfe59eb987        3 weeks ago         356.9 MB
docker.io/osixia/openldap         1.1.7               7043188ce9b7        4 weeks ago         223 MB
...
```

## List of launched/running docker containers:

``docker ps``

Show all docker containers running and details about execution, ports and status.

## Download a image of docker container:

``docker pull``

Download the image container selected. You can go to https://hub.docker.com/ and search for 'dockerized' images from other users.

```
#docker pull <nameofcontainer>

docker pull jetbrainshost/telegram-bot
# It will download a telegram bot named: jetbrainshost/telegram-bot
```

## Run a container

``docker run``

Run a docker image. It allows to assign port in/out, name, external folders, etc. 

```
#docker run -d -p <yourport>:80 --name <name> <container>

docker run -d -p 8080:80 --name testnginx nginx
# It will run nginx at 8080 external port ant connect it with internal container port 80. The name of container will be testnginx.
```

## Stop a container

You can stop a container using

```
#docker stop <nameofcontainer or container ID>

docker stop testnginx
```

## Restarting a container

You can restart a container using:

```
#docker restart <nameofcontainer or container ID>

docker restart testnginx
```

## Deleting a container or shutdown a container

If the lifecycle of your container has  finished and you want remove it:

```
#docker rm <nameofcontainer or container ID>

docker rm testnginx
```

If the container is running, first try ``stop `` and then ``rm``, but if you cant stop it, force remove:

```
#docker rm --force <nameofcontainer or container ID>

docker rm --force testnginx
```

## Execute commands inside running container:

If you need modify files or add something inside container:

```
#docker exec -i -t <nameofcontainer or ID> /bin/bash

docker exec -i -t testnginx /bin/bash
```

It will open a shell (bash) into the container.


## Upload image to docker::hub

If you have created a container with an application or service already prepared you can upload it to the application repository of DockerHub.

```
docker push manuparra/myapp_dockerized
```

Previously you must have registered on the dockerhub platform.

## Statistics of a container

Display a live stream of container resource usage statistics. It is very useful to know what is the performance of you services or application deployed with docker.

```
docker stats testnginx
```








# References and more information

- Definition of docker: https://www.docker.com/what-docker
- Docker Hub: https://hub.docker.com/
- First steps with Docker: https://docs.docker.com/engine/getstarted/
- Running `Hello World`: https://docs.docker.com/engine/tutorials/dockerizing/
- Free EBook about Docker:  https://goto.docker.com/docker-for-the-virtualization-admin.html
