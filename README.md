# Starting with docker (Primeros pasos con docker)
Master en CiberSeguridad 2016-2017. Universidad de Granada.

Manuel J. Parra Royón & José. M. Benítez Sánchez

Soft Computing and Intelligent Information Systems

University of Granada


# What is docker?

Docker containers wrap a piece of software in a complete filesystem that contains everything needed to run: code, runtime, system tools, system libraries, anything that can be installed on a server. This guarantees that the software will always run the same, regardless of its environment.

## What is the difference between Docker and Virtual Machines?

Containers and virtual machines have similar resource isolation and allocation benefits, but a different architectural approach allows containers to be more portable and efficient.

**VIRTUAL MACHINES**

Virtual machines include the application, the necessary binaries and libraries, and an **entire guest operating system**,  all of which can amount to tens of GBs.

![VMsDiff](https://www.docker.com/sites/default/files/WhatIsDocker_2_VMs_0-2_2.png)

**CONTAINERS**

Containers include the application and all of its dependencies, but **share the kernel with other containers, running as isolated processes in user space on the host operating system**. Docker containers are not tied to any specific infrastructure: they run on any computer, on any infrastructure, and in any cloud.

![DockersDiff](https://www.docker.com/sites/default/files/WhatIsDocker_3_Containers_2_0.png)

## Advantages of Docker

**Rapid development**

Stop wasting hours setting up developer environments, spinning up new instances, and making copies of production code to run locally. With Docker, you simply take copies of your live environment and run them on any new endpoint running a Docker engine.

**Work comfortably**

The isolation capabilities of Docker containers free developers from constraints: they can use the best language and tools for their application services without worrying about causing internal tooling conflicts.

**Forget inconsistences**

Packaging an application in a container with its configs and dependencies guarantees that the application will always work as designed in any environment: locally, on another machine, in test or production. No more worries about having to install the same configurations into different environments.

**Share your containers**

Store, distribute, and manage Docker images in Docker Hub with your team. Image updates, changes, and history are automatically shared across your organization.

**Scale**

- Docker allows you to dynamically change your application from adding new capabilities and scaling services, to quickly changing problem areas.

- Docker containers spin up and down in seconds, making it easy to scale application services to satisfy peak customer demand, and then reduce running containers.

- Docker makes it easy to identify issues, isolate the problem container, quickly roll back to make the necessary changes, and then push the updated container into production. Isolation between containers makes these changes less disruptive than in traditional software models.


**Multiplataform**

Docker runs on Windows, Mac and Linux.



#





# References and more information

- Definition of docker: https://www.docker.com/what-docker
- Docker Hub: https://hub.docker.com/
- First steps with Docker: https://docs.docker.com/engine/getstarted/
- Running `Hello World`: https://docs.docker.com/engine/tutorials/dockerizing/
- Free EBook about Docker:  https://goto.docker.com/docker-for-the-virtualization-admin.html
