# Starting with MongoDB (Primeros pasos con MongoDB)

![LogoHeadMasterCES](https://sites.google.com/site/manuparra/home/logo_master_ciber.png)


[UGR](http://www.ugr.es) | [DICITS](http://dicits.ugr.es) | [SCI2S](http://sci2s.ugr.es) | [DECSAI](http://decsai.ugr.es)

Manuel J. Parra Royón (manuelparra@decsai.ugr.es) & José. M. Benítez Sánchez (j.m.benitez@decsai.ugr.es)



Table of Contents
=================



# Using MongoDB, why? and where?

MongoDB main features:

* Document Oriented Storage − Data is stored in the form of JSON style documents.
* Index on any attribute
* Replication and high availability
* Auto-sharding
* Rich queries

Using Mongo:

* Big Data
* Content Management and Delivery
* Mobile and Social Infrastructure
* User Data Management
* Data Hub



# Starting with MongoDB

Log and connect to our system with:

```
ssh manuparra@.........es
```

First of all, check that you have access to the mongo tools system, try this command:

``mongo + tab``

it will show:

```
mongo         mongodump     mongoexport   mongofiles    
mongoimport   mongooplog    mongoperf     mongorestore  mongostat     mongotop 
```

## Connecting with MongoDB Service:

The default port for mongodb and mongos instances is 27017. 
You can change this port with port or --port when connect.

Write:

```
mongo
```

It will connect with defaults parameters: ``localhost`` , port: ``27017`` and database: ``test``

```
MongoDB shell version: 2.6.12
connecting to: test
>
```

Exit using ``CTRL+C`` or ``exit``

Each user have an account on mongodb service. To connect:

```
mongo localhost:27017/manuparra -p 
```

It will us ``password``. 

```
mongo localhost:27017/manuparra -p mipasss 
```


MongoDB service is running locally in Docker systems, so, if you connect from docker containers or Virtual Machines, you must to use local docker system IP:

```
mongo 192.168.10.30:27017/manuparra -p mipasss 
```

## Selecting/Creating/Deleting DataBase

The command will create a new database if it doesn't exist, otherwise it will return the existing database.

``
> use manuparra:
``

Now you are using ``manuparra`` database.

If you want to kwnow what database are you using:

``
> db
``

The ```command db.dropDatabase()`` is used to drop a existing database.

DO NOT USE THIS COMMAND, WARNING:

``db.dropDatabase()``

To kwnow the size of databases:

```
show dbs
```

## Creating a Collection

Basic syntax of createCollection() command is as follows:

```
db.createCollection(name, options)
```

where ``options`` is Optional and specify options about memory size and indexing.

Remember that firstly mongodb needs to kwnow what is the Database where it will create the Collection. Use ``show dbs`` and then ``use <your database>``.

``
use manuparra;
``

And then create the collection:

```
db.createCollection("MyFirstCollection")
```

When created check:

```
show collections
```

In MongoDB, you don't need to create the collection. MongoDB creates collection automatically, when you insert some document:

```
db.MySecondCollection.insert({"name" : "Manuel Parra"})
```

You have new collections created:

```
show collections
```

## Delete collections

To remove a collection from the database:

```
db.MySecondCollection.drop();

```








